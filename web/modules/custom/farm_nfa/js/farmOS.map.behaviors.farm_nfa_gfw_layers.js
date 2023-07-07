(function () {
    farmOS.map.behaviors.farm_nfa_gfw_layers = {
      attach: async function (instance) {
        // Add layers for fire and deforestation alerts in the GFW plan tab
        const fireAlertsUrl = 'https://data-api.globalforestwatch.org/dataset/nasa_viirs_fire_alerts/v20220726/query/json';
        const deforestationAlertsUrl = 'https://data-api.globalforestwatch.org/dataset/gfw_integrated_alerts/v20230215/query/json';
        farmNfaPlotGfwApiMap(instance, 'fire', fireAlertsUrl);
        farmNfaPlotGfwApiMap(instance,'deforestation', deforestationAlertsUrl);
        const dateApplyButton = document.querySelector('.comiseo-daterangepicker-buttonpanel button');
        const dateUpdateButtons = document.querySelectorAll('[role="menuitem"]');
        dateUpdateButtons.forEach((dateUpdateButton) => {
          dateUpdateButton.addEventListener('click', () => {
            updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl);
          });
        });
        dateApplyButton.addEventListener('click', () => {
          updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl);
        });
      }
    }
}())

async function updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl) {
  const dateRange = await getStartEndDate();
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

function getStartEndDate() {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      const dateRange = document.querySelector('.daterangepicker-container')?.innerText;
      const dateRangeArray = dateRange?.split(' - ');
      let startDate = dateRangeArray[0]?.trim();
      let endDate = dateRangeArray[1]?.trim();
      startDate = startDate && new Date(startDate);
      endDate = endDate && new Date(endDate);
      startDate = startDate && startDate.toISOString().split('T')[0];
      endDate = endDate && endDate.toISOString().split('T')[0];
      resolve({startDate, endDate});
    }, 100);
  });
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
  let forestsFireQuery = `SELECT latitude,longitude FROM results`;
  let deforestationQuery = `SELECT latitude,longitude FROM results`;
  if (hasBothdateRange) {
    forestsFireQuery += ` WHERE iso='UGA' AND alert__date >='${startDate}' AND alert__date <='${endDate}'`;
    deforestationQuery += ` WHERE gfw_integrated_alerts__date >='${startDate}' AND gfw_integrated_alerts__date <='${endDate}'`;
  } else if (hasSingleDateRange) { 
    forestsFireQuery += ` WHERE iso='UGA' AND alert__date ='${startDate}'`;
    deforestationQuery += ` WHERE gfw_integrated_alerts__date ='${startDate}'`;
  } else {
    forestsFireQuery += ` WHERE iso='UGA' AND alert__date >='${getLastThreeMonthsDate()}'`;
    deforestationQuery += ` WHERE gfw_integrated_alerts__date >='${getLastThreeMonthsDate()}'`;
  }

  let planId = instance.farmMapSettings.plan;
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
        "sql": `${mapType == "fire" ? forestsFireQuery : deforestationQuery}`
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
