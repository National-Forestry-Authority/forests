const fireAlertsUrl = 'https://data-api.globalforestwatch.org/dataset/nasa_viirs_fire_alerts/v20220726/query/json';
const deforestationAlertsUrl = 'https://data-api.globalforestwatch.org/dataset/gfw_integrated_alerts/v20230215/query/json';

(function ($, Drupal) {
  farmOS.map.behaviors.farm_nfa_gfw_layers = {
    attach: async function (instance) {
      const mapTargets = ['farm-map-farm-nfa-plan-locations', 'farm-map map-with-ol-side-panel',
        'farm-map-dashboard'
      ];
      const targetId = instance.target.id;
      if (!mapTargets.includes(targetId)) return;
      const isGfwDashboard = targetId == 'farm-map-dashboard';
      const pageOrigin = `${window.location.protocol}//${instance.farmMapSettings.host}`;
      const planId = instance.farmMapSettings.plan
      const assetId = instance.farmMapSettings.asset
      const assetType = instance.farmMapSettings.asset_type;
      let defaultMonthDuration = (assetType == 'land' || targetId == 'farm-map-dashboard') ? 1 : 3;
      const dateType = isGfwDashboard ? '' : 'date';
      const { startDate, endDate } = date.getDefaultDates(dateType, defaultMonthDuration);
      if (isGfwDashboard) {
        const dashboardGeometryUrl = `${pageOrigin}/assets/geojson/full/cfr?is_location=1`;
        const geometry = await geometryHelper.getGeometry(dashboardGeometryUrl);
        await gfwMap.updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl, {startDate, endDate}, geometry);
      } else {
        let geometryUrl = '';
        if (planId) geometryUrl = `/nfa-assets/geojson/${planId}`;
        if (assetId) geometryUrl = `/asset/geojson/${assetId}`;
        if (!geometryUrl) return; 
        geometryUrl = `${pageOrigin}${geometryUrl}`;
        const geometry = await geometryHelper.getGeometry(geometryUrl);
        // Add layers for fire and deforestation alerts in the GFW plan tab
        $(".daterangepicker").daterangepicker({
          change: function () {
            gfwMap.updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl, '', geometry);
          },
        });
        $(".daterangepicker").daterangepicker("setRange", {start: startDate, end: endDate});
      }
    }
  }
}(jQuery, Drupal))

