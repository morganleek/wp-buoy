/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
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
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/google-charts/dist/googleCharts.esm.js":
/*!*************************************************************!*\
  !*** ./node_modules/google-charts/dist/googleCharts.esm.js ***!
  \*************************************************************/
/*! exports provided: default, GoogleCharts */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "GoogleCharts", function() { return GoogleCharts; });
/* googleCharts.js Version: 1.5.0 Built On: 2018-12-30 */
const loadScript = Symbol('loadScript');
const instance = Symbol('instance');

let _instance;

class GoogleChartsManager {
  get [instance]() {
    return _instance;
  }

  set [instance](value) {
    _instance = value;
  }

  constructor() {
    if (this[instance]) {
      return this[instance];
    }

    this[instance] = this;
  }

  reset() {
    _instance = null;
  }

  [loadScript]() {
    if (!this.scriptPromise) {
      this.scriptPromise = new Promise(resolve => {
        const body = document.getElementsByTagName('body')[0];
        const script = document.createElement('script');
        script.type = 'text/javascript';

        script.onload = function () {
          GoogleCharts.api = window.google;
          GoogleCharts.api.charts.load('current', {
            packages: ['corechart', 'table']
          });
          GoogleCharts.api.charts.setOnLoadCallback(() => {
            resolve();
          });
        };

        script.src = 'https://www.gstatic.com/charts/loader.js';
        body.appendChild(script);
      });
    }

    return this.scriptPromise;
  }

  load(callback, type) {
    return this[loadScript]().then(() => {
      if (type) {
        let config = {};

        if (type instanceof Object) {
          config = type;
        } else if (Array.isArray(type)) {
          config = {
            packages: type
          };
        } else {
          config = {
            packages: [type]
          };
        }

        this.api.charts.load('current', config);
        this.api.charts.setOnLoadCallback(callback);
      } else {
        if (typeof callback != 'function') {
          throw 'callback must be a function';
        } else {
          callback();
        }
      }
    });
  }

}

const GoogleCharts = new GoogleChartsManager();
/* harmony default export */ __webpack_exports__["default"] = (GoogleChartsManager);


/***/ }),

/***/ "./src/js/bundle.js":
/*!**************************!*\
  !*** ./src/js/bundle.js ***!
  \**************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var google_charts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! google-charts */ "./node_modules/google-charts/dist/googleCharts.esm.js");

 // Charts

var chartsArray = []; // , dataPointsArray = [];

jquery__WEBPACK_IMPORTED_MODULE_0___default()(function () {
  // get charts
  jquery__WEBPACK_IMPORTED_MODULE_0___default()('.ticks-chart').each(function () {
    var $chart = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this);
    var $surround = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).closest('.chart-surround');
    var id, buoyID, waveTickMax, waveTicks, peakTickMax, peakTicks, dataPoints;

    if ($chart.attr('id') !== undefined) {
      // chartsArray.push();
      id = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).attr('id');
      var $ticksData = $surround.find('.ticks-data');

      if ($ticksData.length === 1) {
        buoyID = $ticksData.attr('data-buoy-id') !== undefined ? $ticksData.attr('data-buoy-id') : '';
        waveTickMax = $ticksData.attr('data-wave-tick-max') !== undefined ? $ticksData.attr('data-wave-tick-max') : '';
        waveTicks = $ticksData.attr('data-wave-ticks') !== undefined ? $ticksData.attr('data-wave-ticks').split(',') : '';
        peakTickMax = $ticksData.attr('data-peak-tick-max') !== undefined ? $ticksData.attr('data-peak-tick-max') : '';
        peakTicks = $ticksData.attr('data-peak-ticks') !== undefined ? $ticksData.attr('data-peak-ticks').split(',') : '';
        dataPoints = $ticksData.attr('data-data-points') !== undefined ? eval('[' + JSON.parse($ticksData.attr('data-data-points')) + ']') : '';
      } // local time label


      var offset = new Date().getTimezoneOffset() / 60 * -1; // hours from GMT

      var timeLabel = offset === 0 ? "Time (GMT)" : offset > 0 ? "Time (GMT+" + offset + ")" : "Time (GMT" + offset + ")"; // push to chart array

      if (id !== undefined && buoyID.length > 0 && waveTicks.length > 0 && waveTickMax.length > 0 && peakTicks.length > 0 && peakTickMax.length > 0 && dataPoints.length > 0) {
        chartsArray.push({
          id: id,
          buoyID: buoyID,
          dataPoints: dataPoints.slice(0, -1),
          options: {
            title: '',
            height: 280,
            backgroundColor: {
              fill: "transparent"
            },
            // chartArea: {left: 45, top: 30, right: 45, bottom: 30},
            series: {
              0: {
                targetAxisIndex: 0,
                color: '#449d44',
                type: 'area'
              },
              1: {
                targetAxisIndex: 1,
                color: 'transparent',
                visibleInLegend: true
              }
            },
            // hAxis: {
            //   'title': timeLabel
            // },
            vAxes: {
              0: {
                title: 'Wave Height (m)',
                viewWindow: {
                  min: 0,
                  max: waveTickMax
                },
                ticks: waveTicks
              },
              1: {
                title: 'Peak Period (s)',
                viewWindow: {
                  min: 0,
                  max: peakTickMax
                },
                ticks: peakTicks
              }
            },
            hAxis: {
              // 1: {
              title: timeLabel,
              interval: 1,
              gridlines: {
                units: {
                  days: {
                    format: ['MMM d']
                  },
                  hours: {
                    format: ['ha']
                  }
                }
              },
              minorGridlines: {
                units: {
                  hours: {
                    format: ['ha']
                  }
                }
              } // }

            }
          }
        });
      }
    }
  }); // ES6 Load

  google_charts__WEBPACK_IMPORTED_MODULE_1__["GoogleCharts"].load(drawChart, {
    'packages': ['line', 'corechart']
  });
  jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).resize(function () {
    drawChart();
  });
});

