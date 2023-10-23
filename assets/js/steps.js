let chart;
function prepareDataFunction(rawData) {
  console.log(rawData)
  var result = {};
  var labels = [];
  var data = [];
  for (var i = 0; i < rawData.data.length; i++) {
      var record = rawData.data[i];
      if (record.c0 == undefined && record.r0 !== undefined) {
          var _record = record.r0;
          labels.push(_record);
      }
      if (record.c0 == undefined & record.r0 == undefined) continue;
      if (record.v0 != undefined) {
          data.push(!isNaN(record.v0) ? record.v0 : null);
      }
  }
  result.labels = labels;
  result.data = data;
  return result;
}

function drawChart(rawData) {
  var data = prepareDataFunction(rawData);
  var data_for_charts = {
      datasets: [{
          data: data.data,
          backgroundColor: [
              "#FF6384",
              "#4BC0C0",
              "#FFCE56",
              "#E7E9ED",
              "#36A2EB",
              "#9ccc65",
              "#b3e5fc"

          ]
      }],
      labels: data.labels
  };
  options = {
      responsive: true,
      legend: {
          position: 'right',
      },
      title: {
          display: true,
          fontSize: 18,
          text: 'Profit by Countries'
      },
      scale: {
          ticks: {
              beginAtZero: true
          },
          reverse: false
      },
      animation: {
          animateRotate: false,
          animateScale: true
      }
  };
  var ctx = document.getElementById("chart").getContext("2d");
  chart = new Chart(ctx, {
      data: data_for_charts,
      type: 'bar',
      options: options
  });
}

function updateChart(rawData) {
  // document.getElementById( "chart" ).remove();     
  // let canvas = document.createElement('canvas');     
  // canvas.setAttribute('id','chart');         
  // document.querySelector('#chart-container').appendChild(canvas);
  chart.destroy();
  drawChart(rawData);

  console.log(rawData)
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

        if (!this.data.sql)
          throw new Error("SQL-in işləyəcəyi baza seçilməyib!");

        return true;
      },

      async nextStage(callback) {
        const data = await this.create(() => {
          callback();
        })

        return data
      },

      async create(callback) {
        const response = await $.post("/reports/create", { ...this.data, step: this.current });
        callback();
        return response;
      }
    },

    // Step 2
    {
      id: 'steptwo',
      data: null,
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
        callback();
        return this.data
      },

      mounted() {
        if (!this.container) {
          this.container = document.getElementById(this.id)
          this.generateTable()
        }

        this.container.classList.remove("d-none")
      },
    },

    // Step 3
    {
      id: "stepthree",
      container: null,
      data: null,
      pivot: null,
      chart: null,

      generatePivotTable() {
        this.pivot = new WebDataRocks({
          container: "#pivot",
          toolbar: true,
          report: {
            data: this.data
          },
          reportcomplete: () => {
            this.pivot.off("reportcomplete");
            this.createPolarChart();
          },
          reportchange: () => {
            this.updatePolarChart();
          }
        });

        
      },

      createPolarChart() {

        const slice = {
          "rows": this.pivot.getRows(),
          "columns": this.pivot.getColumns(),
          "measures": this.pivot.getMeasures()
        };

        console.log(webdatarocks.getData({
            slice
        }, drawChart, updateChart));
      },

      updatePolarChart() {
        const slice = {
          "rows": this.pivot.getRows(),
          "columns": this.pivot.getColumns(),
          "measures": this.pivot.getMeasures()
        };

        console.log(slice)
        webdatarocks.getData({
            slice
        }, updateChart);
      },

      destroy() {
        if (this.container)
          this.container.classList.add("d-none")
      },

      mounted() {
        if (!this.container) {
          this.container = document.getElementById(this.id)
          this.generatePivotTable()
        }

        this.container.classList.remove("d-none")
      }
    }

  ],

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

        const data = await this.currentStep.nextStage(() => {
          this.step = this.nextStep
        });

        this.currentStep.data = data
        this.currentStep.mounted();
      })
    })

    this.prevButton.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      this.step = this.prevStep
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
        return "Çart və ya diaqramların hazırlanması";
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

    if (this.current > 0) {
      this.prevButton.classList.remove("d-none")
    } else {
      this.prevButton.classList.add("d-none")
    }
  },

  async tryOrThrow(callback, message = null, loading = false) {
    try {
      return await callback()
    } catch (e) {
      this.exception = message || `${steps.current + 1} nömrəli mərhələdə xəta baş verdi:<br>${e.message || (`${e.status}: ${e.statusText}`)}`;
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
