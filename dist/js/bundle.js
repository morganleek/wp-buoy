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

/***/ "./node_modules/chartjs/chart.js":
/*!***************************************!*\
  !*** ./node_modules/chartjs/chart.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/*global module:true*/


Math.log2 = Math.log2 || function (x) {
  return Math.log(x) / Math.LN2;
};

Math.log10 = Math.log10 || function (x) {
  return Math.log(x) / Math.LN10;
};

(function () {
  var Helpers = {
    avg: function (arr) {
      var v = 0;

      for (var index = 0; index < arr.length; ++index) {
        v += arr[index];
      }

      return v / arr.length;
    },
    min: function (arr) {
      if (arr.length === 0) return 0;
      var v = arr[0];

      for (var index = 1; index < arr.length; ++index) {
        var v2 = arr[index];
        if (Array.isArray(v2)) v2 = Helpers.avg(v2);
        if (v2 < v) v = v2;
      }

      return Math.max(0, v);
    },
    max: function (arr) {
      var v = 0;

      for (var index = 0; index < arr.length; ++index) {
        var v2 = arr[index];
        if (Array.isArray(v2)) v2 = Helpers.avg(v2);
        if (v2 > v) v = v2;
      }

      return Math.max(0, v);
    },
    upperMax: function (arr) {
      var v = 0;

      for (var index = 0; index < arr.length; ++index) {
        var v2 = arr[index];
        if (Array.isArray(v2)) v2 = Helpers.max(v2);
        if (v2 > v) v = v2;
      }

      return Math.max(0, v);
    },
    lowerMin: function (arr) {
      if (arr.length === 0) return 0;
      var v = arr[0] || Infinity;
      if (Array.isArray(v)) v = Helpers.lowerMin(v);

      for (var index = 1; index < arr.length; ++index) {
        var v2 = arr[index];
        if (v2 == null) continue;
        if (Array.isArray(v2)) v2 = Helpers.lowerMin(v2);
        if (v2 < v) v = v2;
      }

      if (isNaN(v) || !isFinite(v)) v = 0;
      return Math.max(0, v);
    },
    niceNumbers: function (range, round) {
      var exponent = Math.floor(Math.log10(range));
      var fraction = range / Math.pow(10, exponent);
      var niceFraction;

      if (round) {
        if (fraction < 1.5) niceFraction = 1;else if (fraction < 3) niceFraction = 2;else if (fraction < 7) niceFraction = 5;else niceFraction = 10;
      } else {
        if (fraction <= 1.0) niceFraction = 1;else if (fraction <= 2) niceFraction = 2;else if (fraction <= 5) niceFraction = 5;else niceFraction = 10;
      }

      return niceFraction * Math.pow(10, exponent);
    },
    getLinearTicks: function (min, max, maxTicks) {
      var range = Helpers.niceNumbers(max - min, false);
      var tickSpacing = Helpers.niceNumbers(range / (maxTicks - 1), true);
      return [Math.floor(min / tickSpacing) * tickSpacing, Math.ceil(max / tickSpacing) * tickSpacing, tickSpacing];
    },
    getFont: function (options) {
      options.style = options.style || 'normal';
      options.variant = options.variant || 'normal';
      options.weight = options.weight || 'lighter';
      options.size = options.size || '12';
      options.family = options.family || 'Arial';
      return [options.style, options.variant, options.weight, options.size + 'px', options.family].join(' ');
    },
    getAxisRatio: function (min, max, value) {
      return (value - min) / (max - min);
    }
  };

  var BarChart = function () {
    function BarChart(ctx, options) {
      this.mouseListeners = [];
      this.currentHint = null;
      this.fillRegions = [];
      this.options = {
        font: 'Helvetica',
        fontWeight: 'normal',
        fontSizeTitle: 24,
        fontSizeAxes: 20,
        fontSizeTicks: 18,
        fontSizeLabels: 18,
        fontDataTags: 18,
        fontSizeLegend: 18,
        fontSizeHint: 18,
        paddingPercentBars: 0.10,
        paddingPercentTicks: 0.15,
        paddingPixelsVertical: 10,
        paddingPixelsHorizontal: 10,
        paddingPixelsTicks: 10,
        maxWidthBars: 0,
        fillColorBackground: 'rgb(255, 255, 255)',
        strokeColorBars: 'rgb(0, 0, 0)',
        fillColorBars: 'rgba(180, 180, 180, 0.25)',
        scaleStyle: 'linear',
        barStyle: 'none',
        stackedBarPadding: 3,
        defaultMaxTick: 0,
        pixelsLegendSquare: 10,
        radiusDot: 5,
        fillColorLegend: 'rgb(230, 230, 230)',
        tickFormatter: null,
        tickFormatterMeasure: null,
        fillRegion: 'normal'
      };
      options = options || {};

      for (var key in this.options) {
        if (options.hasOwnProperty(key)) this.options[key] = options[key];
      }

      this.ctx = ctx;
      this.content = {};
      this.labelPositions = {};
    }

    BarChart.prototype.update = function (content) {
      if (typeof content !== 'object') {
        throw new Error('Collections must be objects.');
      } else if (!(content.hasOwnProperty('labels') && content.hasOwnProperty('data'))) {
        throw new Error('Collection must specify labels and data.');
      } else if (!(Array.isArray(content.labels) && Array.isArray(content.data))) {
        throw new Error('Labels and data must be arrays.');
      } else if (content.labels.length !== content.data.length) {
        throw new Error('Labels and data length must match.');
      }

      content._data_standard_deviation = [];
      content._data_standard_error = [];

      for (var i = 0; i < content.data.length; ++i) {
        var isArr = Array.isArray(content.data[i]);

        if (this.options.scaleStyle === 'log2') {
          if (isArr) {
            for (var i3 = 0; i3 < content.data[i].length; ++i3) content.data[i][i3] = Math.log2(content.data[i][i3]);
          } else content.data[i] = Math.log2(content.data[i]);
        }

        if (isArr) {
          var mean = Helpers.avg(content.data[i]);
          var acc = 0;

          for (var i2 = 0; i2 < content.data[i].length; ++i2) acc += Math.pow(mean - content.data[i][i2], 2);

          acc = Math.sqrt(acc / (content.data[i].length - 1));

          content._data_standard_deviation.push(acc);

          content._data_standard_error.push(acc / Math.sqrt(content.data[i].length));
        } else {
          content._data_standard_deviation.push(0);

          content._data_standard_error.push(0);
        }
      }

      this.content = content;
      this.redraw();
    };

    BarChart.prototype.redraw = function () {
      setTimeout(function () {
        this._draw();
      }.bind(this), 0);
    };

    BarChart.prototype.mousemove = function (x, y) {
      var res = null;

      for (var index = 0; index < this.mouseListeners.length; ++index) {
        if (res = this.mouseListeners[index](x, y)) break;
      }

      if (!res || typeof res !== 'object' || !res.hasOwnProperty('index') || !res.hasOwnProperty('drawIndex')) {
        if (this.currentHint !== null) {
          this.currentHint = null;
          this.redraw();
        }

        return;
      }

      var ch = this.currentHint;

      if (ch == null || ch.index != res.index || ch.drawIndex != res.drawIndex) {
        this.currentHint = res;
        this.redraw();
      }
    };

    BarChart.prototype._draw = function () {
      var labelPositions = {};
      this.mouseListeners = [];
      this.fillRegions = [];
      var options = this.options;
      var ctx = this.ctx,
          content = this.content;
      var width = ctx.canvas.width,
          height = ctx.canvas.height;
      ctx.clearRect(0, 0, width, height);
      ctx.translate(-0.5, -0.5);
      var remainingWidth = width,
          remainingHeight = height;
      var index;

      if (options.fillColorBackground != null) {
        ctx.save();
        ctx.fillStyle = options.fillColorBackground;
        ctx.fillRect(0, 0, width, height);
        ctx.restore();
      }

      var topYPadding = options.paddingPixelsHorizontal;
      remainingHeight -= options.paddingPixelsHorizontal;
      ctx.fillStyle = 'rgb(0, 0, 0)';
      /* Draw title of bar chart */

      if (content.title != null) {
        ctx.save();
        ctx.font = Helpers.getFont({
          weight: options.fontWeight,
          size: options.fontSizeTitle,
          family: options.font
        });
        ctx.textAlign = 'center';
        ctx.fillText(content.title, width / 2, topYPadding + options.fontSizeTitle);
        ctx.restore();
        remainingHeight -= options.fontSizeTitle * 1.25;
        topYPadding += options.fontSizeTitle * 1.25;
      }
      /* Compute required left padding */


      var leftXPadding = options.paddingPixelsVertical;
      remainingWidth -= options.paddingPixelsVertical;
      var leftXDrawYLabel = null;

      if (content.yAxis != null) {
        leftXDrawYLabel = leftXPadding + options.fontSizeAxes * 0.5;
        remainingWidth -= options.fontSizeAxes * 1.25;
        leftXPadding += options.fontSizeAxes * 1.25;
      }

      ctx.save();
      ctx.font = Helpers.getFont({
        weight: options.fontWeight,
        size: options.fontSizeTicks,
        family: options.font
      });
      var maxChartValue, minChartValue;

      if (options.barStyle === 'stacked') {
        maxChartValue = 0;
        minChartValue = Infinity;

        for (var cmIndex = 0; cmIndex < content.data.length; ++cmIndex) {
          var doB;

          if (Array.isArray(doB = content.data[cmIndex])) {
            var tempSum = 0;

            for (var ii2 = 0; ii2 < doB.length; ++ii2) tempSum += doB[ii2];

            maxChartValue = Math.max(maxChartValue, tempSum);
            minChartValue = Math.min(minChartValue, tempSum);
          } else {
            maxChartValue = Math.max(maxChartValue, content.data[cmIndex]);
            minChartValue = Math.min(minChartValue, content.data[cmIndex]);
          }
        }
      } else {
        maxChartValue = Helpers.upperMax(content.data);
        minChartValue = Helpers.lowerMin(content.data);
      }

      if (options.scaleStyle.indexOf('adaptive') === 0) {
        if (options.scaleStyle.indexOf(':') !== -1) {
          var floater = parseFloat(options.scaleStyle.split(/[:]/)[1]);
          minChartValue *= floater;
          maxChartValue *= 1 + (1 - floater) / 2.0;
        }
      } else minChartValue = 0;

      if (options.defaultMaxTick > maxChartValue) maxChartValue = options.defaultMaxTick;

      if (content.bars != null && Array.isArray(content.bars)) {
        for (index = 0; index < content.bars.length; ++index) {
          var cbv = content.bars[index].value;
          if (isNaN(cbv)) continue;
          maxChartValue = Math.max(maxChartValue, cbv);
          minChartValue = Math.min(minChartValue, cbv);
        }
      }

      var maxYAxisTickWidth = options.scaleStyle == 'log2' ? Math.ceil(Math.pow(2, maxChartValue)) : Math.ceil(maxChartValue) + '.00';
      if (options.tickFormatterMeasure != null) maxYAxisTickWidth = options.tickFormatterMeasure;
      maxYAxisTickWidth = ctx.measureText(maxYAxisTickWidth).width;
      maxYAxisTickWidth = Math.ceil(maxYAxisTickWidth) + options.paddingPixelsTicks;
      remainingWidth -= maxYAxisTickWidth;
      leftXPadding += maxYAxisTickWidth;
      ctx.restore();
      var rightXPadding = options.paddingPixelsVertical;
      remainingWidth -= options.paddingPixelsVertical;
      /* Draw legend */

      if (content.legend != null && Array.isArray(content.legend)) {
        ctx.save();
        ctx.font = Helpers.getFont({
          weight: options.fontWeight,
          size: options.fontSizeLegend,
          family: options.font
        });
        var maxLWidth = 0;

        for (var lIndex = 0; lIndex < content.legend.length; ++lIndex) {
          maxLWidth = Math.max(maxLWidth, ctx.measureText(content.legend[lIndex].label).width);
        }

        maxLWidth = Math.ceil(maxLWidth);
        maxLWidth += options.pixelsLegendSquare + 8;
        var legendEntriesPerLine = Math.floor((remainingWidth - options.paddingPixelsHorizontal * 2) / maxLWidth);
        var lLReqHeight = Math.ceil(content.legend.length / legendEntriesPerLine) * options.fontSizeLegend * 1.5;
        remainingHeight -= lLReqHeight;
        bottomYPadding += lLReqHeight;
        ctx.strokeStyle = 'rgb(0, 0, 0)';
        ctx.fillStyle = options.fillColorLegend;
        var bSX, bSY;
        ctx.beginPath();
        ctx.moveTo(bSX = leftXPadding, bSY = topYPadding + remainingHeight);
        ctx.lineTo(bSX + remainingWidth, bSY);
        ctx.lineTo(bSX + remainingWidth, bSY + lLReqHeight);
        ctx.lineTo(bSX, bSY + lLReqHeight);
        ctx.lineTo(bSX, bSY);
        ctx.stroke();
        ctx.fill();

        for (lIndex = 0; lIndex < content.legend.length; ++lIndex) {
          var legLine = Math.floor(lIndex / legendEntriesPerLine);
          var legCol = lIndex % legendEntriesPerLine;
          ctx.fillStyle = content.legend[lIndex].color;
          var boxX = bSX + legCol * maxLWidth + 3,
              boxY = bSY + legLine * options.fontSizeLegend * 1.5 + options.fontSizeLegend * 0.5;
          ctx.beginPath();
          ctx.moveTo(boxX, boxY);
          ctx.lineTo(boxX + options.pixelsLegendSquare, boxY);
          ctx.lineTo(boxX + options.pixelsLegendSquare, boxY + options.pixelsLegendSquare);
          ctx.lineTo(boxX, boxY + options.pixelsLegendSquare);
          ctx.lineTo(boxX, boxY);
          ctx.fill();
          ctx.stroke();
          ctx.textAlign = 'left';
          ctx.fillStyle = 'rgb(0, 0, 0)';
          ctx.fillText(content.legend[lIndex].label, boxX + 3 + options.pixelsLegendSquare, boxY + options.fontSizeLegend * 0.5);
        }

        ctx.restore();
      }
      /* Draw x-axis label of bar chart */


      var bottomYPadding = options.paddingPixelsHorizontal;
      remainingHeight -= options.paddingPixelsHorizontal;

      if (content.xAxis != null) {
        ctx.save();
        ctx.font = Helpers.getFont({
          weight: options.fontWeight,
          size: options.fontSizeAxes,
          family: options.font
        });
        ctx.fillStyle = 'rgb(0, 0, 0)';
        ctx.textAlign = 'center';
        ctx.fillText(content.xAxis, width - remainingWidth + remainingWidth / 2, topYPadding + remainingHeight - bottomYPadding);
        remainingHeight -= options.fontSizeAxes * 1.5;
        bottomYPadding += options.fontSizeAxes * 1.5;
        ctx.restore();
      }

      var widthPerBar = remainingWidth / content.data.length;
      /* Draw x-axis top labels */

      if (content.topLabels != null) {
        ctx.save();
        ctx.textAlign = 'center';
        ctx.font = Helpers.getFont({
          weight: options.fontWeight,
          size: options.fontSizeLabels,
          family: options.font
        });
        remainingHeight -= options.fontSizeLabels * 1.5;
        topYPadding += options.fontSizeLabels * 1.5;

        for (index = 0; index < content.topLabels.length; ++index) {
          ctx.fillText(content.topLabels[index], leftXPadding + index * widthPerBar + widthPerBar / 2, topYPadding - options.fontSizeLabels / 2);
        }

        ctx.restore();
      }
      /* Draw x-axis labels */


      ctx.save();
      var reqWidth = 0;

      if (content.dataTags != null) {
        ctx.font = Helpers.getFont({
          weight: options.fontWeight,
          size: options.fontDataTags,
          family: options.font
        });
        var dataTags = content.dataTags;

        for (index = 0; index < dataTags.length; ++index) {
          if (Array.isArray(dataTags[index])) {
            for (var index2 = 0; index2 < dataTags[index].length; ++index2) {
              reqWidth = Math.max(reqWidth, Math.ceil(ctx.measureText(dataTags[index][index2]).width + 5));
            }
          } else {
            reqWidth = Math.max(reqWidth, Math.ceil(ctx.measureText(dataTags[index]).width + 5));
          }
        }
      }

      ctx.font = Helpers.getFont({
        weight: options.fontWeight,
        size: options.fontSizeLabels,
        family: options.font
      });
      var computedBarPadding = Math.floor(widthPerBar * options.paddingPercentBars / 2);
      var wwh = widthPerBar - computedBarPadding * 2;

      if (wwh < reqWidth) {
        computedBarPadding -= Math.ceil((reqWidth - wwh) / 2);
        computedBarPadding = Math.max(0, computedBarPadding);
      } else if (options.maxWidthBars > 0 && wwh > options.maxWidthBars) {
        computedBarPadding = Math.floor((widthPerBar - options.maxWidthBars) / 2);
      }

      var maxTextWidth = 0,
          maxTextStackSize = 1;

      for (index = 0; index < content.labels.length; ++index) {
        var tLabel = content.labels[index];

        if (Array.isArray(tLabel)) {
          maxTextStackSize = Math.max(maxTextStackSize, tLabel.length);

          for (index2 = 0; index2 < tLabel.length; ++index2) {
            maxTextWidth = Math.max(maxTextWidth, ctx.measureText(tLabel[index2]).width);
          }
        } else maxTextWidth = Math.max(maxTextWidth, ctx.measureText(tLabel).width);
      }

      var xLabelsRotated = false;

      if (maxTextWidth > widthPerBar - computedBarPadding) {
        ctx.textAlign = 'right';
        ctx.rotate(Math.PI * 1.5);
        xLabelsRotated = true;
      } else {
        ctx.textAlign = 'center';
      }

      var lastLabelY = -options.fontSizeLabels;

      for (index = 0; index < content.labels.length; ++index) {
        var cLabel = content.labels[index];
        var x = leftXPadding + index * widthPerBar + widthPerBar / 2,
            y = topYPadding + remainingHeight - options.fontSizeLabels / 2;

        if (xLabelsRotated) {
          y = topYPadding + remainingHeight - maxTextWidth + 5;
          y = [x, x = -y][0];
          if (y < lastLabelY + options.fontSizeLabels) continue;
          lastLabelY = y;
        }

        var yUp = options.fontSizeLabels * (maxTextStackSize - 1);

        if (Array.isArray(cLabel)) {
          if (xLabelsRotated) {
            yUp = options.fontSizeLabels * (cLabel.length - 1.5);
            yUp /= 2;
          }

          for (index2 = 0; index2 < cLabel.length; ++index2) {
            ctx.fillText(cLabel[index2], x, y - yUp);
            yUp -= options.fontSizeLabels;
          }
        } else {
          if (xLabelsRotated) yUp = -options.fontSizeLabels * 0.25;
          ctx.fillText(cLabel, x, y - yUp);
        }
      }

      if (xLabelsRotated) {
        remainingHeight -= maxTextWidth + 5;
        bottomYPadding += maxTextWidth + 5;
      } else {
        var remVal = options.fontSizeLabels * maxTextStackSize;
        remVal += options.fontSizeLabels * 0.5;
        remainingHeight -= remVal;
        bottomYPadding += remVal;
      }

      ctx.restore();
      /* Draw boundaries */

      var boundX1 = leftXPadding,
          boundX2 = leftXPadding + remainingWidth;
      var boundY1 = topYPadding,
          boundY2 = topYPadding + remainingHeight;

      for (index = 0; index < content.labels.length; ++index) labelPositions[index] = {
        xStart: leftXPadding + index * widthPerBar,
        xEnd: leftXPadding + (1 + index) * widthPerBar,
        yStart: boundY1,
        yEnd: boundY2
      };

      ctx.save();
      ctx.strokeStyle = 'rgb(0, 0, 0)';
      ctx.beginPath();

      if (content.topLabels != null) {
        ctx.moveTo(boundX2, boundY1);
        ctx.lineTo(boundX1, boundY1);
      } else {
        ctx.moveTo(boundX1, boundY1);
      }

      ctx.lineTo(boundX1, boundY2);
      ctx.lineTo(boundX2, boundY2);
      if (content.topLabels != null) ctx.lineTo(leftXPadding + remainingWidth, topYPadding);
      ctx.stroke();
      ctx.restore();
      /* Draw top label */

      if (content.topLabel != null) {
        ctx.save();
        ctx.textAlign = 'right';
        ctx.font = Helpers.getFont({
          weight: options.fontWeight,
          size: options.fontSizeLabels,
          family: options.font
        });
        ctx.fillText(content.topLabel, leftXPadding - 3, topYPadding - options.fontSizeLabels / 2);
        ctx.restore();
      }
      /* Draw y-axis label of bar chart */


      if (content.yAxis != null) {
        ctx.save();
        ctx.rotate(Math.PI * 1.5);
        ctx.font = Helpers.getFont({
          weight: options.fontWeight,
          size: options.fontSizeAxes,
          family: options.font
        });
        ctx.fillStyle = 'rgb(0, 0, 0)';
        ctx.textAlign = 'center';
        ctx.fillText(content.yAxis, -(topYPadding + remainingHeight / 2), leftXDrawYLabel);
        ctx.restore();
      }
      /* Draw y-axis labels */


      ctx.save();
      ctx.fillStyle = 'rgb(0, 0, 0)';
      ctx.strokeStyle = 'rgba(0, 0, 0, 0.20)';
      ctx.font = Helpers.getFont({
        weight: options.fontWeight,
        size: options.fontSizeTicks,
        family: options.font
      });
      ctx.textAlign = 'right';
      var tickMeta = Helpers.getLinearTicks(0, maxChartValue, Math.max(2, remainingHeight / (options.fontSizeTicks * (1 + options.paddingPercentTicks))));
      var alpha = maxChartValue / options.fontSizeTicks;
      maxChartValue = tickMeta[1];
      if (maxChartValue > 1) maxChartValue += Math.ceil(alpha);else maxChartValue += alpha;
      var ticks = [];

      while (tickMeta[0] <= tickMeta[1]) {
        ticks.push(tickMeta[0]);
        tickMeta[0] += tickMeta[2];
      }

      for (index = 0; index < ticks.length; ++index) {
        var tickHeight = Math.round(remainingHeight * Helpers.getAxisRatio(minChartValue, maxChartValue, ticks[index]));
        if (tickHeight < 0) continue;
        if (options.scaleStyle == 'log2' && ticks[index] !== 0) ticks[index] = Math.round(Math.pow(2, ticks[index]));else ticks[index] = Math.floor(ticks[index] * 100) / 100;

        if (options.tickFormatter != null && typeof options.tickFormatter === 'function') {
          ctx.fillText(options.tickFormatter(ticks[index]).toString(), leftXPadding - options.paddingPixelsTicks, topYPadding + remainingHeight - tickHeight);
        } else {
          ctx.fillText(ticks[index].toString(), leftXPadding - options.paddingPixelsTicks, topYPadding + remainingHeight - tickHeight);
        }

        if (index == 0) continue;
        ctx.beginPath();
        ctx.moveTo(leftXPadding, topYPadding + remainingHeight - tickHeight);
        ctx.lineTo(leftXPadding + remainingWidth, topYPadding + remainingHeight - tickHeight);
        ctx.stroke();
      }

      ctx.restore();

      if (content.bars != null && Array.isArray(content.bars)) {
        ctx.save();

        for (index = 0; index < content.bars.length; ++index) {
          var cBar = content.bars[index];
          if (cBar.value > maxChartValue) continue;
          var renderBarY = topYPadding + remainingHeight - Math.round(remainingHeight * Helpers.getAxisRatio(minChartValue, maxChartValue, cBar.value));
          ctx.strokeStyle = cBar.style;
          ctx.fillStyle = cBar.style;
          ctx.beginPath();
          ctx.moveTo(boundX1, renderBarY);
          ctx.lineTo(boundX2, renderBarY);
          ctx.stroke();
          ctx.fill();
        }

        ctx.restore();
      }
      /* Draw bars */


      ctx.save();
      var lastData = null;

      for (index = 0; index < content.data.length; ++index) {
        var fillColorForIndex = null;
        var strokeColorForIndex = null;

        if (content.fillColor != null) {
          if (Array.isArray(content.fillColor)) fillColorForIndex = ctx.fillStyle = content.fillColor[index];else ctx.fillStyle = content.fillColor;
        } else ctx.fillStyle = options.fillColorBars;

        if (content.strokeColor != null) {
          if (Array.isArray(content.strokeColor)) strokeColorForIndex = ctx.strokeStyle = content.strokeColor[index];else ctx.strokeStyle = content.strokeColor;
        } else ctx.strokeStyle = options.strokeColorBars;

        var v = content.data[index];
        var vIsArr = Array.isArray(v);
        var renderStartX = leftXPadding + widthPerBar * index;

        if (vIsArr && options.barStyle === 'stacked') {
          var runningValue = 0,
              lastHeight = 0;

          for (var drawIndex = 0; drawIndex < v.length; ++drawIndex) {
            if (fillColorForIndex != null && Array.isArray(fillColorForIndex)) {
              ctx.fillStyle = fillColorForIndex[drawIndex] || options.fillColorBars;
            }

            if (strokeColorForIndex != null && Array.isArray(strokeColorForIndex)) {
              ctx.strokeStyle = strokeColorForIndex[drawIndex] || options.strokeColorBars;
            }

            runningValue += v[drawIndex];
            var renderBarHeight = Math.floor(remainingHeight * Helpers.getAxisRatio(minChartValue, maxChartValue, runningValue));
            var renderUpToY = topYPadding + remainingHeight - renderBarHeight;

            if (Math.abs(renderBarHeight - lastHeight) < options.stackedBarPadding + 2) {
              lastHeight = renderBarHeight;
              continue;
            }

            var barPadP = drawIndex > 0 ? options.stackedBarPadding : 0;
            var tSX, tSY;
            var tEX, tEY;
            ctx.beginPath();
            ctx.moveTo(tSX = renderStartX + computedBarPadding, tSY = topYPadding + remainingHeight - lastHeight - barPadP);
            ctx.lineTo(renderStartX + computedBarPadding, renderUpToY);
            ctx.lineTo(tEX = renderStartX + (widthPerBar - 1) - computedBarPadding, tEY = renderUpToY);
            ctx.lineTo(renderStartX + (widthPerBar - 1) - computedBarPadding, topYPadding + remainingHeight - lastHeight - barPadP);
            if (drawIndex > 0) ctx.lineTo(tSX, tSY);
            ctx.stroke();
            ctx.fill();
            var hint;

            if (content.hints != null && content.hints[index] != null && (hint = content.hints[index][drawIndex]) != null) {
              this.mouseListeners.push(function (index, drawIndex, hint, sx, sy, ex, ey, x, y) {
                var minX = Math.min(sx, ex),
                    maxX = Math.max(sx, ex);
                var minY = Math.min(sy, ey),
                    maxY = Math.max(sy, ey);
                if (x < minX || x > maxX || y < minY || y > maxY) return null;
                return {
                  index: index,
                  drawIndex: drawIndex,
                  rect: {
                    left: minX,
                    right: maxX,
                    top: minY,
                    bottom: maxY
                  },
                  text: hint.split('\n')
                };
              }.bind(this, index, drawIndex, hint, tSX, tSY, tEX, tEY));
            }

            var tagText;

            if (tSY - renderUpToY > options.fontDataTags * 1.25 && content.dataTags != null && (tagText = content.dataTags[index]) != null && (tagText = tagText[drawIndex]) != null) {
              var oFS = ctx.fillStyle;
              ctx.fillStyle = 'rgb(0, 0, 0)';
              ctx.font = Helpers.getFont({
                weight: options.fontWeight,
                size: options.fontDataTags,
                family: options.font
              });
              ctx.textAlign = 'center';
              ctx.fillText(tagText, renderStartX + widthPerBar / 2, tSY - options.fontDataTags * 0.25);
              ctx.fillStyle = oFS;
            }

            lastHeight = renderBarHeight;
          }

          if (content.barTooltips != null) {
            ctx.fillStyle = 'rgb(0, 0, 0)';
            ctx.font = Helpers.getFont({
              weight: options.fontWeight,
              size: options.fontSizeLabels,
              family: options.font
            });
            ctx.textAlign = 'center';
            ctx.fillText(content.barTooltips[index] || '', renderStartX + widthPerBar / 2, renderUpToY - 3);
          }
        } else if (options.barStyle === 'line') {
          if (vIsArr) {
            var rbx = renderStartX + widthPerBar / 2;
            var lDu;

            if (options.fillRegion === 'background') {
              lDu = lastData;
              if (Array.isArray(lDu)) lDu = lDu[0];

              if (lDu != null) {
                var sFS = ctx.fillStyle;
                ctx.fillStyle = lDu.color;
                ctx.fillRect(lDu.x, boundY1, rbx - lDu.x, boundY2 - boundY1);
                ctx.fillStyle = sFS;
              }
            }

            var nLData = [];

            for (var drawIndex = 0; drawIndex < v.length; ++drawIndex) {
              var renderBarHeight3 = Math.round(remainingHeight * Helpers.getAxisRatio(minChartValue, maxChartValue, v[drawIndex]));
              var renderUpToY3 = topYPadding + remainingHeight - renderBarHeight3;
              var rby = renderUpToY3;

              if (lastData != null) {
                var tLX, tLY;

                if (Array.isArray(lastData)) {
                  tLX = (lastData[drawIndex] || {}).x;
                  tLY = (lastData[drawIndex] || {}).y;
                } else {
                  tLX = lastData.x;
                  tLY = lastData.y;
                }

                if (tLX && tLY) {
                  if (Array.isArray(strokeColorForIndex)) {
                    ctx.strokeStyle = strokeColorForIndex[drawIndex] || options.strokeColorBars;
                  } else ctx.strokeStyle = strokeColorForIndex || 'rgb(0, 0, 0)';

                  ctx.beginPath();
                  ctx.moveTo(tLX, tLY);
                  ctx.lineTo(rbx, rby);
                  ctx.stroke();
                }
              }

              if (Array.isArray(fillColorForIndex)) {
                ctx.fillStyle = fillColorForIndex[drawIndex] || options.fillColorBars;
              }

              if (Array.isArray(strokeColorForIndex)) {
                ctx.strokeStyle = strokeColorForIndex[drawIndex] || options.strokeColorBars;
              }

              ctx.beginPath();
              ctx.arc(rbx, rby, options.radiusDot, 0, 2 * Math.PI);
              ctx.stroke();
              ctx.fill();
              nLData[drawIndex] = {
                x: rbx,
                y: rby,
                color: ctx.fillStyle
              };
            }

            lastData = nLData;
            if (lDu != null && lDu.color != lastData[0].color) this.fillRegions.push({
              x: lastData[0].x,
              y: lastData[0].y,
              prev: lDu.color,
              next: lastData[0].color
            });

            if (content.balls != null && Array.isArray(content.balls) && index < content.balls.length) {
              var ball = content.balls[index];

              if (ball != null) {
                ctx.beginPath();
                ctx.fillStyle = ball.fill;
                ctx.strokeStyle = ball.stroke;
                ctx.arc(rbx, topYPadding + remainingHeight - remainingHeight * Helpers.getAxisRatio(minChartValue, maxChartValue, minChartValue + ball.value), ball.radius, 0, 2 * Math.PI);
                ctx.stroke();
                ctx.fill();
              }
            }
          } else {
            var renderBarHeight3 = Math.round(remainingHeight * Helpers.getAxisRatio(minChartValue, maxChartValue, v));
            var renderUpToY3 = topYPadding + remainingHeight - renderBarHeight3;
            var rbx = renderStartX + widthPerBar / 2,
                rby = renderUpToY3;
            var lDu;

            if (options.fillRegion === 'background') {
              if (lastData != null) {
                lDu = lastData;
                if (Array.isArray(lDu)) lDu = lDu[0];
                var sFS = ctx.fillStyle;
                ctx.fillStyle = lDu.color;
                ctx.fillRect(lDu.x, boundY1, rbx - lDu.x, boundY2 - boundY1);
                ctx.fillStyle = sFS;
              }
            }

            ctx.beginPath();
            ctx.arc(rbx, rby, options.radiusDot, 0, 2 * Math.PI);
            ctx.stroke();
            ctx.fill();

            if (lastData != null) {
              if (Array.isArray(lastData)) {
                var tLX, tLY;

                for (var key in lastData) {
                  if (!lastData.hasOwnProperty(key)) continue;
                  tLX = lastData[key].x;
                  tLY = lastData[key].y;

                  if (tLX && tLY) {
                    ctx.strokeStyle = strokeColorForIndex || 'rgb(0, 0, 0)';
                    ctx.beginPath();
                    ctx.moveTo(tLX, tLY);
                    ctx.lineTo(rbx, rby);
                    ctx.stroke();
                  }
                }
              } else {
                var tLX = lastData.x,
                    tLY = lastData.y;

                if (tLX && tLY) {
                  ctx.strokeStyle = strokeColorForIndex || 'rgb(0, 0, 0)';
                  ctx.beginPath();
                  ctx.moveTo(tLX, tLY);
                  ctx.lineTo(rbx, rby);
                  ctx.stroke();
                }
              }
            }

            lastData = {
              x: rbx,
              y: rby,
              color: ctx.fillStyle
            };
            if (lDu != null && lDu.color != lastData.color) this.fillRegions.push({
              x: lastData.x,
              y: lastData.y,
              prev: lDu.color,
              next: lastData.color
            });

            if (content.balls != null && Array.isArray(content.balls) && index < content.balls.length) {
              var ball = content.balls[index];

              if (ball != null) {
                ctx.beginPath();
                ctx.fillStyle = ball.fill;
                ctx.strokeStyle = ball.stroke;
                ctx.arc(rbx, topYPadding + remainingHeight - remainingHeight * Helpers.getAxisRatio(minChartValue, maxChartValue, minChartValue + ball.value), ball.radius, 0, 2 * Math.PI);
                ctx.stroke();
                ctx.fill();
              }
            }
          }

          var hint;

          if (content.hints != null && (hint = content.hints[index]) != null) {
            this.mouseListeners.push(function (index, hint, sx, sy, ex, ey, x, y) {
              var minX = Math.min(sx, ex),
                  maxX = Math.max(sx, ex);
              var minY = Math.min(sy, ey),
                  maxY = Math.max(sy, ey);
              if (x < minX || x > maxX || y < minY || y > maxY) return null;
              return {
                index: index,
                drawIndex: drawIndex,
                rect: {
                  left: minX,
                  right: maxX,
                  top: minY,
                  bottom: maxY
                },
                text: hint.split('\n')
              };
            }.bind(this, index, hint, rbx - 1, topYPadding, rbx + 1, topYPadding + remainingHeight));
          }
        } else {
          if (vIsArr) v = Helpers.avg(v);
          var renderBarHeight2 = Math.round(remainingHeight * Helpers.getAxisRatio(minChartValue, maxChartValue, v));
          var renderUpToY2 = topYPadding + remainingHeight - renderBarHeight2;
          ctx.beginPath();
          ctx.moveTo(renderStartX + computedBarPadding, topYPadding + remainingHeight);
          ctx.lineTo(renderStartX + computedBarPadding, renderUpToY2);
          ctx.lineTo(renderStartX + (widthPerBar - 1) - computedBarPadding, renderUpToY2);
          ctx.lineTo(renderStartX + (widthPerBar - 1) - computedBarPadding, topYPadding + remainingHeight);
          ctx.stroke();
          ctx.fill();

          if (options.barStyle === 'error') {
            var val;

            if ((val = content._data_standard_error[index]) != 0) {
              var renderBarError = Math.round(remainingHeight * Helpers.getAxisRatio(minChartValue, maxChartValue, val));
              ctx.beginPath();
              var wiskerWidth = Math.round((widthPerBar - computedBarPadding * 2) / 8);
              var x_ = leftXPadding + widthPerBar * index + widthPerBar / 2;
              ctx.moveTo(x_ - wiskerWidth, renderUpToY2 + renderBarError);
              ctx.lineTo(x_ + wiskerWidth, renderUpToY2 + renderBarError);
              ctx.moveTo(x_, renderUpToY2 + renderBarError);
              ctx.lineTo(x_, renderUpToY2 - renderBarError);
              ctx.moveTo(x_ - wiskerWidth, renderUpToY2 - renderBarError);
              ctx.lineTo(x_ + wiskerWidth, renderUpToY2 - renderBarError);
              ctx.stroke();
            }
          }

          if (content.barTooltips != null) {
            ctx.fillStyle = 'rgb(0, 0, 0)';
            ctx.font = Helpers.getFont({
              weight: options.fontWeight,
              size: options.fontSizeLabels,
              family: options.font
            });
            ctx.textAlign = 'center';
            ctx.fillText(content.barTooltips[index] || '', renderStartX + widthPerBar / 2, renderUpToY2 - 3);
          }
        }
      }

      ctx.restore();

      if (this.currentHint != null) {
        ctx.save();
        var hRect = this.currentHint.rect,
            hints = this.currentHint.text;
        ctx.fillStyle = 'rgb(0, 0, 0)';
        ctx.font = Helpers.getFont({
          weight: options.fontWeight,
          size: options.fontSizeHint,
          family: options.font
        });
        ctx.textAlign = 'left';
        var boxWidth = 0;

        for (index = 0; index < hints.length; ++index) {
          boxWidth = Math.max(boxWidth, Math.ceil(ctx.measureText(hints[index]).width));
        }

        var boxWidthPadding = 5;
        var lineHeight = options.fontSizeHint * 1.5;
        var boxHeight = hints.length * lineHeight;
        var drawX = hRect.right + 10,
            drawY = (hRect.top + hRect.bottom) / 2;
        boxWidth += boxWidthPadding * 2;

        if (drawX + boxWidth > width) {
          drawX = hRect.left - boxWidth - 10;
        }

        if (drawY - boxHeight / 2 < 0) {
          drawY = Math.ceil(boxHeight / 2) + 1;
        } else if (drawY + boxHeight / 2 > height) {
          drawY = height - boxHeight / 2 - 1;
        }

        ctx.clearRect(drawX, drawY - boxHeight / 2, boxWidth, boxHeight);
        ctx.beginPath();
        ctx.rect(drawX, drawY - boxHeight / 2, boxWidth, boxHeight);
        ctx.stroke();

        for (index = 0; index < hints.length; ++index) {
          ctx.fillText(hints[index], drawX + boxWidthPadding, drawY - boxHeight / 2 + options.fontSizeHint + index * lineHeight);
        }

        ctx.restore();
      }

      ctx.translate(0.5, 0.5);
      this.labelPositions = labelPositions;
    };

    return BarChart;
  }();

  if ( true && typeof module.exports !== 'undefined') {
    module.exports = BarChart;
  } else {
    window.BarChart = BarChart;
  }
})();

