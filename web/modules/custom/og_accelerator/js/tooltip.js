(function (Drupal, $, once) {

  'use strict';

  Drupal.behaviors.og_accelerator_tooltip = {
    attach: function (context, settings) {
      const $elements =  $(once('og_accelerator_tooltip', '.block-tooltip', context));

      $elements.each(function () {
        var description = '<p>' + $('<p>').html($(this).data('block-description')).text() + '</p>';
        $(this).append('<div class="block-tooltip-content">' + description + '</div>');
      });
    }
  };

}(Drupal, jQuery, once));
