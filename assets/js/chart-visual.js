

class Color {
    static light() {
        var letters = 'BCDEF'.split('');
        var color = '#';
        for (var i = 0; i < 6; i++ ) {
            color += letters[Math.floor(Math.random() * letters.length)];
        }
        return color;
    }

    static dark() {
        var lum = -0.25;
        var hex = String('#' + Math.random().toString(16).slice(2, 8).toUpperCase()).replace(/[^0-9a-f]/gi, '');
        if (hex.length < 6) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        var rgb = "#",
            c, i;
        for (i = 0; i < 3; i++) {
            c = parseInt(hex.substr(i * 2, 2), 16);
            c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
            rgb += ("00" + c).substr(c.length);
        }
        return rgb;
        
    }
}

class ApexOptions {
    static mode = 'light';
    static annotations() {
        return {
            yaxis: [{
                y: 0,
                y2: null,
                strokeDashArray: 1,
                borderColor: '#c2c2c2',
                fillColor: '#c2c2c2',
                opacity: 0.3,
                offsetX: 0,
                offsetY: -3,
                width: '100%',
                yAxisIndex: 0,
                label: {
                    borderColor: '#c2c2c2',
                    borderWidth: 1,
                    borderRadius: 2,
                    text: undefined,
                    textAnchor: 'end',
                    position: 'right',
                    offsetX: 0,
                    offsetY: 0,
                    mouseEnter: undefined,
                    mouseLeave: undefined,
                    click: undefined,
                    style: {
                        background: '#fff',
                        color: '#777',
                        fontSize: '12px',
                        fontWeight: 400,
                        fontFamily: undefined,
                        cssClass: 'apexcharts-yaxis-annotation-label',
                        padding: {
                            left: 5,
                            right: 5,
                            top: 0,
                            bottom: 2,
                        }
                    },
                },
            }],
            xaxis: [{
                x: 0,
                x2: null,
                strokeDashArray: 1,
                borderColor: '#c2c2c2',
                fillColor: '#c2c2c2',
                opacity: 0.3,
                offsetX: 0,
                offsetY: 0,
                label: {
                    borderColor: '#c2c2c2',
                    borderWidth: 1,
                    borderRadius: 2,
                    text: undefined,
                    textAnchor: 'middle',
                    position: 'top',
                    orientation: 'vertical',
                    offsetX: 0,
                    offsetY: 0,
                    mouseEnter: undefined,
                    mouseLeave: undefined,
                    click: undefined,
                    style: {
                        background: '#fff',
                        color: '#777',
                        fontSize: '12px',
                        fontWeight: 400,
                        fontFamily: undefined,
                        cssClass: 'apexcharts-xaxis-annotation-label',
                    },
                },
            }],
            points: [{
                x: 0,
                y: null,
                yAxisIndex: 0,
                seriesIndex: 0,
                mouseEnter: undefined,
                mouseLeave: undefined,
                click: undefined,
                marker: {
                    size: 0,
                    fillColor: "#fff",
                    strokeColor: "#333",
                    strokeWidth: 3,
                    shape: "circle",
                    radius: 2,
                    OffsetX: 0,
                    OffsetY: 0,
                    cssClass: '',
                },
                label: {
                    borderColor: '#c2c2c2',
                    borderWidth: 1,
                    borderRadius: 2,
                    text: undefined,
                    textAnchor: 'middle',
                    offsetX: 0,
                    offsetY: -15,
                    mouseEnter: undefined,
                    mouseLeave: undefined,
                    click: undefined,
                    style: {
                        background: '#fff',
                        color: '#777',
                        fontSize: '12px',
                        fontWeight: 400,
                        fontFamily: undefined,
                        cssClass: 'apexcharts-point-annotation-label',
                        padding: {
                            left: 5,
                            right: 5,
                            top: 0,
                            bottom: 2,
                        }
                    },
                },
                image: {
                    path: undefined,
                    width: 20,
                    height: 20,
                    offsetX: 0,
                    offsetY: 0,
                }
            }],

            texts: [{
                x: 0,
                y: 0,
                text: '',
                textAnchor: 'start',
                foreColor: undefined,
                fontSize: '13px',
                fontFamily: undefined,
                fontWeight: 400,
                appendTo: '.apexcharts-annotations',
                backgroundColor: 'transparent',
                borderColor: '#c2c2c2',
                borderRadius: 0,
                borderWidth: 0,
                paddingLeft: 4,
                paddingRight: 4,
                paddingTop: 2,
                paddingBottom: 2,
            }],


            images: [{
                path: '',
                x: 0,
                y: 0,
                width: 20,
                height: 20,
                appendTo: '.apexcharts-annotations'
            }],
        }
    }