// gfw map module
const gfwMap = {
  // function to update the map layers when the date range is changed
  updateMapLayers: async function(instance, fireAlertsUrl, deforestationAlertsUrl, dateRange, geometry) {
    return new Promise(async (resolve, reject) => {
      if (!dateRange) dateRange = date.getStartEndDateFromDOM('.daterangepicker-container');
      const previousDateRange = JSON.parse(localStorage.getItem('gfwDashboardDateRange'));
      const previousStartDate = previousDateRange?.startDate;
      const previousEndDate = previousDateRange?.endDate;
      const isDateRangeSame = previousStartDate == dateRange?.startDate && previousEndDate == dateRange?.endDate;
      const map = instance.map;
      const layers = map.getLayers().getArray();
      for (let i = 0; i < layers.length;) {
        const layerTitle = layers[i]?.values_?.title;
        if (layerTitle == 'Fire Alerts' || layerTitle == 'Deforestation Alerts') await map.removeLayer(layers[i]);
        else i++;
      }
      map.getTargetElement().classList.add('spinner');
      const mapLayers = [
        gfwMap.farmNfaPlotGfwApiMap(instance, 'fire', fireAlertsUrl, dateRange, geometry, isDateRangeSame),
        gfwMap.farmNfaPlotGfwApiMap(instance, 'deforestation', deforestationAlertsUrl, dateRange, geometry, isDateRangeSame)
      ];
      try {
        await Promise.all(mapLayers);
      } catch (err) { }
      map.getTargetElement().classList.remove('spinner');
      resolve('success');
    });
  },
  farmNfaPlotGfwApiMap: async function(instance, mapType, gfwApiUrl, dateRange, geometry, isDateRangeSame) {
    return new Promise(async (resolve, reject) => {
      let startDate = dateRange?.startDate;
      let endDate = dateRange?.endDate;
      const nullDateRange = !startDate && !endDate;
      if (nullDateRange) return;
      const hasBothdateRange = startDate && endDate;
      const hasSingleDateRange = startDate && !endDate;
      const baseQuery = instance.farmMapSettings.base_query || 'SELECT latitude,longitude FROM results';
      // configuring the query to get the data from GFW API
      let query = `${baseQuery} WHERE ${mapType == "fire" ? `iso='UGA' AND ` : ''}`;
      const dateParameter = mapType == "fire" ? "alert__date" : "gfw_integrated_alerts__date";
      if (hasBothdateRange) query += `${dateParameter} >= '${startDate}' AND ${dateParameter} <= '${endDate}'`;
      else if (hasSingleDateRange) query += `${dateParameter} = '${startDate}'`;
      // setting the cfr plan url for the geojson data
      if (!geometry) resolve('No plan or asset id found');
      try {
        const isGfwDashboard = instance.target.id == 'farm-map-dashboard';
        const layerInfo = {
          "fire": {
            "layerColor": "red",
            "layerName": `GFW Fire Alerts ${isGfwDashboard ? `(${startDate||''} - ${endDate || 'Present'})`: ''}`
          },
          "deforestation": {
            "layerColor": isGfwDashboard ? "orange" : "green",
            "layerName": `GFW Deforestation Alerts ${isGfwDashboard ? `(${startDate||''} ${endDate ? `- ${endDate}`:''})`: ''}`
          }
        }
        let geoJson = await gfwMap.fetchGfwData(instance, geometry, gfwApiUrl, query, mapType, dateRange, isDateRangeSame);
        if (!geoJson) resolve('No data found');
        if (!isGfwDashboard) {
          instance.map.getLayers().forEach(layer => {
            const layerName = layerInfo[mapType].layerName;
            if (layer?.get('title') && layer?.get('title') == layerName){
              instance.map.removeLayer(layer);
            }
          });
        }
        await instance.addLayer('geojson', {
          title: layerInfo[mapType].layerName,
          geojson : geoJson,
          color: layerInfo[mapType].layerColor,
        });
        let allLayersControllers = document.querySelectorAll(".layer-switcher input");
        allLayersControllers.forEach((layerController) => {
          let shouldDisableLayer = layerController.nextSibling.innerText.trim() !== "Locations";
          if (!isGfwDashboard) {
            shouldDisableLayer = shouldDisableLayer && layerController.nextSibling.innerText.trim() != layerInfo["fire"].layerName.trim();            
          }
          if (shouldDisableLayer) {
            layerController.click();
          }
        });
        resolve('success');
      } catch (err) {
        reject(err);
      }
    });
  },
  fetchGfwData: async function(instance, geometry, gfwApiUrl, query, key, dateRange, isDateRangeSame) {
    const geoJson = {
      "type": "FeatureCollection",
      "features": []
    };
    const isGfwDashboard = instance.target.id == 'farm-map-dashboard';
    const db = isGfwDashboard ? await idxDB.open('NfaDatabase', 7, 'gfwDashboardGeometryStore', 'id', true) : null;
    if (isGfwDashboard) {
      if (isDateRangeSame) {
        const gfwGeometryData = geometryHelper.extractGeometryDataFromIdxDb(db, 'gfwDashboardGeometryStore', key);
        if (gfwGeometryData) return gfwGeometryData;
      } else {
        localStorage.setItem('gfwDashboardDateRange', JSON.stringify({ ...dateRange }));
      }
    }
   
    try {
      const featureCalls = Math.ceil(geometry.features.length/50);
      let locationData = [];
      for (let j = 0; j < featureCalls; j++) {
        let featureStartIndex = j * 50;
        let featureEndIndex = featureStartIndex + 50;
        let featureSlice = geometry.features.slice(featureStartIndex, featureEndIndex);
        const gfwLocationData = await this.getGfwLocationDataPromises(featureSlice, gfwApiUrl, query);
        if(!gfwLocationData) continue;
        locationData.push(...gfwLocationData);
      }
      locationData.forEach((location) => {
        let locations = location && location.data;
        if (locations) {
          locations.forEach((location) => {
            let latLongArray = [location.longitude, location.latitude];
            let geoJsonFeature = {
              "type": "Feature",
              "properties": {},
              "geometry": {
                "coordinates": latLongArray,
                "type": "Point"
              }
            };
            geoJson.features.push(geoJsonFeature);
          })
        }
      });
      if (isGfwDashboard) {
        const objectStore = idxDB.getObjectStore(db, 'gfwDashboardGeometryStore', 'readwrite');
        await idxDB.clear(objectStore, key);
        await idxDB.add(objectStore, {
          [key]: JSON.stringify(geoJson)
        }, key);
      }
    } catch (err) {
    }
    return geoJson;
  },
  getGfwLocationDataPromises: async function(geometry, gfwApiUrl, query) {
    let locationData = [];
    try {
      if(geometry.length == 0) return [];
      let gfwLocationData = [];
      const iterableGeometries = 50
      for (let i = 0; i < iterableGeometries; i++) {
        if (!geometry[i]) continue;
        let locationGeometry = [
          ...(geometry[i]?.geometry?.coordinates || []),
        ];
        if (locationGeometry.length == 0 && geometry[i]?.geometry?.geometries) {
          geometry[i]?.geometry?.geometries?.forEach((geometry) => { 
            locationGeometry.push(...geometry?.coordinates);
          });
        }
        locationGeometry = locationGeometry.filter((coordinate) => coordinate);
        if(locationGeometry.length == 0) continue;
        let gfwApiBody = {
          "geometry": {
            "type": "Polygon",
            "coordinates": []
          },
          "sql": `${query}`
        };
        gfwApiBody.geometry.coordinates = [...locationGeometry];
        gfwLocationData.push(
          fetch(gfwApiUrl, {
            method: 'POST',
            body: JSON.stringify(gfwApiBody),
            headers: {
              'Content-Type': 'application/json'
            }
          }).then((response) => response.json())
            .catch((err) => {
              return null;
            })
        );
      }
      gfwLocationData = await Promise.allSettled(gfwLocationData)
        .then(results => {
          results?.forEach((result, index) => {
            if (result.status === 'fulfilled') locationData[index] = result?.value;
          });
        }).catch((err) => {
          return null;
        });
    } catch (err) {}
    return locationData;
  }  
}

