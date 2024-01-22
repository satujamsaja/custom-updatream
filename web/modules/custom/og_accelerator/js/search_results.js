(function ($, once) {
  "use strict";

  Drupal.behaviors.og_accelerator_search_results = {
    attach: function (context, settings) {
      var ele = $(once("search-result-page", ".view-search-result-page"));
      if (ele.length) {
        var resulttext = $(".search-result").text();
        var rawKeywords = $("#edit-search-keywords").val();
        var keywords = Drupal.checkPlain(rawKeywords);
        let end = $('.search-item-wrapper').length;

        var from_query = true;
        if (keywords === "") {
          var split = window.location.pathname.split("/");
          if (split[2]) {
            rawKeywords = decodeURI(split[2]);
            keywords = Drupal.checkPlain(rawKeywords);
            $("#edit-search-keywords").val(keywords);
            from_query = false;
          }
        }
        var total = $(".view-header span.total").text();
        var replacement = resulttext
          .replace(/@keyword/g, "<strong>" + keywords + "</strong>")
          .replace(/@total/g, "<strong>" + total + "</strong>")
          .replace(/@end/g, "<strong>" + end + "</strong>")
        ;
        $(".search-result")
          .html("<h2>" + replacement + "</h2>")
          .show();
        var searchUrl = window.location.origin + location.pathname;
        if (keywords && from_query) {
          searchUrl += "?keywords=" + rawKeywords;
        }

        window.history.pushState("", "", searchUrl);
        var params = new Proxy(new URLSearchParams(window.location.search), {
          get: (searchParams, prop) => searchParams.get(prop),
        });
        if (params.keywords) {
          var heading = document.querySelectorAll(
            ".search-description-wrapper h3"
          );
          var content = document.querySelectorAll(".search-result-except");
          var markHeading = new Mark(heading);
          var markContent = new Mark(content);
          markHeading.mark(params.keywords);
          markContent.mark(params.keywords);
        }

        // let newUrl = window.location.origin + "/search/" + keywords;
        // if (window.location.href.includes("/search?keywords=")) {
        //   newUrl = window.location.href.replace(
        //     "/search?keywords=",
        //     "/search/"
        //   );
        // }

        // window.history.replaceState(null, null, newUrl);
      }
      $(once("navbar-search-click", "#views-exposed-form-search-page .js-form-submit", context)).on("click", function () {
        $("body").addClass("is-search-navbar");
      });
      $(".view-search-result-page #load-more-button").on("click", function (e) {
        e.preventDefault();
        $("#moreResults").remove();
        $(
          ".view-search-result-page .views-infinite-scroll-content-wrapper"
        ).append(
          '<span id="moreResults" tabindex="-1" class="sr-only visually-hidden position-absolute">More results loaded</span>'
        );
      });
      $(".view-search-result-page", context).on(
        "views_infinite_scroll.new_content",
        function (e) {
          setTimeout(function () {
            $("#moreResults").next("div").find("a").focus();
          }, 500);
        }
      );
    },
  };
})(jQuery, once);
