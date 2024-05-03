(function () {
  farmOS.map.behaviors.farm_nfa_plan_locations_layers = {
    attach: function (instance) {
      let geometryUrl = ''
      const planId = instance.farmMapSettings.plan
      const assetId = instance.farmMapSettings.asset
      if (planId) {
        geometryUrl = `/nfa-assets/geojson/${planId}`;
        layerTitle = Drupal.t('CFRs')
      }
      if (assetId) {
        geometryUrl = `/asset/geojson/${assetId}`;
        layerTitle = Drupal.t('Geometry')
      }
      var url = new URL(geometryUrl, window.location.origin + drupalSettings.path.baseUrl)
      var newLayer = instance.addLayer('geojson', {
        title: layerTitle ?? Drupal.t('Locations'),
        url,
        color: 'orange',
      })
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