/***/ }),

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
/* harmony import */ var _charts_charts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./charts/charts */ "./src/js/charts/charts.js");


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

/***/ "./src/js/charts/charts.js":
/*!*********************************!*\
  !*** ./src/js/charts/charts.js ***!
  \*********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_chartjs__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../node_modules/chartjs */ "./node_modules/chartjs/chart.js");
/* harmony import */ var _node_modules_chartjs__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_chartjs__WEBPACK_IMPORTED_MODULE_1__);


jquery__WEBPACK_IMPORTED_MODULE_0___default()(function () {
  var charts = document.getElementsByClassName('chart-js-layout');

  if (charts.length > 0) {
    var _loop = function _loop(i) {
      var buoyID = charts[i].dataset['buoy']; // Ajax

      var init = {
        method: 'POST'
      };
      var params = {
        action: 'uwa_datawell_wave_points_json',
        buoy_id: buoyID,
        wave_from: '2020-10-04+22:00:00',
        wave_until: '2020-10-01+22:00:00',
        time_adjustment: '+8'
      };
      var paramsString = Object.keys(params).map(function (key) {
        return key + '=' + params[key];
      }).join('&');
      fetch(ajax_object.ajax_url + '?' + paramsString, init).then(function (response) {
        return response.json();
      }).then(function (waves) {// // text is the response body
        // console.log('Text');
        // console.log(text);
      })["catch"](function (e) {
        // error in e.message
        console.log(e);
      });
    };

    for (var i = 0; i < charts.length; i++) {
      _loop(i);
    }
  }
});

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vbm9kZV9tb2R1bGVzL2NoYXJ0anMvY2hhcnQuanMiLCJ3ZWJwYWNrOi8vLy4vbm9kZV9tb2R1bGVzL2dvb2dsZS1jaGFydHMvZGlzdC9nb29nbGVDaGFydHMuZXNtLmpzIiwid2VicGFjazovLy8uL3NyYy9qcy9idW5kbGUuanMiLCJ3ZWJwYWNrOi8vLy4vc3JjL2pzL2NoYXJ0cy9jaGFydHMuanMiLCJ3ZWJwYWNrOi8vL2V4dGVybmFsIFwialF1ZXJ5XCIiXSwibmFtZXMiOlsiTWF0aCIsImxvZzIiLCJ4IiwibG9nIiwiTE4yIiwibG9nMTAiLCJMTjEwIiwiSGVscGVycyIsImF2ZyIsImFyciIsInYiLCJpbmRleCIsImxlbmd0aCIsIm1pbiIsInYyIiwiQXJyYXkiLCJpc0FycmF5IiwibWF4IiwidXBwZXJNYXgiLCJsb3dlck1pbiIsIkluZmluaXR5IiwiaXNOYU4iLCJpc0Zpbml0ZSIsIm5pY2VOdW1iZXJzIiwicmFuZ2UiLCJyb3VuZCIsImV4cG9uZW50IiwiZmxvb3IiLCJmcmFjdGlvbiIsInBvdyIsIm5pY2VGcmFjdGlvbiIsImdldExpbmVhclRpY2tzIiwibWF4VGlja3MiLCJ0aWNrU3BhY2luZyIsImNlaWwiLCJnZXRGb250Iiwib3B0aW9ucyIsInN0eWxlIiwidmFyaWFudCIsIndlaWdodCIsInNpemUiLCJmYW1pbHkiLCJqb2luIiwiZ2V0QXhpc1JhdGlvIiwidmFsdWUiLCJCYXJDaGFydCIsImN0eCIsIm1vdXNlTGlzdGVuZXJzIiwiY3VycmVudEhpbnQiLCJmaWxsUmVnaW9ucyIsImZvbnQiLCJmb250V2VpZ2h0IiwiZm9udFNpemVUaXRsZSIsImZvbnRTaXplQXhlcyIsImZvbnRTaXplVGlja3MiLCJmb250U2l6ZUxhYmVscyIsImZvbnREYXRhVGFncyIsImZvbnRTaXplTGVnZW5kIiwiZm9udFNpemVIaW50IiwicGFkZGluZ1BlcmNlbnRCYXJzIiwicGFkZGluZ1BlcmNlbnRUaWNrcyIsInBhZGRpbmdQaXhlbHNWZXJ0aWNhbCIsInBhZGRpbmdQaXhlbHNIb3Jpem9udGFsIiwicGFkZGluZ1BpeGVsc1RpY2tzIiwibWF4V2lkdGhCYXJzIiwiZmlsbENvbG9yQmFja2dyb3VuZCIsInN0cm9rZUNvbG9yQmFycyIsImZpbGxDb2xvckJhcnMiLCJzY2FsZVN0eWxlIiwiYmFyU3R5bGUiLCJzdGFja2VkQmFyUGFkZGluZyIsImRlZmF1bHRNYXhUaWNrIiwicGl4ZWxzTGVnZW5kU3F1YXJlIiwicmFkaXVzRG90IiwiZmlsbENvbG9yTGVnZW5kIiwidGlja0Zvcm1hdHRlciIsInRpY2tGb3JtYXR0ZXJNZWFzdXJlIiwiZmlsbFJlZ2lvbiIsImtleSIsImhhc093blByb3BlcnR5IiwiY29udGVudCIsImxhYmVsUG9zaXRpb25zIiwicHJvdG90eXBlIiwidXBkYXRlIiwiRXJyb3IiLCJsYWJlbHMiLCJkYXRhIiwiX2RhdGFfc3RhbmRhcmRfZGV2aWF0aW9uIiwiX2RhdGFfc3RhbmRhcmRfZXJyb3IiLCJpIiwiaXNBcnIiLCJpMyIsIm1lYW4iLCJhY2MiLCJpMiIsInNxcnQiLCJwdXNoIiwicmVkcmF3Iiwic2V0VGltZW91dCIsIl9kcmF3IiwiYmluZCIsIm1vdXNlbW92ZSIsInkiLCJyZXMiLCJjaCIsImRyYXdJbmRleCIsIndpZHRoIiwiY2FudmFzIiwiaGVpZ2h0IiwiY2xlYXJSZWN0IiwidHJhbnNsYXRlIiwicmVtYWluaW5nV2lkdGgiLCJyZW1haW5pbmdIZWlnaHQiLCJzYXZlIiwiZmlsbFN0eWxlIiwiZmlsbFJlY3QiLCJyZXN0b3JlIiwidG9wWVBhZGRpbmciLCJ0aXRsZSIsInRleHRBbGlnbiIsImZpbGxUZXh0IiwibGVmdFhQYWRkaW5nIiwibGVmdFhEcmF3WUxhYmVsIiwieUF4aXMiLCJtYXhDaGFydFZhbHVlIiwibWluQ2hhcnRWYWx1ZSIsImNtSW5kZXgiLCJkb0IiLCJ0ZW1wU3VtIiwiaWkyIiwiaW5kZXhPZiIsImZsb2F0ZXIiLCJwYXJzZUZsb2F0Iiwic3BsaXQiLCJiYXJzIiwiY2J2IiwibWF4WUF4aXNUaWNrV2lkdGgiLCJtZWFzdXJlVGV4dCIsInJpZ2h0WFBhZGRpbmciLCJsZWdlbmQiLCJtYXhMV2lkdGgiLCJsSW5kZXgiLCJsYWJlbCIsImxlZ2VuZEVudHJpZXNQZXJMaW5lIiwibExSZXFIZWlnaHQiLCJib3R0b21ZUGFkZGluZyIsInN0cm9rZVN0eWxlIiwiYlNYIiwiYlNZIiwiYmVnaW5QYXRoIiwibW92ZVRvIiwibGluZVRvIiwic3Ryb2tlIiwiZmlsbCIsImxlZ0xpbmUiLCJsZWdDb2wiLCJjb2xvciIsImJveFgiLCJib3hZIiwieEF4aXMiLCJ3aWR0aFBlckJhciIsInRvcExhYmVscyIsInJlcVdpZHRoIiwiZGF0YVRhZ3MiLCJpbmRleDIiLCJjb21wdXRlZEJhclBhZGRpbmciLCJ3d2giLCJtYXhUZXh0V2lkdGgiLCJtYXhUZXh0U3RhY2tTaXplIiwidExhYmVsIiwieExhYmVsc1JvdGF0ZWQiLCJyb3RhdGUiLCJQSSIsImxhc3RMYWJlbFkiLCJjTGFiZWwiLCJ5VXAiLCJyZW1WYWwiLCJib3VuZFgxIiwiYm91bmRYMiIsImJvdW5kWTEiLCJib3VuZFkyIiwieFN0YXJ0IiwieEVuZCIsInlTdGFydCIsInlFbmQiLCJ0b3BMYWJlbCIsInRpY2tNZXRhIiwiYWxwaGEiLCJ0aWNrcyIsInRpY2tIZWlnaHQiLCJ0b1N0cmluZyIsImNCYXIiLCJyZW5kZXJCYXJZIiwibGFzdERhdGEiLCJmaWxsQ29sb3JGb3JJbmRleCIsInN0cm9rZUNvbG9yRm9ySW5kZXgiLCJmaWxsQ29sb3IiLCJzdHJva2VDb2xvciIsInZJc0FyciIsInJlbmRlclN0YXJ0WCIsInJ1bm5pbmdWYWx1ZSIsImxhc3RIZWlnaHQiLCJyZW5kZXJCYXJIZWlnaHQiLCJyZW5kZXJVcFRvWSIsImFicyIsImJhclBhZFAiLCJ0U1giLCJ0U1kiLCJ0RVgiLCJ0RVkiLCJoaW50IiwiaGludHMiLCJzeCIsInN5IiwiZXgiLCJleSIsIm1pblgiLCJtYXhYIiwibWluWSIsIm1heFkiLCJyZWN0IiwibGVmdCIsInJpZ2h0IiwidG9wIiwiYm90dG9tIiwidGV4dCIsInRhZ1RleHQiLCJvRlMiLCJiYXJUb29sdGlwcyIsInJieCIsImxEdSIsInNGUyIsIm5MRGF0YSIsInJlbmRlckJhckhlaWdodDMiLCJyZW5kZXJVcFRvWTMiLCJyYnkiLCJ0TFgiLCJ0TFkiLCJhcmMiLCJwcmV2IiwibmV4dCIsImJhbGxzIiwiYmFsbCIsInJhZGl1cyIsInJlbmRlckJhckhlaWdodDIiLCJyZW5kZXJVcFRvWTIiLCJ2YWwiLCJyZW5kZXJCYXJFcnJvciIsIndpc2tlcldpZHRoIiwieF8iLCJoUmVjdCIsImJveFdpZHRoIiwiYm94V2lkdGhQYWRkaW5nIiwibGluZUhlaWdodCIsImJveEhlaWdodCIsImRyYXdYIiwiZHJhd1kiLCJtb2R1bGUiLCJleHBvcnRzIiwid2luZG93IiwibG9hZFNjcmlwdCIsIlN5bWJvbCIsImluc3RhbmNlIiwiX2luc3RhbmNlIiwiR29vZ2xlQ2hhcnRzTWFuYWdlciIsImNvbnN0cnVjdG9yIiwicmVzZXQiLCJzY3JpcHRQcm9taXNlIiwiUHJvbWlzZSIsInJlc29sdmUiLCJib2R5IiwiZG9jdW1lbnQiLCJnZXRFbGVtZW50c0J5VGFnTmFtZSIsInNjcmlwdCIsImNyZWF0ZUVsZW1lbnQiLCJ0eXBlIiwib25sb2FkIiwiR29vZ2xlQ2hhcnRzIiwiYXBpIiwiZ29vZ2xlIiwiY2hhcnRzIiwibG9hZCIsInBhY2thZ2VzIiwic2V0T25Mb2FkQ2FsbGJhY2siLCJzcmMiLCJhcHBlbmRDaGlsZCIsImNhbGxiYWNrIiwidGhlbiIsImNvbmZpZyIsIk9iamVjdCIsImNoYXJ0c0FycmF5IiwiJCIsImVhY2giLCIkY2hhcnQiLCIkc3Vycm91bmQiLCJjbG9zZXN0IiwiaWQiLCJidW95SUQiLCJ3YXZlVGlja01heCIsIndhdmVUaWNrcyIsInBlYWtUaWNrTWF4IiwicGVha1RpY2tzIiwiZGF0YVBvaW50cyIsImF0dHIiLCJ1bmRlZmluZWQiLCIkdGlja3NEYXRhIiwiZmluZCIsImV2YWwiLCJKU09OIiwicGFyc2UiLCJvZmZzZXQiLCJEYXRlIiwiZ2V0VGltZXpvbmVPZmZzZXQiLCJ0aW1lTGFiZWwiLCJzbGljZSIsImJhY2tncm91bmRDb2xvciIsInNlcmllcyIsInRhcmdldEF4aXNJbmRleCIsInZpc2libGVJbkxlZ2VuZCIsInZBeGVzIiwidmlld1dpbmRvdyIsImhBeGlzIiwiaW50ZXJ2YWwiLCJncmlkbGluZXMiLCJ1bml0cyIsImRheXMiLCJmb3JtYXQiLCJob3VycyIsIm1pbm9yR3JpZGxpbmVzIiwiZHJhd0NoYXJ0IiwicmVzaXplIiwidmlzdWFsaXphdGlvbiIsIkRhdGFUYWJsZSIsImFkZENvbHVtbiIsInJvbGUiLCJhZGRSb3dzIiwiZGF0YUNoYXJ0IiwiTGluZUNoYXJ0IiwiZ2V0RWxlbWVudEJ5SWQiLCJldmVudHMiLCJhZGRMaXN0ZW5lciIsImRyYXdNYXJrZXJzIiwiZHJhdyIsImNoYXJ0RGF0YSIsImNsaSIsImdldENoYXJ0TGF5b3V0SW50ZXJmYWNlIiwicXVlcnlTZWxlY3RvciIsImdldFlMb2NhdGlvbiIsImdldFhMb2NhdGlvbiIsImdldEJvdW5kaW5nQm94IiwiZ2V0RWxlbWVudHNCeUNsYXNzTmFtZSIsImRhdGFzZXQiLCJpbml0IiwibWV0aG9kIiwicGFyYW1zIiwiYWN0aW9uIiwiYnVveV9pZCIsIndhdmVfZnJvbSIsIndhdmVfdW50aWwiLCJ0aW1lX2FkanVzdG1lbnQiLCJwYXJhbXNTdHJpbmciLCJrZXlzIiwibWFwIiwiZmV0Y2giLCJhamF4X29iamVjdCIsImFqYXhfdXJsIiwicmVzcG9uc2UiLCJqc29uIiwid2F2ZXMiLCJlIiwiY29uc29sZSJdLCJtYXBwaW5ncyI6IjtRQUFBO1FBQ0E7O1FBRUE7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBOzs7UUFHQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0EsMENBQTBD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QUM1USxNQUEzQixFQUFtQzZFLENBQUMsRUFBcEMsRUFBeUM7QUFBQSxZQUFoQ0EsQ0FBZ0M7QUFrQ3hDO0FBQ0Q7QUFDRCxDQXZDQSxDQUFELEM7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDSEEsd0IiLCJmaWxlIjoiYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZ2V0dGVyIH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSBmdW5jdGlvbihleHBvcnRzKSB7XG4gXHRcdGlmKHR5cGVvZiBTeW1ib2wgIT09ICd1bmRlZmluZWQnICYmIFN5bWJvbC50b1N0cmluZ1RhZykge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuIFx0XHR9XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCAnX19lc01vZHVsZScsIHsgdmFsdWU6IHRydWUgfSk7XG4gXHR9O1xuXG4gXHQvLyBjcmVhdGUgYSBmYWtlIG5hbWVzcGFjZSBvYmplY3RcbiBcdC8vIG1vZGUgJiAxOiB2YWx1ZSBpcyBhIG1vZHVsZSBpZCwgcmVxdWlyZSBpdFxuIFx0Ly8gbW9kZSAmIDI6IG1lcmdlIGFsbCBwcm9wZXJ0aWVzIG9mIHZhbHVlIGludG8gdGhlIG5zXG4gXHQvLyBtb2RlICYgNDogcmV0dXJuIHZhbHVlIHdoZW4gYWxyZWFkeSBucyBvYmplY3RcbiBcdC8vIG1vZGUgJiA4fDE6IGJlaGF2ZSBsaWtlIHJlcXVpcmVcbiBcdF9fd2VicGFja19yZXF1aXJlX18udCA9IGZ1bmN0aW9uKHZhbHVlLCBtb2RlKSB7XG4gXHRcdGlmKG1vZGUgJiAxKSB2YWx1ZSA9IF9fd2VicGFja19yZXF1aXJlX18odmFsdWUpO1xuIFx0XHRpZihtb2RlICYgOCkgcmV0dXJuIHZhbHVlO1xuIFx0XHRpZigobW9kZSAmIDQpICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgJiYgdmFsdWUuX19lc01vZHVsZSkgcmV0dXJuIHZhbHVlO1xuIFx0XHR2YXIgbnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIobnMpO1xuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkobnMsICdkZWZhdWx0JywgeyBlbnVtZXJhYmxlOiB0cnVlLCB2YWx1ZTogdmFsdWUgfSk7XG4gXHRcdGlmKG1vZGUgJiAyICYmIHR5cGVvZiB2YWx1ZSAhPSAnc3RyaW5nJykgZm9yKHZhciBrZXkgaW4gdmFsdWUpIF9fd2VicGFja19yZXF1aXJlX18uZChucywga2V5LCBmdW5jdGlvbihrZXkpIHsgcmV0dXJuIHZhbHVlW2tleV07IH0uYmluZChudWxsLCBrZXkpKTtcbiBcdFx0cmV0dXJuIG5zO1xuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDApO1xuIiwiLypnbG9iYWwgbW9kdWxlOnRydWUqL1xuJ3VzZSBzdHJpY3QnO1xuXG5NYXRoLmxvZzIgPSBNYXRoLmxvZzIgfHwgZnVuY3Rpb24oeCkge1xuICByZXR1cm4gTWF0aC5sb2coeCkgLyBNYXRoLkxOMjtcbn07XG5cbk1hdGgubG9nMTAgPSBNYXRoLmxvZzEwIHx8IGZ1bmN0aW9uKHgpIHtcbiAgcmV0dXJuIE1hdGgubG9nKHgpIC8gTWF0aC5MTjEwO1xufTtcblxuKGZ1bmN0aW9uKCkge1xuICB2YXIgSGVscGVycyA9IHtcbiAgICBhdmc6IGZ1bmN0aW9uKGFycikge1xuICAgICAgdmFyIHYgPSAwO1xuICAgICAgZm9yICh2YXIgaW5kZXggPSAwOyBpbmRleCA8IGFyci5sZW5ndGg7ICsraW5kZXgpIHtcbiAgICAgICAgdiArPSBhcnJbaW5kZXhdO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHYgLyBhcnIubGVuZ3RoO1xuICAgIH0sXG4gICAgbWluOiBmdW5jdGlvbihhcnIpIHtcbiAgICAgIGlmIChhcnIubGVuZ3RoID09PSAwKSByZXR1cm4gMDtcbiAgICAgIHZhciB2ID0gYXJyWzBdO1xuICAgICAgZm9yICh2YXIgaW5kZXggPSAxOyBpbmRleCA8IGFyci5sZW5ndGg7ICsraW5kZXgpIHtcbiAgICAgICAgdmFyIHYyID0gYXJyW2luZGV4XTtcbiAgICAgICAgaWYgKEFycmF5LmlzQXJyYXkodjIpKSB2MiA9IEhlbHBlcnMuYXZnKHYyKTtcbiAgICAgICAgaWYgKHYyIDwgdikgdiA9IHYyO1xuICAgICAgfVxuICAgICAgcmV0dXJuIE1hdGgubWF4KDAsIHYpO1xuICAgIH0sXG4gICAgbWF4OiBmdW5jdGlvbihhcnIpIHtcbiAgICAgIHZhciB2ID0gMDtcbiAgICAgIGZvciAodmFyIGluZGV4ID0gMDsgaW5kZXggPCBhcnIubGVuZ3RoOyArK2luZGV4KSB7XG4gICAgICAgIHZhciB2MiA9IGFycltpbmRleF07XG4gICAgICAgIGlmIChBcnJheS5pc0FycmF5KHYyKSkgdjIgPSBIZWxwZXJzLmF2Zyh2Mik7XG4gICAgICAgIGlmICh2MiA+IHYpIHYgPSB2MjtcbiAgICAgIH1cbiAgICAgIHJldHVybiBNYXRoLm1heCgwLCB2KTtcbiAgICB9LFxuICAgIHVwcGVyTWF4OiBmdW5jdGlvbihhcnIpIHtcbiAgICAgIHZhciB2ID0gMDtcbiAgICAgIGZvciAodmFyIGluZGV4ID0gMDsgaW5kZXggPCBhcnIubGVuZ3RoOyArK2luZGV4KSB7XG4gICAgICAgIHZhciB2MiA9IGFycltpbmRleF07XG4gICAgICAgIGlmIChBcnJheS5pc0FycmF5KHYyKSkgdjIgPSBIZWxwZXJzLm1heCh2Mik7XG4gICAgICAgIGlmICh2MiA+IHYpIHYgPSB2MjtcbiAgICAgIH1cbiAgICAgIHJldHVybiBNYXRoLm1heCgwLCB2KTtcbiAgICB9LFxuICAgIGxvd2VyTWluOiBmdW5jdGlvbihhcnIpIHtcbiAgICAgIGlmIChhcnIubGVuZ3RoID09PSAwKSByZXR1cm4gMDtcbiAgICAgIHZhciB2ID0gYXJyWzBdIHx8IEluZmluaXR5O1xuICAgICAgaWYgKEFycmF5LmlzQXJyYXkodikpIHYgPSBIZWxwZXJzLmxvd2VyTWluKHYpO1xuICAgICAgZm9yICh2YXIgaW5kZXggPSAxOyBpbmRleCA8IGFyci5sZW5ndGg7ICsraW5kZXgpIHtcbiAgICAgICAgdmFyIHYyID0gYXJyW2luZGV4XTtcbiAgICAgICAgaWYgKHYyID09IG51bGwpIGNvbnRpbnVlO1xuICAgICAgICBpZiAoQXJyYXkuaXNBcnJheSh2MikpIHYyID0gSGVscGVycy5sb3dlck1pbih2Mik7XG4gICAgICAgIGlmICh2MiA8IHYpIHYgPSB2MjtcbiAgICAgIH1cbiAgICAgIGlmIChpc05hTih2KSB8fCAhaXNGaW5pdGUodikpIHYgPSAwO1xuICAgICAgcmV0dXJuIE1hdGgubWF4KDAsIHYpO1xuICAgIH0sXG4gICAgbmljZU51bWJlcnM6IGZ1bmN0aW9uKHJhbmdlLCByb3VuZCkge1xuICAgICAgdmFyIGV4cG9uZW50ID0gTWF0aC5mbG9vcihNYXRoLmxvZzEwKHJhbmdlKSk7XG4gICAgICB2YXIgZnJhY3Rpb24gPSByYW5nZSAvIE1hdGgucG93KDEwLCBleHBvbmVudCk7XG4gICAgICB2YXIgbmljZUZyYWN0aW9uO1xuICAgICAgaWYgKHJvdW5kKSB7XG4gICAgICAgIGlmIChmcmFjdGlvbiA8IDEuNSkgbmljZUZyYWN0aW9uID0gMTtcbiAgICAgICAgZWxzZSBpZiAoZnJhY3Rpb24gPCAzKSBuaWNlRnJhY3Rpb24gPSAyO1xuICAgICAgICBlbHNlIGlmIChmcmFjdGlvbiA8IDcpIG5pY2VGcmFjdGlvbiA9IDU7XG4gICAgICAgIGVsc2UgbmljZUZyYWN0aW9uID0gMTA7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBpZiAoZnJhY3Rpb24gPD0gMS4wKSBuaWNlRnJhY3Rpb24gPSAxO1xuICAgICAgICBlbHNlIGlmIChmcmFjdGlvbiA8PSAyKSBuaWNlRnJhY3Rpb24gPSAyO1xuICAgICAgICBlbHNlIGlmIChmcmFjdGlvbiA8PSA1KSBuaWNlRnJhY3Rpb24gPSA1O1xuICAgICAgICBlbHNlIG5pY2VGcmFjdGlvbiA9IDEwO1xuICAgICAgfVxuICAgICAgcmV0dXJuIG5pY2VGcmFjdGlvbiAqIE1hdGgucG93KDEwLCBleHBvbmVudCk7XG4gICAgfSxcbiAgICBnZXRMaW5lYXJUaWNrczogZnVuY3Rpb24obWluLCBtYXgsIG1heFRpY2tzKSB7XG4gICAgICB2YXIgcmFuZ2UgPSBIZWxwZXJzLm5pY2VOdW1iZXJzKG1heCAtIG1pbiwgZmFsc2UpO1xuICAgICAgdmFyIHRpY2tTcGFjaW5nID0gSGVscGVycy5uaWNlTnVtYmVycyhyYW5nZSAvIChtYXhUaWNrcyAtIDEpLCB0cnVlKTtcbiAgICAgIHJldHVybiBbXG4gICAgICAgIE1hdGguZmxvb3IobWluIC8gdGlja1NwYWNpbmcpICogdGlja1NwYWNpbmcsXG4gICAgICAgIE1hdGguY2VpbChtYXggLyB0aWNrU3BhY2luZykgKiB0aWNrU3BhY2luZyxcbiAgICAgICAgdGlja1NwYWNpbmdcbiAgICAgIF07XG4gICAgfSxcbiAgICBnZXRGb250OiBmdW5jdGlvbihvcHRpb25zKSB7XG4gICAgICBvcHRpb25zLnN0eWxlID0gb3B0aW9ucy5zdHlsZSB8fCAnbm9ybWFsJztcbiAgICAgIG9wdGlvbnMudmFyaWFudCA9IG9wdGlvbnMudmFyaWFudCB8fCAnbm9ybWFsJztcbiAgICAgIG9wdGlvbnMud2VpZ2h0ID0gb3B0aW9ucy53ZWlnaHQgfHwgJ2xpZ2h0ZXInO1xuICAgICAgb3B0aW9ucy5zaXplID0gb3B0aW9ucy5zaXplIHx8ICcxMic7XG4gICAgICBvcHRpb25zLmZhbWlseSA9IG9wdGlvbnMuZmFtaWx5IHx8ICdBcmlhbCc7XG4gICAgICByZXR1cm4gW29wdGlvbnMuc3R5bGUsIG9wdGlvbnMudmFyaWFudCwgb3B0aW9ucy53ZWlnaHQsIG9wdGlvbnMuc2l6ZSArICdweCcsIG9wdGlvbnMuZmFtaWx5XS5qb2luKCcgJyk7XG4gICAgfSxcbiAgICBnZXRBeGlzUmF0aW86IGZ1bmN0aW9uKG1pbiwgbWF4LCB2YWx1ZSkge1xuICAgICAgcmV0dXJuICh2YWx1ZSAtIG1pbikgLyAobWF4IC0gbWluKTtcbiAgICB9XG4gIH07XG5cbiAgdmFyIEJhckNoYXJ0ID0gKGZ1bmN0aW9uKCkge1xuICAgIGZ1bmN0aW9uIEJhckNoYXJ0KGN0eCwgb3B0aW9ucykge1xuICAgICAgdGhpcy5tb3VzZUxpc3RlbmVycyA9IFtdO1xuICAgICAgdGhpcy5jdXJyZW50SGludCA9IG51bGw7XG4gICAgICB0aGlzLmZpbGxSZWdpb25zID0gW11cbiAgICAgIHRoaXMub3B0aW9ucyA9IHtcbiAgICAgICAgZm9udDogJ0hlbHZldGljYScsXG4gICAgICAgIGZvbnRXZWlnaHQ6ICdub3JtYWwnLFxuICAgICAgICBmb250U2l6ZVRpdGxlOiAyNCxcbiAgICAgICAgZm9udFNpemVBeGVzOiAyMCxcbiAgICAgICAgZm9udFNpemVUaWNrczogMTgsXG4gICAgICAgIGZvbnRTaXplTGFiZWxzOiAxOCxcbiAgICAgICAgZm9udERhdGFUYWdzOiAxOCxcbiAgICAgICAgZm9udFNpemVMZWdlbmQ6IDE4LFxuICAgICAgICBmb250U2l6ZUhpbnQ6IDE4LFxuICAgICAgICBwYWRkaW5nUGVyY2VudEJhcnM6IDAuMTAsXG4gICAgICAgIHBhZGRpbmdQZXJjZW50VGlja3M6IDAuMTUsXG4gICAgICAgIHBhZGRpbmdQaXhlbHNWZXJ0aWNhbDogMTAsXG4gICAgICAgIHBhZGRpbmdQaXhlbHNIb3Jpem9udGFsOiAxMCxcbiAgICAgICAgcGFkZGluZ1BpeGVsc1RpY2tzOiAxMCxcbiAgICAgICAgbWF4V2lkdGhCYXJzOiAwLFxuICAgICAgICBmaWxsQ29sb3JCYWNrZ3JvdW5kOiAncmdiKDI1NSwgMjU1LCAyNTUpJyxcbiAgICAgICAgc3Ryb2tlQ29sb3JCYXJzOiAncmdiKDAsIDAsIDApJyxcbiAgICAgICAgZmlsbENvbG9yQmFyczogJ3JnYmEoMTgwLCAxODAsIDE4MCwgMC4yNSknLFxuICAgICAgICBzY2FsZVN0eWxlOiAnbGluZWFyJyxcbiAgICAgICAgYmFyU3R5bGU6ICdub25lJyxcbiAgICAgICAgc3RhY2tlZEJhclBhZGRpbmc6IDMsXG4gICAgICAgIGRlZmF1bHRNYXhUaWNrOiAwLFxuICAgICAgICBwaXhlbHNMZWdlbmRTcXVhcmU6IDEwLFxuICAgICAgICByYWRpdXNEb3Q6IDUsXG4gICAgICAgIGZpbGxDb2xvckxlZ2VuZDogJ3JnYigyMzAsIDIzMCwgMjMwKScsXG4gICAgICAgIHRpY2tGb3JtYXR0ZXI6IG51bGwsXG4gICAgICAgIHRpY2tGb3JtYXR0ZXJNZWFzdXJlOiBudWxsLFxuICAgICAgICBmaWxsUmVnaW9uOiAnbm9ybWFsJ1xuICAgICAgfTtcbiAgICAgIG9wdGlvbnMgPSBvcHRpb25zIHx8IHsgfTtcbiAgICAgIGZvciAodmFyIGtleSBpbiB0aGlzLm9wdGlvbnMpIHtcbiAgICAgICAgaWYgKG9wdGlvbnMuaGFzT3duUHJvcGVydHkoa2V5KSkgdGhpcy5vcHRpb25zW2tleV0gPSBvcHRpb25zW2tleV07XG4gICAgICB9XG4gICAgICB0aGlzLmN0eCA9IGN0eDtcbiAgICAgIHRoaXMuY29udGVudCA9IHsgfTtcbiAgICAgIHRoaXMubGFiZWxQb3NpdGlvbnMgPSB7IH1cbiAgICB9XG5cbiAgICBCYXJDaGFydC5wcm90b3R5cGUudXBkYXRlID0gZnVuY3Rpb24oY29udGVudCkge1xuICAgICAgaWYgKHR5cGVvZiBjb250ZW50ICE9PSAnb2JqZWN0Jykge1xuICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ0NvbGxlY3Rpb25zIG11c3QgYmUgb2JqZWN0cy4nKTtcbiAgICAgIH0gZWxzZSBpZiAoIShjb250ZW50Lmhhc093blByb3BlcnR5KCdsYWJlbHMnKSAmJiBjb250ZW50Lmhhc093blByb3BlcnR5KCdkYXRhJykpKSB7XG4gICAgICAgIHRocm93IG5ldyBFcnJvcignQ29sbGVjdGlvbiBtdXN0IHNwZWNpZnkgbGFiZWxzIGFuZCBkYXRhLicpO1xuICAgICAgfSBlbHNlIGlmICghKEFycmF5LmlzQXJyYXkoY29udGVudC5sYWJlbHMpICYmIEFycmF5LmlzQXJyYXkoY29udGVudC5kYXRhKSkpIHtcbiAgICAgICAgdGhyb3cgbmV3IEVycm9yKCdMYWJlbHMgYW5kIGRhdGEgbXVzdCBiZSBhcnJheXMuJyk7XG4gICAgICB9IGVsc2UgaWYgKGNvbnRlbnQubGFiZWxzLmxlbmd0aCAhPT0gY29udGVudC5kYXRhLmxlbmd0aCkge1xuICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ0xhYmVscyBhbmQgZGF0YSBsZW5ndGggbXVzdCBtYXRjaC4nKTtcbiAgICAgIH1cbiAgICAgIGNvbnRlbnQuX2RhdGFfc3RhbmRhcmRfZGV2aWF0aW9uID0gW107XG4gICAgICBjb250ZW50Ll9kYXRhX3N0YW5kYXJkX2Vycm9yID0gW107XG4gICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGNvbnRlbnQuZGF0YS5sZW5ndGg7ICsraSkge1xuICAgICAgICB2YXIgaXNBcnIgPSBBcnJheS5pc0FycmF5KGNvbnRlbnQuZGF0YVtpXSk7XG4gICAgICAgIGlmICh0aGlzLm9wdGlvbnMuc2NhbGVTdHlsZSA9PT0gJ2xvZzInKSB7XG4gICAgICAgICAgaWYgKGlzQXJyKSB7XG4gICAgICAgICAgICBmb3IgKHZhciBpMyA9IDA7IGkzIDwgY29udGVudC5kYXRhW2ldLmxlbmd0aDsgKytpMykgY29udGVudC5kYXRhW2ldW2kzXSA9IE1hdGgubG9nMihjb250ZW50LmRhdGFbaV1baTNdKTtcbiAgICAgICAgICB9IGVsc2UgY29udGVudC5kYXRhW2ldID0gTWF0aC5sb2cyKGNvbnRlbnQuZGF0YVtpXSk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGlzQXJyKSB7XG4gICAgICAgICAgdmFyIG1lYW4gPSBIZWxwZXJzLmF2Zyhjb250ZW50LmRhdGFbaV0pO1xuICAgICAgICAgIHZhciBhY2MgPSAwO1xuICAgICAgICAgIGZvciAodmFyIGkyID0gMDsgaTIgPCBjb250ZW50LmRhdGFbaV0ubGVuZ3RoOyArK2kyKSBhY2MgKz0gTWF0aC5wb3cobWVhbiAtIGNvbnRlbnQuZGF0YVtpXVtpMl0sIDIpO1xuICAgICAgICAgIGFjYyA9IE1hdGguc3FydChhY2MgLyAoY29udGVudC5kYXRhW2ldLmxlbmd0aCAtIDEpKTtcbiAgICAgICAgICBjb250ZW50Ll9kYXRhX3N0YW5kYXJkX2RldmlhdGlvbi5wdXNoKGFjYyk7XG4gICAgICAgICAgY29udGVudC5fZGF0YV9zdGFuZGFyZF9lcnJvci5wdXNoKGFjYyAvIE1hdGguc3FydChjb250ZW50LmRhdGFbaV0ubGVuZ3RoKSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgY29udGVudC5fZGF0YV9zdGFuZGFyZF9kZXZpYXRpb24ucHVzaCgwKTtcbiAgICAgICAgICBjb250ZW50Ll9kYXRhX3N0YW5kYXJkX2Vycm9yLnB1c2goMCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIHRoaXMuY29udGVudCA9IGNvbnRlbnQ7XG4gICAgICB0aGlzLnJlZHJhdygpO1xuICAgIH07XG5cbiAgICBCYXJDaGFydC5wcm90b3R5cGUucmVkcmF3ID0gZnVuY3Rpb24oKSB7XG4gICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICB0aGlzLl9kcmF3KCk7XG4gICAgICB9LmJpbmQodGhpcyksIDApO1xuICAgIH07XG5cbiAgICBCYXJDaGFydC5wcm90b3R5cGUubW91c2Vtb3ZlID0gZnVuY3Rpb24oeCwgeSkge1xuICAgICAgdmFyIHJlcyA9IG51bGw7XG4gICAgICBmb3IgKHZhciBpbmRleCA9IDA7IGluZGV4IDwgdGhpcy5tb3VzZUxpc3RlbmVycy5sZW5ndGg7ICsraW5kZXgpIHtcbiAgICAgICAgaWYgKChyZXMgPSB0aGlzLm1vdXNlTGlzdGVuZXJzW2luZGV4XSh4LCB5KSkpIGJyZWFrO1xuICAgICAgfVxuICAgICAgaWYgKCFyZXMgfHwgKHR5cGVvZiByZXMpICE9PSAnb2JqZWN0JyB8fCAhcmVzLmhhc093blByb3BlcnR5KCdpbmRleCcpIHx8ICFyZXMuaGFzT3duUHJvcGVydHkoJ2RyYXdJbmRleCcpKSB7XG4gICAgICAgIGlmICh0aGlzLmN1cnJlbnRIaW50ICE9PSBudWxsKSB7XG4gICAgICAgICAgdGhpcy5jdXJyZW50SGludCA9IG51bGw7XG4gICAgICAgICAgdGhpcy5yZWRyYXcoKTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgY2ggPSB0aGlzLmN1cnJlbnRIaW50O1xuICAgICAgaWYgKGNoID09IG51bGwgfHwgY2guaW5kZXggIT0gcmVzLmluZGV4IHx8IGNoLmRyYXdJbmRleCAhPSByZXMuZHJhd0luZGV4KSB7XG4gICAgICAgIHRoaXMuY3VycmVudEhpbnQgPSByZXM7XG4gICAgICAgIHRoaXMucmVkcmF3KCk7XG4gICAgICB9XG4gICAgfTtcblxuICAgIEJhckNoYXJ0LnByb3RvdHlwZS5fZHJhdyA9IGZ1bmN0aW9uKCkge1xuICAgICAgdmFyIGxhYmVsUG9zaXRpb25zID0geyB9XG4gICAgICB0aGlzLm1vdXNlTGlzdGVuZXJzID0gW107XG4gICAgICB0aGlzLmZpbGxSZWdpb25zID0gW107XG5cbiAgICAgIHZhciBvcHRpb25zID0gdGhpcy5vcHRpb25zO1xuICAgICAgdmFyIGN0eCA9IHRoaXMuY3R4LCBjb250ZW50ID0gdGhpcy5jb250ZW50O1xuICAgICAgdmFyIHdpZHRoID0gY3R4LmNhbnZhcy53aWR0aCwgaGVpZ2h0ID0gY3R4LmNhbnZhcy5oZWlnaHQ7XG4gICAgICBjdHguY2xlYXJSZWN0KDAsIDAsIHdpZHRoLCBoZWlnaHQpO1xuICAgICAgY3R4LnRyYW5zbGF0ZSgtMC41LCAtMC41KTtcbiAgICAgIHZhciByZW1haW5pbmdXaWR0aCA9IHdpZHRoLCByZW1haW5pbmdIZWlnaHQgPSBoZWlnaHQ7XG4gICAgICB2YXIgaW5kZXg7XG5cbiAgICAgIGlmIChvcHRpb25zLmZpbGxDb2xvckJhY2tncm91bmQgIT0gbnVsbCkge1xuICAgICAgICBjdHguc2F2ZSgpO1xuICAgICAgICBjdHguZmlsbFN0eWxlID0gb3B0aW9ucy5maWxsQ29sb3JCYWNrZ3JvdW5kO1xuICAgICAgICBjdHguZmlsbFJlY3QoMCwgMCwgd2lkdGgsIGhlaWdodCk7XG4gICAgICAgIGN0eC5yZXN0b3JlKCk7XG4gICAgICB9XG5cbiAgICAgIHZhciB0b3BZUGFkZGluZyA9IG9wdGlvbnMucGFkZGluZ1BpeGVsc0hvcml6b250YWw7XG4gICAgICByZW1haW5pbmdIZWlnaHQgLT0gb3B0aW9ucy5wYWRkaW5nUGl4ZWxzSG9yaXpvbnRhbDtcbiAgICAgIGN0eC5maWxsU3R5bGUgPSAncmdiKDAsIDAsIDApJztcbiAgICAgIC8qIERyYXcgdGl0bGUgb2YgYmFyIGNoYXJ0ICovXG4gICAgICBpZiAoY29udGVudC50aXRsZSAhPSBudWxsKSB7XG4gICAgICAgIGN0eC5zYXZlKCk7XG4gICAgICAgIGN0eC5mb250ID0gSGVscGVycy5nZXRGb250KHsgd2VpZ2h0OiBvcHRpb25zLmZvbnRXZWlnaHQsIHNpemU6IG9wdGlvbnMuZm9udFNpemVUaXRsZSwgZmFtaWx5OiBvcHRpb25zLmZvbnQgfSk7XG4gICAgICAgIGN0eC50ZXh0QWxpZ24gPSAnY2VudGVyJztcbiAgICAgICAgY3R4LmZpbGxUZXh0KGNvbnRlbnQudGl0bGUsIHdpZHRoIC8gMiwgdG9wWVBhZGRpbmcgKyBvcHRpb25zLmZvbnRTaXplVGl0bGUpO1xuICAgICAgICBjdHgucmVzdG9yZSgpO1xuICAgICAgICByZW1haW5pbmdIZWlnaHQgLT0gb3B0aW9ucy5mb250U2l6ZVRpdGxlICogMS4yNTtcbiAgICAgICAgdG9wWVBhZGRpbmcgKz0gb3B0aW9ucy5mb250U2l6ZVRpdGxlICogMS4yNTtcbiAgICAgIH1cblxuICAgICAgLyogQ29tcHV0ZSByZXF1aXJlZCBsZWZ0IHBhZGRpbmcgKi9cbiAgICAgIHZhciBsZWZ0WFBhZGRpbmcgPSBvcHRpb25zLnBhZGRpbmdQaXhlbHNWZXJ0aWNhbDtcbiAgICAgIHJlbWFpbmluZ1dpZHRoICAtPSBvcHRpb25zLnBhZGRpbmdQaXhlbHNWZXJ0aWNhbDtcblxuICAgICAgdmFyIGxlZnRYRHJhd1lMYWJlbCA9IG51bGw7XG4gICAgICBpZiAoY29udGVudC55QXhpcyAhPSBudWxsKSB7XG4gICAgICAgIGxlZnRYRHJhd1lMYWJlbCA9IGxlZnRYUGFkZGluZyArIG9wdGlvbnMuZm9udFNpemVBeGVzICogMC41O1xuICAgICAgICByZW1haW5pbmdXaWR0aCAtPSBvcHRpb25zLmZvbnRTaXplQXhlcyAqIDEuMjU7XG4gICAgICAgIGxlZnRYUGFkZGluZyArPSBvcHRpb25zLmZvbnRTaXplQXhlcyAqIDEuMjU7XG4gICAgICB9XG5cbiAgICAgIGN0eC5zYXZlKCk7XG4gICAgICBjdHguZm9udCA9IEhlbHBlcnMuZ2V0Rm9udCh7IHdlaWdodDogb3B0aW9ucy5mb250V2VpZ2h0LCBzaXplOiBvcHRpb25zLmZvbnRTaXplVGlja3MsIGZhbWlseTogb3B0aW9ucy5mb250IH0pO1xuICAgICAgdmFyIG1heENoYXJ0VmFsdWUsIG1pbkNoYXJ0VmFsdWU7XG4gICAgICBpZiAob3B0aW9ucy5iYXJTdHlsZSA9PT0gJ3N0YWNrZWQnKSB7XG4gICAgICAgIG1heENoYXJ0VmFsdWUgPSAwO1xuICAgICAgICBtaW5DaGFydFZhbHVlID0gSW5maW5pdHk7XG4gICAgICAgIGZvciAodmFyIGNtSW5kZXggPSAwOyBjbUluZGV4IDwgY29udGVudC5kYXRhLmxlbmd0aDsgKytjbUluZGV4KSB7XG4gICAgICAgICAgdmFyIGRvQjtcbiAgICAgICAgICBpZiAoQXJyYXkuaXNBcnJheShkb0IgPSBjb250ZW50LmRhdGFbY21JbmRleF0pKSB7XG4gICAgICAgICAgICB2YXIgdGVtcFN1bSA9IDA7XG4gICAgICAgICAgICBmb3IgKHZhciBpaTIgPSAwOyBpaTIgPCBkb0IubGVuZ3RoOyArK2lpMikgdGVtcFN1bSArPSBkb0JbaWkyXTtcbiAgICAgICAgICAgIG1heENoYXJ0VmFsdWUgPSBNYXRoLm1heChtYXhDaGFydFZhbHVlLCB0ZW1wU3VtKTtcbiAgICAgICAgICAgIG1pbkNoYXJ0VmFsdWUgPSBNYXRoLm1pbihtaW5DaGFydFZhbHVlLCB0ZW1wU3VtKTtcbiAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgbWF4Q2hhcnRWYWx1ZSA9IE1hdGgubWF4KG1heENoYXJ0VmFsdWUsIGNvbnRlbnQuZGF0YVtjbUluZGV4XSk7XG4gICAgICAgICAgICBtaW5DaGFydFZhbHVlID0gTWF0aC5taW4obWluQ2hhcnRWYWx1ZSwgY29udGVudC5kYXRhW2NtSW5kZXhdKTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIG1heENoYXJ0VmFsdWUgPSBIZWxwZXJzLnVwcGVyTWF4KGNvbnRlbnQuZGF0YSk7XG4gICAgICAgIG1pbkNoYXJ0VmFsdWUgPSBIZWxwZXJzLmxvd2VyTWluKGNvbnRlbnQuZGF0YSk7XG4gICAgICB9XG4gICAgICBpZiAob3B0aW9ucy5zY2FsZVN0eWxlLmluZGV4T2YoJ2FkYXB0aXZlJykgPT09IDApIHtcbiAgICAgICAgaWYgKG9wdGlvbnMuc2NhbGVTdHlsZS5pbmRleE9mKCc6JykgIT09IC0xKSB7XG4gICAgICAgICAgdmFyIGZsb2F0ZXIgPSBwYXJzZUZsb2F0KG9wdGlvbnMuc2NhbGVTdHlsZS5zcGxpdCgvWzpdLylbMV0pO1xuICAgICAgICAgIG1pbkNoYXJ0VmFsdWUgKj0gZmxvYXRlcjtcbiAgICAgICAgICBtYXhDaGFydFZhbHVlICo9IDEgKyAoMSAtIGZsb2F0ZXIpIC8gMi4wO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgbWluQ2hhcnRWYWx1ZSA9IDA7XG4gICAgICBpZiAob3B0aW9ucy5kZWZhdWx0TWF4VGljayA+IG1heENoYXJ0VmFsdWUpIG1heENoYXJ0VmFsdWUgPSBvcHRpb25zLmRlZmF1bHRNYXhUaWNrO1xuICAgICAgaWYgKGNvbnRlbnQuYmFycyAhPSBudWxsICYmIEFycmF5LmlzQXJyYXkoY29udGVudC5iYXJzKSkge1xuICAgICAgICBmb3IgKGluZGV4ID0gMDsgaW5kZXggPCBjb250ZW50LmJhcnMubGVuZ3RoOyArK2luZGV4KSB7XG4gICAgICAgICAgdmFyIGNidiA9IGNvbnRlbnQuYmFyc1tpbmRleF0udmFsdWU7XG4gICAgICAgICAgaWYgKGlzTmFOKGNidikpIGNvbnRpbnVlO1xuICAgICAgICAgIG1heENoYXJ0VmFsdWUgPSBNYXRoLm1heChtYXhDaGFydFZhbHVlLCBjYnYpO1xuICAgICAgICAgIG1pbkNoYXJ0VmFsdWUgPSBNYXRoLm1pbihtaW5DaGFydFZhbHVlLCBjYnYpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICB2YXIgbWF4WUF4aXNUaWNrV2lkdGggPSBvcHRpb25zLnNjYWxlU3R5bGUgPT0gJ2xvZzInID8gTWF0aC5jZWlsKE1hdGgucG93KDIsIG1heENoYXJ0VmFsdWUpKSA6IChNYXRoLmNlaWwobWF4Q2hhcnRWYWx1ZSkgKyAnLjAwJyk7XG4gICAgICBpZiAob3B0aW9ucy50aWNrRm9ybWF0dGVyTWVhc3VyZSAhPSBudWxsKSBtYXhZQXhpc1RpY2tXaWR0aCA9IG9wdGlvbnMudGlja0Zvcm1hdHRlck1lYXN1cmU7XG4gICAgICBtYXhZQXhpc1RpY2tXaWR0aCA9IGN0eC5tZWFzdXJlVGV4dChtYXhZQXhpc1RpY2tXaWR0aCkud2lkdGg7XG4gICAgICBtYXhZQXhpc1RpY2tXaWR0aCA9IE1hdGguY2VpbChtYXhZQXhpc1RpY2tXaWR0aCkgKyBvcHRpb25zLnBhZGRpbmdQaXhlbHNUaWNrcztcbiAgICAgIHJlbWFpbmluZ1dpZHRoIC09IG1heFlBeGlzVGlja1dpZHRoO1xuICAgICAgbGVmdFhQYWRkaW5nICs9IG1heFlBeGlzVGlja1dpZHRoO1xuICAgICAgY3R4LnJlc3RvcmUoKTtcblxuICAgICAgdmFyIHJpZ2h0WFBhZGRpbmcgPSBvcHRpb25zLnBhZGRpbmdQaXhlbHNWZXJ0aWNhbDtcbiAgICAgIHJlbWFpbmluZ1dpZHRoIC09IG9wdGlvbnMucGFkZGluZ1BpeGVsc1ZlcnRpY2FsO1xuXG4gICAgICAvKiBEcmF3IGxlZ2VuZCAqL1xuICAgICAgaWYgKGNvbnRlbnQubGVnZW5kICE9IG51bGwgJiYgQXJyYXkuaXNBcnJheShjb250ZW50LmxlZ2VuZCkpIHtcbiAgICAgICAgY3R4LnNhdmUoKTtcbiAgICAgICAgY3R4LmZvbnQgPSBIZWxwZXJzLmdldEZvbnQoeyB3ZWlnaHQ6IG9wdGlvbnMuZm9udFdlaWdodCwgc2l6ZTogb3B0aW9ucy5mb250U2l6ZUxlZ2VuZCwgZmFtaWx5OiBvcHRpb25zLmZvbnQgfSk7XG4gICAgICAgIHZhciBtYXhMV2lkdGggPSAwO1xuICAgICAgICBmb3IgKHZhciBsSW5kZXggPSAwOyBsSW5kZXggPCBjb250ZW50LmxlZ2VuZC5sZW5ndGg7ICsrbEluZGV4KSB7XG4gICAgICAgICAgbWF4TFdpZHRoID0gTWF0aC5tYXgobWF4TFdpZHRoLCBjdHgubWVhc3VyZVRleHQoY29udGVudC5sZWdlbmRbbEluZGV4XS5sYWJlbCkud2lkdGgpO1xuICAgICAgICB9XG4gICAgICAgIG1heExXaWR0aCA9IE1hdGguY2VpbChtYXhMV2lkdGgpO1xuICAgICAgICBtYXhMV2lkdGggKz0gb3B0aW9ucy5waXhlbHNMZWdlbmRTcXVhcmUgKyA4O1xuICAgICAgICB2YXIgbGVnZW5kRW50cmllc1BlckxpbmUgPSBNYXRoLmZsb29yKChyZW1haW5pbmdXaWR0aCAtIG9wdGlvbnMucGFkZGluZ1BpeGVsc0hvcml6b250YWwgKiAyKSAvIG1heExXaWR0aCk7XG4gICAgICAgIHZhciBsTFJlcUhlaWdodCA9IE1hdGguY2VpbChjb250ZW50LmxlZ2VuZC5sZW5ndGggLyBsZWdlbmRFbnRyaWVzUGVyTGluZSkgKiBvcHRpb25zLmZvbnRTaXplTGVnZW5kICogMS41O1xuICAgICAgICByZW1haW5pbmdIZWlnaHQgLT0gbExSZXFIZWlnaHQ7XG4gICAgICAgIGJvdHRvbVlQYWRkaW5nICs9IGxMUmVxSGVpZ2h0O1xuXG4gICAgICAgIGN0eC5zdHJva2VTdHlsZSA9ICdyZ2IoMCwgMCwgMCknO1xuICAgICAgICBjdHguZmlsbFN0eWxlID0gb3B0aW9ucy5maWxsQ29sb3JMZWdlbmQ7XG4gICAgICAgIHZhciBiU1gsIGJTWTtcbiAgICAgICAgY3R4LmJlZ2luUGF0aCgpO1xuICAgICAgICBjdHgubW92ZVRvKGJTWCA9IGxlZnRYUGFkZGluZywgYlNZID0gdG9wWVBhZGRpbmcgKyByZW1haW5pbmdIZWlnaHQpO1xuICAgICAgICBjdHgubGluZVRvKGJTWCArIHJlbWFpbmluZ1dpZHRoLCBiU1kpO1xuICAgICAgICBjdHgubGluZVRvKGJTWCArIHJlbWFpbmluZ1dpZHRoLCBiU1kgKyBsTFJlcUhlaWdodCk7XG4gICAgICAgIGN0eC5saW5lVG8oYlNYLCBiU1kgKyBsTFJlcUhlaWdodCk7XG4gICAgICAgIGN0eC5saW5lVG8oYlNYLCBiU1kpO1xuICAgICAgICBjdHguc3Ryb2tlKCk7XG4gICAgICAgIGN0eC5maWxsKCk7XG5cbiAgICAgICAgZm9yIChsSW5kZXggPSAwOyBsSW5kZXggPCBjb250ZW50LmxlZ2VuZC5sZW5ndGg7ICsrbEluZGV4KSB7XG4gICAgICAgICAgdmFyIGxlZ0xpbmUgPSBNYXRoLmZsb29yKGxJbmRleCAvIGxlZ2VuZEVudHJpZXNQZXJMaW5lKTtcbiAgICAgICAgICB2YXIgbGVnQ29sID0gbEluZGV4ICUgbGVnZW5kRW50cmllc1BlckxpbmU7XG4gICAgICAgICAgY3R4LmZpbGxTdHlsZSA9IGNvbnRlbnQubGVnZW5kW2xJbmRleF0uY29sb3I7XG4gICAgICAgICAgdmFyIGJveFggPSBiU1ggKyBsZWdDb2wgKiBtYXhMV2lkdGggKyAzLCBib3hZID0gYlNZICsgbGVnTGluZSAqIG9wdGlvbnMuZm9udFNpemVMZWdlbmQgKiAxLjUgKyBvcHRpb25zLmZvbnRTaXplTGVnZW5kICogMC41O1xuICAgICAgICAgIGN0eC5iZWdpblBhdGgoKTtcbiAgICAgICAgICBjdHgubW92ZVRvKGJveFgsIGJveFkpO1xuICAgICAgICAgIGN0eC5saW5lVG8oYm94WCArIG9wdGlvbnMucGl4ZWxzTGVnZW5kU3F1YXJlLCBib3hZKTtcbiAgICAgICAgICBjdHgubGluZVRvKGJveFggKyBvcHRpb25zLnBpeGVsc0xlZ2VuZFNxdWFyZSwgYm94WSArIG9wdGlvbnMucGl4ZWxzTGVnZW5kU3F1YXJlKTtcbiAgICAgICAgICBjdHgubGluZVRvKGJveFgsIGJveFkgKyBvcHRpb25zLnBpeGVsc0xlZ2VuZFNxdWFyZSk7XG4gICAgICAgICAgY3R4LmxpbmVUbyhib3hYLCBib3hZKTtcbiAgICAgICAgICBjdHguZmlsbCgpO1xuICAgICAgICAgIGN0eC5zdHJva2UoKTtcblxuICAgICAgICAgIGN0eC50ZXh0QWxpZ24gPSAnbGVmdCc7XG4gICAgICAgICAgY3R4LmZpbGxTdHlsZSA9ICdyZ2IoMCwgMCwgMCknO1xuICAgICAgICAgIGN0eC5maWxsVGV4dChjb250ZW50LmxlZ2VuZFtsSW5kZXhdLmxhYmVsLCBib3hYICsgMyArIG9wdGlvbnMucGl4ZWxzTGVnZW5kU3F1YXJlLCBib3hZICsgb3B0aW9ucy5mb250U2l6ZUxlZ2VuZCAqIDAuNSk7XG4gICAgICAgIH1cblxuICAgICAgICBjdHgucmVzdG9yZSgpO1xuICAgICAgfVxuXG4gICAgICAvKiBEcmF3IHgtYXhpcyBsYWJlbCBvZiBiYXIgY2hhcnQgKi9cbiAgICAgIHZhciBib3R0b21ZUGFkZGluZyA9IG9wdGlvbnMucGFkZGluZ1BpeGVsc0hvcml6b250YWw7XG4gICAgICByZW1haW5pbmdIZWlnaHQgLT0gb3B0aW9ucy5wYWRkaW5nUGl4ZWxzSG9yaXpvbnRhbDtcbiAgICAgIGlmIChjb250ZW50LnhBeGlzICE9IG51bGwpIHtcbiAgICAgICAgY3R4LnNhdmUoKTtcbiAgICAgICAgY3R4LmZvbnQgPSBIZWxwZXJzLmdldEZvbnQoeyB3ZWlnaHQ6IG9wdGlvbnMuZm9udFdlaWdodCwgc2l6ZTogb3B0aW9ucy5mb250U2l6ZUF4ZXMsIGZhbWlseTogb3B0aW9ucy5mb250IH0pO1xuICAgICAgICBjdHguZmlsbFN0eWxlID0gJ3JnYigwLCAwLCAwKSc7XG4gICAgICAgIGN0eC50ZXh0QWxpZ24gPSAnY2VudGVyJztcbiAgICAgICAgY3R4LmZpbGxUZXh0KGNvbnRlbnQueEF4aXMsICh3aWR0aCAtIHJlbWFpbmluZ1dpZHRoKSArIHJlbWFpbmluZ1dpZHRoIC8gMiwgdG9wWVBhZGRpbmcgKyByZW1haW5pbmdIZWlnaHQgLSBib3R0b21ZUGFkZGluZyk7XG4gICAgICAgIHJlbWFpbmluZ0hlaWdodCAtPSBvcHRpb25zLmZvbnRTaXplQXhlcyAqIDEuNTtcbiAgICAgICAgYm90dG9tWVBhZGRpbmcgKz0gb3B0aW9ucy5mb250U2l6ZUF4ZXMgKiAxLjU7XG4gICAgICAgIGN0eC5yZXN0b3JlKCk7XG4gICAgICB9XG5cbiAgICAgIHZhciB3aWR0aFBlckJhciA9IHJlbWFpbmluZ1dpZHRoIC8gY29udGVudC5kYXRhLmxlbmd0aDtcblxuICAgICAgLyogRHJhdyB4LWF4aXMgdG9wIGxhYmVscyAqL1xuICAgICAgaWYgKGNvbnRlbnQudG9wTGFiZWxzICE9IG51bGwpIHtcbiAgICAgICAgY3R4LnNhdmUoKTtcbiAgICAgICAgY3R4LnRleHRBbGlnbiA9ICdjZW50ZXInO1xuICAgICAgICBjdHguZm9udCA9IEhlbHBlcnMuZ2V0Rm9udCh7IHdlaWdodDogb3B0aW9ucy5mb250V2VpZ2h0LCBzaXplOiBvcHRpb25zLmZvbnRTaXplTGFiZWxzLCBmYW1pbHk6IG9wdGlvbnMuZm9udCB9KTtcbiAgICAgICAgcmVtYWluaW5nSGVpZ2h0IC09IG9wdGlvbnMuZm9udFNpemVMYWJlbHMgKiAxLjU7XG4gICAgICAgIHRvcFlQYWRkaW5nICs9IG9wdGlvbnMuZm9udFNpemVMYWJlbHMgKiAxLjU7XG4gICAgICAgIGZvciAoaW5kZXggPSAwOyBpbmRleCA8IGNvbnRlbnQudG9wTGFiZWxzLmxlbmd0aDsgKytpbmRleCkge1xuICAgICAgICAgIGN0eC5maWxsVGV4dChcbiAgICAgICAgICAgIGNvbnRlbnQudG9wTGFiZWxzW2luZGV4XSxcbiAgICAgICAgICAgIGxlZnRYUGFkZGluZyArIGluZGV4ICogd2lkdGhQZXJCYXIgKyB3aWR0aFBlckJhciAvIDIsXG4gICAgICAgICAgICB0b3BZUGFkZGluZyAtIG9wdGlvbnMuZm9udFNpemVMYWJlbHMgLyAyXG4gICAgICAgICAgKTtcbiAgICAgICAgfVxuICAgICAgICBjdHgucmVzdG9yZSgpO1xuICAgICAgfVxuXG4gICAgICAvKiBEcmF3IHgtYXhpcyBsYWJlbHMgKi9cbiAgICAgIGN0eC5zYXZlKCk7XG4gICAgICB2YXIgcmVxV2lkdGggPSAwO1xuICAgICAgaWYgKGNvbnRlbnQuZGF0YVRhZ3MgIT0gbnVsbCkge1xuICAgICAgICBjdHguZm9udCA9IEhlbHBlcnMuZ2V0Rm9udCh7IHdlaWdodDogb3B0aW9ucy5mb250V2VpZ2h0LCBzaXplOiBvcHRpb25zLmZvbnREYXRhVGFncywgZmFtaWx5OiBvcHRpb25zLmZvbnQgfSk7XG4gICAgICAgIHZhciBkYXRhVGFncyA9IGNvbnRlbnQuZGF0YVRhZ3M7XG4gICAgICAgIGZvciAoaW5kZXggPSAwOyBpbmRleCA8IGRhdGFUYWdzLmxlbmd0aDsgKytpbmRleCkge1xuICAgICAgICAgIGlmIChBcnJheS5pc0FycmF5KGRhdGFUYWdzW2luZGV4XSkpIHtcbiAgICAgICAgICAgIGZvciAodmFyIGluZGV4MiA9IDA7IGluZGV4MiA8IGRhdGFUYWdzW2luZGV4XS5sZW5ndGg7ICsraW5kZXgyKSB7XG4gICAgICAgICAgICAgIHJlcVdpZHRoID0gTWF0aC5tYXgocmVxV2lkdGgsIE1hdGguY2VpbChjdHgubWVhc3VyZVRleHQoZGF0YVRhZ3NbaW5kZXhdW2luZGV4Ml0pLndpZHRoICsgNSkpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICByZXFXaWR0aCA9IE1hdGgubWF4KHJlcVdpZHRoLCBNYXRoLmNlaWwoY3R4Lm1lYXN1cmVUZXh0KGRhdGFUYWdzW2luZGV4XSkud2lkdGggKyA1KSk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIGN0eC5mb250ID0gSGVscGVycy5nZXRGb250KHsgd2VpZ2h0OiBvcHRpb25zLmZvbnRXZWlnaHQsIHNpemU6IG9wdGlvbnMuZm9udFNpemVMYWJlbHMsIGZhbWlseTogb3B0aW9ucy5mb250IH0pO1xuICAgICAgdmFyIGNvbXB1dGVkQmFyUGFkZGluZyA9IE1hdGguZmxvb3IoKHdpZHRoUGVyQmFyICogb3B0aW9ucy5wYWRkaW5nUGVyY2VudEJhcnMpIC8gMik7XG4gICAgICB2YXIgd3doID0gd2lkdGhQZXJCYXIgLSBjb21wdXRlZEJhclBhZGRpbmcgKiAyO1xuICAgICAgaWYgKHd3aCA8IHJlcVdpZHRoKSB7XG4gICAgICAgIGNvbXB1dGVkQmFyUGFkZGluZyAtPSBNYXRoLmNlaWwoKHJlcVdpZHRoIC0gd3doKSAvIDIpO1xuICAgICAgICBjb21wdXRlZEJhclBhZGRpbmcgPSBNYXRoLm1heCgwLCBjb21wdXRlZEJhclBhZGRpbmcpO1xuICAgICAgfSBlbHNlIGlmIChvcHRpb25zLm1heFdpZHRoQmFycyA+IDAgJiYgd3doID4gb3B0aW9ucy5tYXhXaWR0aEJhcnMpIHtcbiAgICAgICAgY29tcHV0ZWRCYXJQYWRkaW5nID0gTWF0aC5mbG9vcigod2lkdGhQZXJCYXIgLSBvcHRpb25zLm1heFdpZHRoQmFycykgLyAyKTtcbiAgICAgIH1cbiAgICAgIHZhciBtYXhUZXh0V2lkdGggPSAwLCBtYXhUZXh0U3RhY2tTaXplID0gMTtcbiAgICAgIGZvciAoaW5kZXggPSAwOyBpbmRleCA8IGNvbnRlbnQubGFiZWxzLmxlbmd0aDsgKytpbmRleCkge1xuICAgICAgICB2YXIgdExhYmVsID0gY29udGVudC5sYWJlbHNbaW5kZXhdO1xuICAgICAgICBpZiAoQXJyYXkuaXNBcnJheSh0TGFiZWwpKSB7XG4gICAgICAgICAgbWF4VGV4dFN0YWNrU2l6ZSA9IE1hdGgubWF4KG1heFRleHRTdGFja1NpemUsIHRMYWJlbC5sZW5ndGgpO1xuICAgICAgICAgIGZvciAoaW5kZXgyID0gMDsgaW5kZXgyIDwgdExhYmVsLmxlbmd0aDsgKytpbmRleDIpIHtcbiAgICAgICAgICAgIG1heFRleHRXaWR0aCA9IE1hdGgubWF4KG1heFRleHRXaWR0aCwgY3R4Lm1lYXN1cmVUZXh0KHRMYWJlbFtpbmRleDJdKS53aWR0aCk7XG4gICAgICAgICAgfVxuICAgICAgICB9IGVsc2UgbWF4VGV4dFdpZHRoID0gTWF0aC5tYXgobWF4VGV4dFdpZHRoLCBjdHgubWVhc3VyZVRleHQodExhYmVsKS53aWR0aCk7XG4gICAgICB9XG4gICAgICB2YXIgeExhYmVsc1JvdGF0ZWQgPSBmYWxzZTtcbiAgICAgIGlmIChtYXhUZXh0V2lkdGggPiB3aWR0aFBlckJhciAtIGNvbXB1dGVkQmFyUGFkZGluZykge1xuICAgICAgICBjdHgudGV4dEFsaWduID0gJ3JpZ2h0JztcbiAgICAgICAgY3R4LnJvdGF0ZShNYXRoLlBJICogMS41KTtcbiAgICAgICAgeExhYmVsc1JvdGF0ZWQgPSB0cnVlO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY3R4LnRleHRBbGlnbiA9ICdjZW50ZXInO1xuICAgICAgfVxuICAgICAgdmFyIGxhc3RMYWJlbFkgPSAtb3B0aW9ucy5mb250U2l6ZUxhYmVscztcbiAgICAgIGZvciAoaW5kZXggPSAwOyBpbmRleCA8IGNvbnRlbnQubGFiZWxzLmxlbmd0aDsgKytpbmRleCkge1xuICAgICAgICB2YXIgY0xhYmVsID0gY29udGVudC5sYWJlbHNbaW5kZXhdO1xuICAgICAgICB2YXIgeCA9IGxlZnRYUGFkZGluZyArIGluZGV4ICogd2lkdGhQZXJCYXIgKyB3aWR0aFBlckJhciAvIDIsIHkgPSB0b3BZUGFkZGluZyArIHJlbWFpbmluZ0hlaWdodCAtIG9wdGlvbnMuZm9udFNpemVMYWJlbHMgLyAyO1xuICAgICAgICBpZiAoeExhYmVsc1JvdGF0ZWQpIHtcbiAgICAgICAgICB5ID0gdG9wWVBhZGRpbmcgKyByZW1haW5pbmdIZWlnaHQgLSBtYXhUZXh0V2lkdGggKyA1O1xuICAgICAgICAgIHkgPSBbeCwgeCA9IC15XVswXTtcblxuICAgICAgICAgIGlmICh5IDwgbGFzdExhYmVsWSArIG9wdGlvbnMuZm9udFNpemVMYWJlbHMpIGNvbnRpbnVlO1xuICAgICAgICAgIGxhc3RMYWJlbFkgPSB5O1xuICAgICAgICB9XG4gICAgICAgIHZhciB5VXAgPSBvcHRpb25zLmZvbnRTaXplTGFiZWxzICogKG1heFRleHRTdGFja1NpemUgLSAxKTtcbiAgICAgICAgaWYgKEFycmF5LmlzQXJyYXkoY0xhYmVsKSkge1xuICAgICAgICAgIGlmICh4TGFiZWxzUm90YXRlZCkge1xuICAgICAgICAgICAgeVVwID0gb3B0aW9ucy5mb250U2l6ZUxhYmVscyAqIChjTGFiZWwubGVuZ3RoIC0gMS41KTtcbiAgICAgICAgICAgIHlVcCAvPSAyO1xuICAgICAgICAgIH1cbiAgICAgICAgICBmb3IgKGluZGV4MiA9IDA7IGluZGV4MiA8IGNMYWJlbC5sZW5ndGg7ICsraW5kZXgyKSB7XG4gICAgICAgICAgICBjdHguZmlsbFRleHQoY0xhYmVsW2luZGV4Ml0sIHgsIHkgLSB5VXApO1xuICAgICAgICAgICAgeVVwIC09IG9wdGlvbnMuZm9udFNpemVMYWJlbHM7XG4gICAgICAgICAgfVxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGlmICh4TGFiZWxzUm90YXRlZCkgeVVwID0gLW9wdGlvbnMuZm9udFNpemVMYWJlbHMgKiAwLjI1O1xuICAgICAgICAgIGN0eC5maWxsVGV4dChjTGFiZWwsIHgsIHkgLSB5VXApO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBpZiAoeExhYmVsc1JvdGF0ZWQpIHtcbiAgICAgICAgcmVtYWluaW5nSGVpZ2h0IC09IG1heFRleHRXaWR0aCArIDU7XG4gICAgICAgIGJvdHRvbVlQYWRkaW5nICs9IG1heFRleHRXaWR0aCArIDU7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB2YXIgcmVtVmFsID0gb3B0aW9ucy5mb250U2l6ZUxhYmVscyAqIG1heFRleHRTdGFja1NpemU7XG4gICAgICAgIHJlbVZhbCArPSBvcHRpb25zLmZvbnRTaXplTGFiZWxzICogMC41O1xuICAgICAgICByZW1haW5pbmdIZWlnaHQgLT0gcmVtVmFsO1xuICAgICAgICBib3R0b21ZUGFkZGluZyArPSByZW1WYWw7XG4gICAgICB9XG4gICAgICBjdHgucmVzdG9yZSgpO1xuXG4gICAgICAvKiBEcmF3IGJvdW5kYXJpZXMgKi9cbiAgICAgIHZhciBib3VuZFgxID0gbGVmdFhQYWRkaW5nLCBib3VuZFgyID0gbGVmdFhQYWRkaW5nICsgcmVtYWluaW5nV2lkdGg7XG4gICAgICB2YXIgYm91bmRZMSA9IHRvcFlQYWRkaW5nLCBib3VuZFkyID0gdG9wWVBhZGRpbmcgKyByZW1haW5pbmdIZWlnaHQ7XG5cbiAgICAgIGZvciAoaW5kZXggPSAwOyBpbmRleCA8IGNvbnRlbnQubGFiZWxzLmxlbmd0aDsgKytpbmRleCkgbGFiZWxQb3NpdGlvbnNbaW5kZXhdID0ge1xuICAgICAgICB4U3RhcnQ6IGxlZnRYUGFkZGluZyArIGluZGV4ICogd2lkdGhQZXJCYXIsXG4gICAgICAgIHhFbmQ6IGxlZnRYUGFkZGluZyArICgxICsgaW5kZXgpICogd2lkdGhQZXJCYXIsXG4gICAgICAgIHlTdGFydDogYm91bmRZMSwgeUVuZDogYm91bmRZMlxuICAgICAgfVxuXG4gICAgICBjdHguc2F2ZSgpO1xuICAgICAgY3R4LnN0cm9rZVN0eWxlID0gJ3JnYigwLCAwLCAwKSc7XG4gICAgICBjdHguYmVnaW5QYXRoKCk7XG4gICAgICBpZiAoY29udGVudC50b3BMYWJlbHMgIT0gbnVsbCkge1xuICAgICAgICBjdHgubW92ZVRvKGJvdW5kWDIsIGJvdW5kWTEpO1xuICAgICAgICBjdHgubGluZVRvKGJvdW5kWDEsIGJvdW5kWTEpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY3R4Lm1vdmVUbyhib3VuZFgxLCBib3VuZFkxKTtcbiAgICAgIH1cbiAgICAgIGN0eC5saW5lVG8oYm91bmRYMSwgYm91bmRZMik7XG4gICAgICBjdHgubGluZVRvKGJvdW5kWDIsIGJvdW5kWTIpO1xuICAgICAgaWYgKGNvbnRlbnQudG9wTGFiZWxzICE9IG51bGwpIGN0eC5saW5lVG8obGVmdFhQYWRkaW5nICsgcmVtYWluaW5nV2lkdGgsIHRvcFlQYWRkaW5nKTtcbiAgICAgIGN0eC5zdHJva2UoKTtcbiAgICAgIGN0eC5yZXN0b3JlKCk7XG5cbiAgICAgIC8qIERyYXcgdG9wIGxhYmVsICovXG4gICAgICBpZiAoY29udGVudC50b3BMYWJlbCAhPSBudWxsKSB7XG4gICAgICAgIGN0eC5zYXZlKCk7XG4gICAgICAgIGN0eC50ZXh0QWxpZ24gPSAncmlnaHQnO1xuICAgICAgICBjdHguZm9udCA9IEhlbHBlcnMuZ2V0Rm9udCh7IHdlaWdodDogb3B0aW9ucy5mb250V2VpZ2h0LCBzaXplOiBvcHRpb25zLmZvbnRTaXplTGFiZWxzLCBmYW1pbHk6IG9wdGlvbnMuZm9udCB9KTtcbiAgICAgICAgY3R4LmZpbGxUZXh0KGNvbnRlbnQudG9wTGFiZWwsIGxlZnRYUGFkZGluZyAtIDMsIHRvcFlQYWRkaW5nIC0gb3B0aW9ucy5mb250U2l6ZUxhYmVscyAvIDIpO1xuICAgICAgICBjdHgucmVzdG9yZSgpO1xuICAgICAgfVxuXG4gICAgICAvKiBEcmF3IHktYXhpcyBsYWJlbCBvZiBiYXIgY2hhcnQgKi9cbiAgICAgIGlmIChjb250ZW50LnlBeGlzICE9IG51bGwpIHtcbiAgICAgICAgY3R4LnNhdmUoKTtcbiAgICAgICAgY3R4LnJvdGF0ZShNYXRoLlBJICogMS41KTtcbiAgICAgICAgY3R4LmZvbnQgPSBIZWxwZXJzLmdldEZvbnQoeyB3ZWlnaHQ6IG9wdGlvbnMuZm9udFdlaWdodCwgc2l6ZTogb3B0aW9ucy5mb250U2l6ZUF4ZXMsIGZhbWlseTogb3B0aW9ucy5mb250IH0pO1xuICAgICAgICBjdHguZmlsbFN0eWxlID0gJ3JnYigwLCAwLCAwKSc7XG4gICAgICAgIGN0eC50ZXh0QWxpZ24gPSAnY2VudGVyJztcbiAgICAgICAgY3R4LmZpbGxUZXh0KGNvbnRlbnQueUF4aXMsIC0odG9wWVBhZGRpbmcgKyByZW1haW5pbmdIZWlnaHQgLyAyKSwgbGVmdFhEcmF3WUxhYmVsKTtcbiAgICAgICAgY3R4LnJlc3RvcmUoKTtcbiAgICAgIH1cblxuICAgICAgLyogRHJhdyB5LWF4aXMgbGFiZWxzICovXG4gICAgICBjdHguc2F2ZSgpO1xuICAgICAgY3R4LmZpbGxTdHlsZSA9ICdyZ2IoMCwgMCwgMCknO1xuICAgICAgY3R4LnN0cm9rZVN0eWxlID0gJ3JnYmEoMCwgMCwgMCwgMC4yMCknO1xuICAgICAgY3R4LmZvbnQgPSBIZWxwZXJzLmdldEZvbnQoeyB3ZWlnaHQ6IG9wdGlvbnMuZm9udFdlaWdodCwgc2l6ZTogb3B0aW9ucy5mb250U2l6ZVRpY2tzLCBmYW1pbHk6IG9wdGlvbnMuZm9udCB9KTtcbiAgICAgIGN0eC50ZXh0QWxpZ24gPSAncmlnaHQnO1xuICAgICAgdmFyIHRpY2tNZXRhID0gSGVscGVycy5nZXRMaW5lYXJUaWNrcygwLCBtYXhDaGFydFZhbHVlLCBNYXRoLm1heCgyLCByZW1haW5pbmdIZWlnaHQgLyAob3B0aW9ucy5mb250U2l6ZVRpY2tzICogKDEgKyBvcHRpb25zLnBhZGRpbmdQZXJjZW50VGlja3MpKSkpO1xuICAgICAgdmFyIGFscGhhID0gbWF4Q2hhcnRWYWx1ZSAvIG9wdGlvbnMuZm9udFNpemVUaWNrcztcbiAgICAgIG1heENoYXJ0VmFsdWUgPSB0aWNrTWV0YVsxXTtcbiAgICAgIGlmIChtYXhDaGFydFZhbHVlID4gMSkgbWF4Q2hhcnRWYWx1ZSArPSBNYXRoLmNlaWwoYWxwaGEpO1xuICAgICAgZWxzZSBtYXhDaGFydFZhbHVlICs9IGFscGhhO1xuICAgICAgdmFyIHRpY2tzID0gW107XG4gICAgICB3aGlsZSAodGlja01ldGFbMF0gPD0gdGlja01ldGFbMV0pIHtcbiAgICAgICAgdGlja3MucHVzaCh0aWNrTWV0YVswXSk7XG4gICAgICAgIHRpY2tNZXRhWzBdICs9IHRpY2tNZXRhWzJdO1xuICAgICAgfVxuICAgICAgZm9yIChpbmRleCA9IDA7IGluZGV4IDwgdGlja3MubGVuZ3RoOyArK2luZGV4KSB7XG4gICAgICAgIHZhciB0aWNrSGVpZ2h0ID0gTWF0aC5yb3VuZChyZW1haW5pbmdIZWlnaHQgKiBIZWxwZXJzLmdldEF4aXNSYXRpbyhtaW5DaGFydFZhbHVlLCBtYXhDaGFydFZhbHVlLCB0aWNrc1tpbmRleF0pKTtcbiAgICAgICAgaWYgKHRpY2tIZWlnaHQgPCAwKSBjb250aW51ZTtcbiAgICAgICAgaWYgKG9wdGlvbnMuc2NhbGVTdHlsZSA9PSAnbG9nMicgJiYgdGlja3NbaW5kZXhdICE9PSAwKSB0aWNrc1tpbmRleF0gPSBNYXRoLnJvdW5kKE1hdGgucG93KDIsIHRpY2tzW2luZGV4XSkpO1xuICAgICAgICBlbHNlIHRpY2tzW2luZGV4XSA9IE1hdGguZmxvb3IodGlja3NbaW5kZXhdICogMTAwKSAvIDEwMDtcbiAgICAgICAgaWYgKG9wdGlvbnMudGlja0Zvcm1hdHRlciAhPSBudWxsICYmIHR5cGVvZiBvcHRpb25zLnRpY2tGb3JtYXR0ZXIgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICBjdHguZmlsbFRleHQob3B0aW9ucy50aWNrRm9ybWF0dGVyKHRpY2tzW2luZGV4XSkudG9TdHJpbmcoKSwgbGVmdFhQYWRkaW5nIC0gb3B0aW9ucy5wYWRkaW5nUGl4ZWxzVGlja3MsIHRvcFlQYWRkaW5nICsgcmVtYWluaW5nSGVpZ2h0IC0gdGlja0hlaWdodCk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgY3R4LmZpbGxUZXh0KHRpY2tzW2luZGV4XS50b1N0cmluZygpLCBsZWZ0WFBhZGRpbmcgLSBvcHRpb25zLnBhZGRpbmdQaXhlbHNUaWNrcywgdG9wWVBhZGRpbmcgKyByZW1haW5pbmdIZWlnaHQgLSB0aWNrSGVpZ2h0KTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoaW5kZXggPT0gMCkgY29udGludWU7XG4gICAgICAgIGN0eC5iZWdpblBhdGgoKTtcbiAgICAgICAgY3R4Lm1vdmVUbyhsZWZ0WFBhZGRpbmcsIHRvcFlQYWRkaW5nICsgcmVtYWluaW5nSGVpZ2h0IC0gdGlja0hlaWdodCk7XG4gICAgICAgIGN0eC5saW5lVG8obGVmdFhQYWRkaW5nICsgcmVtYWluaW5nV2lkdGgsIHRvcFlQYWRkaW5nICsgcmVtYWluaW5nSGVpZ2h0IC0gdGlja0hlaWdodCk7XG4gICAgICAgIGN0eC5zdHJva2UoKTtcbiAgICAgIH1cbiAgICAgIGN0eC5yZXN0b3JlKCk7XG5cbiAgICAgIGlmIChjb250ZW50LmJhcnMgIT0gbnVsbCAmJiBBcnJheS5pc0FycmF5KGNvbnRlbnQuYmFycykpIHtcbiAgICAgICAgY3R4LnNhdmUoKTtcbiAgICAgICAgZm9yIChpbmRleCA9IDA7IGluZGV4IDwgY29udGVudC5iYXJzLmxlbmd0aDsgKytpbmRleCkge1xuICAgICAgICAgIHZhciBjQmFyID0gY29udGVudC5iYXJzW2luZGV4XTtcbiAgICAgICAgICBpZiAoY0Jhci52YWx1ZSA+IG1heENoYXJ0VmFsdWUpIGNvbnRpbnVlO1xuICAgICAgICAgIHZhciByZW5kZXJCYXJZID0gdG9wWVBhZGRpbmcgKyByZW1haW5pbmdIZWlnaHQgLSBNYXRoLnJvdW5kKHJlbWFpbmluZ0hlaWdodCAqIEhlbHBlcnMuZ2V0QXhpc1JhdGlvKG1pbkNoYXJ0VmFsdWUsIG1heENoYXJ0VmFsdWUsIGNCYXIudmFsdWUpKTtcbiAgICAgICAgICBjdHguc3Ryb2tlU3R5bGUgPSBjQmFyLnN0eWxlO1xuICAgICAgICAgIGN0eC5maWxsU3R5bGUgPSBjQmFyLnN0eWxlO1xuICAgICAgICAgIGN0eC5iZWdpblBhdGgoKTtcbiAgICAgICAgICBjdHgubW92ZVRvKGJvdW5kWDEsIHJlbmRlckJhclkpO1xuICAgICAgICAgIGN0eC5saW5lVG8oYm91bmRYMiwgcmVuZGVyQmFyWSk7XG4gICAgICAgICAgY3R4LnN0cm9rZSgpO1xuICAgICAgICAgIGN0eC5maWxsKCk7XG4gICAgICAgIH1cbiAgICAgICAgY3R4LnJlc3RvcmUoKTtcbiAgICAgIH1cblxuICAgICAgLyogRHJhdyBiYXJzICovXG4gICAgICBjdHguc2F2ZSgpO1xuICAgICAgdmFyIGxhc3REYXRhID0gbnVsbDtcbiAgICAgIGZvciAoaW5kZXggPSAwOyBpbmRleCA8IGNvbnRlbnQuZGF0YS5sZW5ndGg7ICsraW5kZXgpIHtcbiAgICAgICAgdmFyIGZpbGxDb2xvckZvckluZGV4ID0gbnVsbDtcbiAgICAgICAgdmFyIHN0cm9rZUNvbG9yRm9ySW5kZXggPSBudWxsO1xuICAgICAgICBpZiAoY29udGVudC5maWxsQ29sb3IgIT0gbnVsbCkge1xuICAgICAgICAgIGlmIChBcnJheS5pc0FycmF5KGNvbnRlbnQuZmlsbENvbG9yKSkgZmlsbENvbG9yRm9ySW5kZXggPSBjdHguZmlsbFN0eWxlID0gY29udGVudC5maWxsQ29sb3JbaW5kZXhdO1xuICAgICAgICAgIGVsc2UgY3R4LmZpbGxTdHlsZSA9IGNvbnRlbnQuZmlsbENvbG9yO1xuICAgICAgICB9IGVsc2UgY3R4LmZpbGxTdHlsZSA9IG9wdGlvbnMuZmlsbENvbG9yQmFycztcbiAgICAgICAgaWYgKGNvbnRlbnQuc3Ryb2tlQ29sb3IgIT0gbnVsbCkge1xuICAgICAgICAgIGlmIChBcnJheS5pc0FycmF5KGNvbnRlbnQuc3Ryb2tlQ29sb3IpKSBzdHJva2VDb2xvckZvckluZGV4ID0gY3R4LnN0cm9rZVN0eWxlID0gY29udGVudC5zdHJva2VDb2xvcltpbmRleF07XG4gICAgICAgICAgZWxzZSBjdHguc3Ryb2tlU3R5bGUgPSBjb250ZW50LnN0cm9rZUNvbG9yO1xuICAgICAgICB9IGVsc2UgY3R4LnN0cm9rZVN0eWxlID0gb3B0aW9ucy5zdHJva2VDb2xvckJhcnM7XG4gICAgICAgIHZhciB2ID0gY29udGVudC5kYXRhW2luZGV4XTtcbiAgICAgICAgdmFyIHZJc0FyciA9IEFycmF5LmlzQXJyYXkodik7XG4gICAgICAgIHZhciByZW5kZXJTdGFydFggPSBsZWZ0WFBhZGRpbmcgKyB3aWR0aFBlckJhciAqIGluZGV4O1xuICAgICAgICBpZiAodklzQXJyICYmIG9wdGlvbnMuYmFyU3R5bGUgPT09ICdzdGFja2VkJykge1xuICAgICAgICAgIHZhciBydW5uaW5nVmFsdWUgPSAwLCBsYXN0SGVpZ2h0ID0gMDtcbiAgICAgICAgICBmb3IgKHZhciBkcmF3SW5kZXggPSAwOyBkcmF3SW5kZXggPCB2Lmxlbmd0aDsgKytkcmF3SW5kZXgpIHtcbiAgICAgICAgICAgIGlmIChmaWxsQ29sb3JGb3JJbmRleCAhPSBudWxsICYmIEFycmF5LmlzQXJyYXkoZmlsbENvbG9yRm9ySW5kZXgpKSB7XG4gICAgICAgICAgICAgIGN0eC5maWxsU3R5bGUgPSBmaWxsQ29sb3JGb3JJbmRleFtkcmF3SW5kZXhdIHx8IG9wdGlvbnMuZmlsbENvbG9yQmFycztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChzdHJva2VDb2xvckZvckluZGV4ICE9IG51bGwgJiYgQXJyYXkuaXNBcnJheShzdHJva2VDb2xvckZvckluZGV4KSkge1xuICAgICAgICAgICAgICBjdHguc3Ryb2tlU3R5bGUgPSBzdHJva2VDb2xvckZvckluZGV4W2RyYXdJbmRleF0gfHwgb3B0aW9ucy5zdHJva2VDb2xvckJhcnM7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJ1bm5pbmdWYWx1ZSArPSB2W2RyYXdJbmRleF07XG4gICAgICAgICAgICB2YXIgcmVuZGVyQmFySGVpZ2h0ID0gTWF0aC5mbG9vcihyZW1haW5pbmdIZWlnaHQgKiBIZWxwZXJzLmdldEF4aXNSYXRpbyhtaW5DaGFydFZhbHVlLCBtYXhDaGFydFZhbHVlLCBydW5uaW5nVmFsdWUpKTtcbiAgICAgICAgICAgIHZhciByZW5kZXJVcFRvWSA9IHRvcFlQYWRkaW5nICsgcmVtYWluaW5nSGVpZ2h0IC0gcmVuZGVyQmFySGVpZ2h0O1xuICAgICAgICAgICAgaWYgKE1hdGguYWJzKHJlbmRlckJhckhlaWdodCAtIGxhc3RIZWlnaHQpIDwgb3B0aW9ucy5zdGFja2VkQmFyUGFkZGluZyArIDIpIHtcbiAgICAgICAgICAgICAgbGFzdEhlaWdodCA9IHJlbmRlckJhckhlaWdodDtcbiAgICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHZhciBiYXJQYWRQID0gZHJhd0luZGV4ID4gMCA/IG9wdGlvbnMuc3RhY2tlZEJhclBhZGRpbmcgOiAwO1xuICAgICAgICAgICAgdmFyIHRTWCwgdFNZO1xuICAgICAgICAgICAgdmFyIHRFWCwgdEVZO1xuICAgICAgICAgICAgY3R4LmJlZ2luUGF0aCgpO1xuICAgICAgICAgICAgY3R4Lm1vdmVUbyh0U1ggPSByZW5kZXJTdGFydFggKyBjb21wdXRlZEJhclBhZGRpbmcsIHRTWSA9IHRvcFlQYWRkaW5nICsgcmVtYWluaW5nSGVpZ2h0IC0gbGFzdEhlaWdodCAtIGJhclBhZFApO1xuICAgICAgICAgICAgY3R4LmxpbmVUbyhyZW5kZXJTdGFydFggKyBjb21wdXRlZEJhclBhZGRpbmcsIHJlbmRlclVwVG9ZKTtcbiAgICAgICAgICAgIGN0eC5saW5lVG8odEVYID0gcmVuZGVyU3RhcnRYICsgKHdpZHRoUGVyQmFyIC0gMSkgLSBjb21wdXRlZEJhclBhZGRpbmcsIHRFWSA9IHJlbmRlclVwVG9ZKTtcbiAgICAgICAgICAgIGN0eC5saW5lVG8ocmVuZGVyU3RhcnRYICsgKHdpZHRoUGVyQmFyIC0gMSkgLSBjb21wdXRlZEJhclBhZGRpbmcsIHRvcFlQYWRkaW5nICsgcmVtYWluaW5nSGVpZ2h0IC0gbGFzdEhlaWdodCAtIGJhclBhZFApO1xuICAgICAgICAgICAgaWYgKGRyYXdJbmRleCA+IDApIGN0eC5saW5lVG8odFNYLCB0U1kpO1xuICAgICAgICAgICAgY3R4LnN0cm9rZSgpO1xuICAgICAgICAgICAgY3R4LmZpbGwoKTtcbiAgICAgICAgICAgIHZhciBoaW50O1xuICAgICAgICAgICAgaWYgKGNvbnRlbnQuaGludHMgIT0gbnVsbCAmJiBjb250ZW50LmhpbnRzW2luZGV4XSAhPSBudWxsICYmIChoaW50ID0gY29udGVudC5oaW50c1tpbmRleF1bZHJhd0luZGV4XSkgIT0gbnVsbCkge1xuICAgICAgICAgICAgICB0aGlzLm1vdXNlTGlzdGVuZXJzLnB1c2goZnVuY3Rpb24oaW5kZXgsIGRyYXdJbmRleCwgaGludCwgc3gsIHN5LCBleCwgZXksIHgsIHkpIHtcbiAgICAgICAgICAgICAgICB2YXIgbWluWCA9IE1hdGgubWluKHN4LCBleCksIG1heFggPSBNYXRoLm1heChzeCwgZXgpO1xuICAgICAgICAgICAgICAgIHZhciBtaW5ZID0gTWF0aC5taW4oc3ksIGV5KSwgbWF4WSA9IE1hdGgubWF4KHN5LCBleSk7XG4gICAgICAgICAgICAgICAgaWYgKHggPCBtaW5YIHx8IHggPiBtYXhYIHx8IHkgPCBtaW5ZIHx8IHkgPiBtYXhZKSByZXR1cm4gbnVsbDtcbiAgICAgICAgICAgICAgICByZXR1cm4geyBpbmRleDogaW5kZXgsIGRyYXdJbmRleDogZHJhd0luZGV4LCByZWN0OiB7IGxlZnQ6IG1pblgsIHJpZ2h0OiBtYXhYLCB0b3A6IG1pblksIGJvdHRvbTogbWF4WSB9LCB0ZXh0OiBoaW50LnNwbGl0KCdcXG4nKSB9O1xuICAgICAgICAgICAgICB9LmJpbmQodGhpcywgaW5kZXgsIGRyYXdJbmRleCwgaGludCwgdFNYLCB0U1ksIHRFWCwgdEVZKSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHZhciB0YWdUZXh0O1xuICAgICAgICAgICAgaWYgKHRTWSAtIHJlbmRlclVwVG9ZID4gb3B0aW9ucy5mb250RGF0YVRhZ3MgKiAxLjI1ICYmIGNvbnRlbnQuZGF0YVRhZ3MgIT0gbnVsbCAmJiAodGFnVGV4dCA9IGNvbnRlbnQuZGF0YVRhZ3NbaW5kZXhdKSAhPSBudWxsICYmICh0YWdUZXh0ID0gdGFnVGV4dFtkcmF3SW5kZXhdKSAhPSBudWxsKSB7XG4gICAgICAgICAgICAgIHZhciBvRlMgPSBjdHguZmlsbFN0eWxlO1xuICAgICAgICAgICAgICBjdHguZmlsbFN0eWxlID0gJ3JnYigwLCAwLCAwKSc7XG4gICAgICAgICAgICAgIGN0eC5mb250ID0gSGVscGVycy5nZXRGb250KHsgd2VpZ2h0OiBvcHRpb25zLmZvbnRXZWlnaHQsIHNpemU6IG9wdGlvbnMuZm9udERhdGFUYWdzLCBmYW1pbHk6IG9wdGlvbnMuZm9udCB9KTtcbiAgICAgICAgICAgICAgY3R4LnRleHRBbGlnbiA9ICdjZW50ZXInO1xuICAgICAgICAgICAgICBjdHguZmlsbFRleHQodGFnVGV4dCwgcmVuZGVyU3RhcnRYICsgd2lkdGhQZXJCYXIgLyAyLCB0U1kgLSBvcHRpb25zLmZvbnREYXRhVGFncyAqIDAuMjUpO1xuICAgICAgICAgICAgICBjdHguZmlsbFN0eWxlID0gb0ZTO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBsYXN0SGVpZ2h0ID0gcmVuZGVyQmFySGVpZ2h0O1xuICAgICAgICAgIH1cblxuICAgICAgICAgIGlmIChjb250ZW50LmJhclRvb2x0aXBzICE9IG51bGwpIHtcbiAgICAgICAgICAgIGN0eC5maWxsU3R5bGUgPSAncmdiKDAsIDAsIDApJztcbiAgICAgICAgICAgIGN0eC5mb250ID0gSGVscGVycy5nZXRGb250KHsgd2VpZ2h0OiBvcHRpb25zLmZvbnRXZWlnaHQsIHNpemU6IG9wdGlvbnMuZm9udFNpemVMYWJlbHMsIGZhbWlseTogb3B0aW9ucy5mb250IH0pO1xuICAgICAgICAgICAgY3R4LnRleHRBbGlnbiA9ICdjZW50ZXInO1xuICAgICAgICAgICAgY3R4LmZpbGxUZXh0KGNvbnRlbnQuYmFyVG9vbHRpcHNbaW5kZXhdIHx8ICcnLCByZW5kZXJTdGFydFggKyB3aWR0aFBlckJhciAvIDIsIHJlbmRlclVwVG9ZIC0gMyk7XG4gICAgICAgICAgfVxuICAgICAgICB9IGVsc2UgaWYgKG9wdGlvbnMuYmFyU3R5bGUgPT09ICdsaW5lJykge1xuICAgICAgICAgIGlmICh2SXNBcnIpIHtcbiAgICAgICAgICAgIHZhciByYnggPSByZW5kZXJTdGFydFggKyB3aWR0aFBlckJhciAvIDI7XG5cbiAgICAgICAgICAgIHZhciBsRHU7XG4gICAgICAgICAgICBpZiAob3B0aW9ucy5maWxsUmVnaW9uID09PSAnYmFja2dyb3VuZCcpIHtcbiAgICAgICAgICAgICAgbER1ID0gbGFzdERhdGE7XG4gICAgICAgICAgICAgIGlmIChBcnJheS5pc0FycmF5KGxEdSkpIGxEdSA9IGxEdVswXTtcbiAgICAgICAgICAgICAgaWYgKGxEdSAhPSBudWxsKSB7XG4gICAgICAgICAgICAgICAgdmFyIHNGUyA9IGN0eC5maWxsU3R5bGVcbiAgICAgICAgICAgICAgICBjdHguZmlsbFN0eWxlID0gbER1LmNvbG9yXG4gICAgICAgICAgICAgICAgY3R4LmZpbGxSZWN0KGxEdS54LCBib3VuZFkxLCByYnggLSBsRHUueCwgYm91bmRZMiAtIGJvdW5kWTEpXG4gICAgICAgICAgICAgICAgY3R4LmZpbGxTdHlsZSA9IHNGU1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHZhciBuTERhdGEgPSBbXTtcbiAgICAgICAgICAgIGZvciAodmFyIGRyYXdJbmRleCA9IDA7IGRyYXdJbmRleCA8IHYubGVuZ3RoOyArK2RyYXdJbmRleCkge1xuICAgICAgICAgICAgICB2YXIgcmVuZGVyQmFySGVpZ2h0MyA9IE1hdGgucm91bmQocmVtYWluaW5nSGVpZ2h0ICogSGVscGVycy5nZXRBeGlzUmF0aW8obWluQ2hhcnRWYWx1ZSwgbWF4Q2hhcnRWYWx1ZSwgdltkcmF3SW5kZXhdKSk7XG4gICAgICAgICAgICAgIHZhciByZW5kZXJVcFRvWTMgPSB0b3BZUGFkZGluZyArIHJlbWFpbmluZ0hlaWdodCAtIHJlbmRlckJhckhlaWdodDM7XG5cbiAgICAgICAgICAgICAgdmFyIHJieSA9IHJlbmRlclVwVG9ZMztcbiAgICAgICAgICAgICAgaWYgKGxhc3REYXRhICE9IG51bGwpIHtcbiAgICAgICAgICAgICAgICB2YXIgdExYLCB0TFk7XG4gICAgICAgICAgICAgICAgaWYgKEFycmF5LmlzQXJyYXkobGFzdERhdGEpKSB7XG4gICAgICAgICAgICAgICAgICB0TFggPSAobGFzdERhdGFbZHJhd0luZGV4XSB8fCB7IH0pLng7XG4gICAgICAgICAgICAgICAgICB0TFkgPSAobGFzdERhdGFbZHJhd0luZGV4XSB8fCB7IH0pLnk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgIHRMWCA9IGxhc3REYXRhLng7XG4gICAgICAgICAgICAgICAgICB0TFkgPSBsYXN0RGF0YS55O1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmICh0TFggJiYgdExZKSB7XG4gICAgICAgICAgICAgICAgICBpZiAoQXJyYXkuaXNBcnJheShzdHJva2VDb2xvckZvckluZGV4KSkge1xuICAgICAgICAgICAgICAgICAgICBjdHguc3Ryb2tlU3R5bGUgPSBzdHJva2VDb2xvckZvckluZGV4W2RyYXdJbmRleF0gfHwgb3B0aW9ucy5zdHJva2VDb2xvckJhcnM7XG4gICAgICAgICAgICAgICAgICB9IGVsc2UgY3R4LnN0cm9rZVN0eWxlID0gc3Ryb2tlQ29sb3JGb3JJbmRleCB8fCAncmdiKDAsIDAsIDApJztcbiAgICAgICAgICAgICAgICAgIGN0eC5iZWdpblBhdGgoKTtcbiAgICAgICAgICAgICAgICAgIGN0eC5tb3ZlVG8odExYLCB0TFkpO1xuICAgICAgICAgICAgICAgICAgY3R4LmxpbmVUbyhyYngsIHJieSk7XG4gICAgICAgICAgICAgICAgICBjdHguc3Ryb2tlKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgaWYgKEFycmF5LmlzQXJyYXkoZmlsbENvbG9yRm9ySW5kZXgpKSB7XG4gICAgICAgICAgICAgICAgY3R4LmZpbGxTdHlsZSA9IGZpbGxDb2xvckZvckluZGV4W2RyYXdJbmRleF0gfHwgb3B0aW9ucy5maWxsQ29sb3JCYXJzO1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgIGlmIChBcnJheS5pc0FycmF5KHN0cm9rZUNvbG9yRm9ySW5kZXgpKSB7XG4gICAgICAgICAgICAgICAgY3R4LnN0cm9rZVN0eWxlID0gc3Ryb2tlQ29sb3JGb3JJbmRleFtkcmF3SW5kZXhdIHx8IG9wdGlvbnMuc3Ryb2tlQ29sb3JCYXJzO1xuICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgY3R4LmJlZ2luUGF0aCgpO1xuICAgICAgICAgICAgICBjdHguYXJjKHJieCwgcmJ5LCBvcHRpb25zLnJhZGl1c0RvdCwgMCwgMiAqIE1hdGguUEkpO1xuICAgICAgICAgICAgICBjdHguc3Ryb2tlKCk7XG4gICAgICAgICAgICAgIGN0eC5maWxsKCk7XG5cbiAgICAgICAgICAgICAgbkxEYXRhW2RyYXdJbmRleF0gPSB7IHg6IHJieCwgeTogcmJ5LCBjb2xvcjogY3R4LmZpbGxTdHlsZSB9O1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgbGFzdERhdGEgPSBuTERhdGE7XG4gICAgICAgICAgICBpZiAobER1ICE9IG51bGwgJiYgbER1LmNvbG9yICE9IGxhc3REYXRhWzBdLmNvbG9yKSB0aGlzLmZpbGxSZWdpb25zLnB1c2goe1xuICAgICAgICAgICAgICB4OiBsYXN0RGF0YVswXS54LFxuICAgICAgICAgICAgICB5OiBsYXN0RGF0YVswXS55LFxuICAgICAgICAgICAgICBwcmV2OiBsRHUuY29sb3IsXG4gICAgICAgICAgICAgIG5leHQ6IGxhc3REYXRhWzBdLmNvbG9yXG4gICAgICAgICAgICB9KVxuXG4gICAgICAgICAgICBpZiAoY29udGVudC5iYWxscyAhPSBudWxsICYmIEFycmF5LmlzQXJyYXkoY29udGVudC5iYWxscykgJiYgaW5kZXggPCBjb250ZW50LmJhbGxzLmxlbmd0aCkge1xuICAgICAgICAgICAgICB2YXIgYmFsbCA9IGNvbnRlbnQuYmFsbHNbaW5kZXhdXG4gICAgICAgICAgICAgIGlmIChiYWxsICE9IG51bGwpIHtcbiAgICAgICAgICAgICAgICBjdHguYmVnaW5QYXRoKCk7XG4gICAgICAgICAgICAgICAgY3R4LmZpbGxTdHlsZSA9IGJhbGwuZmlsbDtcbiAgICAgICAgICAgICAgICBjdHguc3Ryb2tlU3R5bGUgPSBiYWxsLnN0cm9rZTtcbiAgICAgICAgICAgICAgICBjdHguYXJjKHJieCwgdG9wWVBhZGRpbmcgKyByZW1haW5pbmdIZWlnaHQgLSAocmVtYWluaW5nSGVpZ2h0ICogSGVscGVycy5nZXRBeGlzUmF0aW8obWluQ2hhcnRWYWx1ZSwgbWF4Q2hhcnRWYWx1ZSwgbWluQ2hhcnRWYWx1ZSArIGJhbGwudmFsdWUpKSwgYmFsbC5yYWRpdXMsIDAsIDIgKiBNYXRoLlBJKTtcbiAgICAgICAgICAgICAgICBjdHguc3Ryb2tlKCk7XG4gICAgICAgICAgICAgICAgY3R4LmZpbGwoKTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICB2YXIgcmVuZGVyQmFySGVpZ2h0MyA9IE1hdGgucm91bmQocmVtYWluaW5nSGVpZ2h0ICogSGVscGVycy5nZXRBeGlzUmF0aW8obWluQ2hhcnRWYWx1ZSwgbWF4Q2hhcnRWYWx1ZSwgdikpO1xuICAgICAgICAgICAgdmFyIHJlbmRlclVwVG9ZMyA9IHRvcFlQYWRkaW5nICsgcmVtYWluaW5nSGVpZ2h0IC0gcmVuZGVyQmFySGVpZ2h0MztcblxuICAgICAgICAgICAgdmFyIHJieCA9IHJlbmRlclN0YXJ0WCArIHdpZHRoUGVyQmFyIC8gMiwgcmJ5ID0gcmVuZGVyVXBUb1kzO1xuICAgICAgICAgICAgdmFyIGxEdTtcbiAgICAgICAgICAgIGlmIChvcHRpb25zLmZpbGxSZWdpb24gPT09ICdiYWNrZ3JvdW5kJykge1xuICAgICAgICAgICAgICBpZiAobGFzdERhdGEgIT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIGxEdSA9IGxhc3REYXRhO1xuICAgICAgICAgICAgICAgIGlmIChBcnJheS5pc0FycmF5KGxEdSkpIGxEdSA9IGxEdVswXTtcbiAgICAgICAgICAgICAgICB2YXIgc0ZTID0gY3R4LmZpbGxTdHlsZVxuICAgICAgICAgICAgICAgIGN0eC5maWxsU3R5bGUgPSBsRHUuY29sb3JcbiAgICAgICAgICAgICAgICBjdHguZmlsbFJlY3QobER1LngsIGJvdW5kWTEsIHJieCAtIGxEdS54LCBib3VuZFkyIC0gYm91bmRZMSlcbiAgICAgICAgICAgICAgICBjdHguZmlsbFN0eWxlID0gc0ZTXG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGN0eC5iZWdpblBhdGgoKTtcbiAgICAgICAgICAgIGN0eC5hcmMocmJ4LCByYnksIG9wdGlvbnMucmFkaXVzRG90LCAwLCAyICogTWF0aC5QSSk7XG4gICAgICAgICAgICBjdHguc3Ryb2tlKCk7XG4gICAgICAgICAgICBjdHguZmlsbCgpO1xuXG4gICAgICAgICAgICBpZiAobGFzdERhdGEgIT0gbnVsbCkge1xuICAgICAgICAgICAgICBpZiAoQXJyYXkuaXNBcnJheShsYXN0RGF0YSkpIHtcbiAgICAgICAgICAgICAgICB2YXIgdExYLCB0TFk7XG4gICAgICAgICAgICAgICAgZm9yICh2YXIga2V5IGluIGxhc3REYXRhKSB7XG4gICAgICAgICAgICAgICAgICBpZiAoIWxhc3REYXRhLmhhc093blByb3BlcnR5KGtleSkpIGNvbnRpbnVlO1xuICAgICAgICAgICAgICAgICAgdExYID0gbGFzdERhdGFba2V5XS54O1xuICAgICAgICAgICAgICAgICAgdExZID0gbGFzdERhdGFba2V5XS55O1xuICAgICAgICAgICAgICAgICAgaWYgKHRMWCAmJiB0TFkpIHtcbiAgICAgICAgICAgICAgICAgICAgY3R4LnN0cm9rZVN0eWxlID0gc3Ryb2tlQ29sb3JGb3JJbmRleCB8fCAncmdiKDAsIDAsIDApJztcbiAgICAgICAgICAgICAgICAgICAgY3R4LmJlZ2luUGF0aCgpO1xuICAgICAgICAgICAgICAgICAgICBjdHgubW92ZVRvKHRMWCwgdExZKTtcbiAgICAgICAgICAgICAgICAgICAgY3R4LmxpbmVUbyhyYngsIHJieSk7XG4gICAgICAgICAgICAgICAgICAgIGN0eC5zdHJva2UoKTtcbiAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgdmFyIHRMWCA9IGxhc3REYXRhLngsIHRMWSA9IGxhc3REYXRhLnk7XG4gICAgICAgICAgICAgICAgaWYgKHRMWCAmJiB0TFkpIHtcbiAgICAgICAgICAgICAgICAgIGN0eC5zdHJva2VTdHlsZSA9IHN0cm9rZUNvbG9yRm9ySW5kZXggfHwgJ3JnYigwLCAwLCAwKSc7XG4gICAgICAgICAgICAgICAgICBjdHguYmVnaW5QYXRoKCk7XG4gICAgICAgICAgICAgICAgICBjdHgubW92ZVRvKHRMWCwgdExZKTtcbiAgICAgICAgICAgICAgICAgIGN0eC5saW5lVG8ocmJ4LCByYnkpO1xuICAgICAgICAgICAgICAgICAgY3R4LnN0cm9rZSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBsYXN0RGF0YSA9IHsgeDogcmJ4LCB5OiByYnksIGNvbG9yOiBjdHguZmlsbFN0eWxlIH07XG4gICAgICAgICAgICBpZiAobER1ICE9IG51bGwgJiYgbER1LmNvbG9yICE9IGxhc3REYXRhLmNvbG9yKSB0aGlzLmZpbGxSZWdpb25zLnB1c2goe1xuICAgICAgICAgICAgICB4OiBsYXN0RGF0YS54LFxuICAgICAgICAgICAgICB5OiBsYXN0RGF0YS55LFxuICAgICAgICAgICAgICBwcmV2OiBsRHUuY29sb3IsXG4gICAgICAgICAgICAgIG5leHQ6IGxhc3REYXRhLmNvbG9yXG4gICAgICAgICAgICB9KVxuXG4gICAgICAgICAgICBpZiAoY29udGVudC5iYWxscyAhPSBudWxsICYmIEFycmF5LmlzQXJyYXkoY29udGVudC5iYWxscykgJiYgaW5kZXggPCBjb250ZW50LmJhbGxzLmxlbmd0aCkge1xuICAgICAgICAgICAgICB2YXIgYmFsbCA9IGNvbnRlbnQuYmFsbHNbaW5kZXhdXG4gICAgICAgICAgICAgIGlmIChiYWxsICE9IG51bGwpIHtcbiAgICAgICAgICAgICAgICBjdHguYmVnaW5QYXRoKCk7XG4gICAgICAgICAgICAgICAgY3R4LmZpbGxTdHlsZSA9IGJhbGwuZmlsbDtcbiAgICAgICAgICAgICAgICBjdHguc3Ryb2tlU3R5bGUgPSBiYWxsLnN0cm9rZTtcbiAgICAgICAgICAgICAgICBjdHguYXJjKHJieCwgdG9wWVBhZGRpbmcgKyByZW1haW5pbmdIZWlnaHQgLSAocmVtYWluaW5nSGVpZ2h0ICogSGVscGVycy5nZXRBeGlzUmF0aW8obWluQ2hhcnRWYWx1ZSwgbWF4Q2hhcnRWYWx1ZSwgbWluQ2hhcnRWYWx1ZSArIGJhbGwudmFsdWUpKSwgYmFsbC5yYWRpdXMsIDAsIDIgKiBNYXRoLlBJKTtcbiAgICAgICAgICAgICAgICBjdHguc3Ryb2tlKCk7XG4gICAgICAgICAgICAgICAgY3R4LmZpbGwoKTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cblxuICAgICAgICAgIHZhciBoaW50O1xuICAgICAgICAgIGlmIChjb250ZW50LmhpbnRzICE9IG51bGwgJiYgKGhpbnQgPSBjb250ZW50LmhpbnRzW2luZGV4XSkgIT0gbnVsbCkge1xuICAgICAgICAgICAgdGhpcy5tb3VzZUxpc3RlbmVycy5wdXNoKGZ1bmN0aW9uKGluZGV4LCBoaW50LCBzeCwgc3ksIGV4LCBleSwgeCwgeSkge1xuICAgICAgICAgICAgICB2YXIgbWluWCA9IE1hdGgubWluKHN4LCBleCksIG1heFggPSBNYXRoLm1heChzeCwgZXgpO1xuICAgICAgICAgICAgICB2YXIgbWluWSA9IE1hdGgubWluKHN5LCBleSksIG1heFkgPSBNYXRoLm1heChzeSwgZXkpO1xuICAgICAgICAgICAgICBpZiAoeCA8IG1pblggfHwgeCA+IG1heFggfHwgeSA8IG1pblkgfHwgeSA+IG1heFkpIHJldHVybiBudWxsO1xuICAgICAgICAgICAgICByZXR1cm4geyBpbmRleDogaW5kZXgsIGRyYXdJbmRleDogZHJhd0luZGV4LCByZWN0OiB7IGxlZnQ6IG1pblgsIHJpZ2h0OiBtYXhYLCB0b3A6IG1pblksIGJvdHRvbTogbWF4WSB9LCB0ZXh0OiBoaW50LnNwbGl0KCdcXG4nKSB9O1xuICAgICAgICAgICAgfS5iaW5kKHRoaXMsIGluZGV4LCBoaW50LCByYnggLSAxLCB0b3BZUGFkZGluZywgcmJ4ICsgMSwgdG9wWVBhZGRpbmcgKyByZW1haW5pbmdIZWlnaHQpKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgaWYgKHZJc0FycikgdiA9IEhlbHBlcnMuYXZnKHYpO1xuICAgICAgICAgIHZhciByZW5kZXJCYXJIZWlnaHQyID0gTWF0aC5yb3VuZChyZW1haW5pbmdIZWlnaHQgKiBIZWxwZXJzLmdldEF4aXNSYXRpbyhtaW5DaGFydFZhbHVlLCBtYXhDaGFydFZhbHVlLCB2KSk7XG4gICAgICAgICAgdmFyIHJlbmRlclVwVG9ZMiA9IHRvcFlQYWRkaW5nICsgcmVtYWluaW5nSGVpZ2h0IC0gcmVuZGVyQmFySGVpZ2h0MjtcbiAgICAgICAgICBjdHguYmVnaW5QYXRoKCk7XG4gICAgICAgICAgY3R4Lm1vdmVUbyhyZW5kZXJTdGFydFggKyBjb21wdXRlZEJhclBhZGRpbmcsIHRvcFlQYWRkaW5nICsgcmVtYWluaW5nSGVpZ2h0KTtcbiAgICAgICAgICBjdHgubGluZVRvKHJlbmRlclN0YXJ0WCArIGNvbXB1dGVkQmFyUGFkZGluZywgcmVuZGVyVXBUb1kyKTtcbiAgICAgICAgICBjdHgubGluZVRvKHJlbmRlclN0YXJ0WCArICh3aWR0aFBlckJhciAtIDEpIC0gY29tcHV0ZWRCYXJQYWRkaW5nLCByZW5kZXJVcFRvWTIpO1xuICAgICAgICAgIGN0eC5saW5lVG8ocmVuZGVyU3RhcnRYICsgKHdpZHRoUGVyQmFyIC0gMSkgLSBjb21wdXRlZEJhclBhZGRpbmcsIHRvcFlQYWRkaW5nICsgcmVtYWluaW5nSGVpZ2h0KTtcbiAgICAgICAgICBjdHguc3Ryb2tlKCk7XG4gICAgICAgICAgY3R4LmZpbGwoKTtcblxuICAgICAgICAgIGlmIChvcHRpb25zLmJhclN0eWxlID09PSAnZXJyb3InKSB7XG4gICAgICAgICAgICB2YXIgdmFsO1xuICAgICAgICAgICAgaWYgKCh2YWwgPSBjb250ZW50Ll9kYXRhX3N0YW5kYXJkX2Vycm9yW2luZGV4XSkgIT0gMCkge1xuICAgICAgICAgICAgICB2YXIgcmVuZGVyQmFyRXJyb3IgPSBNYXRoLnJvdW5kKHJlbWFpbmluZ0hlaWdodCAqIEhlbHBlcnMuZ2V0QXhpc1JhdGlvKG1pbkNoYXJ0VmFsdWUsIG1heENoYXJ0VmFsdWUsIHZhbCkpO1xuICAgICAgICAgICAgICBjdHguYmVnaW5QYXRoKCk7XG4gICAgICAgICAgICAgIHZhciB3aXNrZXJXaWR0aCA9IE1hdGgucm91bmQoKHdpZHRoUGVyQmFyIC0gY29tcHV0ZWRCYXJQYWRkaW5nICogMikgLyA4KTtcbiAgICAgICAgICAgICAgdmFyIHhfID0gbGVmdFhQYWRkaW5nICsgd2lkdGhQZXJCYXIgKiBpbmRleCArIHdpZHRoUGVyQmFyIC8gMjtcbiAgICAgICAgICAgICAgY3R4Lm1vdmVUbyh4XyAtIHdpc2tlcldpZHRoLCByZW5kZXJVcFRvWTIgKyByZW5kZXJCYXJFcnJvcik7XG4gICAgICAgICAgICAgIGN0eC5saW5lVG8oeF8gKyB3aXNrZXJXaWR0aCwgcmVuZGVyVXBUb1kyICsgcmVuZGVyQmFyRXJyb3IpO1xuICAgICAgICAgICAgICBjdHgubW92ZVRvKHhfLCByZW5kZXJVcFRvWTIgKyByZW5kZXJCYXJFcnJvcik7XG4gICAgICAgICAgICAgIGN0eC5saW5lVG8oeF8sIHJlbmRlclVwVG9ZMiAtIHJlbmRlckJhckVycm9yKTtcbiAgICAgICAgICAgICAgY3R4Lm1vdmVUbyh4XyAtIHdpc2tlcldpZHRoLCByZW5kZXJVcFRvWTIgLSByZW5kZXJCYXJFcnJvcik7XG4gICAgICAgICAgICAgIGN0eC5saW5lVG8oeF8gKyB3aXNrZXJXaWR0aCwgcmVuZGVyVXBUb1kyIC0gcmVuZGVyQmFyRXJyb3IpO1xuICAgICAgICAgICAgICBjdHguc3Ryb2tlKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgaWYgKGNvbnRlbnQuYmFyVG9vbHRpcHMgIT0gbnVsbCkge1xuICAgICAgICAgICAgY3R4LmZpbGxTdHlsZSA9ICdyZ2IoMCwgMCwgMCknO1xuICAgICAgICAgICAgY3R4LmZvbnQgPSBIZWxwZXJzLmdldEZvbnQoeyB3ZWlnaHQ6IG9wdGlvbnMuZm9udFdlaWdodCwgc2l6ZTogb3B0aW9ucy5mb250U2l6ZUxhYmVscywgZmFtaWx5OiBvcHRpb25zLmZvbnQgfSk7XG4gICAgICAgICAgICBjdHgudGV4dEFsaWduID0gJ2NlbnRlcic7XG4gICAgICAgICAgICBjdHguZmlsbFRleHQoY29udGVudC5iYXJUb29sdGlwc1tpbmRleF0gfHwgJycsIHJlbmRlclN0YXJ0WCArIHdpZHRoUGVyQmFyIC8gMiwgcmVuZGVyVXBUb1kyIC0gMyk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgICBjdHgucmVzdG9yZSgpO1xuXG4gICAgICBpZiAodGhpcy5jdXJyZW50SGludCAhPSBudWxsKSB7XG4gICAgICAgIGN0eC5zYXZlKCk7XG4gICAgICAgIHZhciBoUmVjdCA9IHRoaXMuY3VycmVudEhpbnQucmVjdCwgaGludHMgPSB0aGlzLmN1cnJlbnRIaW50LnRleHQ7XG4gICAgICAgIGN0eC5maWxsU3R5bGUgPSAncmdiKDAsIDAsIDApJztcbiAgICAgICAgY3R4LmZvbnQgPSBIZWxwZXJzLmdldEZvbnQoeyB3ZWlnaHQ6IG9wdGlvbnMuZm9udFdlaWdodCwgc2l6ZTogb3B0aW9ucy5mb250U2l6ZUhpbnQsIGZhbWlseTogb3B0aW9ucy5mb250IH0pO1xuICAgICAgICBjdHgudGV4dEFsaWduID0gJ2xlZnQnO1xuICAgICAgICB2YXIgYm94V2lkdGggPSAwO1xuICAgICAgICBmb3IgKGluZGV4ID0gMDsgaW5kZXggPCBoaW50cy5sZW5ndGg7ICsraW5kZXgpIHtcbiAgICAgICAgICBib3hXaWR0aCA9IE1hdGgubWF4KGJveFdpZHRoLCBNYXRoLmNlaWwoY3R4Lm1lYXN1cmVUZXh0KGhpbnRzW2luZGV4XSkud2lkdGgpKTtcbiAgICAgICAgfVxuICAgICAgICB2YXIgYm94V2lkdGhQYWRkaW5nID0gNTtcbiAgICAgICAgdmFyIGxpbmVIZWlnaHQgPSBvcHRpb25zLmZvbnRTaXplSGludCAqIDEuNTtcbiAgICAgICAgdmFyIGJveEhlaWdodCA9IGhpbnRzLmxlbmd0aCAqIGxpbmVIZWlnaHQ7XG4gICAgICAgIHZhciBkcmF3WCA9IGhSZWN0LnJpZ2h0ICsgMTAsIGRyYXdZID0gKGhSZWN0LnRvcCArIGhSZWN0LmJvdHRvbSkgLyAyO1xuICAgICAgICBib3hXaWR0aCArPSBib3hXaWR0aFBhZGRpbmcgKiAyO1xuICAgICAgICBpZiAoZHJhd1ggKyBib3hXaWR0aCA+IHdpZHRoKSB7XG4gICAgICAgICAgZHJhd1ggPSBoUmVjdC5sZWZ0IC0gYm94V2lkdGggLSAxMDtcbiAgICAgICAgfVxuICAgICAgICBpZiAoZHJhd1kgLSBib3hIZWlnaHQgLyAyIDwgMCkge1xuICAgICAgICAgIGRyYXdZID0gTWF0aC5jZWlsKGJveEhlaWdodCAvIDIpICsgMTtcbiAgICAgICAgfSBlbHNlIGlmIChkcmF3WSArIGJveEhlaWdodCAvIDIgPiBoZWlnaHQpIHtcbiAgICAgICAgICBkcmF3WSA9IGhlaWdodCAtIGJveEhlaWdodCAvIDIgLSAxO1xuICAgICAgICB9XG4gICAgICAgIGN0eC5jbGVhclJlY3QoZHJhd1gsIGRyYXdZIC0gYm94SGVpZ2h0IC8gMiwgYm94V2lkdGgsIGJveEhlaWdodCk7XG4gICAgICAgIGN0eC5iZWdpblBhdGgoKTtcbiAgICAgICAgY3R4LnJlY3QoZHJhd1gsIGRyYXdZIC0gYm94SGVpZ2h0IC8gMiwgYm94V2lkdGgsIGJveEhlaWdodCk7XG4gICAgICAgIGN0eC5zdHJva2UoKTtcbiAgICAgICAgZm9yIChpbmRleCA9IDA7IGluZGV4IDwgaGludHMubGVuZ3RoOyArK2luZGV4KSB7XG4gICAgICAgICAgY3R4LmZpbGxUZXh0KGhpbnRzW2luZGV4XSwgZHJhd1ggKyBib3hXaWR0aFBhZGRpbmcsIGRyYXdZIC0gYm94SGVpZ2h0IC8gMiArIG9wdGlvbnMuZm9udFNpemVIaW50ICsgaW5kZXggKiBsaW5lSGVpZ2h0KTtcbiAgICAgICAgfVxuICAgICAgICBjdHgucmVzdG9yZSgpO1xuICAgICAgfVxuXG4gICAgICBjdHgudHJhbnNsYXRlKDAuNSwgMC41KTtcblxuICAgICAgdGhpcy5sYWJlbFBvc2l0aW9ucyA9IGxhYmVsUG9zaXRpb25zO1xuICAgIH07XG5cbiAgICByZXR1cm4gQmFyQ2hhcnQ7XG4gIH0pKCk7XG5cbiAgaWYgKHR5cGVvZiBtb2R1bGUgIT09ICd1bmRlZmluZWQnICYmIHR5cGVvZiBtb2R1bGUuZXhwb3J0cyAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICBtb2R1bGUuZXhwb3J0cyA9IEJhckNoYXJ0O1xuICB9IGVsc2Uge1xuICAgIHdpbmRvdy5CYXJDaGFydCA9IEJhckNoYXJ0O1xuICB9XG59KSgpO1xuIiwiLyogZ29vZ2xlQ2hhcnRzLmpzIFZlcnNpb246IDEuNS4wIEJ1aWx0IE9uOiAyMDE4LTEyLTMwICovXG5jb25zdCBsb2FkU2NyaXB0ID0gU3ltYm9sKCdsb2FkU2NyaXB0Jyk7XG5jb25zdCBpbnN0YW5jZSA9IFN5bWJvbCgnaW5zdGFuY2UnKTtcbmxldCBfaW5zdGFuY2U7XG5cbmNsYXNzIEdvb2dsZUNoYXJ0c01hbmFnZXIge1xuICAgIGdldCBbaW5zdGFuY2VdKCkge1xuICAgICAgICByZXR1cm4gX2luc3RhbmNlXG4gICAgfVxuXG4gICAgc2V0IFtpbnN0YW5jZV0odmFsdWUpIHtcbiAgICAgICAgX2luc3RhbmNlID0gdmFsdWU7XG4gICAgfVxuXG4gICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgIGlmICh0aGlzW2luc3RhbmNlXSkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXNbaW5zdGFuY2VdXG4gICAgICAgIH1cblxuICAgICAgICB0aGlzW2luc3RhbmNlXSA9IHRoaXM7XG4gICAgfVxuXG4gICAgcmVzZXQoKSB7XG4gICAgICAgIF9pbnN0YW5jZSA9IG51bGw7XG4gICAgfVxuXG4gICAgW2xvYWRTY3JpcHRdKCkge1xuICAgICAgICBpZiAoIXRoaXMuc2NyaXB0UHJvbWlzZSkge1xuICAgICAgICAgICAgdGhpcy5zY3JpcHRQcm9taXNlID0gbmV3IFByb21pc2UocmVzb2x2ZSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc3QgYm9keSA9IGRvY3VtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKCdib2R5JylbMF07XG4gICAgICAgICAgICAgICAgY29uc3Qgc2NyaXB0ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnc2NyaXB0Jyk7XG4gICAgICAgICAgICAgICAgc2NyaXB0LnR5cGUgPSAndGV4dC9qYXZhc2NyaXB0JztcbiAgICAgICAgICAgICAgICBzY3JpcHQub25sb2FkID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIEdvb2dsZUNoYXJ0cy5hcGkgPSB3aW5kb3cuZ29vZ2xlO1xuICAgICAgICAgICAgICAgICAgICBHb29nbGVDaGFydHMuYXBpLmNoYXJ0cy5sb2FkKCdjdXJyZW50Jywge1xuICAgICAgICAgICAgICAgICAgICAgICAgcGFja2FnZXM6IFsnY29yZWNoYXJ0JywgJ3RhYmxlJ10sXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICBHb29nbGVDaGFydHMuYXBpLmNoYXJ0cy5zZXRPbkxvYWRDYWxsYmFjaygoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgc2NyaXB0LnNyYyA9ICdodHRwczovL3d3dy5nc3RhdGljLmNvbS9jaGFydHMvbG9hZGVyLmpzJztcbiAgICAgICAgICAgICAgICBib2R5LmFwcGVuZENoaWxkKHNjcmlwdCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gdGhpcy5zY3JpcHRQcm9taXNlXG4gICAgfVxuXG4gICAgbG9hZChjYWxsYmFjaywgdHlwZSkge1xuICAgICAgICByZXR1cm4gdGhpc1tsb2FkU2NyaXB0XSgpLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgaWYgKHR5cGUpIHtcbiAgICAgICAgICAgICAgICBsZXQgY29uZmlnID0ge307XG4gICAgICAgICAgICAgICAgaWYgKHR5cGUgaW5zdGFuY2VvZiBPYmplY3QpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uZmlnID0gdHlwZTtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKEFycmF5LmlzQXJyYXkodHlwZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uZmlnID0geyBwYWNrYWdlczogdHlwZSB9O1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbmZpZyA9IHsgcGFja2FnZXM6IFt0eXBlXSB9O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB0aGlzLmFwaS5jaGFydHMubG9hZCgnY3VycmVudCcsIGNvbmZpZyk7XG4gICAgICAgICAgICAgICAgdGhpcy5hcGkuY2hhcnRzLnNldE9uTG9hZENhbGxiYWNrKGNhbGxiYWNrKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrICE9ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhyb3coJ2NhbGxiYWNrIG11c3QgYmUgYSBmdW5jdGlvbicpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKCk7ICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9KVxuICAgIH1cbn1cblxuY29uc3QgR29vZ2xlQ2hhcnRzID0gbmV3IEdvb2dsZUNoYXJ0c01hbmFnZXIoKTtcblxuZXhwb3J0IGRlZmF1bHQgR29vZ2xlQ2hhcnRzTWFuYWdlcjtcbmV4cG9ydCB7IEdvb2dsZUNoYXJ0cyB9O1xuIiwiaW1wb3J0ICQgZnJvbSAnanF1ZXJ5JztcbmltcG9ydCB7IEdvb2dsZUNoYXJ0cyB9IGZyb20gJ2dvb2dsZS1jaGFydHMnO1xuXG5pbXBvcnQgJy4vY2hhcnRzL2NoYXJ0cyc7XG5cbi8vIENoYXJ0c1xubGV0IGNoYXJ0c0FycmF5ID0gW107IC8vICwgZGF0YVBvaW50c0FycmF5ID0gW107XG5cbiQoZnVuY3Rpb24oKSB7XG4gIC8vIGdldCBjaGFydHNcbiAgJCgnLnRpY2tzLWNoYXJ0JykuZWFjaChmdW5jdGlvbigpIHtcbiAgICBsZXQgJGNoYXJ0ID0gJCh0aGlzKTtcbiAgICBsZXQgJHN1cnJvdW5kID0gJCh0aGlzKS5jbG9zZXN0KCcuY2hhcnQtc3Vycm91bmQnKTtcbiAgICBsZXQgaWQsIGJ1b3lJRCwgd2F2ZVRpY2tNYXgsIHdhdmVUaWNrcywgcGVha1RpY2tNYXgsIHBlYWtUaWNrcywgZGF0YVBvaW50cztcbiAgICBpZigkY2hhcnQuYXR0cignaWQnKSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAvLyBjaGFydHNBcnJheS5wdXNoKCk7XG4gICAgICBpZCA9ICQodGhpcykuYXR0cignaWQnKTtcbiAgICAgIGxldCAkdGlja3NEYXRhID0gJHN1cnJvdW5kLmZpbmQoJy50aWNrcy1kYXRhJyk7XG4gICAgICBpZigkdGlja3NEYXRhLmxlbmd0aCA9PT0gMSkge1xuICAgICAgICBidW95SUQgPSAoJHRpY2tzRGF0YS5hdHRyKCdkYXRhLWJ1b3ktaWQnKSAhPT0gdW5kZWZpbmVkKSA/ICR0aWNrc0RhdGEuYXR0cignZGF0YS1idW95LWlkJykgOiAnJztcbiAgICAgICAgd2F2ZVRpY2tNYXggPSAoJHRpY2tzRGF0YS5hdHRyKCdkYXRhLXdhdmUtdGljay1tYXgnKSAhPT0gdW5kZWZpbmVkKSA/ICR0aWNrc0RhdGEuYXR0cignZGF0YS13YXZlLXRpY2stbWF4JykgOiAnJztcbiAgICAgICAgd2F2ZVRpY2tzID0gKCR0aWNrc0RhdGEuYXR0cignZGF0YS13YXZlLXRpY2tzJykgIT09IHVuZGVmaW5lZCkgPyAkdGlja3NEYXRhLmF0dHIoJ2RhdGEtd2F2ZS10aWNrcycpLnNwbGl0KCcsJykgOiAnJztcbiAgICAgICAgcGVha1RpY2tNYXggPSAoJHRpY2tzRGF0YS5hdHRyKCdkYXRhLXBlYWstdGljay1tYXgnKSAhPT0gdW5kZWZpbmVkKSA/ICR0aWNrc0RhdGEuYXR0cignZGF0YS1wZWFrLXRpY2stbWF4JykgOiAnJztcbiAgICAgICAgcGVha1RpY2tzID0gKCR0aWNrc0RhdGEuYXR0cignZGF0YS1wZWFrLXRpY2tzJykgIT09IHVuZGVmaW5lZCkgPyAkdGlja3NEYXRhLmF0dHIoJ2RhdGEtcGVhay10aWNrcycpLnNwbGl0KCcsJykgOiAnJztcbiAgICAgICAgZGF0YVBvaW50cyA9ICgkdGlja3NEYXRhLmF0dHIoJ2RhdGEtZGF0YS1wb2ludHMnKSAhPT0gdW5kZWZpbmVkKSA/IGV2YWwoJ1snICsgSlNPTi5wYXJzZSgkdGlja3NEYXRhLmF0dHIoJ2RhdGEtZGF0YS1wb2ludHMnKSkgKyAnXScpIDogJyc7XG4gICAgICB9XG5cbiAgICAgIC8vIGxvY2FsIHRpbWUgbGFiZWxcbiAgICAgIGNvbnN0IG9mZnNldCA9IG5ldyBEYXRlKCkuZ2V0VGltZXpvbmVPZmZzZXQoKSAvIDYwICogLTE7IC8vIGhvdXJzIGZyb20gR01UXG4gICAgICBjb25zdCB0aW1lTGFiZWwgPSAob2Zmc2V0ID09PSAwKSA/IFwiVGltZSAoR01UKVwiIDogKG9mZnNldCA+IDApID8gXCJUaW1lIChHTVQrXCIgKyBvZmZzZXQgKyBcIilcIiA6IFwiVGltZSAoR01UXCIgKyBvZmZzZXQgKyBcIilcIjtcblxuICAgICAgLy8gcHVzaCB0byBjaGFydCBhcnJheVxuICAgICAgaWYoaWQgIT09IHVuZGVmaW5lZCAmJiBidW95SUQubGVuZ3RoID4gMCAmJiB3YXZlVGlja3MubGVuZ3RoID4gMCAmJiB3YXZlVGlja01heC5sZW5ndGggPiAwICYmIHBlYWtUaWNrcy5sZW5ndGggPiAwICYmIHBlYWtUaWNrTWF4Lmxlbmd0aCA+IDAgJiYgZGF0YVBvaW50cy5sZW5ndGggPiAwKSB7XG4gICAgICAgIGNoYXJ0c0FycmF5LnB1c2goe2lkOiBpZCwgYnVveUlEOiBidW95SUQsIGRhdGFQb2ludHM6IGRhdGFQb2ludHMuc2xpY2UoMCwgLTEpLCBvcHRpb25zOiB7XG4gICAgICAgICAgdGl0bGU6ICcnLFxuICAgICAgICAgIGhlaWdodDogMjgwLFxuICAgICAgICAgIGJhY2tncm91bmRDb2xvcjogeyBmaWxsOiBcInRyYW5zcGFyZW50XCIgfSxcbiAgICAgICAgICAvLyBjaGFydEFyZWE6IHtsZWZ0OiA0NSwgdG9wOiAzMCwgcmlnaHQ6IDQ1LCBib3R0b206IDMwfSxcbiAgICAgICAgICBzZXJpZXM6IHtcbiAgICAgICAgICAgIDA6IHt0YXJnZXRBeGlzSW5kZXg6IDAsIGNvbG9yOiAnIzQ0OWQ0NCcsIHR5cGU6ICdhcmVhJ30sXG4gICAgICAgICAgICAxOiB7dGFyZ2V0QXhpc0luZGV4OiAxLCBjb2xvcjogJ3RyYW5zcGFyZW50JywgdmlzaWJsZUluTGVnZW5kOiB0cnVlfVxuICAgICAgICAgIH0sXG4gICAgICAgICAgLy8gaEF4aXM6IHtcbiAgICAgICAgICAvLyAgICd0aXRsZSc6IHRpbWVMYWJlbFxuICAgICAgICAgIC8vIH0sXG4gICAgICAgICAgdkF4ZXM6IHtcbiAgICAgICAgICAgIDA6IHtcbiAgICAgICAgICAgICAgdGl0bGU6ICdXYXZlIEhlaWdodCAobSknLFxuICAgICAgICAgICAgICB2aWV3V2luZG93OiB7XG4gICAgICAgICAgICAgICAgbWluOiAwLFxuICAgICAgICAgICAgICAgIG1heDogd2F2ZVRpY2tNYXhcbiAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgdGlja3M6IHdhdmVUaWNrc1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIDE6IHtcbiAgICAgICAgICAgICAgdGl0bGU6ICdQZWFrIFBlcmlvZCAocyknLFxuICAgICAgICAgICAgICB2aWV3V2luZG93OiB7XG4gICAgICAgICAgICAgICAgbWluOiAwLFxuICAgICAgICAgICAgICAgIG1heDogcGVha1RpY2tNYXhcbiAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgdGlja3M6IHBlYWtUaWNrc1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH0sXG4gICAgICAgICAgaEF4aXM6IHtcbiAgICAgICAgICAgIC8vIDE6IHtcbiAgICAgICAgICAgIHRpdGxlOiB0aW1lTGFiZWwsXG4gICAgICAgICAgICBpbnRlcnZhbDogMSxcbiAgICAgICAgICAgIGdyaWRsaW5lczoge1xuICAgICAgICAgICAgICB1bml0czoge1xuICAgICAgICAgICAgICAgIGRheXM6IHtmb3JtYXQ6IFsnTU1NIGQnXX0sXG4gICAgICAgICAgICAgICAgaG91cnM6IHtmb3JtYXQ6IFsnaGEnXX0sXG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBtaW5vckdyaWRsaW5lczoge1xuICAgICAgICAgICAgICB1bml0czoge1xuICAgICAgICAgICAgICAgIGhvdXJzOiB7Zm9ybWF0OiBbJ2hhJ119XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIC8vIH1cbiAgICAgICAgICB9XG4gICAgICAgIH19KTtcbiAgICAgIH1cbiAgICB9XG4gIH0pO1xuXG4gIC8vIEVTNiBMb2FkXG4gIEdvb2dsZUNoYXJ0cy5sb2FkKGRyYXdDaGFydCwgXG4gICAgeydwYWNrYWdlcyc6IFsnbGluZScsICdjb3JlY2hhcnQnXX1cbiAgKTtcblxuICAkKHdpbmRvdykucmVzaXplKGZ1bmN0aW9uKCkge1xuICAgIGRyYXdDaGFydCgpO1xuICB9KTtcbn0pO1xuXG5mdW5jdGlvbiBkcmF3Q2hhcnQoKSB7XG4gIGlmKGNoYXJ0c0FycmF5Lmxlbmd0aCA+IDApIHtcbiAgICBmb3IobGV0IGkgPSAwOyBpIDwgY2hhcnRzQXJyYXkubGVuZ3RoOyBpKyspIHtcbiAgICAgIC8vIGxhYmVscyArIGNoYXJ0IGRhdGFcbiAgICAgIHZhciBkYXRhID0gbmV3IEdvb2dsZUNoYXJ0cy5hcGkudmlzdWFsaXphdGlvbi5EYXRhVGFibGUoKTtcbiAgICAgIGRhdGEuYWRkQ29sdW1uKCdkYXRlJywgJ01vbnRoJyk7XG4gICAgICBkYXRhLmFkZENvbHVtbignbnVtYmVyJywgXCJTaWduaWZpY2FudCBXYXZlIEhlaWdodFwiKTtcbiAgICAgIGRhdGEuYWRkQ29sdW1uKHt0eXBlOiAnc3RyaW5nJywgcm9sZTogJ3Rvb2x0aXAnfSk7XG4gICAgICBkYXRhLmFkZENvbHVtbignbnVtYmVyJywgXCJQZWFrIFBlcmlvZCBhbmQgRGlyZWN0aW9uXCIpO1xuICAgICAgZGF0YS5hZGRDb2x1bW4oe3R5cGU6ICdzdHJpbmcnLCByb2xlOiAndG9vbHRpcCd9KTtcbiAgICAgIGRhdGEuYWRkUm93cyhjaGFydHNBcnJheVtpXS5kYXRhUG9pbnRzKTtcblxuICAgICAgY29uc3QgZGF0YUNoYXJ0ID0gbmV3IEdvb2dsZUNoYXJ0cy5hcGkudmlzdWFsaXphdGlvbi5MaW5lQ2hhcnQoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoY2hhcnRzQXJyYXlbaV0uaWQpKTtcbiAgICAgIEdvb2dsZUNoYXJ0cy5hcGkudmlzdWFsaXphdGlvbi5ldmVudHMuYWRkTGlzdGVuZXIoZGF0YUNoYXJ0LCBcInJlYWR5XCIsIGRyYXdNYXJrZXJzLmJpbmQoZGF0YUNoYXJ0LCBjaGFydHNBcnJheVtpXSkpO1xuICAgICAgZGF0YUNoYXJ0LmRyYXcoZGF0YSwgY2hhcnRzQXJyYXlbaV0ub3B0aW9ucyk7XG4gICAgfVxuICB9XG59XG5cbmZ1bmN0aW9uIGRyYXdNYXJrZXJzKGNoYXJ0RGF0YSkge1xuICAvLyBpbnRlcmZhY2VcbiAgbGV0IGNsaSA9IHRoaXMuZ2V0Q2hhcnRMYXlvdXRJbnRlcmZhY2UoKTtcbiAgLy8gZm9yIGVhY2ggY2hhcnRcbiAgZm9yKGxldCBpID0gMDsgaSA8IGNoYXJ0RGF0YS5kYXRhUG9pbnRzLmxlbmd0aDsgaSsrKSB7XG4gICAgbGV0IGxhYmVsID0gJy5vbS0nICsgY2hhcnREYXRhLmJ1b3lJRCArICctb3ZlcmxheS1tYXJrZXItJyArIGk7XG4gICAgaWYoZG9jdW1lbnQucXVlcnlTZWxlY3RvcihsYWJlbCkgIT09IG51bGwpIHtcbiAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IobGFiZWwpLnN0eWxlLnRvcCA9IE1hdGguZmxvb3IoY2xpLmdldFlMb2NhdGlvbihjaGFydERhdGEuZGF0YVBvaW50c1tpXVszXSwgMSkpIC0gMjUgKyBcInB4XCI7XG4gICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKGxhYmVsKS5zdHlsZS5sZWZ0ID0gTWF0aC5mbG9vcihjbGkuZ2V0WExvY2F0aW9uKGNoYXJ0RGF0YS5kYXRhUG9pbnRzW2ldWzBdKSkgLSAyNSArIFwicHhcIjtcbiAgICB9XG4gIH1cblxuICAvLyBQbGFjZSBMZWdlbmQgTWFya2VyXG4gIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJy5vbS0nICsgY2hhcnREYXRhLmJ1b3lJRCArICctbGVnZW5kLW1hcmtlcicpLnN0eWxlLnRvcCA9IE1hdGguZmxvb3IoY2xpLmdldEJvdW5kaW5nQm94KFwibGVnZW5kZW50cnkjMVwiKS50b3ApIC0gOCArIFwicHhcIjtcbiAgZG9jdW1lbnQucXVlcnlTZWxlY3RvcignLm9tLScgKyBjaGFydERhdGEuYnVveUlEICsgJy1sZWdlbmQtbWFya2VyJykuc3R5bGUubGVmdCA9IE1hdGguZmxvb3IoY2xpLmdldEJvdW5kaW5nQm94KFwibGVnZW5kZW50cnkjMVwiKS5sZWZ0KSArIDQgKyBcInB4XCI7XG59IiwiaW1wb3J0ICQgZnJvbSAnanF1ZXJ5JztcbmltcG9ydCBDaGFydCBmcm9tICcuLi8uLi8uLi9ub2RlX21vZHVsZXMvY2hhcnRqcyc7XG5cbiQoZnVuY3Rpb24oKSB7XG5cdGNvbnN0IGNoYXJ0cyA9IGRvY3VtZW50LmdldEVsZW1lbnRzQnlDbGFzc05hbWUoJ2NoYXJ0LWpzLWxheW91dCcpO1xuXHRpZiggY2hhcnRzLmxlbmd0aCA+IDAgKSB7XG5cdFx0Zm9yKCBsZXQgaSA9IDA7IGkgPCBjaGFydHMubGVuZ3RoOyBpKysgKSB7XG5cdFx0XHRjb25zdCBidW95SUQgPSBjaGFydHNbaV0uZGF0YXNldFsnYnVveSddO1xuXG5cdFx0XHQvLyBBamF4XG5cdFx0XHRjb25zdCBpbml0ID0ge1xuXHRcdFx0XHRtZXRob2Q6ICdQT1NUJ1xuXHRcdFx0fTtcblxuXHRcdFx0Y29uc3QgcGFyYW1zID0ge1xuXHRcdFx0XHRhY3Rpb246ICd1d2FfZGF0YXdlbGxfd2F2ZV9wb2ludHNfanNvbicsXG5cdFx0XHRcdGJ1b3lfaWQ6IGJ1b3lJRCxcblx0XHRcdFx0d2F2ZV9mcm9tOiAnMjAyMC0xMC0wNCsyMjowMDowMCcsXG5cdFx0XHRcdHdhdmVfdW50aWw6ICcyMDIwLTEwLTAxKzIyOjAwOjAwJyxcblx0XHRcdFx0dGltZV9hZGp1c3RtZW50OiAnKzgnXG5cdFx0XHR9O1xuXG5cdFx0XHRsZXQgcGFyYW1zU3RyaW5nID0gT2JqZWN0LmtleXMocGFyYW1zKS5tYXAoZnVuY3Rpb24oa2V5KSB7XG5cdFx0XHRcdHJldHVybiBrZXkgKyAnPScgKyBwYXJhbXNba2V5XTtcblx0XHRcdH0pLmpvaW4oJyYnKTtcblxuXHRcdFx0ZmV0Y2goYWpheF9vYmplY3QuYWpheF91cmwgKyAnPycgKyBwYXJhbXNTdHJpbmcsIGluaXQpXG5cdFx0XHQudGhlbigocmVzcG9uc2UpID0+IHtcblx0XHRcdCAgcmV0dXJuIHJlc3BvbnNlLmpzb24oKTtcblx0XHRcdH0pXG5cdFx0XHQudGhlbigod2F2ZXMpID0+IHtcblx0XHRcdFx0Ly8gLy8gdGV4dCBpcyB0aGUgcmVzcG9uc2UgYm9keVxuXHRcdFx0XHQvLyBjb25zb2xlLmxvZygnVGV4dCcpO1xuXHRcdFx0XHQvLyBjb25zb2xlLmxvZyh0ZXh0KTtcblx0XHRcdFx0XG5cdFx0XHR9KVxuXHRcdFx0LmNhdGNoKChlKSA9PiB7XG5cdFx0XHRcdC8vIGVycm9yIGluIGUubWVzc2FnZVxuXHRcdFx0XHRjb25zb2xlLmxvZyhlKTtcblx0XHRcdH0pO1xuXHRcdH1cblx0fVxufSk7XG5cbiIsIm1vZHVsZS5leHBvcnRzID0galF1ZXJ5OyJdLCJzb3VyY2VSb290IjoiIn0=