    static chart() {
        return {
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            },
            background: '#fff',
            brush: {
                enabled: false,
                target: undefined,
                autoScaleYaxis: false
            },
            defaultLocale: 'en',
            dropShadow: {
                enabled: false,
                enabledOnSeries: undefined,
                top: 0,
                left: 0,
                blur: 3,
                color: '#000',
                opacity: 0.35
            },
            defaultLocale: 'az',
            locales: [{
                name: 'az',
                options: {
                    months: ["Yanvar", "Fevral", "Mart", "Aprel", "May", "İyun", "İyul", "Avqust", "Sentyabr", "Oktyabr", "Noyabr", "Dekabr"],
                    shortMonths: ["Yan", "Fev", "Mar", "Apr", "May", "İyn", "İyl", "Avq", "Sen", "Okt", "Noy", "Dek"],
                    days: ["Bazar", "Bazar ertəsi", "Çərşənbə axşamı", "Çərşənbə", "Cümə axşamı", "Cümə", "Şənbə"],
                    shortDays: ["Baz", "B.e.", "Ç.a.", "Çər", "C.a.", "Cüm", "Şən"],
                    toolbar: {
                        download: "SVG yüklə",
                        selection: "Seçim",
                        selectionZoom: "Seçim yaxınlaşdırma",
                        zoomIn: "Yaxınlaşdır",
                        zoomOut: "Uzaqlaşdır",
                        pan: "Panning",
                        reset: "Yaxınlaşdırmanı sıfırla"
                    }
                }
            }],
            offsetX: 0,
            offsetY: 0,
            parentHeightOffset: 15,
            redrawOnWindowResize: true,
            selection: {
                enabled: true,
                type: 'x',
                fill: {
                    color: '#24292e',
                    opacity: 0.1
                },
                stroke: {
                    width: 1,
                    dashArray: 3,
                    color: '#24292e',
                    opacity: 0.4
                },
                xaxis: {
                    min: undefined,
                    max: undefined
                },
                yaxis: {
                    min: undefined,
                    max: undefined
                }
            },
            sparkline: {
                enabled: false,
            },
            stacked: false,
            stackType: 'normal',
            stackOnlyBar: true,
            zoom: {
                enabled: true,
                type: 'x',
                autoScaleYaxis: false,
                zoomedArea: {
                    fill: {
                        color: '#90CAF9',
                        opacity: 0.4
                    },
                    stroke: {
                        color: '#0D47A1',
                        opacity: 0.4,
                        width: 1
                    }
                }
            }
        }
    }

    static colors() {
        return new Array('#2E93fA', '#66DA26', '#546E7A', '#E91E63', '#FF9800');
    }

    static dataLabels() {
        return {
            enabled: true,
            enabledOnSeries: undefined,
            formatter: function (val, opts) {
                return val
            },
            textAnchor: 'middle',
            distributed: false,
            offsetX: 0,
            offsetY: 0,
            style: {
                fontSize: '14px',
                fontFamily: 'Helvetica, Arial, sans-serif',
                fontWeight: 'bold',
                colors: undefined
            },
            background: {
                enabled: true,
                foreColor: '#fff',
                padding: 4,
                borderRadius: 2,
                borderWidth: 1,
                borderColor: '#fff',
                opacity: 0.9,
                dropShadow: {
                    enabled: false,
                    top: 1,
                    left: 1,
                    blur: 1,
                    color: '#000',
                    opacity: 0.45
                }
            },
            dropShadow: {
                enabled: false,
                top: 1,
                left: 1,
                blur: 1,
                color: '#000',
                opacity: 0.45
            }
        }
    }

    static fill() {
        return {
            colors: undefined,
            opacity: 0.9,
            type: 'solid',
            gradient: {
                shade: 'dark',
                type: "horizontal",
                shadeIntensity: 0.5,
                gradientToColors: undefined,
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 50, 100],
                colorStops: []
            },
            image: {
                src: [],
                width: undefined,
                height: undefined
            },
            pattern: {
                style: 'verticalLines',
                width: 6,
                height: 6,
                strokeWidth: 2,
            },
        }
    }

    static forecastDataPoints() {
        return {
            count: 0,
            fillOpacity: 0.5,
            strokeWidth: undefined,
            dashArray: 4,
        }
    }

    static grid() {
        return {
            show: true,
            borderColor: '#90A4AE',
            strokeDashArray: 0,
            position: 'back',
            xaxis: {
                lines: {
                    show: false
                }
            },
            yaxis: {
                lines: {
                    show: false
                }
            },
            row: {
                colors: undefined,
                opacity: 0.5
            },
            column: {
                colors: undefined,
                opacity: 0.5
            },
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            },
        }
    }

    static legend() {
        return {
            show: true,
            showForSingleSeries: false,
            showForNullSeries: true,
            showForZeroSeries: true,
            position: 'bottom',
            horizontalAlign: 'center',
            floating: false,
            fontSize: '14px',
            fontFamily: 'Helvetica, Arial',
            fontWeight: 400,
            formatter: undefined,
            inverseOrder: false,
            width: undefined,
            height: undefined,
            tooltipHoverFormatter: undefined,
            customLegendItems: [],
            offsetX: 0,
            offsetY: 0,
            labels: {
                colors: undefined,
                useSeriesColors: false
            },
            markers: {
                size: 6,
                shape: ['circle', 'square', 'line', 'plus', 'cross'],
                strokeWidth: 2,
                fillColors: undefined,
                radius: 2,
                customHTML: undefined,
                onClick: undefined,
                offsetX: 0,
                offsetY: 0
            },
            itemMargin: {
                horizontal: 5,
                vertical: 0
            },
            onItemClick: {
                toggleDataSeries: true
            },
            onItemHover: {
                highlightDataSeries: true
            },
        }
    }

    static markers() {
        return {
            size: 0,
            colors: undefined,
            strokeColors: '#fff',
            strokeWidth: 2,
            strokeOpacity: 0.9,
            strokeDashArray: 0,
            fillOpacity: 1,
            discrete: [],
            shape: "circle",
            radius: 2,
            offsetX: 0,
            offsetY: 0,
            onClick: undefined,
            onDblClick: undefined,
            showNullDataPoints: true,
            hover: {
                size: undefined,
                sizeOffset: 3
            }
        }
    }

    static plotOptions(type) {
        return {
            line: {
                isSlopeChart: false,
            },
            area: {
                fillTo: 'origin',
            },
            bar: {
                horizontal: false,
                borderRadius: 0,
                borderRadiusApplication: 'around',
                borderRadiusWhenStacked: 'last',
                columnWidth: '70%',
                barHeight: '70%',
                distributed: false,
                rangeBarOverlap: true,
                rangeBarGroupRows: false,
                hideZeroBarsWhenGrouped: false,
                isDumbbell: false,
                dumbbellColors: undefined,
                isFunnel: false,
                isFunnel3d: true,
                colors: {
                    ranges: [{
                        from: 0,
                        to: 0,
                        color: undefined
                    }],
                    backgroundBarColors: [],
                    backgroundBarOpacity: 1,
                    backgroundBarRadius: 0,
                },
                dataLabels: {
                    position: 'top',
                    maxItems: 100,
                    hideOverflowingLabels: true,
                    orientation: 'horizontal',
                    total: {
                        enabled: false,
                        formatter: undefined,
                        offsetX: 0,
                        offsetY: 0,
                        style: {
                            color: '#373d3f',
                            fontSize: '12px',
                            fontFamily: undefined,
                            fontWeight: 600
                        }
                    }
                }
            },
            bubble: {
                zScaling: true,
                minBubbleRadius: undefined,
                maxBubbleRadius: undefined,
            },
            candlestick: {
                colors: {
                    upward: '#00B746',
                    downward: '#EF403C'
                },
                wick: {
                    useFillColor: true
                }
            },
            boxPlot: {
                colors: {
                    upper: '#00E396',
                    lower: '#008FFB'
                }
            },
            heatmap: {
                radius: 2,
                enableShades: true,
                shadeIntensity: 0.5,
                reverseNegativeShade: true,
                distributed: false,
                useFillColorAsStroke: false,
                colorScale: {
                    ranges: [{
                        from: 0,
                        to: 0,
                        color: undefined,
                        foreColor: undefined,
                        name: undefined,
                    }],
                    inverse: false,
                    min: undefined,
                    max: undefined
                },
            },
            treemap: {
                enableShades: true,
                shadeIntensity: 0.5,
                reverseNegativeShade: true,
                distributed: false,
                useFillColorAsStroke: false,
                dataLabels: {
                    format: "scale"
                },
                colorScale: {
                    ranges: [{
                        from: 0,
                        to: 0,
                        color: undefined,
                        foreColor: undefined,
                        name: undefined,
                    }],
                    inverse: false,
                    min: undefined,
                    max: undefined
                },
            },
            pie: {
                startAngle: 0,
                endAngle: 360,
                expandOnClick: true,
                offsetX: 0,
                offsetY: 0,
                customScale: 1,
                dataLabels: {
                    offset: 0,
                    minAngleToShowLabel: 10
                },
                donut: {
                    size: '65%',
                    background: 'transparent',
                    labels: {
                        show: false,
                        name: {
                            show: true,
                            fontSize: '22px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 600,
                            color: undefined,
                            offsetY: -10,
                            formatter: function (val) {
                                return val
                            }
                        },
                        value: {
                            show: true,
                            fontSize: '16px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 400,
                            color: undefined,
                            offsetY: 16,
                            formatter: function (val) {
                                return val
                            }
                        },
                        total: {
                            show: false,
                            showAlways: false,
                            label: 'Total',
                            fontSize: '22px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 600,
                            color: '#373d3f',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b
                                }, 0)
                            }
                        }
                    }
                },
            },
            polarArea: {
                rings: {
                    strokeWidth: 1,
                    strokeColor: '#e8e8e8',
                },
                spokes: {
                    strokeWidth: 1,
                    connectorColors: '#e8e8e8',
                }
            },
            radar: {
                size: undefined,
                offsetX: 0,
                offsetY: 0,
                polygons: {
                    strokeColors: '#e8e8e8',
                    strokeWidth: 1,
                    connectorColors: '#e8e8e8',
                    fill: {
                        colors: undefined
                    }
                }
            },
            radialBar: {
                inverseOrder: false,
                startAngle: 0,
                endAngle: 360,
                offsetX: 0,
                offsetY: 0,
                hollow: {
                    margin: 5,
                    size: '50%',
                    background: 'transparent',
                    image: undefined,
                    imageWidth: 150,
                    imageHeight: 150,
                    imageOffsetX: 0,
                    imageOffsetY: 0,
                    imageClipped: true,
                    position: 'front',
                    dropShadow: {
                        enabled: false,
                        top: 0,
                        left: 0,
                        blur: 3,
                        opacity: 0.5
                    }
                },
                track: {
                    show: true,
                    startAngle: undefined,
                    endAngle: undefined,
                    background: '#f2f2f2',
                    strokeWidth: '97%',
                    opacity: 1,
                    margin: 5,
                    dropShadow: {
                        enabled: false,
                        top: 0,
                        left: 0,
                        blur: 3,
                        opacity: 0.5
                    }
                },
                dataLabels: {
                    show: true,
                    name: {
                        show: true,
                        fontSize: '16px',
                        fontFamily: undefined,
                        fontWeight: 600,
                        color: undefined,
                        offsetY: -10
                    },
                    value: {
                        show: true,
                        fontSize: '14px',
                        fontFamily: undefined,
                        fontWeight: 400,
                        color: undefined,
                        offsetY: 16,
                        formatter: function (val) {
                            return val + '%'
                        }
                    },
                    total: {
                        show: false,
                        label: 'Total',
                        color: '#373d3f',
                        fontSize: '16px',
                        fontFamily: undefined,
                        fontWeight: 600,
                        formatter: function (w) {
                            return w.globals.seriesTotals.reduce((a, b) => {
                                return a + b
                            }, 0) / w.globals.series.length + '%'
                        }
                    }
                },
                barLabels: {
                    enabled: false,
                    offsetX: 0,
                    offsetY: 0,
                    useSeriesColors: true,
                    fontFamily: undefined,
                    fontWeight: 600,
                    fontSize: '16px',
                    formatter: (val) => {
                        return val
                    },
                    onClick: undefined,
                },
            }
        }[type]
    }

    static states() {
        return {
            normal: {
                filter: {
                    type: 'none',
                    value: 0,
                }
            },
            hover: {
                filter: {
                    type: 'lighten',
                    value: 0.15,
                }
            },
            active: {
                allowMultipleDataPointsSelection: false,
                filter: {
                    type: 'darken',
                    value: 0.35,
                }
            },
        }
    }

    static stroke() {
        return {
            show: true,
            curve: 'straight',
            lineCap: 'butt',
            colors: undefined,
            width: 2,
            dashArray: 0, 
        }
    }

    static subtitle() {
        return {
            text: undefined,
            align: 'left',
            margin: 10,
            offsetX: 0,
            offsetY: 0,
            floating: false,
            style: {
              fontSize:  '12px',
              fontWeight:  'normal',
              fontFamily:  undefined,
              color:  '#9699a2'
            },
        }
    }

    static theme(mode = 'light', palette = 'pallette1') {
        return {
            mode, 
            palette, 
            monochrome: {
                enabled: false,
                color: '#255aee',
                shadeTo: 'light',
                shadeIntensity: 0.65
            },
        }
    }

    static title() {
        return {
            text: undefined,
            align: 'left',
            margin: 10,
            offsetX: 0,
            offsetY: 0,
            floating: false,
            style: {
              fontSize:  '14px',
              fontWeight:  'bold',
              fontFamily:  undefined,
              color:  '#263238'
            },
        }
    }

    static tooltip() {
        return {
            enabled: true,
            enabledOnSeries: undefined,
            shared: true,
            followCursor: false,
            intersect: false,
            inverseOrder: false,
            custom: undefined,
            hideEmptySeries: true,
            fillSeriesColor: false,
            theme: false,
            style: {
              fontSize: '12px',
              fontFamily: undefined
            },
            onDatasetHover: {
                highlightDataSeries: false,
            },
            x: {
                show: true,
                format: 'dd MMM',
                formatter: undefined,
            },
            y: {
                formatter: undefined,
                title: {
                    formatter: (seriesName) => seriesName,
                },
            },
            z: {
                formatter: undefined,
                title: 'Size: '
            },
            marker: {
                show: true,
            },
            items: {
               display: 'flex',
            },
            fixed: {
                enabled: false,
                position: 'topRight',
                offsetX: 0,
                offsetY: 0,
            },
        }
    }

    static xaxis() {
        return {
            type: 'category',
            categories: [],
            tickAmount: undefined,
            tickPlacement: 'between',
            min: undefined,
            max: undefined,
            stepSize: undefined,
            range: undefined,
            floating: false,
            decimalsInFloat: undefined,
            overwriteCategories: undefined,
            position: 'bottom',
            labels: {
                show: true,
                rotate: -45,
                rotateAlways: false,
                hideOverlappingLabels: true,
                showDuplicates: false,
                trim: false,
                minHeight: undefined,
                maxHeight: 120,
                style: {
                    colors: [],
                    fontSize: '12px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 400,
                    cssClass: 'apexcharts-xaxis-label',
                },
                offsetX: 0,
                offsetY: 0,
                format: undefined,
                formatter: undefined,
                datetimeUTC: true,
                datetimeFormatter: {
                    year: 'yyyy',
                    month: "MMM 'yy",
                    day: 'dd MMM',
                    hour: 'HH:mm',
                    minute: 'HH:mm:ss',
                    second: 'HH:mm:ss',
                },
            },
            group: {
              groups: [],
              style: {
                colors: [],
                fontSize: '12px',
                fontWeight: 400,
                fontFamily: undefined,
                cssClass: ''
              }
            },
            axisBorder: {
                show: true,
                color: '#78909C',
                height: 1,
                width: '100%',
                offsetX: 0,
                offsetY: 0
            },
            axisTicks: {
                show: true,
                borderType: 'solid',
                color: '#78909C',
                height: 6,
                offsetX: 0,
                offsetY: 0
            },
           
            title: {
                text: undefined,
                offsetX: 0,
                offsetY: 0,
                style: {
                    color: undefined,
                    fontSize: '12px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 600,
                    cssClass: 'apexcharts-xaxis-title',
                },
            },
            crosshairs: {
                show: true,
                width: 1,
                position: 'back',
                opacity: 0.9,        
                stroke: {
                    color: '#b6b6b6',
                    width: 0,
                    dashArray: 0,
                },
                fill: {
                    type: 'solid',
                    color: '#B1B9C4',
                    gradient: {
                        colorFrom: '#D8E3F0',
                        colorTo: '#BED1E6',
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5,
                    },
                },
                dropShadow: {
                    enabled: false,
                    top: 0,
                    left: 0,
                    blur: 1,
                    opacity: 0.4,
                },
            },
            tooltip: {
                enabled: true,
                formatter: undefined,
                offsetY: 0,
                style: {
                  fontSize: 0,
                  fontFamily: 0,
                },
            },
        }
    }

    static yaxis() {
        return {
            show: true,
            showAlways: false,
            showForNullSeries: true,
            seriesName: undefined,
            opposite: false,
            reversed: false,
            logarithmic: false,
            logBase: 10,
            tickAmount: undefined,
            min: undefined,
            max: undefined,
            stepSize: undefined,
            forceNiceScale: false,
            floating: false,
            decimalsInFloat: undefined,
            labels: {
                show: true,
                align: 'right',
                minWidth: 0,
                maxWidth: 160,
                style: {
                    colors: [],
                    fontSize: '12px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 400,
                    cssClass: 'apexcharts-yaxis-label',
                },
                offsetX: 0,
                offsetY: 0,
                rotate: 0,
                formatter: (value) => { return val },
            },
            axisBorder: {
                show: true,
                color: '#78909C',
                offsetX: 0,
                offsetY: 0
            },
            axisTicks: {
                show: true,
                borderType: 'solid',
                color: '#78909C',
                width: 6,
                offsetX: 0,
                offsetY: 0
            },
            title: {
                text: undefined,
                rotate: -90,
                offsetX: 0,
                offsetY: 0,
                style: {
                    color: undefined,
                    fontSize: '12px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 600,
                    cssClass: 'apexcharts-yaxis-title',
                },
            },
            crosshairs: {
                show: true,
                position: 'back',
                stroke: {
                    color: '#b6b6b6',
                    width: 1,
                    dashArray: 0,
                },
            },
            tooltip: {
                enabled: true,
                offsetX: 0,
            },
            
        }
    }
}

