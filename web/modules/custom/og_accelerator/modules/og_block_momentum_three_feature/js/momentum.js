(function ($) {
  "use strict";

  Drupal.behaviors.og_block_momentum_three_feature = {
    attach: function (context, settings) {
      $(window).on("load", function () {
        titleTagEqualHeight();
        itemEqualHeight();
      });

      $(window).on("resize", function () {
        titleTagEqualHeight();
        itemEqualHeight();
      });
    },
  };

  function titleTagEqualHeight() {
    $(".momentum").each(function () {
      var thisMomentum = $(this);
      var titleTagHeight = 0;

      $(thisMomentum).find(".momentum__item .title-tag").height("auto");

      $(thisMomentum)
        .find(".momentum__item")
        .each(function () {
          var momentumItem = $(this);
          var titleTag = momentumItem.find(".title-tag");
          if (titleTag.length) {
            if (titleTagHeight < $(titleTag).height()) {
              titleTagHeight = $(titleTag).height();
            }
          } else {
            $(
              '<p class="title-tag bg-transparant"><span>&nbsp;</span></p>'
            ).insertBefore(momentumItem.find(".momentum__body h2"));
          }
        });

      if (titleTagHeight > 0) {
        if ($(window).width() < 768) {
          titleTagHeight = "auto";
        }
        $(thisMomentum)
          .find(".momentum__item .title-tag")
          .height(titleTagHeight);
      }
    });
  }

  function itemEqualHeight() {
    $(".momentum").each(function () {
      var thisMomentum = $(this);
      var imageHeight = 0;

      imageHeight = $(thisMomentum).find(".momentum__item").width();

      if ($(window).width() < 768) {
        $(thisMomentum).find(".momentum__item").height(imageHeight);
      }
      else{
        $(thisMomentum).find(".momentum__item").removeAttr("style"); 
      }
 
    });
  }
})(jQuery);
