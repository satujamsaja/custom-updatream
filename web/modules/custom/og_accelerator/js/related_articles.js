(function ($) {

  'use strict';

  Drupal.behaviors.og_accelerator_related_articles = {
    attach: function (context, settings) {
      if ($('.view-related-articles').length) {
        if ($('.view-related-articles').find('.related-article-item').length > 0) {
          $('.related-article-heading').removeClass('hidden');
        }
      }
    }
  };

})(jQuery);
