let isConsoleLog = false;
let delay = 500;
let timeouttimeline = false;

(function ($) {
  "use strict";

  Drupal.behaviors.og_block_timeline_date = {
    attach: function (context, settings) {
      populateVerticalTimeline();
      initVerticalTimeline();
    },
  };

  // Make the text part of the feature card to be same height
  function initVerticalTimeline() {
    // $(window).on("load", function () {
    // });

    $(".vertical-timeline-block-wrapper .year-filter a.first-active").trigger(
      "click"
    );
    setTimeout(function () {
      $(".vertical-timeline-previous-block").addClass("is-initialized");
      $(".vertical-timeline-next-block").addClass("is-initialized");
      $(".vertical-timeline-block").addClass("is-initialized");
      $(".year-filter").addClass("is-initialized");
      reshuffleItems();
    }, delay);
  }

  $(window).on("resize", function () {
    if (timeouttimeline) clearTimeout(timeouttimeline);
    timeouttimeline = setTimeout(reshuffleItems, 100);
  });

  // Capture scroll events
  $(window).scroll(function () {
    checkAnimation();
  });

  function reshuffleItems() {
    updatePrevNextTimeline();
    var timelineItems = $(".item").not(".hide");
    var len1 = 0,
      len2 = 0,
      marginB = 28;

    for (var i = 0; i < timelineItems.length; i++) {
      if (len1 <= len2) {
        // Column 1
        $(timelineItems[i]).css("order", 1);
        len1 += parseInt(timelineItems[i].offsetHeight) + marginB;

        $(timelineItems[i]).removeClass("item-right").addClass("item-left");
      } else {
        // Column 2
        $(timelineItems[i]).css("order", 2);
        len2 += parseInt(timelineItems[i].offsetHeight) + marginB;

        $(timelineItems[i]).removeClass("item-left").addClass("item-right");
      }
    }

    var containerH = len1 > len2 ? len1 : len2;
    containerH = containerH + 90 + "px"; //80 for margin top
    $(".vertical-timeline-block").css("height", containerH);
    $(".item").removeClass("init");
  }

  function updatePrevNextTimeline() {
    var currentactiveyear = $(
      ".vertical-timeline-block-wrapper .year-filter a.active"
    );
    $(".vertical-timeline-previous-block").removeClass("no-previous");
    $(".vertical-timeline-next-block").removeClass("no-next");

    if (currentactiveyear.is(":first-child")) {
      $(".vertical-timeline-previous-block").addClass("no-previous");
    }

    if (currentactiveyear.is(":last-child")) {
      $(".vertical-timeline-next-block").addClass("no-next");
    }
  }

  function populateVerticalTimeline() {
    var response = $(".timeline-data").data("json");
    $.each(response, function (propName, propVal) {
      $.each(propVal, function (itemkey, itemvalue) {
        var newitem = $(".vertical-timeline-block .item.template").clone();
        newitem.removeClass("template");
        newitem.addClass("filtered-by-year-" + propName);
        newitem.addClass("hide");

        if (itemvalue.title) {
          newitem.find(".timeline-item-title").html(itemvalue.title);
        } else {
          newitem.find(".timeline-item-title").remove();
        }

        if (itemvalue.quote) {
          newitem.find("blockquote p").html(itemvalue.quote);
        } else {
          newitem.find("blockquote").remove();
        }

        if (itemvalue.image) {
          newitem.find(".timeline-item-image").attr("src", itemvalue.image);
        } else {
          newitem.find(".timeline-item-image").remove();
        }

        if (itemvalue.description) {
          newitem
            .find(".timeline-item-description")
            .html(itemvalue.description);
        } else {
          newitem.find(".timeline-item-description").remove();
        }

        if (itemvalue.link_text) {
          newitem.find(".timeline-item-link a").html(itemvalue.link_text);
        } else {
          newitem.find(".timeline-item-link").remove();
        }

        if (itemvalue.link_url) {
          newitem
            .find(".timeline-item-link a")
            .attr("href", itemvalue.link_url);
        } else {
          newitem.find(".timeline-item-link").remove();
        }

        $(".vertical-timeline-block").append(newitem);
      });
    });

    // Create line
    $(".vertical-timeline-block").append("<div class='line'></div>");

    // Remove template item
    $(".vertical-timeline-block .item.template").remove();

    // Add event listener for year filter
    // Trigger click to show modal with correct selection
    $(".vertical-timeline-block-wrapper .year-filter a").on(
      "click",
      function (e) {
        e.preventDefault();
        if (!$(this).hasClass("active")) {
          $(".vertical-timeline-block-wrapper .year-filter a").removeClass(
            "active"
          );
          $(this).addClass("active");

          var yeartobeactive = $(this).data("year");

          $(".vertical-timeline-block .item").addClass("hide");
          $(".vertical-timeline-block .item").removeClass("animate");
          $(".vertical-timeline-block .item").removeClass("animate-start");

          $(".filtered-by-year-" + yeartobeactive).removeClass("hide");
          $(".filtered-by-year-" + yeartobeactive).addClass("animate");

          $(".vertical-timeline-block-wrapper .year-tag.active-year").html(
            $(this).html()
          );

          reshuffleItems();
          setTimeout(function () {
            checkAnimation();
          }, 300);
        }
      }
    );

    $(".vertical-timeline-block-wrapper .see-previous a")
      .off()
      .on("click", function (e) {
        e.preventDefault();

        var yearcurrent = $(
          ".vertical-timeline-block-wrapper .year-filter a.active"
        );
        var previousyear = yearcurrent.prev();
        yearcurrent.removeClass("active");
        previousyear.addClass("active");

        var yeartobeactive = previousyear.data("year");

        transitionHideTimeline();

        setTimeout(function () {
          $(".vertical-timeline-block .item").addClass("hide");
          $(".vertical-timeline-block .item").removeClass("animate");
          $(".vertical-timeline-block .item").removeClass("animate-start");

          $(".filtered-by-year-" + yeartobeactive).removeClass("hide");
          $(".filtered-by-year-" + yeartobeactive).addClass("animate");

          $(".vertical-timeline-block-wrapper .year-tag.active-year").html(
            previousyear.html()
          );
          reshuffleItems();
          setTimeout(function () {
            checkAnimation();
          }, 300);
        }, 500);
      });

    $(".vertical-timeline-block-wrapper .see-next a")
      .off()
      .on("click", function (e) {
        e.preventDefault();

        transitionHideTimelineImportant();
        var yearcurrent = $(
          ".vertical-timeline-block-wrapper .year-filter a.active"
        );
        var nextyear = yearcurrent.next();
        yearcurrent.removeClass("active");
        nextyear.addClass("active");

        var yeartobeactive = nextyear.data("year");
        $(".vertical-timeline-block-wrapper .year-tag.active-year").html(
          nextyear.html()
        );
        updatePrevNextTimeline(); //force this to fix issue on change focus

        //check if login (to disable unwanted scroll on add block 215)

        if (!$(".toolbar-horizontal").length) {
          //scroll to active year
          $("html, body").animate(
            {
              scrollTop:
                $(".vertical-timeline-block-wrapper").offset().top -
                $("header").height(),
              duration: 1500,
              easing: "linear",
            },
            0,
            function () {}
          );
        }

        setTimeout(function () {
          transitionHideTimeline();
          transitionShowTimelineImportant();

          $(".vertical-timeline-block .item").addClass("hide");
          $(".filtered-by-year-" + yeartobeactive).removeClass("hide");
          $(".filtered-by-year-" + yeartobeactive).addClass("animate");

          reshuffleItems();
          setTimeout(function () {
            checkAnimation();
          }, 300);

          $(".vertical-timeline-block-wrapper .see-previous a").focus();
        }, 1000);
      });
  }

  $.fn.isInViewport = function () {
    var elementTop = $(this).offset().top;
    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();

    return elementTop < viewportBottom - $(window).height() * 0.3;
  };

  // Check if it's time to start the animation.
  function checkAnimation() {
    $(".item.animate").each(function (index, value) {
      // If the animation has already been started
      if ($(this).hasClass("animate-start")) return;

      if ($(this).isInViewport()) {
        // Start the animation
        $(this).addClass("animate-start");
      }
    });
  }

  function transitionHideTimeline() {
    $(".item.animate").removeClass("animate-start");
  }

  function transitionHideTimelineImportant() {
    $(".item.animate").addClass("opacityimportant");
  }

  function transitionShowTimelineImportant() {
    $(".item.animate").removeClass("opacityimportant");
  }
})(jQuery);
