(function () {
  farmOS.map.behaviors.farm_nfa_plan_locations_layers = {
    attach: async function (instance) {
      var url = new URL('/nfa-assets/geojson/' + instance.farmMapSettings.plan, window.location.origin + drupalSettings.path.baseUrl)
      var newLayer = instance.addLayer('geojson', {
        title: Drupal.t('Locations'),
        url,
        color: 'orange',
      })

      // Add a layer for fire alerts in the GFW plan tab
      let gfwRegex = a = /\/plan\/\d+\/gfw/;
      let windowUrl = window.location.href;
      if(windowUrl.match(gfwRegex)) {
        let gfwApiUrl = "https://data-api.globalforestwatch.org/dataset/nasa_viirs_fire_alerts/latest/query"; 
        let planId = window.location.href.split('/')[4]
        let cfrPlanUrl = `${window.origin}/nfa-assets/geojson/${planId}`
        try{
          let cfr = await (await fetch(cfrPlanUrl)).json()
          cfr.features.forEach(async (feature) => {
            let cfrGeometry = feature.geometry
            let gfwApiBody = {
              "geometry": {
                  "type": "Polygon",
                  "coordinates": []
              },
              "sql": "SELECT latitude,longitude FROM results WHERE iso='UGA' AND alert__date >='2023-01-11'"
            } 
            if(cfrGeometry && cfrGeometry.coordinates) {
              gfwApiBody.geometry.coordinates.push(cfrGeometry.coordinates[0])
              let locationData = await (await fetch(gfwApiUrl,{
                method: 'POST',
                body: JSON.stringify(gfwApiBody),
                headers: {
                  'Content-Type': 'application/json'
                }
              })).json()
              let locations= locationData && locationData.data;;
              let geoJson = {
                "type": "FeatureCollection",
                "features": [
                ]
              }
              if(locations){
                locations.forEach((location) => {
                  let latLongArray = [location.longitude, location.latitude]
                  let geoGsonFeature = {
                    "type": "Feature",
                    "properties": {},
                    "geometry": {
                      "coordinates": latLongArray,
                      "type": "Point"
                    }
                  }
                  geoJson.features.push(geoGsonFeature)
                })
              }
              instance.addLayer('geojson', {
                title: 'Fire Alerts',
                geojson : geoJson,
                color: 'red'
              })
            }
          })  
        }catch(err) {
          console.log(err)
        }
      }

      // Zoom to the new layer when it is loaded.
      var source = newLayer.getSource()
      source.on('change', function () {
        instance.zoomToVectors()
      })

      // Load area details via AJAX when an area popup is displayed.
      instance.popup.on('farmOS-map.popup', function (event) {
        var link = event.target.element.querySelector('.ol-popup-name a');
        if (link) {
          var assetLink = link.getAttribute('href')
          var description = event.target.element.querySelector('.ol-popup-description');
          description.innerHTML = 'Loading asset details...';
          fetch(assetLink + '/map-popup')
            .then((response) => {
              return response.text();
            })
            .then((html) => {
              description.innerHTML = html;
              instance.popup.panIntoView();
            });
        }
      })
    }
  }
}())
