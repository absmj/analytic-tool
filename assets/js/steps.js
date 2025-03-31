const queryFormModal = new bootstrap.Modal(
	document.getElementById("queryparams")
);
const table = document.getElementById("table-data");
const paramsForm = $('[name="queryparams-form"]');
const editor = CodeMirror.fromTextArea(document.getElementById("sql"), {
	mode: "sql",
	indentWithTabs: true,
	smartIndent: true,
	lineNumbers: true,
	matchBrackets: true,
	autofocus: true,
	extraKeys: { "Ctrl-Space": "autocomplete" },
	hintOptions: {
		tables: {
			users: ["name", "score", "birthDate"],
			countries: ["name", "population", "size"],
		},
	},
});

// const steps = {
//   current: 0,
//   maxStage: 4,
//   error: null,
//   nextButton: null,
//   prevButton: null,
//   steps: [
//     {
//       data: {
//         name: null,
//         type: 'off',
//         database: null,
//         sql: null,
//         params: new Set
//       },
//       id: 'stepone',
//       container: null,

//       params(p) {
//         this.data.params.add(p)
//         Array.from(this.data.params).forEach(p => {
//           queryForm.append(`
//             <div class="mb-3">
//               <label for="${p}" class="col-form-label">${p}:</label>
//               <pe="text" class="form-control" required name="params[]">
//             </div>
//           `)
//         })
//       },

//       mounted(formCheck) {
//         if (!this.container) {
//           this.container = document.getElementById(this.id)
//           const editor = CodeMirror.fromTextArea(document.getElementById('sql'), {
//             mode: 'sql',
//             indentWithTabs: true,
//             smartIndent: true,
//             lineNumbers: true,
//             matchBrackets: true,
//             autofocus: true,
//             extraKeys: { "Ctrl-Space": "autocomplete" },
//             hintOptions: {
//               tables: {
//                 users: ["name", "score", "birthDate"],
//                 countries: ["name", "population", "size"]
//               }
//             }
//           });

//           editor.setValue(currentSql || '')

//           editor.on('blur', () => {
//             this.data.sql = editor.getValue()
//           })
//         } else {
//           this.container.classList.remove("d-none")
//         }

//       },

//       destroy() {
//         if (this.container)
//           this.container.classList.add("d-none")
//       },

//       async nextStage(callback, step) {
//         const data = await this.create(() => {
//           callback();
//         }, step)

//         return data
//       },

//       async create(callback, step = 0) {
//         const response = await $.post(`/reports/${isEdit ? `edit/${reportId}` : 'create'}`, { ...this.data, step });
//         callback();
//         return response;
//       }
//     },

//     // Step 2
//     {
//       id: 'steptwo',
//       data: null,
//       that: null,
//       container: null,
//       generateTable() {
//         const table = document.getElementById("table-data")
//         table.innerHTML = (`<table class='table table-hover'><thead><tr>${Object.keys(this.data[0]).map(th => `<th>${th}</th>`).join("")}</tr></thead><tbody>${this.data.map(td => `<tr>${Object.values(td).map(v => `<td>${v}</td>`).join("")}</tr>`).join("")}</tbody><table>`)
//       },

//       destroy() {
//         if (this.container)
//           this.container.classList.add("d-none")
//       },

//       nextStage(callback) {
//         return this.that.steps[0].nextStage(callback, 1)
//       },

//       mounted(that) {
//         if (!this.container) {
//           this.container = document.getElementById(this.id)
//           this.generateTable()
//         }
//         this.that = that;
//         this.container.classList.remove("d-none")
//       },
//     }

//   ],

//   get lastStep() {
//     return this.steps.length
//   },

//   get report() {
//     return {
//       ...this.steps[0].data,
//       chart: JSON.stringify(decycle({
//         data: this.steps[2].chart.data,
//         options: this.steps[2].chart.options
//       }))
//     }
//   },

//   get currentStep() {
//     return this.steps?.[this.current]
//   },

//   get prevStep() {
//     return this.steps?.[this.current - 1]
//   },

//   set exception(e) {
//     this.error = e;
//     if (e) {
//       uiInterface.error = e
//       throw new Error(e);
//     }

//   },

//   mounted() {
//     this.currentStep.mounted()
//     this.render("stepDescription")
//     this.nextButton = document.getElementById(`next`)
//     this.prevButton = document.getElementById(`prev`)
//     this.errorElement = document.getElementById("error")

//     const fCheck = () => this.formCheck(async () => {
//       const runOrSave = this.currentStep.data['run_or_save'] == 'on';
//       if (this.current == this.steps.length - 1) {
//         return;
//       }

//       this.nextButton.setAttribute('disabled', true)
//       this.nextButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
//       const data = await this.currentStep.nextStage(() => {
//         if(runOrSave) {
//           this.nextButton.removeAttribute('disabled')
//           this.step = this.nextStep
//           this.nextButton.innerHTML = 'Yadda saxla'
//         }
//       }, this.current);

