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
        this.apex = new ApexCharts(this.el, this[type])

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
                zoom: {
                    enabled: false
                }
            },
            title: {
                text: this.title,
                style: {
                    fontSize: '18px'
                }
            },
            dataLabels: {
                enabled: true
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
                stacked: Object.keys(this.data.colValues).length > 0,
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
                enabled: true
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
            labels: this.data.labels
        }
    }

    get radar() {
        return {
            series: this.series,
            chart: {
                type: 'radar',
                height: this.height,
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: true
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
            }]
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
    /**
     * @param {any} h
     */
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