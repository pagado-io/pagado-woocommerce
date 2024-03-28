/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/html-entities":
/*!**************************************!*\
  !*** external ["wp","htmlEntities"] ***!
  \**************************************/
/***/ ((module) => {

module.exports = window["wp"]["htmlEntities"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/html-entities */ "@wordpress/html-entities");
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);



const {
  registerPaymentMethod
} = window.wc.wcBlocksRegistry;
const {
  getSetting
} = window.wc.wcSettings;
const settings = getSetting('pagado_data', {});
const label = (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_1__.decodeEntities)(settings.title);
const server = 'https://pagado.io';
const Content = () => {
  return (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_1__.decodeEntities)(settings.description || '');
};
const PagadoIframeContent = props => {
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useEffect)(() => {
    jQuery.ajax({
      method: 'post',
      url: '/?wc-ajax=get_pagado_data'
    }).done(function (res, status, xhr) {
      if (status === 'success') {
        initializeIframe(res.data);
      }
    }).fail(function (xhr, status, error) {
      console.log(error);
    });
  }, []);
  console.log(props);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_1__.decodeEntities)(settings.description || '')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    id: "pagado-checkout-wrapper"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("iframe", {
    id: "pagado-checkout-iframe",
    src: "https://pagado.io/checkout-buttons",
    name: "pagado_checkout_iframe",
    height: "100%",
    width: "100%",
    title: "Pagado Checkout",
    style: {
      border: 'none'
    }
  })));
};
const Label = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PaymentMethodLabel, {
    text: label
  });
};
registerPaymentMethod({
  name: "pagado",
  label: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Label, null),
  content: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PagadoIframeContent, null),
  edit: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Content, null),
  canMakePayment: () => true,
  ariaLabel: label,
  supports: {
    features: settings.supports
  }
});

/**
 * Initialize iframe with required data and take action
 * based on message communication between windows.
 *
 * @param {*} data Required data for the window
 * @returns
 */
function initializeIframe(data) {
  const iframe = document.getElementById('pagado-checkout-iframe');
  if (!iframe) {
    return;
  }
  const iframeWindow = iframe.contentWindow;
  iframe.addEventListener('load', handleLoad, true);
  function handleLoad() {
    iframeWindow.postMessage('loaded', server);
  }
  let checkoutWindow;
  jQuery(window).on('message', function (e) {
    e = e.originalEvent;
    if (e.origin === server) {
      if (e.data) {
        const eventData = JSON.parse(e.data);

        // console.log(eventData);
        // return;

        if (eventData.target === 'checkout_button' && eventData.event === 'click') {
          const urlData = {
            origin: window.origin,
            redirect: window.location.href,
            variant: data.variant,
            to: data.to,
            price: data.price,
            currency: data.currency,
            version: data.version
          };
          const urlParams = `currency=${urlData.currency}&price=${urlData.price}&to=${urlData.to}&redirect=${urlData.redirect}&variant=${urlData.variant}&version=${urlData.version}`;
          const settings = {
            url: `${server}/checkout?${urlParams}`,
            target: 'pagado-checkout-window',
            width: 300,
            height: 500,
            left: 100,
            top: 100
          };
          settings.left = window.innerWidth / 2 - settings.width / 2;
          settings.top = window.innerHeight / 2 - settings.height / 2;
          const features = `width=${settings.width}, height=${settings.height}, left=${settings.left}, top=${settings.top}`;
          checkoutWindow = window.open(settings.url, settings.target, features);
        }
        if (eventData.target === 'checkout_window' && eventData.event === 'checkout') {
          const checkoutForm = $('form.checkout');
          const transactionIdHiddenField = checkoutForm.find('#pagado_data');
          if (transactionIdHiddenField.length) {
            transactionIdHiddenField.remove();
          }
          if (checkoutWindow && !checkoutWindow.closed) {
            checkoutWindow.close();
            checkoutForm.append(`<input type='hidden' id='pagado_data' name='pagado_data' value=${JSON.stringify(eventData)}>`);
            $('#place_order').click();
          }
        }
      }
    }
  });
}
})();

/******/ })()
;
//# sourceMappingURL=index.js.map