(function ($, Drupal, Swiper) {
  'use strict';

  Drupal.behaviors.planGallery = {
    attach: function (context) {
      once('planGallerySwiper', '.plan-gallery.swiper', context).forEach(function(element) {
        new Swiper(element, {
          direction: 'horizontal',
          slidesPerView: 1,
          spaceBetween: 24,
          loop: false,
          cssMode: true,
          navigation: {
            nextEl: $(element).parent().find('.swiper-button-next').get(0),
            prevEl: $(element).parent().find('.swiper-button-prev').get(0),
          },
          pagination: {
            el: '.swiper-pagination',
            type: 'bullets',
          },
          keyboard: {
            enabled: true,
          },
        });

      });
    }
  }
})(jQuery, Drupal, Swiper);
