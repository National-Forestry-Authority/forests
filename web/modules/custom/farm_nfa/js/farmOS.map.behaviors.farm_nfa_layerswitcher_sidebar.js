(function ($, Drupal) {
  farmOS.map.behaviors.farm_nfa_layerswitcher_sidebar = {
    attach: function (instance) {
      // opening layer switcher side bar by default
      console.log(instance)
      console.log("test")
      console.log()
      let assetId = window.location.href.split('/').pop();
      if (assetId == "gfw") {
        assetId = window.location.href.split('/').slice(-2)[0];
      } 
      fetch(`http://forests.ddev.site/asset/${assetId}/geojson/children`)
        .then((response) => {
          return response.json();
        })
        .then((data) => {
          console.log(data);
          const sectorId = data.features[0].properties.id
          console.log(sectorId)
          instance.addLayer('geojson', {
            title: 'CFR',
            url: `http://forests.ddev.site/asset/${sectorId}/geojson/children`,
            color: 'green',
          })
        });
      console.log(assetId)
      window.onload = () => {
        const sideBar = document.querySelectorAll('button[role="tab"]');
        if (sideBar.length > 0) {
          sideBar.forEach((element) => {
            element.click();
          });
        }
      };
    }
  }
}())
