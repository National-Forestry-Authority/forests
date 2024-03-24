(function ($, Drupal) {
  farmOS.map.behaviors.farm_nfa_layerswitcher_sidebar = {
    attach: function (instance) {
      // opening layer switcher side bar by default
      window.onload = () => {
        const sideBar = document.querySelector('button[role="tab"]');
        if (sideBar) {
          sideBar.click();
          this.setDefaultBaseLayer();
        }
      };
    },
    setDefaultBaseLayer: function () {
      const baseLayerOptions = document.querySelectorAll('.layer-switcher-base-group .layer');
      baseLayerOptions?.forEach((layer) => {
        if (layer.innerText.includes('Mapbox Satellite')) {
          const radioButton = layer.querySelector('input[type="radio"]');
          radioButton.click();
        }
      });
    }
  }
}())
