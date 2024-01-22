(function ($) {

  "use strict";

  Drupal.behaviors.og_block_cta_banner = {
    attach: function (context, settings) {
      
      $(window).on("load", function() {
        var lazyBackgrounds = [].slice.call($(".bg-lazy"));

        var count=1;
        $(".cta-banner-background.bg-lazy").each(function () {
          $(this).attr("data-flag",count);
          $(this).addClass("flag"+count);
          count++;
        });
      
        if ("IntersectionObserver" in window) {
          let lazyBackgroundObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
              if (entry.isIntersecting) {
                let bgImage = entry.target.getAttribute('data-bgimage');
                var flag = entry.target.getAttribute('data-flag')
                if (typeof bgImage !== 'undefined' && bgImage !== '') {
                    $('.'+'flag'+flag).css('backgroundImage', 'url(' + bgImage + ')');
                }

                lazyBackgroundObserver.unobserve(entry.target);
              }
            });
          });
      
          lazyBackgrounds.forEach(function(lazyBackground) {
            lazyBackgroundObserver.observe(lazyBackground);
          });
        }
      });

    }
  };

})(jQuery);