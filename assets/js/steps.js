function decycle(obj, stack = []) {
  if (!obj || typeof obj !== 'object')
    return obj;

  if (stack.includes(obj))
    return null;

  let s = stack.concat([obj]);

  return Array.isArray(obj)
    ? obj.map(x => decycle(x, s))
    : Object.fromEntries(
      Object.entries(obj)
        .map(([k, v]) => [k, decycle(v, s)]));
}

const steps = {
  current: 0,
  maxStage: 4,
  error: null,
  nextButton: null,
  prevButton: null,
  steps: [
    {
      data: {
        name: null,
        type: 'off',
        database: null,
        sql: null,
      },
      id: 'stepone',
      container: null,
      mounted(formCheck) {
        if (!this.container) {
          this.container = document.getElementById(this.id)
          const editor = CodeMirror.fromTextArea(document.getElementById('sql'), {
            mode: 'sql',
            indentWithTabs: true,
            smartIndent: true,
            lineNumbers: true,
            matchBrackets: true,
            autofocus: true,
            extraKeys: { "Ctrl-Space": "autocomplete" },
            hintOptions: {
              tables: {
                users: ["name", "score", "birthDate"],
                countries: ["name", "population", "size"]
              }
            }
          });

          editor.setValue("select * from train limit 10")

          editor.on('blur', () => {
            this.data.sql = editor.getValue()
          })
        } else {
          this.container.classList.remove("d-none")
        }

      },

      destroy() {
        if (this.container)
          this.container.classList.add("d-none")
      },

      validity() {
        if (!this.data.name)
          throw new Error("Hesabatın adı daxil edilməyib!");

        if (!this.data.sql)
          throw new Error("Hesabatın SQL sorğusu daxil edilməyib!");

        if (/(update|insert|delete|truncate|drop|alter|create|revoke|commit|rollback)/muis.test(this.data.sql))
          throw new Error("Hesabatın SQL sorğusu düzgün deyil. Bazadan yalnız məlumatların oxunması mümkündür!");

        if (!this.data.report_folder)
          throw new Error("Hesabat qovluğu seçilməyib!");

        if (!this.data.sql)
          throw new Error("SQL-in işləyəcəyi baza seçilməyib!");

        if (!this.data.cron_job)
          throw new Error("İşləmə tezliyi Fərdi olaraq seçilibsə, CRON job seçilməlidir!");

        return true;
      },

      async nextStage(callback, step) {
        const data = await this.create(() => {
          callback();
        }, step)

        return data
      },

      async create(callback, step = 0) {
        const response = await $.post("/reports/create", { ...this.data, step });
        callback();
        return response;
      }
    },

    // Step 2
    {
      id: 'steptwo',
      data: null,
      that: null,
      container: null,
      generateTable() {
        const table = document.getElementById("table-data")
        table.innerHTML = (`<table class='table table-hover'><thead><tr>${Object.keys(this.data[0]).map(th => `<th>${th}</th>`).join("")}</tr></thead><tbody>${this.data.map(td => `<tr>${Object.values(td).map(v => `<td>${v}</td>`).join("")}</tr>`).join("")}</tbody><table>`)
      },

      destroy() {
        if (this.container)
          this.container.classList.add("d-none")
      },

      nextStage(callback) {
        return this.that.steps[0].nextStage(callback, 1)
      },

      mounted(that) {
        if (!this.container) {
          this.container = document.getElementById(this.id)
          this.generateTable()
        }
        this.that = that;
        this.container.classList.remove("d-none")
      },
    },

    // Step 3
    {
      id: "stepthree",
      container: null,
      data: null,
      pivot: null,
      chart: {
        type: 'line',
        loading: false,
        instance: null,
        data: null,
        options: null,
        slice: null
      },

      get chartOptions() {
        // if(this.chart.options) return this.chart.options;
        return {
          series: this.chart.data.series,
          chart: {
            height: 400,
            type: this.chart.type,
            zoom: {
              enabled: false
            }
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            curve: 'straight'
          },
          title: {
            text: '',
            align: 'left'
          },
          grid: {
            row: {
              colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
              opacity: 0.5
            },
          },
          xaxis: {
            categories: this.chart.data.xaxis.categories,
          }
        };
      },

      set chartOptions({field}) {
        console.log(field)
        this.chart.options = {...this.chart.options, ...field}
        this.chart.instance.updateOptions(this.chartOptions)
      },

      nextStage(callback) {
        callback();
        return this.chart;
      },

      generatePivotTable() {
        this.pivot = new WebDataRocks({
          container: "#pivot",
          toolbar: true,
          report: {
            data: this.data
          },
          reportcomplete: () => {
            this.pivot.off("reportcomplete");
            this.createChart();
          }
        });
      },

      get dumpDataType() {
        switch (this.chartType) {
          case 'line':
          case 'column':
          case 'bar':
            return 'bar';
          case 'pie':
            return 'pie';
          case 'area':
            return 'line';
          case 'histogram':
            return 'bar';
          case 'scatter':
            return 'line';
          case 'combo':
            return 'line';
          default:
            return 'line';
        }
      },

      prepareSeries(_data) {
        const series = []
        const r0 = [];
        const data = _data.data;
        let maxRow;
        console.log(_data)
        for (let a = 0; a < _data.meta.vAmount; a++) {
          let values = [];
          for(let i in data) {
            const keys = Object.keys(data[i]);
            if (data[i].r0 === undefined) continue;
            r0.push(data[i].r0);
            maxRow = Math.max(keys.filter(k => k.startsWith("r")).map(k => k.substring(1)))
            values.push(data[i]['v' + a])
          }
          series.push({
            name: _data.meta['v' + a + 'Name'],
            data: values
          })
        }
        return { series, xaxis: { categories: r0 } }
      },

      createApexChart(el = 'chart-container') {
        const drawChart = (_data) => {
          this.chart.data = this.prepareSeries(_data);
          this.chart.options = this.chartOptions
          this.chart.instance = new ApexCharts(document.getElementById(el), this.chart.options);
          console.log(el)
          this.chart.instance.render();
        };

        const run = data => {
          drawChart(data)
          // console.log(this.chart.data)
        }

        const update = data => {
          this.chart.data = this.prepareSeries(data)
          // console.log(this.chart.data)
          // console.log(this.chart.data)
          this.chartOptions = {field: this.chart.data}
          
          this.chart.instance.updateOptions(this.chart.options);
        }

        if (this.chart.loading) {
          this.slice = {
            rows: this.pivot.getRows(),
            columns: this.pivot.getColumns(),
            measures: this.pivot.getMeasures()
          }
          this.pivot.getData(this.slice, run, update)
        }

      },

      createChart(el = 'chart-container') {
        this.chart.loading = false;

        const onApexChartsLoaded = () => {
          this.chart.loading = true;
          this.createApexChart(el);
        };

        onApexChartsLoaded();

        return this.chart;
      },

      destroy() {
        if (this.container) this.container.classList.add("d-none");
      },

      mounted(_this) {
        if (!this.container) {
          this.container = document.getElementById(this.id);
          // $(".toggle-sidebar-btn").click();
          this.generatePivotTable();
        } else {
          this.createApexChart();
        }

        document.getElementById("legend").addEventListener("keyup", (e) => {
          this.legend = e.target.value;
        });

        document.getElementById("chartType").addEventListener("change", (e) => {
          this.chartType = e.target.value;
        });

        this.container.classList.remove("d-none");
      },

      set legend(l) {
        console.log(this.chart.options)
        this.chart.options.title.text = l;
        this.chart.instance.updateOptions(this.chart.options);
      },

      set chartType(t) {
        this.chart.options.chart.type = t;
        this.chart.instance.updateOptions(this.chart.options);
      },
    },


    // Step 4
    {
      id: 'stepfour',
      container: null,
      chart: null,
      data: null,
      that: null,
      generateReportInfoTable(data) {
        document.getElementById("info-report").innerHTML = `
        <table class="table table-hover">
          ${Object.keys(data).map(th => `<tr>
            <th>${th}</th>
            <td>${typeof data[th] == 'object' ? 'blob' : data[th]}</td>
          </tr>`).join("")}
        </table>`;

      },

      generateChartChanger(chart) {
        document.getElementById("info-report-labels").innerHTML = `
                                  <h6>Leybllar</h6>
                                  <select id='chart-labels' class="form-select">
                                    ${(chart.data.series).map((th, k) => `<tr>
                                      <option value="${k}">${th.name}</option>
                                    </tr>`).join("")}
                                  </select>`;

        document.getElementById("info-report-columns").innerHTML = `
                                  <h6>Sütunlar</h6>
                                  <select id='chart-columns' class="form-select">
                                    ${(chart.data.xaxis.categories).map((th, k) => `<tr>
                                      <option value="${k}">${th}</option>
                                    </tr>`).join("")}
                                  </select>`;


        document.getElementById("chart-labels")?.addEventListener("change", (e) => {
          document.getElementById("label-changer")?.remove();
          const input = document.createElement("input");
          input.classList.add("form-control", "mt-2")
          input.id = "label-changer";
          input.placeholder = input.value = chart.data.series[e.target.value].name
          input.onkeyup = (v) => {
            chart.data.series[e.target.value].name = v.target.value
            chart.instance.updateOptions(chart.data.options)
          }

          e.target.parentNode.append(input)
        })

        document.getElementById("chart-columns")?.addEventListener("change", (e) => {
          document.getElementById("column-changer")?.remove();
          const input = document.createElement("input");
          input.classList.add("form-control", "mt-2")
          input.id = "column-changer";
          input.placeholder = input.value = chart.data.series[e.target.value].name
          input.onkeyup = (v) => {
            input.value = chart.data.series[e.target.value].name = v.target.value
          }

          input.onfocusout = v => {
            chart.instance.updateOptions(chart.options)
          }

          e.target.parentNode.append(input)
        })
      },

      async save(callback = () => { }) {
        console.log(this.that.report)
        // const response = await $.post("/reports/create", { ...this.that.report, step: this.that.lastStep });
        // callback();
        // return response;
      },

      destroy() {
        if (this.container)
          this.container.classList.add("d-none")
      },

      mounted(_this) {

        if (!this.container) {
          this.container = document.getElementById(this.id)
        }

        if (!this.that) {
          this.that = _this;
        }

        // this.chart = this.that.chart

        _this.nextButton.onclick = async () => {
          await this.save()
        }


        this.chart = _this.steps[2].createChart('chart-container-preview')
        this.generateChartChanger(this.chart)

        this.container.classList.remove("d-none")

      }
    }

  ],

  get lastStep() {
    return this.steps.length
  },

  get report() {
    return {
      ...this.steps[0].data,
      chart: JSON.stringify(decycle({
        data: this.steps[2].chart.data,
        options: this.steps[2].chart.options
      }))
    }
  },

  get currentStep() {
    return this.steps?.[this.current]
  },

  set exception(e) {
    this.error = e;
    if (e) {
      this.errorElement.classList.remove("d-none")
      this.errorElement.classList.add("fade", "show")
      this.render('error')
      throw new Error(e);
    } else {
      this.errorElement.classList.add("d-none")
    }

  },

  mounted() {
    this.currentStep.mounted()
    this.render("stepDescription")
    this.nextButton = document.getElementById(`next`)
    this.prevButton = document.getElementById(`prev`)
    this.errorElement = document.getElementById("error")

    this.nextButton.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();

      this.formCheck(async () => {

        if (this.current == this.steps.length - 1) {
          return;
        }


        this.nextButton.setAttribute('disabled', true)
        this.nextButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
        const runOrSave = this.currentStep.data['run_or_save'] == 'on';

        const data = await this.currentStep.nextStage(() => {
          if(runOrSave) {
            this.nextButton.removeAttribute('disabled')
            this.step = this.nextStep
            this.nextButton.innerHTML = 'Yadda saaxla'
          }
        }, this.current);
        
        if(runOrSave) {
          this.currentStep.data = data.data
          this.currentStep.mounted(this);
        } else {
          this.nextButton.classList.add("btn-success")
          this.nextButton.innerHTML = data.message;
        }

      })
    })

    this.prevButton.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      this.step = this.prevStep
      this.nextButton.classList.remove('btn-success');
      this.nextButton.removeAttribute('disabled')
      this.currentStep.mounted();
    })
  },


  get nextStep() {
    if (this.current < this.steps.length) {
      return this.current + 1;
    }
  },

  get prevStep() {
    if (this.current > 0) {
      return this.current - 1;
    }
  },

  get stepDescription() {
    switch (this.current) {
      case 0:
        return "Hesabat məlumatlarının daxil edilməsi";
      case 1:
        return "Nəticə ilə tanış olmaq";
      case 2:
        return "Aralıq cədvəlin hazılanması";
      case 3:
        return "Məlumatların nəzərdən keçirilməsi və yadda saxlanılması";
      default:
        return null;

    }
  },

  set step(s) {
    if (this.current < this.steps.length) {
      this.currentStep.destroy();
      this.current = s;
      this.render("stepDescription")
    }

    if(this.current == this.steps.length - 1) {
      this.nextButton.id = "save-report";
      this.nextButton.textContent = "Yadda saxla"
    } else {
      this.nextButton.textContent = "Növbəti"
    }

    if (this.current > 0) {
      // this.prevButton.classList.remove("invisible")
    } else {
      // this.prevButton.classList.add("visible")
    }
  },

  async tryOrThrow(callback, message = null, loading = false) {
    try {
      return await callback()
    } catch (e) {
      this.exception = message || `${steps.current + 1} nömrəli mərhələdə xəta baş verdi:<br>${e.message || e.responseJSON.message || (`${e.status}: ${e.statusText}`)}`;
    } finally {

    }
  },

  async formCheck(callback) {
    this.tryOrThrow(async () => {
      this.exception = null
      const form = document.forms[`${this.currentStep.id}-form`]
      if (form) {
        const formData = new FormData(form)
        const ff = Object.fromEntries(formData)
        this.currentStep.data = { ...this.currentStep.data, ...ff }
        
        if (!this.currentStep.validity() && !form.checkValidity()) {
          try {
            form.reportValidity();
          } catch (e) {

          } finally {
            throw new Error("Form elementləri tam doldurulmayıb. * ilə işarələnmiş xanalara tələb olunan dəyərlər daxil edilməlidir.")
          }
        }
      }
      
      await callback()
    })
  },

  render(field) {
    document.getElementById(field).innerHTML = this[field]
  }
}

const reports = new Proxy(steps, {
  get(t, n, r) {
    t.tryOrThrow(() => {
      if (typeof t[n] == 'function')
        return t[n]();
      else return t[n];
    })
  },

})

function getSelectValues(select) {
  var result = [];
  var options = select && select.options;
  var opt;

  for (var i = 0, iLen = options.length; i < iLen; i++) {
    opt = options[i];
    const insertData = opt.value || opt.text
    if (opt.selected && insertData) {
      result.push(insertData);
    }
  }
  return result;
}

const changeState = (e, field, data = null, multiple = false) => {
  let value = e?.value || e?.target?.value || e?.target?.textContent || e
  if (value) {
    state.catchError = null

    if (multiple) {
      value = getSelectValues(e)
    }

    if (data) {
      value = {
        ...data,
        data: value
      }
    }

    state[field] = value
  }
}

document.addEventListener("DOMContentLoaded", () => {
  if (reports.mounted) {
    reports.mounted()
  }
})
