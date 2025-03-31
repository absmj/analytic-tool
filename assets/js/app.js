const API = "";

const services = {
	db_list: "reports/db_list",
	cron_list: "cron/list",
	folder_list: "folders/all",
	report_create: "reports/create",
	report_list: "reports/index",
	report_get: "reports/get/:id",
	report_update: "reports/update/:id",
	report_delete: "reports/delete/:id",
	report_run: "reports/run/:id",
	dashboard_create: "dashboard/create/:id",
	dashboard_update: "dashboard/update/:id",
	report_edit: "reports/edit/:id",
	report_delete: "reports/delete/:id",
	page_list: "pages/index",
	page_get: "pages/get/:id",
	page_create: "page/create",
	page_edit: "page/edit/:id",
	page_delete: "pages/delete/:id",
	user: "user/login",
	auth: "user/index",
	group: "user/groups",
	csv: "reports/csv/:location",
};

class Endpoint {
	static instance;
	services;
	endpoint;

	constructor() {}

	set currentEndpoint(url) {
		this.endpoint = url;
	}

	get currentEndpoint() {
		return `${API}/${this.endpoint}`;
	}

	static get(endpoint) {
		if (!this.instance) {
			this.instance = new this();
		}
		this.instance.currentEndpoint = endpoint;
		return this.instance;
	}

	get url() {
		return this.currentEndpoint;
	}

	params(params) {
		let url = this.endpoint;
		for (let [key, value] of Object.entries(params)) {
			const injectParamsValue = new RegExp(`:${key}`, "gm");
			url = url.replace(injectParamsValue, value);
		}
		this.endpoint = url;

		return this;
	}

	query(obj = null) {
		const urlSearch = new URLSearchParams();
		console.log(obj);
		for (let i in obj) {
			if (Array.isArray(obj[i])) urlSearch.append(i, obj[i].join(","));
			else urlSearch.append(i, obj[i]);
		}

		this.endpoint += urlSearch.toString() ? "?" + urlSearch.toString() : "";
		console.log(this.endpoint);
		return this;
	}
}
