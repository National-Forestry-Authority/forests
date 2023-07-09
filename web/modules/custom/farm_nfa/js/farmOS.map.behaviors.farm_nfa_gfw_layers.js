(function ($, Drupal) {
    farmOS.map.behaviors.farm_nfa_gfw_layers = {
      attach: async function (instance) {
        // Add layers for fire and deforestation alerts in the GFW plan tab
        const fireAlertsUrl = 'https://data-api.globalforestwatch.org/dataset/nasa_viirs_fire_alerts/v20220726/query/json';
        const deforestationAlertsUrl = 'https://data-api.globalforestwatch.org/dataset/gfw_integrated_alerts/v20230215/query/json';
        farmNfaPlotGfwApiMap(instance, 'fire', fireAlertsUrl);
        farmNfaPlotGfwApiMap(instance,'deforestation', deforestationAlertsUrl);
        // updating the map layers on date range change
        $(".daterangepicker").daterangepicker({
          change: function () {
            updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl);
          },
        });
      }
    }
}(jQuery, Drupal))

async function updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl) {
  const dateRange = getStartEndDate();
  const map = instance.map;
  const layers = map.getLayers().getArray();
  let noOfLayers = layers.length;
  for (let i = 0; i < noOfLayers;) {
    const layerTitle = layers[i]?.values_?.title;
    if (layerTitle == 'Fire Alerts' || layerTitle == 'Deforestation Alerts') { 
      await map.removeLayer(layers[i]);
      noOfLayers--;
    } else {
      i++;
    }
  }
  farmNfaPlotGfwApiMap(instance, 'fire', fireAlertsUrl, dateRange);
  farmNfaPlotGfwApiMap(instance, 'deforestation', deforestationAlertsUrl, dateRange);
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

// make a function to get the date of last 3 months
function getLastThreeMonthsDate() {
  const currentDate = new Date(); // Get current date
  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();
  const day = currentDate.getDate();
  // getting last 3 months date as default, to avoid filling the map with too many data points
  const lastThreeMonthsDate = new Date(year, month - 3, day);
  // Format the date as "YYYY-MM-DD"
  const formattedDate = lastThreeMonthsDate.toISOString().slice(0, 10);
  return formattedDate;
}

async function farmNfaPlotGfwApiMap(instance, mapType, gfwApiUrl, dateRange) {
  let startDate = dateRange?.startDate;
  let endDate = dateRange?.endDate;
  const hasBothdateRange = startDate && endDate;
  const hasSingleDateRange = startDate && !endDate;
  const baseQuery = instance.farmMapSettings.base_query;
  // configuring the query to get the data from GFW API
  let query = `${baseQuery} WHERE ${mapType == "fire" ? `iso='UGA' AND ` : ''}`;
  const dateParameter = mapType == "fire" ? "alert__date" : "gfw_integrated_alerts__date";
  if (hasBothdateRange) query += `${dateParameter} >= '${startDate}' AND ${dateParameter} <= '${endDate}'`;
  else if (hasSingleDateRange) query += `${dateParameter} = '${startDate}'`;
  else query += `${dateParameter} >= '${getLastThreeMonthsDate()}'`;
  // setting the cfr plan url for the geojson data

  let planId = instance.farmMapSettings.plan;
  if(!planId) return;
  const pageOrigin = 'http://' + instance.farmMapSettings.host;
  let cfrPlanUrl = `${pageOrigin}/nfa-assets/geojson/${planId}`;
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
        if (layerController.nextSibling.innerText !== "Fire Alerts") {
          layerController.click();
        }
      });
    }
  } catch(err) {}
}
