const api = axios.create({
	withCredentials: true,
});

const uiContext = {
	loadingState: false,
	successState: null,
	errorState: null,
	setUi(field, value) {
		console.log(this[field], field, value);
		this[field] = value;
	},
	set loading(state) {
		this.loadingState = state;
	},
	set success(state) {
		Toastify({
			text: state,
			className: "toastify-success",
		}).showToast();
		this.successState = state;
	},
	set error(state) {
		Toastify({
			text: state,
			className: "toastify-error",
		}).showToast();
		this.errorState = state;
	},
};

const setUi = uiContext.setUi.bind(uiContext);

const folderListFetch = async () => {
	return catchAsync({
		endpoint: Endpoint.get(services.folder_list).url,
		method: "get",
	});
};

const auth = async ({ onSuccess, onError }) => {
	return catchAsync({
		endpoint: Endpoint.get(services.auth).url,
		method: "get",
		onSuccess,
		onError,
	});
};

const user = async ({ onSuccess }) => {
	return catchAsync({
		endpoint: Endpoint.get(services.user).url,
		method: "get",
		onSuccess,
	});
};

const group = async ({ onSuccess }) => {
	return catchAsync({
		endpoint: Endpoint.get(services.group).url,
		method: "get",
		onSuccess,
	});
};

const dbListFetch = async ({}) => {
	return catchAsync({
		endpoint: Endpoint.get(services.db_list).url,
		method: "get",
	});
};

const cronListFetch = async ({}) => {
	return catchAsync({
		endpoint: Endpoint.get(services.cron_list).url,
		method: "get",
	});
};

const getCsv = async ({ location, onSuccess }) => {
	return catchAsync({
		endpoint: Endpoint.get(services.csv).params({ location }).url,
		method: "get",
		onSuccess,
	});
};

const reportListFetch = async ({ onSuccess }) => {
	return catchAsync({
		endpoint: Endpoint.get(services.report_list).url,
		method: "get",
		onSuccess,
	});
};

const dashboardCreate = async ({ id, data, onSuccess }, isUpdate = false) => {
	return catchAsync({
		endpoint: Endpoint.get(
			isUpdate ? services.dashboard_update : services.dashboard_create
		).params({ id }).url,
		method: "post",
		data,
		onSuccess,
	});
};

const dashboardDelete = async ({ id, onSuccess }) => {
	if (!id) return;

	return catchAsync({
		endpoint: Endpoint.get(services.page_delete).params({ id }).url,
		method: "get",
		onSuccess,
	});
};

const reportGet = async ({ id, onSuccess }) => {
	if (!id) return;

	return catchAsync({
		endpoint: Endpoint.get(services.report_get).params({ id }).url,
		method: "get",
		onSuccess,
	});
};

const reportRun = async ({ id, query, onSuccess }) => {
	if (!id) return;

	return catchAsync({
		endpoint: Endpoint.get(services.report_run).params({ id }).query(query).url,
		method: "post",
		onSuccess,
	});
};

const reportDelete = async ({ id, onSuccess }) => {
	if (!id) return;

	return catchAsync({
		endpoint: Endpoint.get(services.report_delete).params({ id }).url,
		method: "get",
		onSuccess,
	});
};

const getReportData = async ({ id, onSuccess }) => {
	if (!id) return;

	return catchAsync({
		endpoint: Endpoint.get(services.dashboard_create).params({ id }).url,
		method: "get",
		onSuccess,
	});
};

const dashboardGet = async ({ id, onSuccess, filter }) => {
	if (!id) return;
	return catchAsync({
		endpoint: Endpoint.get(services.page_get).params({ id }).query(filter).url,
		method: "get",
		onSuccess,
	});
};

const pageListFetch = async ({ onSuccess }) => {
	return catchAsync({
		endpoint: Endpoint.get(services.page_list).url,
		method: "get",
		onSuccess,
	});
};

const catchAsync = async ({
	endpoint,
	params,
	headers,
	data,
	method,
	onSuccess,
	onError,
	onFinally,
}) => {
	setUi("loading", true);
	try {
		method = String(method).toLowerCase();
		if (!["post", "put", "patch", "delete", "get"].includes(method)) {
			throw new Error("Method doesn't allowed");
		}

		const request = await fetch(endpoint, {
			method,
			body: data instanceof Function ? data() : data,
			headers,
			credentials: "include",
		});

		const response = await request.json();

		if (!request.ok) {
			throw new Error(response.message);
		}

		if (method != "get") setUi("success", "Əməliyyat yerinə yetirildi");

		onSuccess instanceof Function && (await onSuccess({ data: response }));
		return response.data;
	} catch (e) {
		setUi("error", e.message);
		onError instanceof Function && onError();
	} finally {
		setUi("loading", false);
		onFinally instanceof Function && onFinally();
	}
};
