<?php
require_once APPPATH . "custom/BaseController.php";

class Dashboard extends BaseController
{
	protected $view = "dashboards";

	public function __construct()
	{
		parent::__construct();
		// autorize();
		if (ENVIRONMENT != 'development') {
			$username = strtolower($_SESSION['user_login']);
			if (!in_array($username, ['khanalid', 'shahriyara', 'alasgara', 'abbasm', 'zahraag'])) {
				die("Sizin icazəniz yoxdur");
			}
		}
	}

	public function index()
	{
		$this->page("dashboards/index", []);
	}

	public function create($report_id)
	{
		$this->load->model("Report_model", "report");

		if (isPostRequest()) {

			$post = post();
			// dd($post);
			$this->load->model("Page_model", "page");
			$this->load->model("Chart_model", "chart");

			$page_id = $this->page->insert([
				'title' => $post['title'],
				// "template" => $this->input->get('template'),
				"report_id" => $report_id,
				"access" => $post['access'] ?? []
			]);


			foreach ($post['charts'] as &$value) {
				$value['page_id'] = $page_id;
				unset($value['id']);
			}

			$data = $this->chart->insertBatch($post['charts']);
			echo BaseResponse::ok("success", $data);
			exit;
		}

		$this->set("styles", [
			"css/folder.css"
		])
			->set("vendorStyles", [
				"vendor/pivottable/pivot.css",
				"vendor/datatables/datatables.css",
				"vendor/gridstack/gridstack.css",
				"vendor/select2/select2.css",
			])
			->set("vendorScripts", [
				"vendor/datatables/datatables.js",
				"vendor/pivottable/pivottable.js",
				"vendor/gridstack/gridstack.js",
				"vendor/chart.js/chart.umd.js",
				"vendor/select2/select2.js",
			])
			->set("vendorScripts", [
				"js/functions.js",
				"js/charts/chart-js.js"
			])
			->set("scripts", [
				// "js/chart-visual.js",
				"js/functions.js",
				// "js/dashboard-create.js",
			]);

		$this->set("vendorScripts", [
			"vendor/codemirror/codemirror.js",
			"vendor/codemirror/autorefresh.js",
			"vendor/codemirror/match-brackets.js",
			"vendor/codemirror/js/javascript.js",
		])
			->set("vendorStyles", [
				"vendor/codemirror/codemirror.css",
			]);

		$this->title = "Səhifə yaradılması";

		$report = $this->report->get($report_id);

		if ($report['report_table']) {
			$result = $this->report->getReportData($report['report_table'], 100, json_decode($report['params'] ?? '[]', 1) ?? [], array_keys(json_decode($report['fields_map'] ?? '[]', 1) ?? []), $this->input->get());
		}
		$fieldsMap = json_decode($report['fields_map'], 1);
		$data['columns'] = array_values(array_filter($this->report->columns($report['report_table']), function ($item) {
			return !preg_match('/[_]*id/ui', $item['column_name']);
		}));
		$data['filters'] = $this->report->getFieldDistinctValues($report['report_table'], ($fieldsMap));
		// dd($data);
		$data['result'] = $result ?? [];
		$data['report'] = $report ?? [];
		// dd($data['report']);
		// echo BaseResponse::ok("Success", $data['result']);
		$this->page("create", $data, false);
	}

	public function templates()
	{
		echo BaseResponse::ok("Success", ["view" => $this->view("templates/index", [], true)]);
	}

	public function template($name = "simple")
	{
		echo BaseResponse::ok("Success", ["view" => $this->view("templates/" . $name, [], true)]);
	}

	public function update($page_id)
	{
		if (isPostRequest()) {
			$post = json_decode(file_get_contents("php://input"), 1);
			$this->load->model("Page_model", "page");
			$this->load->model("Chart_model", "chart");
			$this->page->update($page_id, [
				"title" => $post['title'],
				"access" => $post['access'] ?? []
			]);
			$updated = [];
			$inserted = [];
			$deleted = [];
			$currentCharts = $this->chart->findByPageId($page_id);
			$deleted = [];
			$newCharts = array_filter($post['charts'] ?? [], function ($chart) {
				return $chart['id'];
			});

			if (count($newCharts) < count($currentCharts)) {
				$deleted = array_diff(array_column($currentCharts, "id"), array_column($newCharts, "id"));
				foreach ($deleted as $id) $this->chart->delete($id, "id");
			}

			foreach ($post['charts'] as &$value) {
				$value['page_id'] = $page_id;
				if ($value['id']) {
					$updated[] = $value;
				} else {
					unset($value['id']);
					$inserted[] = $value;
				}
			}

			if (count($updated) > 0)
				$this->chart->updateBatch("id", $updated);
			if (count($inserted) > 0)
				$this->chart->insertBatch($inserted);



			echo BaseResponse::ok("success", ["updated" => count($updated), "inserted" => count($inserted), "deleted" => count($deleted)]);
			exit;
		}
	}
}
