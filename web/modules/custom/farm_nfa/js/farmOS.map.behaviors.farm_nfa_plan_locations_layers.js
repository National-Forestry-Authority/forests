(function () {
  farmOS.map.behaviors.farm_nfa_plan_locations_layers = {
    attach: function (instance) {
      var url = new URL('/nfa-assets/geojson/' + instance.farmMapSettings.plan, window.location.origin + drupalSettings.path.baseUrl)
      var newLayer = instance.addLayer('geojson', {
        title: Drupal.t('Locations'),
        url,
        color: 'orange',
      })
      var source = newLayer.getSource()
      source.on('change', function () {
        instance.zoomToVectors()
      })
    }
  }
}())
