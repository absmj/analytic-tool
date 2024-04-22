class ChartVisualisation {
    pivot;

    constructor(pivot) {
        this.pivot = pivot;
    }


    get data() {
        let row = new Set, 
            col = new Set,
            labels = null,
            values = new Array(this.pivot.meta.vAmount),
            colValues = {},
            totals = {};

        const colExists = this.pivot.meta.cAmount > 0;
        const rowExists = this.pivot.meta.rAmount > 0;
        let colIndex = 0, rowIndex = 0;
        // console.log(this.pivot)
        totals = Object.values(this.pivot.data[0])
        for(let di = 0; di < this.pivot.data.length; di++) {
            const currentObject = this.pivot.data[di]
            for(let obj in currentObject) {
                if(/^r/.test(obj)) row.add(currentObject[obj])
                if(/^c/.test(obj)) col.add(currentObject[obj])
            }
        }
        
        for (let i = 0; i < values.length; i++) {
            values[i] = new Array((colExists && rowExists ? col.size + row.size  : this.pivot.data.length) - 1)
            this.pivot.data.slice(1, colExists && rowExists ? col.size + row.size + 1 : this.pivot.data.length).forEach((l, p) => {
                values[i][p] = (l["v" + i])
            })
        }

        for (const item of this.pivot.data) {
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

        

        // this.pivot.data.slice(1)
        //                             .forEach((element, index) => {
        //                                 values[this.pivot.meta['v' + index+'Name']] = [...Object.keys(element).filter(k => /^v/.test(k)).map(f => element[f])]
        //                             })


        labels = Object.keys(this.pivot.meta).filter(f => /^v\d+Name/i.test(f)).map(l => this.pivot.meta[l])

        return { x: Array.from(row), colValues, labels, values, totals }
    }

}

class Apex extends ChartVisualisation {
    apex;
    el;
    
    constructor(element, data, type, title = "Untitled") {
        super(data)
        this.el = document.querySelector(element)
        this.title = title
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
        if(Object.keys(this.data.colValues).length > 0) {
            console.log(this.data.colValues)
            const data = [];
            const dataMap = new Map;

            for(let i = 0; i < this.pivot.meta.vAmount; i++) {
                let rowIndex = 0;
                for (const rKey of Object.keys(this.data.colValues)) {
                    const cObject = this.data.colValues[rKey];
                    let colIndex = 0;
                    for (const cKey of Object.keys(cObject)) {
                        const vValue = cObject[cKey];
                        data[colIndex] = ({ name: cKey, group: this.pivot.meta['v'+i+'Name'], data: [...Object.keys(this.data.colValues).map(v => Object.keys(this.data.colValues[v]).filter(a => a == cKey).map(b => this.data.colValues[v][b])[0])] });
                        colIndex++;
                    }
                    dataMap.set(i, data)
                }

            }
            return Array.from(dataMap)
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
            }]
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
                    data: this.data.values[0].map((d,k)=> ({ x: this.data.x[k], y: d }))
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

    update(data) {
        this.destroy()
        this.apex = new ApexCharts((this.el), JSON.parse(data))
        this.render(data)
    }

    renderOptions(type) {
        return 
    }
}