class ChartVisualisation {
    static makeData(pivot) {
        let row = new Set,
            col = new Set,
            labels = null,
            values = new Array(pivot.meta.vAmount),
            colValues = {},
            totals = {};

        const colExists = pivot.meta.cAmount > 0;
        const rowExists = pivot.meta.rAmount > 0;

        totals = Object.values(pivot.data[0])
        for (let di = 0; di < pivot.data.length; di++) {
            const currentObject = pivot.data[di]
            for (let obj in currentObject) {
                if (/^r/.test(obj)) row.add(currentObject[obj])
                if (/^c/.test(obj)) col.add(currentObject[obj])
            }
        }

        for (let i = 0; i < values.length; i++) {
            values[i] = new Array((colExists && rowExists ? col.size + row.size : pivot.data.length) - 1)
            pivot.data.slice(1, colExists && rowExists ? col.size + row.size + 1 : pivot.data.length).forEach((l, p) => {
                values[i][p] = (l["v" + i])
            })
        }

        for (const item of pivot.data) {
            const rKey = Object.keys(item).find(key => key.startsWith('r'));
            const cKey = Object.keys(item).find(key => key.startsWith('c'));
            const vKey = Object.keys(item).find(key => key.startsWith('v'));

            if (rKey && cKey && vKey) {
                const rValue = item[rKey];
                const cValue = item[cKey];
                const vValue = item[vKey];
                colValues[rValue] = colValues[rValue] || {};
                colValues[rValue][cValue] = isNaN(vValue) ? 0 : vValue;
            }
        }

        labels = Object.keys(pivot.meta).filter(f => /^v\d+Name/i.test(f)).map(l => pivot.meta[l])

        return { x: Array.from(row), colValues, labels, values, totals }
    }
}

