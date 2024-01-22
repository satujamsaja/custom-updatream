(function ($) {

  "use strict";

  Drupal.behaviors.og_block_anchor_nav = {
    attach: function (context, settings) {
      $(document).ready(function() {
        $(".anchor-nav .nav-link").on("click", function(e) {
          e.preventDefault();
          var targetID = $(this).attr("href");
          var headerHeight = $("header").height();

          // Login state
          if ($('body').hasClass('toolbar-fixed')) {
            headerHeight = headerHeight + 60
          }

          // To focus selected area content when tabbing enter
          $(targetID).attr('tabindex', -1).focus(); 
          $(targetID).css('outline', "none");

          if (targetID.length === 0) {
            return;
          }

          $("html, body").animate({
            scrollTop: $(targetID).offset().top - headerHeight
          }, 400);
        });
      });
    }
  };

})(jQuery);