//       if(runOrSave) {
//         this.currentStep.data = data.data
//         this.currentStep.mounted(this);
//       } else {
//         this.nextButton.classList.add("btn-success")
//         this.nextButton.innerHTML = data.message;
//         uiInterface.success = true
//       }
//     })

//     document.getElementById('run-query').addEventListener('click', () => {
//       fCheck()
//     })

//     this.nextButton.addEventListener('click', (e) => {

//       console.log(this.current)
//       e.preventDefault();
//       e.stopPropagation();
//       console.log(this.currentStep)
//       if(this.current == 0 && this.currentStep.data.params.size > 0) {
//         queryFormModal.show();
//         return;
//       }

//       fCheck()
//     })

//     this.prevButton.addEventListener('click', (e) => {
//       e.preventDefault();
//       e.stopPropagation();
//       this.step = this.prevStep
//       this.nextButton.classList.remove('btn-success');
//       this.nextButton.removeAttribute('disabled')
//       this.currentStep.mounted();
//     })
//   },

//   get nextStep() {
//     if (this.current < this.steps.length) {
//       return this.current + 1;
//     }
//   },

//   get prevStep() {
//     if (this.current > 0) {
//       return this.current - 1;
//     }
//   },

//   get stepDescription() {
//     switch (this.current) {
//       case 0:
//         return "Hesabat məlumatlarının daxil edilməsi";
//       case 1:
//         return "Nəticə ilə tanış olmaq";
//       case 2:
//         return "Aralıq cədvəlin hazılanması";
//       case 3:
//         return "Məlumatların nəzərdən keçirilməsi və yadda saxlanılması";
//       default:
//         return null;

//     }
//   },

//   set step(s) {
//     if (this.current < this.steps.length) {
//       this.currentStep.destroy();
//       this.current = s;
//       this.render("stepDescription")
//     }

//     if(this.current == this.steps.length - 1) {
//       this.nextButton.id = "save-report";
//       this.nextButton.textContent = "Yadda saxla"
//     } else {
//       this.nextButton.textContent = "Növbəti"
//     }

//     if (this.current > 0) {
//       // this.prevButton.classList.remove("invisible")
//     } else {
//       // this.prevButton.classList.add("visible")
//     }
//   },

//   async tryOrThrow(callback, message = null, loading = false) {
//     try {
//       uiInterface.loading = true
//       return await callback()
//     } catch (e) {
//       this.exception = message || `${steps.current + 1} nömrəli mərhələdə xəta baş verdi:<br>${e.message || e.responseJSON.message || (`${e.status}: ${e.statusText}`)}`;
//     } finally {
//       uiInterface.loading = false
//     }
//   },

//   async formCheck(callback) {
//     this.tryOrThrow(async () => {
//       this.exception = null
//       const form = document.forms[`${this.currentStep.id}-form`]
//       const queryParamForm = document.forms[`queryparams-form`]
//       if (form && queryParamForm) {
//         const formData = new FormData(form)
//         const ff = Object.fromEntries(formData)
//         const paramsData = new FormData(queryParamForm)
//         const params = Object.fromEntries(paramsData)
//         this.currentStep.data = { ...this.currentStep.data, ...ff, ...params }
//         if (!this.currentStep.validity() && !form.checkValidity() && !queryParamForm.checkValidity()) {
//             form.reportValidity();
//             throw new Error("Form elementləri tam doldurulmayıb. * ilə işarələnmiş xanalara tələb olunan dəyərlər daxil edilməlidir.")
//         }

//       }

//       await callback()
//     })
//   },

//   render(field) {
//     document.getElementById(field).innerHTML = this[field]
//   }
// }

// const reports = new Proxy(steps, {
//   get(t, n, r) {
//     t.tryOrThrow(() => {
//       if (typeof t[n] == 'function')
//         return t[n]();
//       else return t[n];
//     })
//   },

// })

// function getSelectValues(select) {
//   var result = [];
//   var options = select && select.options;
//   var opt;

//   for (var i = 0, iLen = options.length; i < iLen; i++) {
//     opt = options[i];
//     const insertData = opt.value || opt.text
//     if (opt.selected && insertData) {
//       result.push(insertData);
//     }
//   }
//   return result;
// }

// const changeState = (e, field, data = null, multiple = false) => {
//   let value = e?.value || e?.target?.value || e?.target?.textContent || e
//   if (value) {
//     state.catchError = null

//     if (multiple) {
//       value = getSelectValues(e)
//     }

//     if (data) {
//       value = {
//         ...data,
//         data: value
//       }
//     }

//     state[field] = value
//   }
// }

