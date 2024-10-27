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
				"title" => $post['title'],
				"report_id" => $report_id,
				"access" => $post["access"]
			]);
			

			foreach($post['charts'] as &$value) {
				$value['page_id'] = $page_id;
			}
			// dd($value);
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

		$report = $this->report->get($report_id);

		if($report['report_table']) {
			$result = $this->report->getReportData($report['report_table'], 1000, json_decode($report['params'] ?? '[]', 1),array_keys(json_decode($report['fields_map'] ?? '[]', 1)), $this->input->get());
			
		}

		$data['result'] = $result ?? [];
		echo BaseResponse::ok("Success", $result);
		exit;

		$this->page("create", $data, false);
	}

	public function update($page_id) {
		if(isPostRequest()) {
			$post = json_decode(file_get_contents("php://input"), 1);
			$this->load->model("Page_model", "page");
			$this->load->model("Chart_model", "chart");

			$page_id = $this->page->update($page_id, [
				"title" => $post['title'],
				"access" => $post["access"]
			]);
			
			$data = $this->chart->updateBatch("id", $post['charts']);
			echo BaseResponse::ok("success", $data);
			exit;
		}
	}

}
