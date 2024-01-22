(function ($) {

  'use strict';

  Drupal.behaviors.og_accelerator_article_listing = {
    attach: function (context, settings) {
      if (document.getElementById('article-listing-title') != null) {
        document.getElementById('article-listing-title').innerHTML = document.getElementById('filter-article-heading').innerHTML;
      }
      let finalTitle = [];
      if (document.getElementsByTagName('title')[0] != undefined) {
        let getTitle = document.getElementsByTagName('title')[0].innerHTML;
        let splitTitle = getTitle.split(' ');
        let indexPipe;
        splitTitle.map((item, i) => {
          if (item == '|') {
            indexPipe = i;
          }
        })
        for (let i = indexPipe; i < splitTitle.length; i++) {
          finalTitle.push(splitTitle[i]);
        }
      }
      let finalResultTitle = finalTitle.join(' ');
      $('#views-exposed-form-article-listing-block-article-listing .form-radios input[type="radio"]').click(function () {
        let idVal = $(this).attr('id');
        let choosenCatDefault = $('label[for="' + idVal + '"]').text();
        let choosenCat = $('label[for="' + idVal + '"]').text().toLowerCase();
        let dataTitle = $('.article-card--listing').attr('data-title');
        let dataHeading = $('.article-card--listing').attr('data-heading');
        let dataUrl = JSON.parse($('.article-card--listing').attr('data-url'));
        let getCatUrl = Object.fromEntries(Object.entries(dataUrl).filter((key) => key.includes(choosenCatDefault)));
        let pageUrl = choosenCat !== 'all' ? '/articles/' + getCatUrl[choosenCatDefault] : '/articles/';
        let combineSiteTitle = dataTitle.replace('[filter-name]', choosenCatDefault).concat(' ' + finalResultTitle);
        let pageH1 = choosenCat !== 'all' ? dataHeading.replace('[filter-name]', choosenCatDefault) : 'Latest Articles';
        let pageTitle = choosenCat !== 'all' ? combineSiteTitle : 'Articles ' + finalResultTitle;
        document.getElementsByClassName('article-card--listing')[0].getElementsByTagName('h1')[0].innerHTML = pageH1;
        document.getElementsByTagName('#article-listing-title h1').innerHTML = pageH1;
        document.title = pageTitle;
        history.pushState({ page: choosenCat }, pageTitle, pageUrl);
      });
      $('.block--article-listing #load-more-button').on('click', function (e) {
        e.preventDefault();
        $('#moreNews').remove();
        $('.block--article-listing .views-infinite-scroll-content-wrapper')
          .append(
            '<span id="moreNews" tabindex="-1" class="sr-only visually-hidden position-relative">More news loaded</span>'
          );
      });
      $('.view-article-listing', context).on('views_infinite_scroll.new_content', function (e) {
        setTimeout(function () {
          $('#moreNews').next('.col-12').find('a').focus();
        }, 500);
      });
    }
  };

})(jQuery);
