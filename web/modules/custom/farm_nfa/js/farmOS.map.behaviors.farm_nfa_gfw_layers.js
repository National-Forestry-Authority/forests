(function ($, Drupal) {
  farmOS.map.behaviors.farm_nfa_gfw_layers = {
    attach: async function (instance) {
      // Add layers for fire and deforestation alerts in the GFW plan tab
      const assetType = instance.farmMapSettings.asset_type;
      const fireAlertsUrl = 'https://data-api.globalforestwatch.org/dataset/nasa_viirs_fire_alerts/v20220726/query/json';
      const deforestationAlertsUrl = 'https://data-api.globalforestwatch.org/dataset/gfw_integrated_alerts/v20230215/query/json';
      let defaultMonthDuration = 3;
      let defaultDaysDuration = 0;
      // setting the geometry url
      const pageOrigin = `${window.location.protocol}//${instance.farmMapSettings.host}`;
      const planId = instance.farmMapSettings.plan
      const assetId = instance.farmMapSettings.asset
      let geometryUrl = '';
      if (planId) geometryUrl = `/nfa-assets/geojson/${planId}`;
      if (assetId) geometryUrl = `/asset/geojson/${assetId}`;
      if (!geometryUrl) return; 
      geometryUrl = `${pageOrigin}${geometryUrl}`;
      const geometry = await geometryHelper.getGeometry(geometryUrl);
      if (!geometry) return;
      // adding the date range picker
      const dateRangePickerOptions = {
        change: function () {
          updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl, geometry);
        },
      
      };
      if (assetType == 'land') {
        defaultMonthDuration = 0;
        defaultDaysDuration = new Date().getDate() - 1;
        dateRangePickerOptions.presetRanges = [
          {
            text: 'Month to Date',
            dateStart: function() { return moment().startOf('month') },
            dateEnd: function() { return moment() }
          },
          {
            text: 'Last Week (Mo-Su)',
            dateStart: function () { return moment().subtract(1, 'weeks').startOf('isoWeek') },
            dateEnd: function () { return moment().subtract(1, 'weeks').endOf('isoWeek') }
          }
        ],
        dateRangePickerOptions.datepickerOptions = {
          numberOfMonths : 1
        }
        dateRangePickerOptions.open = function () {
          const previousDateRangeElement = document.querySelector('.ui-datepicker-prev');
          previousDateRangeElement && previousDateRangeElement.remove();
        }
      }
      const { startDate, endDate } = getDefaultDates("date", defaultMonthDuration, defaultDaysDuration);
      $(".daterangepicker").daterangepicker(dateRangePickerOptions);
      $(".daterangepicker").daterangepicker("setRange", { start: startDate, end: endDate });
      // opening date range picker by default
      const dateRangePickerElement = document.querySelector('.daterangepicker-container button');
      dateRangePickerElement && dateRangePickerElement.click();
    }
  }
}(jQuery, Drupal))

// function to update the map layers when the date range is changed
async function updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl, geometry) {
  const dateRange = getStartEndDateFromDOM();
  const map = instance.map;
  const layers = map.getLayers().getArray();
  for (let i = 0; i < layers.length;) {
    const layerTitle = layers[i]?.values_?.title;
    if (layerTitle == 'Fire Alerts' || layerTitle == 'Deforestation Alerts') await map.removeLayer(layers[i]);
    else i++;
  }
  map.getTargetElement().classList.add('spinner');
  const mapLayers = [farmNfaPlotGfwApiMap(instance, 'fire', fireAlertsUrl, dateRange, geometry),
    farmNfaPlotGfwApiMap(instance, 'deforestation', deforestationAlertsUrl, dateRange, geometry)
  ];
  try {
    await Promise.all(mapLayers);
  }catch(err) {}
  map.getTargetElement().classList.remove('spinner');
}

// extracting the start and end date from the date range picker to update the map layers
function getStartEndDateFromDOM() {
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
function getDefaultDates(format, monthDuration, days) {
  let endDate = new Date(); // Get current date
  const year = endDate.getFullYear();
  const month = endDate.getMonth();
  const day = endDate.getDate();
  // getting last 3 months date as default, to avoid filling the map with too many data points
  let startDate = new Date(year, month - (monthDuration || 0), day - (days || 0));
  if(format == "date") return {startDate, endDate};
  // Format the date as "YYYY-MM-DD"
  startDate = startDate.toISOString().slice(0, 10);
  endDate = endDate.toISOString().slice(0, 10);
  return {startDate, endDate};
}

async function farmNfaPlotGfwApiMap(instance, mapType, gfwApiUrl, dateRange, geometry) {
  return new Promise(async (resolve, reject) => {
    if(!geometry) resolve('geometry not found');
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
    
    try {
      let geoJson = {
        "type": "FeatureCollection",
        "features": []
      };
      let gfwLocationData = [];
      for (let i = 0; i < geometry.features.length; i++) {
        let locationGeometry = geometry.features[i].geometry;
        let gfwApiBody = {
          "geometry": {
            "type": "Polygon",
            "coordinates": []
          },
          "sql": `${query}`
        };
        if (locationGeometry && locationGeometry.coordinates) {
          gfwApiBody.geometry.coordinates.push(locationGeometry.coordinates[0]);
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
      resolve('success');
    } catch (err) {
      reject(err);
    }
  });
}

const geometryHelper = {
  getGeometry: async function(geometryUrl) {
    try {
      const geometry = await (await fetch(geometryUrl)).json();
      return geometry;
    } catch (err) { }
  },
}
