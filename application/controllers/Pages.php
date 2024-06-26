<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Pages extends BaseController
{
	protected $view = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->model("Page_model", "page");
		$this->title = "Səhifələr";
	}

	public function index()
	{
		$data['pages'] = $this->page->list();
		$this->page("index", $data);
	}

	public function get($id)
	{
		$this->load->model("Chart_model", "chart");
		$page = $this->page->get($id);
		
		$charts = $this->chart->findByPageId($page['id']);
		$data['charts'] = $charts;
		$data['page'] = $page;
		if($page['file_id']) {
			$data['report'] = csv2json(file_get_contents($page['location']));
		}


		$rows = [];
		foreach ($charts as &$chart) {
			$chart['row_index'] = (int)$chart['row_index'];
			// var_dump($chart['row_index']);
			if (is_int((int)$chart['row_index'])) {
				$rows[$chart['row_index']][] = $chart;
			}
		}

		ksort($rows);
		foreach ($rows as &$row) {
			ksort($row);
		}
		$data['rows'] = $rows;

		$this->set("styles", [
			"css/folder.css"
		])
			->set("vendorStyles", [
				"vendor/pivottable/pivot.css",
				"vendor/datatables/datatables.css"
			])
			->set("vendorScripts", [
				"vendor/datatables/datatables.min.js",
				"vendor/pivottable/pivottable.js"
			])
			->set("vendorScripts", [
				"js/functions.js",
				"js/mock-data.js"
			])
			->set("scripts", [
				"js/chart-visual.js",
			]);

		$this->page("dashboards/templates/" . $page['template'], $data, false);
	}

	public function create($report_id)
	{
		$this->load->model("Folder_model", "folder");
		$this->set("styles", [
			"css/folder.css"
		])
			->set("vendorStyles", [
				"vendor/pivottable/pivot.css",
				"vendor/datatables/datatables.css"
			])
			->set("vendorScripts", [
				"vendor/datatables/datatables.min.js",
				"vendor/pivottable/pivottable.js"
			])
			->set("vendorScripts", [
				"js/functions.js",
				"js/mock-data.js"
			])
			->set("scripts", [
				"js/chart-visual.js",
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

		$data['folders'] = $this->folder->list();

		if (isPostRequest()) {
			$post = json_decode(file_get_contents("php://input"), 1);
			$this->load->model("Page_model", "page");
			$this->load->model("Chart_model", "chart");
			$page_id = $this->page->insert([
				"template" => $this->input->get('template'),
				"report_id" => $report_id
			]);


			foreach ($post as &$value) {
				$value['page_id'] = $page_id;
			}

			$data = $this->chart->insertBatch($post);
			echo BaseResponse::ok("success", $data);
			exit;
		}

		$this->page("create", $data);
	}


	public function templates()
	{
		echo BaseResponse::ok("Success", ["view" => $this->view("templates/index", [], true)]);
	}

	public function template($name = "simple")
	{
		echo BaseResponse::ok("Success", ["view" => $this->view("templates/" . $name, [], true)]);
	}
}
