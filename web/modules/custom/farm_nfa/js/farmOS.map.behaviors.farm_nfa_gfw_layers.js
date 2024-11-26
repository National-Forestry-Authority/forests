// This file contains the code to add the fire and deforestation alerts layers
// to the map in the GFW tab.
// The code uses the Global Forest Watch API to get the fire and deforestation
// alerts data and displays it in a layer on the map.
let gfwGeoDataValues = [];
const gfwGeoListOptions = {
  pagination: true,
  page: 10,
  valueNames: [ 'latitude', 'longitude', 'alertType', 'timeStamp' ],
  item: '<tr><td class="latitude"></td><td class="longitude"></td><td class="alertType"></td><td class="timeStamp"></td></tr>'
};
let GFW_API_KEY;
(function ($, Drupal) {
  farmOS.map.behaviors.farm_nfa_gfw_layers = {
    attach: async function (instance) {
      GFW_API_KEY = instance.farmMapSettings.gfw_api_key;
      let defaultNoOfMonths = 3;
      let defaultNoOfDays = 0;

      // Get the latest versions of the fire and deforestation alerts apis.
      const fireAlertsUrl = await getLatestVersion('https://data-api.globalforestwatch.org/dataset/nasa_viirs_fire_alerts/');
      const deforestationAlertsUrl = await getLatestVersion('https://data-api.globalforestwatch.org/dataset/gfw_integrated_alerts/');
      if (!fireAlertsUrl || !deforestationAlertsUrl) { return; }

      // Get the geometry of the plan or asset.
      const planId = instance.farmMapSettings.plan;
      const assetId = instance.farmMapSettings.asset;
      let geometryUrl = '';
      if (planId) { geometryUrl = '/nfa-assets/geojson/' + planId; }
      if (assetId) { geometryUrl = '/asset/geojson/' + assetId; }
      if (!geometryUrl) { return; }
      const pageOrigin = `${window.location.protocol}//${instance.farmMapSettings.host}`;
      geometryUrl = `${pageOrigin}${geometryUrl}`;

      const geometry = await getGeometry(geometryUrl);
      if (!geometry) { return; }

      if ($('.daterangepicker').length !== 0) {
        // Add the date range picker.
        const dateRangePickerOptions = {
          change: function () {
            gfwGeoDataValues = [];
            updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl, geometry);
          },

        };

        const { startDate, endDate } = getDefaultDates("date", defaultNoOfMonths, defaultNoOfDays);
        $(".daterangepicker").daterangepicker(dateRangePickerOptions);
        $(".daterangepicker").daterangepicker("setRange", { start: startDate, end: endDate });

        // Find .daterange-picker-help and change the text to the desired text.
        const dateRangePickerHelp = document.querySelector('.daterange-picker-help');
        dateRangePickerHelp && (dateRangePickerHelp.innerText = 'Click to select the date range for the fire and deforestation alerts.');

        // Open the date range picker by default.
        // const dateRangePickerElement = document.querySelector('.daterangepicker-container button');
        // dateRangePickerElement && dateRangePickerElement.click();
        instance.popup.on('farmOS-map.popup', function (event) {
          event.preventDefault();
          var link = event.target.element.querySelector('.ol-popup-name a');
          if (link) {
            const description = event.target.element.querySelector('.ol-popup-description iframe');
            if (!description) return console.log('description not found');
            description.style.display = 'none';
          }
        });

      }
    }
  }
}(jQuery, Drupal, drupalSettings))

// Function to update the map layers when the date range is changed.
async function updateMapLayers(instance, fireAlertsUrl, deforestationAlertsUrl, geometry) {
  const dateRange = getStartEndDateFromDOM();
  const map = instance.map;
  const layers = map.getLayers().getArray();
  for (let i = 0; i < layers.length;) {
    const layerTitle = layers[i] ?.values_ ?.title;
    if (layerTitle === 'Fire Alerts' || layerTitle === 'Deforestation Alerts') { await map.removeLayer(layers[i]); }
    else { i++; }
  }
  map.getTargetElement().classList.add('spinner');
  const mapLayers = [farmNfaPlotGfwApiMap(instance, 'fire', fireAlertsUrl, dateRange, geometry),
    farmNfaPlotGfwApiMap(instance, 'deforestation', deforestationAlertsUrl, dateRange, geometry)
  ];
  try {
    await Promise.all(mapLayers);
  } catch (err) {}
  map.getTargetElement().classList.remove('spinner');
  const gfwList = document.getElementById('gfw-list');
  if (gfwList) gfwList.remove();
  const gfwListBoilerPlate = `
    <div id="gfw-list">
      <table>
        <thead>
          <th>Latitude</th>
          <th>Longitude</th>
          <th class="sort" data-sort="alertType">Alert Type ↑</th>
          <th class="sort" data-sort="timeStamp">Timestamp ↑</th>
        </thead>
        <tbody class="list">
        </tbody>
      </table>
    <div class="pagination"></div>
  `
  const gfwListElement = document.createElement('div');
  gfwListElement.innerHTML = gfwListBoilerPlate;
  const gfwParentContainer = document.querySelector('#block-nfa-gin-forests-content');
  gfwParentContainer?.appendChild(gfwListElement);
  const sortButtons = document.querySelectorAll('.sort');
  sortButtons?.forEach((button) => {
    button?.addEventListener('click', function (e) {
      const text = e.target.innerText;
      button.innerText = text.includes('↑') ? text.replace('↑', '↓') : text.replace('↓', '↑');
    });
  });
  new List('gfw-list', gfwGeoListOptions, gfwGeoDataValues);
}

