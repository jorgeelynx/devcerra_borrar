/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	
	__webpack_require__(7);
	
	__webpack_require__(9);

/***/ }),
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	
	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }
	
	var _libThreesixtyThreesixtyMin = __webpack_require__(8);
	
	var _libThreesixtyThreesixtyMin2 = _interopRequireDefault(_libThreesixtyThreesixtyMin);
	
	$(document).ready(function () {
	    var videoIframes = undefined,
	        $videoIframesWrapper = undefined,
	        $videoModal = $('#iqit-iqitvideos-modal');
	
	    $('#iqit-threesixty-modal').on('shown.bs.modal', function () {
	        var iqitThreeSixtySlider = (0, _libThreesixtyThreesixtyMin2['default'])(document.querySelector('#iqit-threesixty'), $('#iqit-threesixty').data('threesixty'), {
	            interactive: true,
	            currentImage: 0
	        });
	        iqitThreeSixtySlider.init();
	    });
	
	    $videoModal.on('shown.bs.modal', function () {
	        $videoIframesWrapper = $('#iqitvideos-block');
	        videoIframes = $videoIframesWrapper.html();
	    });
	
	    $videoModal.on('hidden.bs.modal', function () {
	        $videoIframesWrapper.html(videoIframes);
	    });
	});

/***/ }),
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

	/* https://github.com/rbartoli/threesixty/blob/master/dist/threesixty.js */
	"use strict";
	
	!(function (e, t) {
	   true ? module.exports = t() : "function" == typeof define && define.amd ? define("threesixty", [], t) : "object" == typeof exports ? exports.threesixty = t() : e.threesixty = t();
	})(undefined, function () {
	  return (function (e) {
	    function t(r) {
	      if (n[r]) return n[r].exports;var o = n[r] = { exports: {}, id: r, loaded: !1 };return e[r].call(o.exports, o, o.exports, t), o.loaded = !0, o.exports;
	    }var n = {};return t.m = e, t.c = n, t.p = "", t(0);
	  })([function (e, t, n) {
	    n(1), e.exports = n(1);
	  }, function (e, t) {
	    "use strict";Object.defineProperty(t, "__esModule", { value: !0 });var n = function n(e, t, _n) {
	      if (!e) throw new Error("A container argument is required");if (!t) throw new Error("An images argument is required");var o = { interactive: !0, currentFrame: 0 },
	          u = Object.assign({}, o, _n),
	          i = t.length,
	          c = 0,
	          a = 0,
	          d = function d() {
	        s(t, f);
	      },
	          s = function s(e, t) {
	        var n = e.length,
	            r = 0,
	            o = function o() {
	          ++r >= n && t(u);
	        },
	            u = e.map(function (e) {
	          var t = new Image();return t.src = e, t.onload = o, t.onerror = o, t.onabort = o, t.draggable = !1, t;
	        });
	      },
	          f = function f(n) {
	        t = n, r(e), e.appendChild(t[u.currentFrame]), u.interactive && m();
	      },
	          m = function m() {
	        e.addEventListener("touchstart", p), e.addEventListener("mousedown", p);
	      },
	          v = function v(e) {
	        e.preventDefault(), c = void 0 !== e.pageX ? e.pageX : e.changedTouches[0].pageX, c < a ? x() : c > a && g(), a = c;
	      },
	          p = function p(e) {
	        e.preventDefault(), document.addEventListener("touchmove", v), document.addEventListener("mousemove", v), document.addEventListener("touchend", l), document.addEventListener("mouseup", l);
	      },
	          l = function e(t) {
	        t.preventDefault(), document.removeEventListener("touchmove", v), document.removeEventListener("mousemove", v), document.addEventListener("touchend", e), document.addEventListener("mouseup", e);
	      },
	          h = function h() {
	        e.replaceChild(t[u.currentFrame], e.childNodes[0]);
	      },
	          x = function x() {
	        u.currentFrame--, u.currentFrame < 0 && (u.currentFrame = i - 1), h();
	      },
	          g = function g() {
	        u.currentFrame++, u.currentFrame === i && (u.currentFrame = 0), h();
	      },
	          E = function E() {
	        return u.interactive;
	      },
	          F = function F() {
	        return u.currentFrame;
	      };return { init: d, previous: x, next: g, isInteractive: E, getCurrentFrame: F };
	    },
	        r = function r(e) {
	      if (e.hasChildNodes()) for (; e.firstChild;) e.removeChild(e.firstChild);
	    };t["default"] = n, e.exports = t["default"];
	  }]);
	});

/***/ }),
/* 9 */
/***/ (function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);
//# sourceMappingURL=front.js.map