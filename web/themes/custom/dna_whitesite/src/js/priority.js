// The priority.script.js must be loaded before any JavaScript libraries.

/**
 * OGDRUACC-34
 * Add custom attribute for the lightbox button that added using Styles WYSWYG
 */
var lightboxButtons = document.querySelectorAll('.btn-lightbox');
if (lightboxButtons.length > 0) {
	for (var item of lightboxButtons) {
		var attr = item.getAttribute('data-dialog-type');
		if (attr === null) {
			item.setAttribute('data-dialog-type', 'bootstrap4_modal');
			item.setAttribute('data-dialog-options', '{"dialogClasses":"modal-dialog-centered modal-contact","dialogShowHeader":true,"dialogShowHeaderTitle":false}');
		}
	}
}

// Load primary CSS.
window.isFullCSSLoaded = false;
window.fullCssLoad = function () {
	"use strict";
	if (!window.isFullCSSLoaded) {
		var stylesheet = document.createElement('link');
		stylesheet.href = '/themes/custom/dna_whitesite/assets/css/main.css';
		stylesheet.rel = 'stylesheet';
		stylesheet.type = 'text/css';
		stylesheet.media = 'all';
		document.getElementsByTagName('body')[0].appendChild(stylesheet);
		window.isFullCSSLoaded = true;
	}
};
window.fullCssLoad();