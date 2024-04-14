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

	public function get($id) {
		$data['page'] = $this->page->get($id);
		dd($data['page']);
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

			$data = $this->chart->insertBatch($post);
			echo BaseResponse::ok("success", $data);
			exit;
		}

		$this->page("create", $data);
	}


	public function templates() {
		echo BaseResponse::ok("Success", ["view" => $this->view("templates/index", [], true)]);
	}

	public function template($name = "simple") {
		echo BaseResponse::ok("Success", ["view" => $this->view("templates/" . $name, [], true)]);
	}

}
