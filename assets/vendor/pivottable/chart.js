(function () {
    var WebDataRocksChartJs = {};
    window["WebDataRocksChartJs"] = WebDataRocksChartJs;

    WebDataRocksChartJs.getData = function (options, callbackHandler, updateHandler) {
        var type = options.type;
        var slice = options.slice;
        var _prepareDataFunction = options.prepareDataFunction;
        var _updateHandler;

        if (updateHandler != null) {
            _updateHandler = function (data) {
                updateHandler(_prepareDataFunction ? _prepareDataFunction(data) : prepareData(data, type), data);
            };
        }

        this.instance.getData({ slice: slice }, function (data) {
            callbackHandler(_prepareDataFunction ? _prepareDataFunction(data) : prepareData(data, type), data);
        }, _updateHandler);
    };

    WebDataRocksChartJs.getNumberFormat = function (format) {
        if (!format) return {};
        return {
            decimal: format.decimalSeparator,
            precision: format.decimalPlaces || 0,
            thousand: format.thousandsSeparator,
            prefix: (format.currencySymbolAlign === "left" || (format.isPercent && format.currencySymbol === "%")) ? format.currencySymbol : '',
            suffix: (format.currencySymbolAlign === "left" || (format.isPercent && format.currencySymbol === "%")) ? '' : format.currencySymbol || (format.isPercent && "%"),
        };
    };

    WebDataRocksChartJs.getNumberFormatPattern = function (format) {
        var str = "###";
        if (!format) return str;

        if (format.thousandsSeparator) {
            str = "#," + str;
        }

        if (format.decimalPlaces && format.decimalPlaces > 0) {
            str += format.decimalSeparator;
            str += Array.from({ length: format.decimalPlaces }, () => "#").join('');
        }

        if (format.currencySymbol) {
            str = (format.currencySymbolAlign === "left" || (format.isPercent && format.currencySymbol === "%"))
                ? format.currencySymbol + str
                : str + format.currencySymbol;
        } else if (format.isPercent) {
            str += "%";
        }

        return str;
    };

    function prepareData(data, type) {
        var output = { options: prepareChartInfo(data) };

        switch (type) {
            case "bar":
            case "line":
            case "pie":
                prepareSingleSeries(output, data);
                break;
            default:
                prepareSeries(output, data);
        }

        return output;
    }

    function prepareChartInfo(data) {
        return { title: data.meta.caption };
    }

    function prepareSingleSeries(output, data) {
        var labels = data.data.map(record => record.r0);
        var datasets = data.meta.vFields.map((field, index) => ({
            label: field.caption,
            data: data.data.map(record => !isNaN(record["v" + index]) ? record["v" + index] : 0),
        }));

        output.data = [{ labels: labels, datasets: datasets }];
    }

    function prepareSeries(output, data) {
        // Similar logic as prepareSingleSeries, adjust as needed for your specific data structure
        // ...
    }

})();
