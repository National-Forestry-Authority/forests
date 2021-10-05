(function () {
  farmOS.map.behaviors.farm_nfa_map_activities = {
    attach: function (instance) {
      if (instance.farmMapSettings.farm_nfa_map_activities !== undefined) {
        instance.farmMapSettings.farm_nfa_map_activities.geometries.value.forEach(function (wkt) {
          var layer = instance.addLayer('wkt', {
            title: 'Plan asset',
            wkt,
          })
        })
        instance.zoomToVectors()
        instance.map.getView().setZoom((instance.map.getView().getZoom() - 1.5))
      }
    }
  }
}())
