(function ($, Drupal) {
  farmOS.map.behaviors.farm_nfa_gfw_layers = {
    attach: async function (instance) {
      // Add layers for fire and deforestation alerts in the GFW plan tab
      const fireAlertsUrl = 'https://data-api.globalforestwatch.org/dataset/nasa_viirs_fire_alerts/v20220726/query/json';
      const deforestationAlertsUrl = 'https://data-api.globalforestwatch.org/dataset/gfw_integrated_alerts/v20230215/query/json';
      const { startDate, endDate } = getDefaultDates("date");
      $(".daterangepicker").daterangepicker({
        change: function () {
          updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl);
        },
      });
      $(".daterangepicker").daterangepicker("setRange", {start: startDate, end: endDate});
    }
  }
}(jQuery, Drupal))

// function to update the map layers when the date range is changed
async function updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl) {
  const dateRange = getStartEndDate();
  const map = instance.map;
  const layers = map.getLayers().getArray();
  for (let i = 0; i < layers.length;) {
    const layerTitle = layers[i]?.values_?.title;
    if (layerTitle == 'Fire Alerts' || layerTitle == 'Deforestation Alerts') await map.removeLayer(layers[i]);
    else i++;
  }
  map.getTargetElement().classList.add('spinner');
  const mapLayers = [farmNfaPlotGfwApiMap(instance, 'fire', fireAlertsUrl, dateRange),
    farmNfaPlotGfwApiMap(instance, 'deforestation', deforestationAlertsUrl, dateRange)
  ];
  try {
    await Promise.all(mapLayers);
  }catch(err) {}
  map.getTargetElement().classList.remove('spinner');
}

// extracting the start and end date from the date range picker to update the map layers
function getStartEndDate() {
  const dateRange = document.querySelector('.daterangepicker-container')?.innerText;
  const dateRangeArray = dateRange?.split(' - ');
  let startDate = dateRangeArray[0]?.trim();
  let endDate = dateRangeArray[1]?.trim();
  startDate = startDate && new Date(startDate);
  endDate = endDate && new Date(endDate);
  startDate = startDate && startDate.toISOString().split('T')[0];
  endDate = endDate && endDate.toISOString().split('T')[0];
  return {startDate, endDate};
}

// function to get the default date range for the map layers
function getDefaultDates(format) {
  let endDate = new Date(); // Get current date
  const year = endDate.getFullYear();
  const month = endDate.getMonth();
  const day = endDate.getDate();
  // getting last 3 months date as default, to avoid filling the map with too many data points
  let startDate = new Date(year, month - 3, day);
  if(format == "date") return {startDate, endDate};
  // Format the date as "YYYY-MM-DD"
  startDate = startDate.toISOString().slice(0, 10);
  endDate = endDate.toISOString().slice(0, 10);
  return {startDate, endDate};
}

async function farmNfaPlotGfwApiMap(instance, mapType, gfwApiUrl, dateRange) {
  return new Promise(async (resolve, reject) => {
    let startDate = dateRange?.startDate;
    let endDate = dateRange?.endDate;
    const nullDateRange = !startDate && !endDate;
    if (nullDateRange) {
      let dateRange = getDefaultDates();
      startDate = dateRange.startDate;
      endDate = dateRange.endDate;
    }
    const hasBothdateRange = startDate && endDate;
    const hasSingleDateRange = startDate && !endDate;
    const baseQuery = instance.farmMapSettings.base_query;
    // configuring the query to get the data from GFW API
    let query = `${baseQuery} WHERE ${mapType == "fire" ? `iso='UGA' AND ` : ''}`;
    const dateParameter = mapType == "fire" ? "alert__date" : "gfw_integrated_alerts__date";
    if (hasBothdateRange) query += `${dateParameter} >= '${startDate}' AND ${dateParameter} <= '${endDate}'`;
    else if (hasSingleDateRange) query += `${dateParameter} = '${startDate}'`;
    // setting the cfr plan url for the geojson data
  
    let geometryUrl = ''
    const planId = instance.farmMapSettings.plan
    const assetId = instance.farmMapSettings.asset
    if (planId) geometryUrl = `/nfa-assets/geojson/${planId}`
    if (assetId) geometryUrl = `/asset/geojson/${assetId}`
    if(!geometryUrl) resolve('No plan or asset id found');
    const pageOrigin = 'https://' + instance.farmMapSettings.host;
    let cfrPlanUrl = `${pageOrigin}${geometryUrl}`;
    try {
      let cfr = await (await fetch(cfrPlanUrl)).json();
      let geoJson = {
        "type": "FeatureCollection",
        "features": []
      };
      let gfwLocationData = [];
      for (let i = 0; i < cfr.features.length; i++) {
        let cfrGeometry = cfr.features[i].geometry;
        let gfwApiBody = {
          "geometry": {
            "type": "Polygon",
            "coordinates": []
          },
          "sql": `${query}`
        };
        if (cfrGeometry && cfrGeometry.coordinates) {
          gfwApiBody.geometry.coordinates.push(cfrGeometry.coordinates[0]);
          gfwLocationData.push(
            fetch(gfwApiUrl,{
              method: 'POST',
              body: JSON.stringify(gfwApiBody),
              headers: {
                'Content-Type': 'application/json'
              }
            })
          );
        }
      }
      let locationData = await Promise.all(gfwLocationData);
      locationData = await Promise.all(locationData.map((location) => location.json()));
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
      instance.addLayer('geojson', {
        title: `${mapType == "fire" ? "Fire": "Deforestation"} Alerts`,
        geojson : geoJson,
        color: `${mapType == "fire" ? "red": "green"}`
      });
      if (mapType !== "fire") {
        let allLayersControllers = document.querySelectorAll(".layer-switcher input");
        allLayersControllers.forEach((layerController) => {
          const shouldDisableLayer = layerController.nextSibling.innerText !== "Fire Alerts" && layerController.nextSibling.innerText !== "Locations";
          console.log('test')
          if (shouldDisableLayer) {
            layerController.click();
          }
        });
      }
      resolve('success');
    } catch (err) {
      reject(err);
    }
  });
}