class Apex {
    apex;
    el;
    type;
    data;
    pivot;

    constructor(element, data, type, title = "Untitled") {
        this.pivot = data;
        this.data = ChartVisualisation.makeData(data);

        this.el = document.querySelector(element)
        this.title = title
        this.type = type;
        Apex.theme =  {
            palette: 'palette4'
        } 
        this.apex = new ApexCharts(this.el, this[type])

    }

    generateOptions(type, data) {
        this.data = ChartVisualisation.makeData(data);
        return this[type.chart.type];   
    }

    get height() {
        return 350
        // return this.el.clientHeight
    }

    get width() {
        return '100%'
    }

    get series() {
        const rows = Object.keys(this.data.colValues)
        if (rows.length > 0) {
            const data = [];
            const dataMap = new Map;

            for (let i = 0; i < this.pivot.meta.vAmount; i++) {
                for (const rKey of rows) {
                    const cObject = this.data.colValues[rKey];
                    let colIndex = 0;
                    for (const cKey of Object.keys(cObject)) {
                        const vValue = cObject[cKey];
                        data[colIndex + (i * rows.length)] = ({ name: this.pivot.meta['v' + i + 'Name'] + " " + cKey, group: this.pivot.meta['v' + i + 'Name'], data: [...Object.keys(this.data.colValues).map(v => Object.keys(this.data.colValues[v]).filter(a => a == cKey).map(b => this.data.colValues[v][b])[0])] });
                        colIndex++;
                    }
                }

            }
            return data
        } else {
            return this.data.labels.map((d, k) => ({
                name: d,
                data: this.data.values[k]
            }))
        }
    }

