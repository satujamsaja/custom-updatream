(function ($) {

  "use strict";

  Drupal.behaviors.og_accelerator_layout_builder = {
    attach: function (context, settings) {
      $(document).ready(function () {
        // Temporary fix for issue https://www.drupal.org/project/drupal/issues/3050508
        //$('.media-library-widget-modal').removeClass('media-library-widget-modal');
      });
    }
  };

})(jQuery);
