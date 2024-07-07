<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Dashboard extends BaseController
{
	protected $view = "dashboards";

	public function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
        $this->page("dashboards/index", []);
	}

	public function create($report_id)
	{
		$this->load->model("Report_model", "report");

		if(isPostRequest()) {
			$post = json_decode(file_get_contents("php://input"), 1);
			$this->load->model("Page_model", "page");
			$this->load->model("Chart_model", "chart");
			$page_id = $this->page->insert([
				"template" => $this->input->get('template'),
				"report_id" => $report_id
			]);


			foreach($post as &$value) {
				$value['page_id'] = $page_id;
			}
			// dd($value);
			$data = $this->chart->insertBatch($post);
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
				])			
			->set("vendorScripts", [
				"vendor/datatables/datatables.js",
				"vendor/pivottable/pivottable.js",
				"vendor/gridstack/gridstack.js"
			])
			->set("vendorScripts", [
				"js/functions.js",
				"js/mock-data.js"
			])
			->set("scripts", [
				"js/chart-visual.js",
				"js/template.js",
				"js/dashboard-create.js",
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
		$this->load->model("Query_model", "query");
		$query = $this->report->get($report_id);

		if(!empty($query['sql'])) {
			$result = $this->report->run($query['db'], $query['sql'], json_decode($query['params'], 1));
		}

		$data['result'] = $result ?? [];

		$this->page("create", $data, false);
	}


	public function templates() {
		echo BaseResponse::ok("Success", ["view" => $this->view("templates/index", [], true)]);
	}

	public function template($name = "simple") {
		echo BaseResponse::ok("Success", ["view" => $this->view("templates/" . $name, [], true)]);
	}

}
