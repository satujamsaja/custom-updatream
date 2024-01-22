(function (Drupal, $, once) {

  'use strict';

  Drupal.behaviors.ogAcceleratorAdminToolbar = {
    attach: function (context, settings) {
      const $elements =  $(once('oa_admintoolbar', 'body', context));

      $elements.each(function (evt) {
        const tH = $(this).find('#toolbar-bar').height();
        const ttH = $(this).find('.toolbar-tab:visible').outerHeight();
        const totalheight = Math.ceil(ttH + tH);
        $('body').css('padding-top', totalheight + 'px');
      });

    }
  };

}(Drupal, jQuery, once));
