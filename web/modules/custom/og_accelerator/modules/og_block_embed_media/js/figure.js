(function (Drupal, $, once) {

  "use strict";

  Drupal.behaviors.og_block_embed_media = {
    attach: function (context, settings) {
      $(document).ready(function() {
        const $elements =  $(once('og_block_embed_media', '.media-image-video', context));

        $elements.each(function() {
          var videoModal = $(this).find("a").attr("data-bs-target");
          var videoTitle = $(this).find("a").attr("data-video-title");
          var videoSrc = $(videoModal).find("iframe").attr("src");
        
          // on opening the modal
          $(videoModal).on('show.bs.modal', function () { 
            // set the video to autostart
            $(videoModal).find("iframe").attr("src", videoSrc+"?autoplay=1&rel=0");
            $(videoModal).find("iframe").attr("title", videoTitle);
          });
    
          // on close the modal
          $(videoModal).on('hide.bs.modal', function () { 
            // set the video to autostart
            $(videoModal).find("iframe").attr("src", "");
            $(videoModal).find("iframe").attr("title", "");
          });
        });

        // Using ESC to close
        if ($('.modal-video').length) {
          var KEYCODE_ESC = 27;
          $(document).keyup(function(e) {
            if (e.keyCode == KEYCODE_ESC) {
              $('.modal-video .btn-close').click();
            }
          });
        }
        
      });
    }
  };

}(Drupal, jQuery, once));