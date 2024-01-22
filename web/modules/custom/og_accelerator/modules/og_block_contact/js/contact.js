(function ($) {

  "use strict";

  var delay = 250;
  var timeout = false;

  Drupal.behaviors.og_block_contact = {
    attach: function (context, settings) {
      $(document).ready(function() {
        acceleratorContactCard();
      });
    }
  };

  /**
   * Resize 
   */
  $(window).on("resize", function () {
    clearTimeout(timeout);
    timeout = setTimeout(function () {
      acceleratorContactCard(); 
    }, delay);
  });

  // OGDRUACC-132
  window.addEventListener("load", function () {
    console.log('contact.js : load event');

    jQuery('body').on('click', '.accelerator-block--contact a[property]', function(e){
      // e.preventDefault();
      let typeArray = {
        'telephone' : 'contact_phone',
        'mobile' : 'contact_mobile',
        'email' : 'contact_email',
        'url' : 'contact_web'
      };
      
      if (typeof dataLayer.push === 'function') {
        let property = jQuery(this).attr('property');
        if (property==='telephone' && jQuery(this).data('type')==='mobile') {
          property = jQuery(this).data('type');
        }
        let data = jQuery(this).attr('href'); 
        if (property!=='url') {
          data = jQuery(this).data('content'); 
        }
        // let params = {
        //   'event' : 'contact_card__' + property
        // };
        let params = {
          'event' : 'click_contact_card' 
        };
        params[ typeArray[property] ] = data;

        console.log('trigger : dataLayer.push');
        console.log(params);
        dataLayer.push(params);
      }
    });
  });

  /**
   * OGDRUACC-11  
   * Frontend Interaction AC2: On mobile the contact cards should render as collapsed/ accordion style
   */
  function acceleratorContactCard() {
    $(".block--contact").each(function(index) {
      if ($(window).width() < 768) {
        $(this).find(".contact-title").attr("tabindex", 0);
        $(this).find(".contact-title").attr("data-bs-toggle", "collapse");
        $(this).find(".contact-content").addClass("collapse");
      } else {
        $(this).find(".contact-title").attr("tabindex", -1);
        $(this).find(".contact-title").removeAttr("data-bs-toggle");
        $(this).find(".contact-content").removeClass("collapse");
      }
    });
  }

})(jQuery);