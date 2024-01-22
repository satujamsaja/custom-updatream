(function ($, Drupal) {
  "use strict";

  let delay = 250;
  let timeout = false;
  const carouselBreakpoint = 768;
  const showMenuDelay = 400;
  const hideMenuDelay = 400;
  let menuEnterTimer;
  let menuLeaveTimer;

  /**
   * Init
   */
  Drupal.behaviors.accelerator = {
    attach: function (context) {
      acceleratorTableWrap();
      acceleratorForm();
      acceleratorSelectForm();
      acceleratorFormCheckboxCheck();
      acceleratorCarouselColumn();
      acceleratorFooterNav();
      acceleratorAlert();
      acceleratorCloseAlert();
      acceleratorMegaMenuHover();
      acceleratorShrinkNavbar();
      acceleratorMainContentHeight();
      acceleratorCustomGTMEvent();
      acceleratorHeroBgVideo();
      acceleratorHeroImage();
      acceleratorCarousel();
      acceleratorCarouselVideoModal();
      acceleratorLightboxContactForm();
      acceleratorNavSearchForm();
      acceleratorTabbing();
      acceleratorIsLogin();
      acceleratorArticle();
    },
  };

  /**
   * Resize
   */
  $(window).on("resize", function () {
    clearTimeout(timeout);
    timeout = setTimeout(function () {
      acceleratorCarouselColumn();
      acceleratorFooterNav();
      acceleratorMainContentHeight();
      acceleratorHeroImage();
    }, delay);
  });

  /**
   * Select drop down using select2
   */
  function acceleratorSelectForm() {
    //select2 plugin
    $(".webform-select2").select2();

    //select with label placeholder smaller when selected item
    $(".dark-bg .js-form-type-select select").each(function () {
      var valEl = $(this).val();
      if (!valEl == "") {
        $(this).parent(".js-form-type-select").addClass("focused");
      }
    });

    $(".dark-bg .js-form-type-select select").on("change", function () {
      if (!this.value == "") {
        $(this).parent(".js-form-type-select").addClass("focused");
      }
    });
  }

  /**
   * OGDRUACC-118 table Frontend wrap for vertical scroll
   */
  function acceleratorTableWrap() {
    jQuery(".section-content table").each(function () {
      if (jQuery(this).closest(".table-wrap").length <= 0) {
        jQuery(this).wrap('<div class="table-wrap" />');
      }
    });
  }

  /**
   * OGDRUACC-30 AC9 Frontend interaction
   * If the carousel mode on mobile option is checked,
   * they should turn into a carousel when the columns begin to stack.
   */
  function acceleratorCarouselColumn() {
    $(".carousel-on-mobile").each(function () {
      if ($(window).width() > carouselBreakpoint) {
        if ($(this).hasClass("slick-initialized")) {
          $(this).slick("unslick");
        }
      } else {
        if (!$(this).hasClass("slick-initialized")) {
          var thisCarouselColumn = $(this).slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            arrows: true,
            mobileFirst: true,
          });

          // Accessibility
          thisCarouselColumn.find(".slick-dots li").each(function (index) {
            $(this)
              .find("button")
              .attr("aria-label", "Navigate to slide " + (index + 1));
          });

          thisCarouselColumn.on("afterChange", function () {
            var thisEl = $(this);
            setTimeout(function () {
              $(thisEl)
                .find(".slick-dots li")
                .each(function (index) {
                  $(this)
                    .find("button")
                    .attr("aria-label", "Navigate to slide " + (index + 1));
                });
            }, 0);
          });
        }
      }
    });
  }

  /**
   * OGDRUACC-3 AC1 Responsive
   * The heading becomes a clickable toggle link to open and close the link list below
   */
  function acceleratorFooterNav() {
    $(".footer")
      .find(".footer-nav-heading")
      .each(function (index) {
        if ($(window).width() < 768) {
          let ariaExpanded = false;
          if ($(this).parent().find(".navbar-collapse").hasClass("show")) {
            ariaExpanded = true;
          }

          $(this).attr({
            "data-bs-toggle": "collapse",
            "data-bs-target": "#footer-nav-" + (index + 1),
            "aria-controls": "footer-nav-" + (index + 1),
            "aria-expanded": ariaExpanded,
            "aria-label": "Toggle navigation",
          });
        } else {
          $(this).removeAttr(
            "data-bs-toggle data-bs-target aria-controls aria-expanded aria-label"
          );
        }
      });
  }

  /**
   * OGDRUACC-2 AC3 Frontend Interaction
   * Set cookie 24h when the close button of the alert is clicked.
   */
  function acceleratorCloseAlert() {
    $(document).on("click", ".site-alert-item .btn-close", function () {
      const alertID = $(this).parent().data("alert-id");
      acceleratorSetCookie("close-site-alert-item-" + alertID, "yes", 1);
    });
  }

  /**
   * Check if alert should appear or not
   */
  function acceleratorAlert() {
    if ($(".block--sitealert").length) {
      var checkExist = setInterval(function () {
        if ($(".site-alert-item").length) {
          $(".site-alert-item").each(function () {
            const cookieAlert = acceleratorGetCookie(
              "close-site-alert-item-" + $(this).data("alert-id")
            );
            if (cookieAlert != "yes") {
              $(this).removeClass("d-none");
            }
          });
          clearInterval(checkExist);
        }
      }, 100); // check every 100ms
    }
  }

  /**
   * @param {string} cookieName
   * @returns {string}
   */
  function acceleratorGetCookie(cookieName) {
    let name = cookieName + "=";
    let ca = document.cookie.split(";");
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) === " ") {
        c = c.substring(1);
      }
      if (c.indexOf(name) === 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  /**
   * @param {string} cookieName
   * @param {any} cookieValue
   * @param {int} expireDays
   */
  function acceleratorSetCookie(cookieName, cookieValue, expireDays) {
    let d = new Date();
    d.setTime(d.getTime() + expireDays * 24 * 60 * 60 * 1000);
    let expires = "expires=" + d.toUTCString();
    document.cookie =
      cookieName + "=" + cookieValue + ";" + expires + ";path=/";
  }

  /**
   * OGDRUACC-4 AC2 Frontend Interaction
   * Open mega menu on hover
   */
  function acceleratorMegaMenuHover() {
    $("#mainNav .nav-item > .nav-link")
      .mouseenter(function () {
        if ($(window).width() >= 991) {
          let thisItem = $(this);

          // clear the opposite timer
          clearTimeout(menuLeaveTimer);
          // add active class after a delay
          menuEnterTimer = setTimeout(function () {
            if (
              !thisItem.hasClass("show") ||
              !thisItem.hasClass("dropdown-toggle")
            ) {
              acceleratorMegaMenuClose();
            }

            if (thisItem.parent().hasClass("megamenu_content")) {
              thisItem.addClass("show");
              thisItem.attr("aria-expanded", true);

              // Mega menu
              thisItem.parent().find(".dropdown-mega-menu").addClass("show");
              thisItem
                .parent()
                .find(".dropdown-mega-menu")
                .attr("data-bs-popper", "none");
            }

            // Single list menu
            if (thisItem.parent().hasClass("single_list")) {
              thisItem.addClass("show");
              thisItem.attr("aria-expanded", true);
              thisItem.parent().find(".dropdown-single-list").addClass("show");
            }
          }, showMenuDelay);
        }
      })
      .mouseleave(function () {
        clearTimeout(menuEnterTimer);
      });

    $("#mainNav").on("mouseleave", function (e) {
      if (!$(e.target).is(".navbar-menu")) {
        // clear the opposite timer
        clearTimeout(menuEnterTimer);
        // remove active class after a delay
        menuLeaveTimer = setTimeout(function () {
          acceleratorMegaMenuClose();
        }, hideMenuDelay);
      }
    });
  }

  /**
   * OGDRUACC-4 AC3 Frontend Interaction
   * Close mega menu
   */
  function acceleratorMegaMenuClose() {
    $("#mainNav .nav-item > .nav-link").each(function () {
      if ($(window).width() >= 991) {
        let thisItem = $(this);

        // Close mega menu
        if (
          thisItem.parent().hasClass("megamenu_content") &&
          thisItem.hasClass("show")
        ) {
          thisItem.removeClass("show");
          thisItem.attr("aria-expanded", false);
          thisItem.parent().find(".dropdown-mega-menu").removeClass("show");
          thisItem
            .parent()
            .find(".dropdown-mega-menu")
            .remove("data-bs-popper");
        }

        // Close single list menu
        if (
          thisItem.parent().hasClass("single_list") &&
          thisItem.hasClass("show")
        ) {
          thisItem.removeClass("show");
          thisItem.attr("aria-expanded", false);
          thisItem.parent().find(".dropdown-single-list").removeClass("show");
        }
      }
    });
  }

  /**
   * OGDRUACC-5 AC2 Frontend Interaction
   * Condense in height with CSS transition animation
   */
  function acceleratorShrinkNavbar() {
    $(window).scroll(function () {
      let scroll = $(window).scrollTop();

      if (scroll >= 400) {
        $("#mainNav").addClass("navbar-shrink");
      } else {
        $("#mainNav").removeClass("navbar-shrink");
      }
    });
  }

  /**
   * OGDRUACC-105
   * Min height of the main content
   * min height = screen height - header height - footer height
   */
  function acceleratorMainContentHeight() {
    const screenHeight = $(window).height();
    const headerHeight = $(".header").outerHeight();
    const footerHeight = $(".footer").outerHeight();

    const mainContentHeight = screenHeight - headerHeight - footerHeight;

    $("#main-content").css("min-height", mainContentHeight + "px");
  }

  /**
   * OGDRUACC-106
   * Implement custom event javascript to track custom actions in Google Analytics
   */
  function acceleratorCustomGTMEvent() {
    // Contact button
    $("#mainNav .block--buttonblock a.nav-link").on("click", function () {
      var clickTime = Date.now();
      dataLayer.push({
        click_time: clickTime,
        event: "click_contact",
      });
    });
  }

  function acceleratorForm() {
    $("form.webform-submission-form").each(function (index, element) {
      var thisForm = $(element);
      acceleratorFormPlaceholder(thisForm);
    });

    //on focus to the first field when error
    $(".form-actions .form-submit").on("click", function () {
      var thisForm = $(this).parents("form.webform-submission-form");

      if (iOS()) {
        document.querySelectorAll(".form-control.error").first().focus();
      } else {
        setTimeout(function () {
          if (thisForm.find(".form-control.error").first()) {
            thisForm.find(".form-control.error").first().focus();
          } else if ($(".form-check-input.error").first()) {
            thisForm.find(".form-check-input.error").first().focus();
          }
        }, 100);
      }

      setTimeout(function () {
        thisForm
          .find($(".form-item--error-message"))
          .each(function (index, element) {
            var formItemSingleCheckbox = $(element).parents(
              ".form-item-single-checkbox"
            );
            if (formItemSingleCheckbox.length) {
              $(element).appendTo(formItemSingleCheckbox);
              $(element).css("margin-left", "-1.5em");
              $(element).addClass("visible");
            }

            var formCheckboxes = $(element).parents(".webform-type-checkboxes");
            if (formCheckboxes.length) {
              $(element).appendTo(formCheckboxes);
              $(element).addClass("visible");
            }

            var formRadios = $(element).parents(".webform-type-radios");
            if (formRadios.length) {
              $(element).appendTo(formRadios);
              $(element).addClass("visible");
            }

            var formDropdown = $(element).parents(
              ".js-form-item-dropdown-select"
            );
            if (formDropdown.length) {
              $(element).appendTo(formDropdown);
            }
          });
      }, 100);
    });
  }

  $(".js-form-item-dropdown-select select").on("change", function(){
    var optionIndex = $(this).find(":selected").index();
    if(optionIndex != '0')
    {
      $(this)
      .parent(".js-form-item-dropdown-select")
      .find(".form-item--error-message")
      .remove()
      
      $(this).removeClass("error");
      
    }

  });

  function acceleratorFormCheckboxCheck() {
    $(".form-check-input").on("click", function () {
      var thisForm = $(this).parents("form.webform-submission-form");
      $(this)
        .parents(".webform-type-checkboxes")
        .find(".form-item--error-message")
        .remove();
      setTimeout(function () {
        thisForm
          .find($(".form-item--error-message"))
          .each(function (index, element) {
            var formItemSingleCheckbox = $(element).parents(
              ".form-item-single-checkbox"
            );
            if (formItemSingleCheckbox.length) {
              $(element).appendTo(formItemSingleCheckbox);
              $(element).css("margin-left", "-1.5em");
              $(element).addClass("visible");
            }

            var formCheckboxes = $(element).parents(".webform-type-checkboxes");
            if (formCheckboxes.length) {
              $(element).appendTo(formCheckboxes);
              $(element).addClass("visible");
            }

            var formRadios = $(element).parents(".webform-type-radios");
            if (formRadios.length) {
              $(element).appendTo(formRadios);
              $(element).addClass("visible");
            }
          });
      }, 1);
    });
  }

  function acceleratorFormPlaceholder(thisForm) {
    $(thisForm)
      .find("input.form-control,textarea.form-control")
      .not(".btn")
      .each(function (index, element) {
        var formItem = $(element).parents(".form-item");
        if (!$(formItem).find(".input-group").length) {
          $(element).wrap("<div class='input-group'></div>");
          var placeholderText = $(element).attr("placeholder");
          var val = $(element).val();
          if (placeholderText) {
            var placeholder =
              '<p class="form-label d-block form-control-placeholder">' +
              placeholderText +
              "</p>";
            $(element).parents(".input-group").append(placeholder);
            $(element).attr("placeholder", "");
          } else {
            $(element).addClass("no-placeholder");
          }

          if (val) {
            $(element).addClass("hasValue");
          }
        }
      });

    $(thisForm)
      .find("input.form-control")
      .on("keyup", function () {
        if (!$(this).val()) {
          $(this).removeClass("hasValue");
          $(this).removeClass("valid");
        } else {
          $(this).addClass("hasValue");
        }
      });
  }

  /**
   * OGDRUACC-127
   * Implement Hero with video background
   */
  function iOS() {
    return (
      [
        "iPad Simulator",
        "iPhone Simulator",
        "iPod Simulator",
        "iPad",
        "iPhone",
        "iPod",
      ].includes(navigator.platform) ||
      // iPad on iOS 13 detection
      (navigator.userAgent.includes("Mac") && "ontouchend" in document)
    );
  }

  /**
   * Bakcground video from youtube call
   */
  function acceleratorHeroBgVideo() {
    var wWidth = $(window).width();
    if (wWidth > 768 && iOS() == false) {
      $("[data-vbg]").youtube_background();
    }
  }

  function acceleratorHeroImage() {
    //alignmen background responsive hero image
    $(".inner-hero picture").each(function () {
      var wWidth = $(window).width();
      var aLarge = $(this).data("align-large");
      var aWide = $(this).data("align-wide");
      var aMobile = $(this).data("align-mobile");
      if (wWidth > 1200) {
        $(this)
          .find("img")
          .attr("style", "object-position: " + aWide);
      } else if (wWidth > 720) {
        $(this)
          .find("img")
          .attr("style", "object-position: " + aLarge);
      } else {
        $(this)
          .find("img")
          .attr("style", "object-position: " + aMobile);
      }
    });
  }

  function acceleratorCarouselPagination() {
    //change aria label pagination slick
    $(".slick-dots").each(function () {
      $(this)
        .find("li")
        .each(function (i) {
          var num = i + 1;
          $(this)
            .children("button")
            .attr("aria-label", "Navigate to slide " + num);
        });
    });
  }

  function acceleratorCarousel() {
    //slick Carousel
    var carouselSlide = $(".carousel-slide")
      .not(".slick-initialized")
      .slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        dots: true,
        arrows: true,
        prevArrow:
          "<button type='button' aria-label='Navigate to previous slide' class='slick-prev pull-left'><i class='fa fa-angle-left' aria-hidden='true'></i></button>",
        nextArrow:
          "<button type='button' aria-label='Navigate to next slide' class='slick-next pull-right'><i class='fa fa-angle-right' aria-hidden='true'></i></button>",
        responsive: [
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
            },
          },
        ],
      });

    setTimeout(function () {
      acceleratorCarouselPagination();
    }, 100);

    carouselSlide.on(
      "afterChange",
      function (event, slick, currentSlide, nextSlide) {
        setTimeout(function () {
          acceleratorCarouselPagination();
        }, 100);
      }
    );

    // Pause button
    $(".btn-play").on("click", function (event) {
      event.preventDefault();
      if ($(this).hasClass("btn-paused")) {
        carouselSlide.slick("slickPlay");
        $(this).removeClass("btn-paused");
        $(this).attr("aria-label", "Pause content carousel movement");
      } else {
        carouselSlide.slick("slickPause");
        $(this).addClass("btn-paused");
        $(this).attr("aria-label", "Resume content carousel movement");
      }
    });

    //accesibility attribute role=list and role=listitem
    $(".slick-track").attr("role", "list");
    $(".slick-track .slick-slide").attr("role", "listitem");

    //accesibility add info to open pop up video
    $(".carousel-play-video").each(function () {
      var existinfo = $(this)
        .closest(".block--carousel")
        .find(".carousel-info").length;
      if (!existinfo) {
        $(
          '<p class="carousel-info sr-only visually-hidden">For the items below, clicking an image link displays a larger version of the image in a lightbox (visual effect only). Clicking a video link opens a dialog box with a video player.</p>'
        ).insertAfter($(this).closest(".block--carousel").find(".btn-play"));
        return;
      }
    });
  }

  function acceleratorCarouselVideoModal() {
    var videoModal;

    // On opening the modal
    $(".carousel-play-video")
      .on("click", function () {
        videoModal = $(this).attr("data-bs-target");
        var videoSrc = $(this).attr("data-video");

        $(videoModal)
          .find("iframe")
          .attr("src", videoSrc + "?autoplay=1&rel=0");

        setTimeout(function () {
          $(".modal-header .btn-close").focus();
        }, 500);
      });

    // On close the modal
    $(".carousel-close-video")
      .on("click", function () {
        $(videoModal).find("iframe").attr("src", "");
      });
  }

  /**
   * OGDRUACC-34
   */
  function acceleratorLightboxContactForm() {
    $(document).ready(function () {
      // AC3 Accessibility
      // The close button should include an aria-label=”Close the window overlay
      if ($(".modal-contact .btn-close").length) {
        $(".modal-contact .btn-close").attr(
          "aria-label",
          "Close the window overlay"
        );
      }

      // AC4 Frontend Interaction
      // The webform page should include google analytics/ GTM and standard page metadata.
      var isSend = false;
      $(".modal-contact .btn-primary")
        .on("click", function () {
          if (isSend == false) {
            isSend = true;
            var clickTime = Date.now();
            dataLayer.push({
              click_time: clickTime,
              event: "click_contact",
            });
          }
        });
    });
  }

  /**
   * OGDRUACC-38
   * Search form on navbar
   */
  function acceleratorNavSearchForm() {
    $(".toggle-search-form").on("click", function (e) {
      e.preventDefault();
      let tg = $(this).data("target");

      $(tg).toggleClass("collapse");
      $(this).toggleClass("active");
      $(".header").toggleClass("search-form-active");
      $(".toggle-search-form button").attr("aria-label", "Open the search box");
      $(".toggle-search-form.active button").attr(
        "aria-label",
        "Close the search box"
      );

      $(".toggle-search-form").attr("aria-expanded", false);
      $(".toggle-search-form.active").attr("aria-expanded", true);
      if ($(this).hasClass("active")) {
        $("#nav-search-form-container form input[type=text]").focus();
      }
    });

    // Mobile
    $("#mainNav #views-exposed-form-search-page .js-form-submit").on(
      "click",
      function () {
        $("#mainNav .navbar-toggler").trigger("click");
      }
    );
  }

  /**
   * OGDRUACC-287
   * https://ogilvyaunz.atlassian.net/browse/OGDRUACC-287
   * Add tabindex=-1 for tabbing in admin
   */
  function acceleratorTabbing() {
    $(document).ready(function () {
      $("[data-contextual-id] button").attr("tabindex", "-1");

      // for sticky menu not disappear when login admin
      jQuery("html").css("--dadmin-top", jQuery("body").css("paddingTop"));
    });
  }

  /**
   * OGDRUACC-410
   * Add is-login class to fix the anchor that doesn't stop at the correct position.
   * The solution is by adding scroll-padding-top property in html tag
   */
  function acceleratorIsLogin() {
    if ($("body").hasClass("toolbar-fixed")) {
      $("html").addClass("is-login");
    }
  }

  function acceleratorArticle() {
    if (!jQuery("body").hasClass("init-focus")) {
      setTimeout(function () {
        jQuery(
          ".views-infinite-scroll-content-wrapper > div:last-child"
        ).addClass("last-child");
      }, 100);
      jQuery(".pager__item .button").click(function () {
        jQuery(".append-text").remove();
        jQuery(
          '<span id="moreItems" class="append-text sr-only" tabindex="-1">More news loaded</span>'
        ).insertAfter("div.last-child");
        jQuery(".append-text").prev("div").removeClass("last-child");
        jQuery(
          ".views-infinite-scroll-content-wrapper > div:last-child"
        ).addClass("last-child");
        setTimeout(function () {
          jQuery(".append-text + div a").focus();
        }, 1000);
      });
    }
    jQuery("body").addClass("init-focus");
    setTimeout(function () {
      jQuery("body").removeClass("init-focus");
    }, 2000);
  }
})(jQuery, Drupal);
