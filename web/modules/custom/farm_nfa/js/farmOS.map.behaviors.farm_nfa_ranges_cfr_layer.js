(function ($, Drupal) {
  farmOS.map.behaviors.farm_nfa_ranges_cfr_layer = {
    attach: async function (instance) {
      const assetType = document.querySelector('.field--name-name')
      const landType = instance?.farmMapSettings?.land_type;
      if (!(assetType && assetType.innerText.includes('Range')) && landType != 'range') { 
        return;
      }
      let assetId = window.location.href.split('/').pop();
      if (assetId == "gfw") {
        assetId = window.location.href.split('/').slice(-2)[0];
      }
      try {
        const baseUrl = window.location.origin;
        const geometryUrl = `${baseUrl}/asset/${assetId}/geojson/children`
        let geometryData = await (await fetch(geometryUrl)).json();
        let geoJson = {
          "type": "FeatureCollection",
          "features": []
        };
        for (const feature of geometryData?.features) {
          const sectorId = feature.properties.id
          const url = `${baseUrl}/asset/${sectorId}/geojson/children`
          let geometry = await (await fetch(url)).json();
          geometry?.features.forEach((feature) => {
            let encodedStr = feature.properties.geometry
            encodedStr = encodedStr.replace(/&quot;/g, '"')
            const geometry = JSON.parse(encodedStr)
            const assetId = feature.properties.id
            const assetName = feature.properties.name
            const assetDescription = feature.properties.description
            geoJson.features.push({
              "type": "Feature",
              "geometry": geometry,
              "properties": {
                "name": `<a href='/asset/${assetId}'>${assetName}</a>`,
                "description": assetDescription,
                "id": assetId,
              }
            })
          })
        }
        instance.addLayer('geojson', {
          title: 'CFRs',
          geojson: geoJson,
          color: 'green',
        })
      } catch (e) {}
    }
  }
}())

