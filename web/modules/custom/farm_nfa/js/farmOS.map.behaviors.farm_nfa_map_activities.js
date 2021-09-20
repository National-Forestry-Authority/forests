(function () {
  farmOS.map.behaviors.farm_nfa_map_activities = {
    attach: function (instance) {
      if (drupalSettings.farm_map[instance.target].farm_nfa_map_activities !== undefined) {
        drupalSettings.farm_map[instance.target].farm_nfa_map_activities.geometries.value.forEach(function (wkt) {
          var layer = instance.addLayer('wkt', {
            title: 'Plan asset',
            wkt,
            visible: false,
          })
        })
        instance.zoomToVectors()
      }
    }
  }
}())