// Extract the start and end date from the date range picker.
function getStartEndDateFromDOM() {
  const dateRange = document.querySelector('.daterangepicker-container') ?.innerText;
  const dateRangeArray = dateRange?.split(' - ');
  let startDate = dateRangeArray[0]?.trim();
  let endDate = dateRangeArray[1]?.trim();
  startDate = startDate && new Date(startDate);
  endDate = endDate && new Date(endDate);
  startDate = startDate && startDate.toISOString().split('T')[0];
  endDate = endDate && endDate.toISOString().split('T')[0];
  return {startDate, endDate};
}

// Get the default date range for the map layers.
function getDefaultDates(format, noOfMonths, noOfDays) {
  let endDate = new Date(); // Get current date
  const year = endDate.getFullYear();
  const month = endDate.getMonth();
  const day = endDate.getDate();
  // Subtract the number of months and days from the current date to get the start date.
  let startDate = new Date(year, month - (noOfMonths || 0), day - (noOfDays || 0));
  if(format === "date") return {startDate, endDate};
  // Format the date as "YYYY-MM-DD"
  startDate = startDate.toISOString().slice(0, 10);
  endDate = endDate.toISOString().slice(0, 10);
  return {startDate, endDate};
}

async function farmNfaPlotGfwApiMap(instance, mapType, gfwApiUrl, dateRange, geometry) {
  return new Promise(async (resolve, reject) => {
    if (!geometry) { resolve('geometry not found'); }
    let startDate = dateRange?.startDate;
    let endDate = dateRange?.endDate;
    const nullDateRange = !startDate && !endDate;
    if (nullDateRange) {
      let dateRange = getDefaultDates();
      startDate = dateRange.startDate;
      endDate = dateRange.endDate;
    }
    const hasBothDateRange = startDate && endDate;
    const hasSingleDateRange = startDate && !endDate;
    const dateParameter = mapType === "fire" ? "alert__date" : "gfw_integrated_alerts__date";
    // Configure the query to get the data from GFW API.
    let query = `SELECT latitude, longitude, ${dateParameter} FROM results WHERE ${mapType === "fire" ? `iso = 'UGA' AND ` : ''}`;
    if (hasBothDateRange) query += `${dateParameter} >= '${startDate}' AND ${dateParameter} <= '${endDate}'`;
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
                'Content-Type': 'application/json',
                'x-api-key': GFW_API_KEY
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
            let popupDescription = mapType === "fire" ? `Fire Alert` : `Deforestation Alert`;
            popupDescription += `<br> ${location[dateParameter]} <br>`;
            let geoJsonFeature = {
              "type": "Feature",
              "properties": {
                "name": `<a>${location.longitude}, ${location.latitude}</a>`,
                "description": popupDescription
              },
              "geometry": {
                "coordinates": latLongArray,
                "type": "Point"
              }
            };
            geoJson.features.push(geoJsonFeature);
            gfwGeoDataValues.push({ latitude: location.latitude, longitude: location.longitude, alertType: mapType, timeStamp: location[dateParameter] });
          })
        }
      });
      const alertDetails = {
        "fire": { title: "Fire Alerts", color: "#FF6600" },
        "deforestation": { title: "Deforestation Alerts", color: "#E42217" }
      };

      instance.addLayer('geojson', {
        title: alertDetails[mapType].title,
        geojson: geoJson,
        color: alertDetails[mapType].color
      });
      resolve('success');
    } catch (err) {
      reject(err);
    }
  });
}

async function getGeometry(geometryUrl) {
  try {
    const response = await fetch(geometryUrl);
    return await response.json();
  } catch (err) { }
}

async function getLatestVersion(url) {
  try {
    const response = await fetch(url);
    const data = await response.json();
    const versions = data.data.versions;

    const latest_version = versions[versions.length - 1]
    return url + latest_version + '/query/json';
  } catch (error) {
    console.error('Error getting GFW API url:', error);
  }
}