function drawChart() {
  if (chartsArray.length > 0) {
    for (var i = 0; i < chartsArray.length; i++) {
      // labels + chart data
      var data = new google_charts__WEBPACK_IMPORTED_MODULE_1__["GoogleCharts"].api.visualization.DataTable();
      data.addColumn('date', 'Month');
      data.addColumn('number', "Significant Wave Height");
      data.addColumn({
        type: 'string',
        role: 'tooltip'
      });
      data.addColumn('number', "Peak Period and Direction");
      data.addColumn({
        type: 'string',
        role: 'tooltip'
      });
      data.addRows(chartsArray[i].dataPoints);
      var dataChart = new google_charts__WEBPACK_IMPORTED_MODULE_1__["GoogleCharts"].api.visualization.LineChart(document.getElementById(chartsArray[i].id));
      google_charts__WEBPACK_IMPORTED_MODULE_1__["GoogleCharts"].api.visualization.events.addListener(dataChart, "ready", drawMarkers.bind(dataChart, chartsArray[i]));
      dataChart.draw(data, chartsArray[i].options);
      console.log(dataChart);
    }
  }
}

function drawMarkers(chartData) {
  // interface
  var cli = this.getChartLayoutInterface(); // for each chart

  for (var i = 0; i < chartData.dataPoints.length; i++) {
    var label = '.om-' + chartData.buoyID + '-overlay-marker-' + i;

    if (document.querySelector(label) !== null) {
      document.querySelector(label).style.top = Math.floor(cli.getYLocation(chartData.dataPoints[i][3], 1)) - 25 + "px";
      document.querySelector(label).style.left = Math.floor(cli.getXLocation(chartData.dataPoints[i][0])) - 25 + "px";
    }
  } // Place Legend Marker


  document.querySelector('.om-' + chartData.buoyID + '-legend-marker').style.top = Math.floor(cli.getBoundingBox("legendentry#1").top) - 8 + "px";
  document.querySelector('.om-' + chartData.buoyID + '-legend-marker').style.left = Math.floor(cli.getBoundingBox("legendentry#1").left) + 4 + "px";
}

/***/ }),

/***/ 0:
/*!********************************!*\
  !*** multi ./src/js/bundle.js ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /var/www/wpw.grover/wp-content/plugins/wp-buoy/src/js/bundle.js */"./src/js/bundle.js");