const formReport = {
	data: {
		name: null,
		sql: null,
		report_folder: null,
		cron_job: null,
		database: null,
		params: new Set(),
	},

	get params() {
		return this.data.params;
	},

	set params(p) {
		this.data.params = new Set(p.map((e) => e.replace(/\{@(.*?)@\}/g, "$1")));
		paramsForm.html("");
		Array.from(this.params).forEach((p) =>
			paramsForm.append(`
      <div class="mb-3">
        <label for="${p}" class="col-form-label">${p}:</label>
        <input required name="params[${p}]" type="text" class="form-control" id="${p}">
      </div>`)
		);
	},

	set sql(s) {
		this.data.sql = s;
	},

	validity() {
		if (!this.data.name) throw new Error("Hesabatın adı daxil edilməyib!");

		if (!this.data.sql)
			throw new Error("Hesabatın SQL sorğusu daxil edilməyib!");

		if (
			/(update|insert|delete|truncate|drop|alter|create|revoke|commit|rollback)/imsu.test(
				this.data.sql
			)
		)
			throw new Error(
				window.locale?.form?.report?.["valid-sql"] ||
					"Hesabatın SQL sorğusu düzgün deyil. Bazadan yalnız məlumatların oxunması mümkündür!"
			);

		if (!this.data.report_folder)
			throw new Error(
				window.locale?.form?.report?.["valid-folder"] ||
					"Hesabat qovluğu seçilməyib!"
			);

		if (!this.data.database)
			throw new Error(
				window.locale?.form?.report?.["valid-db"] ||
					"SQL-in işləyəcəyi baza seçilməyib!"
			);

		if (!this.data.cron_job)
			throw new Error(
				window.locale?.form?.report?.["valid-freq"] ||
					"İşləmə tezliyi Fərdi olaraq seçilibsə, CRON job seçilməlidir!"
			);

		if (!this.data.report_table)
			throw new Error(
				window.locale?.form?.report?.["valid-table-name"] ||
					"Hesabatın cədvəl adı daxil edilməyib"
			);

		return true;
	},

	async run(e) {
		try {
			const forms = {
				report: document.forms[`report-form`],
				query: document.forms[`queryparams-form`],
				report_options: document.forms[`report_options`],
			};

			const formData = new FormData(forms.report);
			const ff = Object.fromEntries(formData);
			const paramsData = new FormData(forms.query);
			const params = Object.fromEntries(paramsData);
			const reportOptions = new FormData(forms.report_options);
			const rOpts = Object.fromEntries(reportOptions);
			const fields = new Object();
			$("[data-target='fields']").each(function () {
				fields[$(this).attr("data-field")] = $(this).val();
			});
			this.data = { ...this.data, ...ff, ...params, ...rOpts, fields };

			this.validity();

			if (!forms.report.checkValidity()) {
				forms.report.reportValidity();
				throw new Error(
					window.locale?.["form-valid"] ||
						"Form elementləri düzgün doldurulmayıb"
				);
			}
			const step = $(e).attr("data-step");

			if (this.params.size > 0 && !queryFormModal._isShown && step == 0) {
				queryFormModal.show();
				return;
			}

			if (!forms.query.checkValidity()) {
				forms.query.reportValidity();
				throw new Error(
					window.locale?.["form-valid-sql-params"] ||
						"SQL parametrləri daxil edilməyib"
				);
			}

			if (queryFormModal._isShown) queryFormModal.hide();

			$(e).prop("disabled", true);
			uiInterface.loading = true;

			const response = await $.post(
				`/reports/${isEdit ? `edit/${reportId}` : "create"}`,
				{ ...this.data, step }
			);

			if (step == 0) {
				this.generateTable(response.data);
				$(e).attr("data-step", 1);
				$("#next").attr("data-step", 1);
				$("#stepone").addClass("d-none");
				$("#steptwo").removeClass("d-none");
			} else {
				alert(response.message);
				window.location.href = BASE_URL + "reports";
			}
		} catch (e) {
			uiInterface.error = e?.responseJSON?.message || e.message;
		} finally {
			$(e).prop("disabled", false);
			uiInterface.loading = false;
		}
	},

	generateTable(data) {
		table.innerHTML = `
    <h5>Sütunlar</h5>
    <p class='text-muted'>${
			window.locale?.["form.report.save-report-filter"] ||
			"Filterasiya zamanı tətbiq ediləcək sütunlar"
		}</p>
    <small>${
			window.locale?.["form.report.save-report-filter-desc"] ||
			"Hər bir sütunun solundakı seçimi etməklə, unikal sütunu təyin edə bilərsiniz. Bu sütun cədvəldə data-ların yenilənməsi üçün təyin edilib."
		}</small>
    <hr>
    <div class="row">
    ${data
			.map(
				(d) => `<div class="col-md-3">
                        <div class="form-check position-relative">
                            <button type="button" onclick="this.parentNode.remove()" style="right:0" class="position-absolute btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            <input type="radio" class="form-check-input" name="unique" value="${d}">
                            <input data-field="${d}" value="${d}" type="text" class="form-control form-check-label" data-target="fields" required>
                        </div>
                      </div>`
			)
			.join("")}

    </div>
    `;
	},

	init() {
		editor.on("blur", (e) => {
			this.sql = editor.getValue();

			const params = /{@(.*?)@}/gims;

			if (params.test(this.data.sql)) {
				this.params = this.data.sql.match(/{@(.*?)@}/gims);
			} else {
				this.params = [];
			}
		});
	},
};

document.addEventListener("DOMContentLoaded", () => {
	formReport.init();
});