// dates module
const date = {
  // extracting the start and end date from the date range picker to update the map layers
  getStartEndDateFromDOM: function(dateElementSelector) {
    const dateRange = document.querySelector(dateElementSelector)?.innerText;
    const dateRangeArray = dateRange?.split(' - ');
    let startDate = dateRangeArray[0]?.trim();
    let endDate = dateRangeArray[1]?.trim();
    startDate = startDate && new Date(startDate);
    endDate = endDate && new Date(endDate);
    startDate = startDate && startDate.toISOString().split('T')[0];
    endDate = endDate && endDate.toISOString().split('T')[0];
    return {startDate, endDate};
  },
  // function to get the default date range for the map layers
  getDefaultDates: function(format, monthDuration) {
    let endDate = new Date(); // Get current date
    const year = endDate.getFullYear();
    const month = endDate.getMonth();
    const day = endDate.getDate();
    // getting last 3 months date as default, to avoid filling the map with too many data points
    let startDate = new Date(year, month - monthDuration, day);
    if(format == "date") return {startDate, endDate};
    // Format the date as "YYYY-MM-DD"
    startDate = startDate.toISOString().slice(0, 10);
    endDate = endDate.toISOString().slice(0, 10);
    return {startDate, endDate};
  }
}

// indexed db module
const idxDB = {
  open: function(dbName, version, objectStoreName, keyPath, autoIncrement) {
    return new Promise((resolve, reject) => {
      const request = window.indexedDB.open(dbName, version);
      request.onupgradeneeded = (event) => {
        const db = event.target.result;
        if (!db.objectStoreNames.contains(objectStoreName)) {
          db.createObjectStore(objectStoreName, { keyPath: keyPath, autoIncrement: autoIncrement });
        }
      };
      request.onsuccess = (event) => resolve(event.target.result);
      request.onerror = (event) => reject(event.target.error);
    });
  },
  add: function(objectStore, data, key) {
    return new Promise((resolve, reject) => {
      const request = objectStore.add(data, key);
      request.onsuccess = (event) => resolve(event.target.result);
      request.onerror = (event) => reject(event.target.error);
    }
    );
  },
  update: function(objectStore, data) { 
    return new Promise((resolve, reject) => {
      const request = objectStore.put(data);
      request.onsuccess = (event) => resolve(event.target.result);
      request.onerror = (event) => reject(event.target.error);
    }
    );
  },
  clear: function(objectStore, key) {
    return new Promise((resolve, reject) => {
      const request = key ? objectStore.delete(key) : objectStore.clear();
      request.onsuccess = (event) => resolve(event.target.result);
      request.onerror = (event) => reject(event.target.error);
    }
    );
  },
  getAll: function(objectStore) {
    return new Promise((resolve, reject) => {
      const request = objectStore.getAll();
      request.onsuccess = (event) => resolve(event.target.result);
      request.onerror = (event) => reject(event.target.error);
    });
  },
  getObjectStore: function(db, objectStoreName, mode) {
    return db.transaction(objectStoreName, mode).objectStore(objectStoreName);
  }  
}

// geometry helpers to extract the geometry data
const geometryHelper = {
  getGeometry: async function(geometryUrl) {
    try {
      const geometry = await (await fetch(geometryUrl)).json();
      return geometry;
    } catch (err) { }
  },
  extractGeometryDataFromIdxDb: async function(db, objectStoreName, key) {
    let objectStore = idxDB.getObjectStore(db, objectStoreName , 'readwrite');
    let geometryData = await idxDB.getAll(objectStore);
    if (geometryData.length !== 0) {
      for (let i = 0; i < geometryData.length; i++) {
        if (Object.keys(geometryData[i]).includes(key)) {
          return JSON.parse(geometryData[i][key]);
        }
      }
    }
    return null;
  }
}