/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vbm9kZV9tb2R1bGVzL2dvb2dsZS1jaGFydHMvZGlzdC9nb29nbGVDaGFydHMuZXNtLmpzIiwid2VicGFjazovLy8uL3NyYy9qcy9idW5kbGUuanMiLCJ3ZWJwYWNrOi8vL2V4dGVybmFsIFwialF1ZXJ5XCIiXSwibmFtZXMiOlsibG9hZFNjcmlwdCIsIlN5bWJvbCIsImluc3RhbmNlIiwiX2luc3RhbmNlIiwiR29vZ2xlQ2hhcnRzTWFuYWdlciIsInZhbHVlIiwiY29uc3RydWN0b3IiLCJyZXNldCIsInNjcmlwdFByb21pc2UiLCJQcm9taXNlIiwicmVzb2x2ZSIsImJvZHkiLCJkb2N1bWVudCIsImdldEVsZW1lbnRzQnlUYWdOYW1lIiwic2NyaXB0IiwiY3JlYXRlRWxlbWVudCIsInR5cGUiLCJvbmxvYWQiLCJHb29nbGVDaGFydHMiLCJhcGkiLCJ3aW5kb3ciLCJnb29nbGUiLCJjaGFydHMiLCJsb2FkIiwicGFja2FnZXMiLCJzZXRPbkxvYWRDYWxsYmFjayIsInNyYyIsImFwcGVuZENoaWxkIiwiY2FsbGJhY2siLCJ0aGVuIiwiY29uZmlnIiwiT2JqZWN0IiwiQXJyYXkiLCJpc0FycmF5IiwiY2hhcnRzQXJyYXkiLCIkIiwiZWFjaCIsIiRjaGFydCIsIiRzdXJyb3VuZCIsImNsb3Nlc3QiLCJpZCIsImJ1b3lJRCIsIndhdmVUaWNrTWF4Iiwid2F2ZVRpY2tzIiwicGVha1RpY2tNYXgiLCJwZWFrVGlja3MiLCJkYXRhUG9pbnRzIiwiYXR0ciIsInVuZGVmaW5lZCIsIiR0aWNrc0RhdGEiLCJmaW5kIiwibGVuZ3RoIiwic3BsaXQiLCJldmFsIiwiSlNPTiIsInBhcnNlIiwib2Zmc2V0IiwiRGF0ZSIsImdldFRpbWV6b25lT2Zmc2V0IiwidGltZUxhYmVsIiwicHVzaCIsInNsaWNlIiwib3B0aW9ucyIsInRpdGxlIiwiaGVpZ2h0IiwiYmFja2dyb3VuZENvbG9yIiwiZmlsbCIsInNlcmllcyIsInRhcmdldEF4aXNJbmRleCIsImNvbG9yIiwidmlzaWJsZUluTGVnZW5kIiwidkF4ZXMiLCJ2aWV3V2luZG93IiwibWluIiwibWF4IiwidGlja3MiLCJoQXhpcyIsImludGVydmFsIiwiZ3JpZGxpbmVzIiwidW5pdHMiLCJkYXlzIiwiZm9ybWF0IiwiaG91cnMiLCJtaW5vckdyaWRsaW5lcyIsImRyYXdDaGFydCIsInJlc2l6ZSIsImkiLCJkYXRhIiwidmlzdWFsaXphdGlvbiIsIkRhdGFUYWJsZSIsImFkZENvbHVtbiIsInJvbGUiLCJhZGRSb3dzIiwiZGF0YUNoYXJ0IiwiTGluZUNoYXJ0IiwiZ2V0RWxlbWVudEJ5SWQiLCJldmVudHMiLCJhZGRMaXN0ZW5lciIsImRyYXdNYXJrZXJzIiwiYmluZCIsImRyYXciLCJjb25zb2xlIiwibG9nIiwiY2hhcnREYXRhIiwiY2xpIiwiZ2V0Q2hhcnRMYXlvdXRJbnRlcmZhY2UiLCJsYWJlbCIsInF1ZXJ5U2VsZWN0b3IiLCJzdHlsZSIsInRvcCIsIk1hdGgiLCJmbG9vciIsImdldFlMb2NhdGlvbiIsImxlZnQiLCJnZXRYTG9jYXRpb24iLCJnZXRCb3VuZGluZ0JveCJdLCJtYXBwaW5ncyI6IjtRQUFBO1FBQ0E7O1FBRUE7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBOzs7UUFHQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0EsMENBQTBDLGdDQUFnQztRQUMxRTtRQUNBOztRQUVBO1FBQ0E7UUFDQTtRQUNBLHdEQUF3RCxrQkFBa0I7UUFDMUU7UUFDQSxpREFBaUQsY0FBYztRQUMvRDs7UUFFQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0EseUNBQXlDLGlDQUFpQztRQUMxRSxnSEFBZ0gsbUJBQW1CLEVBQUU7UUFDckk7UUFDQTs7UUFFQTtRQUNB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LENBQUNDLEtBQUwsQ0FBV1AsR0FBRyxDQUFDVyxjQUFKLENBQW1CLGVBQW5CLEVBQW9DRixJQUEvQyxJQUF1RCxDQUF2RCxHQUEyRCxJQUE3STtBQUNELEM7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDaklELHdCIiwiZmlsZSI6ImJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAwKTtcbiIsIi8qIGdvb2dsZUNoYXJ0cy5qcyBWZXJzaW9uOiAxLjUuMCBCdWlsdCBPbjogMjAxOC0xMi0zMCAqL1xuY29uc3QgbG9hZFNjcmlwdCA9IFN5bWJvbCgnbG9hZFNjcmlwdCcpO1xuY29uc3QgaW5zdGFuY2UgPSBTeW1ib2woJ2luc3RhbmNlJyk7XG5sZXQgX2luc3RhbmNlO1xuXG5jbGFzcyBHb29nbGVDaGFydHNNYW5hZ2VyIHtcbiAgICBnZXQgW2luc3RhbmNlXSgpIHtcbiAgICAgICAgcmV0dXJuIF9pbnN0YW5jZVxuICAgIH1cblxuICAgIHNldCBbaW5zdGFuY2VdKHZhbHVlKSB7XG4gICAgICAgIF9pbnN0YW5jZSA9IHZhbHVlO1xuICAgIH1cblxuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICBpZiAodGhpc1tpbnN0YW5jZV0pIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzW2luc3RhbmNlXVxuICAgICAgICB9XG5cbiAgICAgICAgdGhpc1tpbnN0YW5jZV0gPSB0aGlzO1xuICAgIH1cblxuICAgIHJlc2V0KCkge1xuICAgICAgICBfaW5zdGFuY2UgPSBudWxsO1xuICAgIH1cblxuICAgIFtsb2FkU2NyaXB0XSgpIHtcbiAgICAgICAgaWYgKCF0aGlzLnNjcmlwdFByb21pc2UpIHtcbiAgICAgICAgICAgIHRoaXMuc2NyaXB0UHJvbWlzZSA9IG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnN0IGJvZHkgPSBkb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnYm9keScpWzBdO1xuICAgICAgICAgICAgICAgIGNvbnN0IHNjcmlwdCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3NjcmlwdCcpO1xuICAgICAgICAgICAgICAgIHNjcmlwdC50eXBlID0gJ3RleHQvamF2YXNjcmlwdCc7XG4gICAgICAgICAgICAgICAgc2NyaXB0Lm9ubG9hZCA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICBHb29nbGVDaGFydHMuYXBpID0gd2luZG93Lmdvb2dsZTtcbiAgICAgICAgICAgICAgICAgICAgR29vZ2xlQ2hhcnRzLmFwaS5jaGFydHMubG9hZCgnY3VycmVudCcsIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHBhY2thZ2VzOiBbJ2NvcmVjaGFydCcsICd0YWJsZSddLFxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgR29vZ2xlQ2hhcnRzLmFwaS5jaGFydHMuc2V0T25Mb2FkQ2FsbGJhY2soKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgIHNjcmlwdC5zcmMgPSAnaHR0cHM6Ly93d3cuZ3N0YXRpYy5jb20vY2hhcnRzL2xvYWRlci5qcyc7XG4gICAgICAgICAgICAgICAgYm9keS5hcHBlbmRDaGlsZChzY3JpcHQpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHRoaXMuc2NyaXB0UHJvbWlzZVxuICAgIH1cblxuICAgIGxvYWQoY2FsbGJhY2ssIHR5cGUpIHtcbiAgICAgICAgcmV0dXJuIHRoaXNbbG9hZFNjcmlwdF0oKS50aGVuKCgpID0+IHtcbiAgICAgICAgICAgIGlmICh0eXBlKSB7XG4gICAgICAgICAgICAgICAgbGV0IGNvbmZpZyA9IHt9O1xuICAgICAgICAgICAgICAgIGlmICh0eXBlIGluc3RhbmNlb2YgT2JqZWN0KSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbmZpZyA9IHR5cGU7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmIChBcnJheS5pc0FycmF5KHR5cGUpKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbmZpZyA9IHsgcGFja2FnZXM6IHR5cGUgfTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBjb25maWcgPSB7IHBhY2thZ2VzOiBbdHlwZV0gfTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgdGhpcy5hcGkuY2hhcnRzLmxvYWQoJ2N1cnJlbnQnLCBjb25maWcpO1xuICAgICAgICAgICAgICAgIHRoaXMuYXBpLmNoYXJ0cy5zZXRPbkxvYWRDYWxsYmFjayhjYWxsYmFjayk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjayAhPSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgICAgICAgICAgIHRocm93KCdjYWxsYmFjayBtdXN0IGJlIGEgZnVuY3Rpb24nKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBjYWxsYmFjaygpOyAgICAgICAgICAgICAgIFxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSlcbiAgICB9XG59XG5cbmNvbnN0IEdvb2dsZUNoYXJ0cyA9IG5ldyBHb29nbGVDaGFydHNNYW5hZ2VyKCk7XG5cbmV4cG9ydCBkZWZhdWx0IEdvb2dsZUNoYXJ0c01hbmFnZXI7XG5leHBvcnQgeyBHb29nbGVDaGFydHMgfTtcbiIsImltcG9ydCAkIGZyb20gJ2pxdWVyeSc7XG5pbXBvcnQgeyBHb29nbGVDaGFydHMgfSBmcm9tICdnb29nbGUtY2hhcnRzJztcblxuLy8gQ2hhcnRzXG5sZXQgY2hhcnRzQXJyYXkgPSBbXTsgLy8gLCBkYXRhUG9pbnRzQXJyYXkgPSBbXTtcblxuJChmdW5jdGlvbigpIHtcbiAgLy8gZ2V0IGNoYXJ0c1xuICAkKCcudGlja3MtY2hhcnQnKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgIGxldCAkY2hhcnQgPSAkKHRoaXMpO1xuICAgIGxldCAkc3Vycm91bmQgPSAkKHRoaXMpLmNsb3Nlc3QoJy5jaGFydC1zdXJyb3VuZCcpO1xuICAgIGxldCBpZCwgYnVveUlELCB3YXZlVGlja01heCwgd2F2ZVRpY2tzLCBwZWFrVGlja01heCwgcGVha1RpY2tzLCBkYXRhUG9pbnRzO1xuICAgIGlmKCRjaGFydC5hdHRyKCdpZCcpICE9PSB1bmRlZmluZWQpIHtcbiAgICAgIC8vIGNoYXJ0c0FycmF5LnB1c2goKTtcbiAgICAgIGlkID0gJCh0aGlzKS5hdHRyKCdpZCcpO1xuICAgICAgbGV0ICR0aWNrc0RhdGEgPSAkc3Vycm91bmQuZmluZCgnLnRpY2tzLWRhdGEnKTtcbiAgICAgIGlmKCR0aWNrc0RhdGEubGVuZ3RoID09PSAxKSB7XG4gICAgICAgIGJ1b3lJRCA9ICgkdGlja3NEYXRhLmF0dHIoJ2RhdGEtYnVveS1pZCcpICE9PSB1bmRlZmluZWQpID8gJHRpY2tzRGF0YS5hdHRyKCdkYXRhLWJ1b3ktaWQnKSA6ICcnO1xuICAgICAgICB3YXZlVGlja01heCA9ICgkdGlja3NEYXRhLmF0dHIoJ2RhdGEtd2F2ZS10aWNrLW1heCcpICE9PSB1bmRlZmluZWQpID8gJHRpY2tzRGF0YS5hdHRyKCdkYXRhLXdhdmUtdGljay1tYXgnKSA6ICcnO1xuICAgICAgICB3YXZlVGlja3MgPSAoJHRpY2tzRGF0YS5hdHRyKCdkYXRhLXdhdmUtdGlja3MnKSAhPT0gdW5kZWZpbmVkKSA/ICR0aWNrc0RhdGEuYXR0cignZGF0YS13YXZlLXRpY2tzJykuc3BsaXQoJywnKSA6ICcnO1xuICAgICAgICBwZWFrVGlja01heCA9ICgkdGlja3NEYXRhLmF0dHIoJ2RhdGEtcGVhay10aWNrLW1heCcpICE9PSB1bmRlZmluZWQpID8gJHRpY2tzRGF0YS5hdHRyKCdkYXRhLXBlYWstdGljay1tYXgnKSA6ICcnO1xuICAgICAgICBwZWFrVGlja3MgPSAoJHRpY2tzRGF0YS5hdHRyKCdkYXRhLXBlYWstdGlja3MnKSAhPT0gdW5kZWZpbmVkKSA/ICR0aWNrc0RhdGEuYXR0cignZGF0YS1wZWFrLXRpY2tzJykuc3BsaXQoJywnKSA6ICcnO1xuICAgICAgICBkYXRhUG9pbnRzID0gKCR0aWNrc0RhdGEuYXR0cignZGF0YS1kYXRhLXBvaW50cycpICE9PSB1bmRlZmluZWQpID8gZXZhbCgnWycgKyBKU09OLnBhcnNlKCR0aWNrc0RhdGEuYXR0cignZGF0YS1kYXRhLXBvaW50cycpKSArICddJykgOiAnJztcbiAgICAgIH1cblxuICAgICAgLy8gbG9jYWwgdGltZSBsYWJlbFxuICAgICAgY29uc3Qgb2Zmc2V0ID0gbmV3IERhdGUoKS5nZXRUaW1lem9uZU9mZnNldCgpIC8gNjAgKiAtMTsgLy8gaG91cnMgZnJvbSBHTVRcbiAgICAgIGNvbnN0IHRpbWVMYWJlbCA9IChvZmZzZXQgPT09IDApID8gXCJUaW1lIChHTVQpXCIgOiAob2Zmc2V0ID4gMCkgPyBcIlRpbWUgKEdNVCtcIiArIG9mZnNldCArIFwiKVwiIDogXCJUaW1lIChHTVRcIiArIG9mZnNldCArIFwiKVwiO1xuXG4gICAgICAvLyBwdXNoIHRvIGNoYXJ0IGFycmF5XG4gICAgICBpZihpZCAhPT0gdW5kZWZpbmVkICYmIGJ1b3lJRC5sZW5ndGggPiAwICYmIHdhdmVUaWNrcy5sZW5ndGggPiAwICYmIHdhdmVUaWNrTWF4Lmxlbmd0aCA+IDAgJiYgcGVha1RpY2tzLmxlbmd0aCA+IDAgJiYgcGVha1RpY2tNYXgubGVuZ3RoID4gMCAmJiBkYXRhUG9pbnRzLmxlbmd0aCA+IDApIHtcbiAgICAgICAgY2hhcnRzQXJyYXkucHVzaCh7aWQ6IGlkLCBidW95SUQ6IGJ1b3lJRCwgZGF0YVBvaW50czogZGF0YVBvaW50cy5zbGljZSgwLCAtMSksIG9wdGlvbnM6IHtcbiAgICAgICAgICB0aXRsZTogJycsXG4gICAgICAgICAgaGVpZ2h0OiAyODAsXG4gICAgICAgICAgYmFja2dyb3VuZENvbG9yOiB7IGZpbGw6IFwidHJhbnNwYXJlbnRcIiB9LFxuICAgICAgICAgIC8vIGNoYXJ0QXJlYToge2xlZnQ6IDQ1LCB0b3A6IDMwLCByaWdodDogNDUsIGJvdHRvbTogMzB9LFxuICAgICAgICAgIHNlcmllczoge1xuICAgICAgICAgICAgMDoge3RhcmdldEF4aXNJbmRleDogMCwgY29sb3I6ICcjNDQ5ZDQ0JywgdHlwZTogJ2FyZWEnfSxcbiAgICAgICAgICAgIDE6IHt0YXJnZXRBeGlzSW5kZXg6IDEsIGNvbG9yOiAndHJhbnNwYXJlbnQnLCB2aXNpYmxlSW5MZWdlbmQ6IHRydWV9XG4gICAgICAgICAgfSxcbiAgICAgICAgICAvLyBoQXhpczoge1xuICAgICAgICAgIC8vICAgJ3RpdGxlJzogdGltZUxhYmVsXG4gICAgICAgICAgLy8gfSxcbiAgICAgICAgICB2QXhlczoge1xuICAgICAgICAgICAgMDoge1xuICAgICAgICAgICAgICB0aXRsZTogJ1dhdmUgSGVpZ2h0IChtKScsXG4gICAgICAgICAgICAgIHZpZXdXaW5kb3c6IHtcbiAgICAgICAgICAgICAgICBtaW46IDAsXG4gICAgICAgICAgICAgICAgbWF4OiB3YXZlVGlja01heFxuICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICB0aWNrczogd2F2ZVRpY2tzXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgMToge1xuICAgICAgICAgICAgICB0aXRsZTogJ1BlYWsgUGVyaW9kIChzKScsXG4gICAgICAgICAgICAgIHZpZXdXaW5kb3c6IHtcbiAgICAgICAgICAgICAgICBtaW46IDAsXG4gICAgICAgICAgICAgICAgbWF4OiBwZWFrVGlja01heFxuICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICB0aWNrczogcGVha1RpY2tzXG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSxcbiAgICAgICAgICBoQXhpczoge1xuICAgICAgICAgICAgLy8gMToge1xuICAgICAgICAgICAgdGl0bGU6IHRpbWVMYWJlbCxcbiAgICAgICAgICAgIGludGVydmFsOiAxLFxuICAgICAgICAgICAgZ3JpZGxpbmVzOiB7XG4gICAgICAgICAgICAgIHVuaXRzOiB7XG4gICAgICAgICAgICAgICAgZGF5czoge2Zvcm1hdDogWydNTU0gZCddfSxcbiAgICAgICAgICAgICAgICBob3Vyczoge2Zvcm1hdDogWydoYSddfSxcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG1pbm9yR3JpZGxpbmVzOiB7XG4gICAgICAgICAgICAgIHVuaXRzOiB7XG4gICAgICAgICAgICAgICAgaG91cnM6IHtmb3JtYXQ6IFsnaGEnXX1cbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgLy8gfVxuICAgICAgICAgIH1cbiAgICAgICAgfX0pO1xuICAgICAgfVxuICAgIH1cbiAgfSk7XG5cbiAgLy8gRVM2IExvYWRcbiAgR29vZ2xlQ2hhcnRzLmxvYWQoZHJhd0NoYXJ0LCBcbiAgICB7J3BhY2thZ2VzJzogWydsaW5lJywgJ2NvcmVjaGFydCddfVxuICApO1xuXG4gICQod2luZG93KS5yZXNpemUoZnVuY3Rpb24oKSB7XG4gICAgZHJhd0NoYXJ0KCk7XG4gIH0pO1xufSk7XG5cbmZ1bmN0aW9uIGRyYXdDaGFydCgpIHtcbiAgaWYoY2hhcnRzQXJyYXkubGVuZ3RoID4gMCkge1xuICAgIGZvcihsZXQgaSA9IDA7IGkgPCBjaGFydHNBcnJheS5sZW5ndGg7IGkrKykge1xuICAgICAgLy8gbGFiZWxzICsgY2hhcnQgZGF0YVxuICAgICAgdmFyIGRhdGEgPSBuZXcgR29vZ2xlQ2hhcnRzLmFwaS52aXN1YWxpemF0aW9uLkRhdGFUYWJsZSgpO1xuICAgICAgZGF0YS5hZGRDb2x1bW4oJ2RhdGUnLCAnTW9udGgnKTtcbiAgICAgIGRhdGEuYWRkQ29sdW1uKCdudW1iZXInLCBcIlNpZ25pZmljYW50IFdhdmUgSGVpZ2h0XCIpO1xuICAgICAgZGF0YS5hZGRDb2x1bW4oe3R5cGU6ICdzdHJpbmcnLCByb2xlOiAndG9vbHRpcCd9KTtcbiAgICAgIGRhdGEuYWRkQ29sdW1uKCdudW1iZXInLCBcIlBlYWsgUGVyaW9kIGFuZCBEaXJlY3Rpb25cIik7XG4gICAgICBkYXRhLmFkZENvbHVtbih7dHlwZTogJ3N0cmluZycsIHJvbGU6ICd0b29sdGlwJ30pO1xuICAgICAgZGF0YS5hZGRSb3dzKGNoYXJ0c0FycmF5W2ldLmRhdGFQb2ludHMpO1xuXG4gICAgICBjb25zdCBkYXRhQ2hhcnQgPSBuZXcgR29vZ2xlQ2hhcnRzLmFwaS52aXN1YWxpemF0aW9uLkxpbmVDaGFydChkb2N1bWVudC5nZXRFbGVtZW50QnlJZChjaGFydHNBcnJheVtpXS5pZCkpO1xuICAgICAgR29vZ2xlQ2hhcnRzLmFwaS52aXN1YWxpemF0aW9uLmV2ZW50cy5hZGRMaXN0ZW5lcihkYXRhQ2hhcnQsIFwicmVhZHlcIiwgZHJhd01hcmtlcnMuYmluZChkYXRhQ2hhcnQsIGNoYXJ0c0FycmF5W2ldKSk7XG4gICAgICBkYXRhQ2hhcnQuZHJhdyhkYXRhLCBjaGFydHNBcnJheVtpXS5vcHRpb25zKTtcbiAgICAgIFxuICAgICAgY29uc29sZS5sb2coZGF0YUNoYXJ0KTtcbiAgICB9XG4gIH1cbn1cblxuZnVuY3Rpb24gZHJhd01hcmtlcnMoY2hhcnREYXRhKSB7XG4gIC8vIGludGVyZmFjZVxuICBsZXQgY2xpID0gdGhpcy5nZXRDaGFydExheW91dEludGVyZmFjZSgpO1xuICAvLyBmb3IgZWFjaCBjaGFydFxuICBmb3IobGV0IGkgPSAwOyBpIDwgY2hhcnREYXRhLmRhdGFQb2ludHMubGVuZ3RoOyBpKyspIHtcbiAgICBsZXQgbGFiZWwgPSAnLm9tLScgKyBjaGFydERhdGEuYnVveUlEICsgJy1vdmVybGF5LW1hcmtlci0nICsgaTtcbiAgICBpZihkb2N1bWVudC5xdWVyeVNlbGVjdG9yKGxhYmVsKSAhPT0gbnVsbCkge1xuICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvcihsYWJlbCkuc3R5bGUudG9wID0gTWF0aC5mbG9vcihjbGkuZ2V0WUxvY2F0aW9uKGNoYXJ0RGF0YS5kYXRhUG9pbnRzW2ldWzNdLCAxKSkgLSAyNSArIFwicHhcIjtcbiAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IobGFiZWwpLnN0eWxlLmxlZnQgPSBNYXRoLmZsb29yKGNsaS5nZXRYTG9jYXRpb24oY2hhcnREYXRhLmRhdGFQb2ludHNbaV1bMF0pKSAtIDI1ICsgXCJweFwiO1xuICAgIH1cbiAgfVxuXG4gIC8vIFBsYWNlIExlZ2VuZCBNYXJrZXJcbiAgZG9jdW1lbnQucXVlcnlTZWxlY3RvcignLm9tLScgKyBjaGFydERhdGEuYnVveUlEICsgJy1sZWdlbmQtbWFya2VyJykuc3R5bGUudG9wID0gTWF0aC5mbG9vcihjbGkuZ2V0Qm91bmRpbmdCb3goXCJsZWdlbmRlbnRyeSMxXCIpLnRvcCkgLSA4ICsgXCJweFwiO1xuICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcub20tJyArIGNoYXJ0RGF0YS5idW95SUQgKyAnLWxlZ2VuZC1tYXJrZXInKS5zdHlsZS5sZWZ0ID0gTWF0aC5mbG9vcihjbGkuZ2V0Qm91bmRpbmdCb3goXCJsZWdlbmRlbnRyeSMxXCIpLmxlZnQpICsgNCArIFwicHhcIjtcbn0iLCJtb2R1bGUuZXhwb3J0cyA9IGpRdWVyeTsiXSwic291cmNlUm9vdCI6IiJ9