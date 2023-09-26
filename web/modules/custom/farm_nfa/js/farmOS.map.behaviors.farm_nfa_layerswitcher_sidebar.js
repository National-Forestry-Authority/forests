(function ($, Drupal) {
  farmOS.map.behaviors.farm_nfa_layerswitcher_sidebar = {
    attach: function (instance) {
      // opening layer switcher side bar by default
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