    get area() {
        return {
            series: this.series,
            chart: {
                type: 'area',
                height: this.height,
            },
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            subtitle: {
                text: '',
                align: 'left'
            },
            labels: this.data.labels,
            xaxis: {
                categories: this.data.x,
            },
            yaxis: {
                opposite: true
            },
            legend: {
                horizontalAlign: 'left'
            }
        }
    }

    get bar() {
        return {
            series: this.series,
            chart: {
                type: 'bar',
                height: this.height,
                stacked: false,
                zoom: {
                    enabled: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: this.data.x,
            },
            fill: {
                opacity: 1
            },
            theme: {
                palette: 'palette4'
            }
        }
    }

    get line() {
        return {
            series: this.series,
            chart: {
                type: 'line',
                height: this.height,
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },

            stroke: {
                width: 5,
                curve: 'smooth'
            },
            xaxis: {
                categories: this.data.x,
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    gradientToColors: ['#FDD835'],
                    shadeIntensity: 1,
                    type: 'horizontal',
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100, 100, 100]
                },
            },

            total: {
                show: true,
                label: 'Total',
                formatter: function (w) {
                    // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                    return w
                }
            },
            labels: this.data.labels,
            theme: {
                palette: 'palette4'
            }
        }
    }

    get radar() {
        return {
            theme: {
                palette: 'palette4'
            },
            series: this.series,
            chart: {
                type: 'radar',
                height: this.height,
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },
            plotOptions: {
                radar: {
                    size: 140,
                    polygons: {
                        strokeColors: '#e9e9e9',
                        fill: {
                            colors: ['#f8f8f8', '#fff']
                        }
                    }
                }
            },

            colors: ['#FF4560'],
            markers: {
                size: 4,
                colors: ['#fff'],
                strokeColor: '#FF4560',
                strokeWidth: 2,
            },
            xaxis: {
                categories: this.data.x,
            }
        }
    }

    get donut() {
        return {
            theme: {
                palette: 'palette4'
            },
            series: this.data.values[0],
            chart: {
                type: 'donut',
                height: this.height,
                width: '85%'

            },
            labels: this.data.x,
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },
            responsive: [{
                options: {
                    chart: {

                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        }
    }

    get pie() {
        return {
            theme: {
                palette: 'palette4'
            },
            series: this.data.values[0],
            chart: {
                type: 'pie',
                height: this.height,
                width: '85%'

            },
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },
            labels: this.data.x,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {

                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            plotOptions: {
                pie: {
                    dataLabels: {
                        name: {
                            fontSize: '22px',
                        },
                        value: {
                            fontSize: '16px',
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: () => {
                                // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                                return this.data.totals
                            }
                        }
                    }
                }
            }
        }
    }

    get radialBar() {
        return {
            theme: {
                palette: 'palette4'
            },
            series: this.data.values[0],
            chart: {
                type: 'pie',
                height: this.height,
                width: '85%'
            },
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },
            labels: this.data.x,
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: {
                            fontSize: '22px',
                        },
                        value: {
                            fontSize: '16px',
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                                return w
                            }
                        }
                    }
                }
            }
        }
    }

    get gauge() {
        return {
            theme: {
                palette: 'palette4'
            },
            series: this.data.totals,
            chart: {
                height: this.height,
                type: 'radialBar',
                offsetY: -10
            },
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    dataLabels: {
                        name: {
                            fontSize: '16px',
                            color: undefined,
                            offsetY: 120
                        },
                        value: {
                            offsetY: 76,
                            fontSize: '22px',
                            color: undefined,
                            formatter: function (val) {
                                return val + "%";
                            }
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    shadeIntensity: 0.15,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 65, 91]
                },
            },
            stroke: {
                dashArray: 4
            },
            labels: this.data.labels,
            theme: {
                palette: 'palette4'
            }
        };
    }

    get treeMap() {
        return {
            series: [
                {
                    data: this.data.values[0].map((d, k) => ({ x: this.data.x[k], y: d }))
                }
            ],
            legend: {
                show: false
            },
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },
            chart: {
                height: this.height,
                type: 'treemap'
            },
            theme: {
                palette: 'palette4'
            }
        }

    }

    get polarArea() {
        return {
            series: this.data.values[0],
            chart: {
                type: 'polarArea',
                width: '85%'
            },
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },
            labels: this.data.x,
            stroke: {
                colors: ['#fff']
            },
            fill: {
                opacity: 0.8
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {

                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            theme: {
                palette: 'palette4'
            }
        };
    }

    get dataLabels() {
        return {
            ...this[this.type],
            chart: {
                height: 350,
                type: 'line',
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 18,
                    left: 7,
                    blur: 10,
                    opacity: 0.2
                },
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                }
            },
            colors: ['#77B6EA', '#545454'],
            dataLabels: {
                enabled: true,
            },
            stroke: {
                curve: 'smooth'
            },
            title: {
                text: 'Average High & Low Temperature',
                align: 'left'
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            markers: {
                size: 1
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5
            }
        }
    }

    get zoomableTimeseries() {
        return {
            series: this.series,
            chart: {
                type: 'area',
                stacked: false,
                height: 350,
                zoom: {
                    type: 'x',
                    enabled: true,
                    autoScaleYaxis: true
                },
                toolbar: {
                    autoSelected: 'zoom'
                }
            },
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 0,
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                },
            },
            yaxis: {
                labels: {

                },
            },
            xaxis: {
                type: 'datetime',
            },
            tooltip: {
                shared: false,
            }
        };
    }

    get lineChartAnnotations() {
        return {
            series: this.series,
            chart: {
                height: 350,
                type: 'line',
            },
            annotations: {
                yaxis: [{
                    y: 8200,
                    borderColor: '#00E396',
                    label: {
                        borderColor: '#00E396',
                        style: {
                            color: '#fff',
                            background: '#00E396',
                        },
                        text: 'Support',
                    }
                }, {
                    y: 8600,
                    y2: 9000,
                    borderColor: '#000',
                    fillColor: '#FEB019',
                    opacity: 0.2,
                    label: {
                        borderColor: '#333',
                        style: {
                            fontSize: '10px',
                            color: '#333',
                            background: '#FEB019',
                        },
                        text: 'Y-axis range',
                    }
                }],
                xaxis: [{
                    x: new Date('23 Nov 2017').getTime(),
                    strokeDashArray: 0,
                    borderColor: '#775DD0',
                    label: {
                        borderColor: '#775DD0',
                        style: {
                            color: '#fff',
                            background: '#775DD0',
                        },
                        text: 'Anno Test',
                    }
                }, {
                    x: new Date('26 Nov 2017').getTime(),
                    x2: new Date('28 Nov 2017').getTime(),
                    fillColor: '#B3F7CA',
                    opacity: 0.4,
                    label: {
                        borderColor: '#B3F7CA',
                        style: {
                            fontSize: '10px',
                            color: '#fff',
                            background: '#00E396',
                        },
                        offsetY: -10,
                        text: 'X-axis range',
                    }
                }],
                points: [{
                    x: new Date('01 Dec 2017').getTime(),
                    y: 8607.55,
                    marker: {
                        size: 8,
                        fillColor: '#fff',
                        strokeColor: 'red',
                        radius: 2,
                        cssClass: 'apexcharts-custom-class'
                    },
                    label: {
                        borderColor: '#FF4560',
                        offsetY: 0,
                        style: {
                            color: '#fff',
                            background: '#FF4560',
                        },

                        text: 'Point Annotation',
                    }
                }, {
                    x: new Date('08 Dec 2017').getTime(),
                    y: 9340.85,
                    marker: {
                        size: 0
                    },
                }]
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            grid: {
                padding: {
                    right: 30,
                    left: 20
                }
            },
            title: {
                text: 'Line with Annotations',
                align: 'left'
            },
            labels: series.monthDataSeries1.dates,
            xaxis: {
                type: 'datetime',
            },
        };

    }

    set height(h) {
        this.height = h
    }

    render() {
        return this.apex.render()
    }

    destroy() {
        return this.apex.destroy()
    }

    update(data, options) {
        this.pivot = data
        this.data = ChartVisualisation.makeData(data)

        this.apex.updateOptions({ ...this[this.type] })
    }



    renderOptions(type) {
        return
    }
}