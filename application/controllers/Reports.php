<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Reports extends BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Report_model", "report");
		$this->load->model("Folder_model", "folder");
	}

	public function create()
	{
		if (isPostRequest()) {
			header("Content-Type: application/json");
			switch (post('step')) {
				case 0:
					return $this->stepOne();
				case 3:
					return $this->saveReport(0);
			}

			exit;
		}

		$this->set("vendorScripts", [
			"vendor/codemirror/codemirror.js",
			"vendor/codemirror/sql/sql.js",
			"vendor/codemirror/match-brackets.js",
			"vendor/codemirror/hint.js",
			"vendor/codemirror/sql/hint.js",
			"vendor/pivottable/plotly.js",
			"vendor/pivottable/pivottable.js",
			"vendor/apexcharts/apexcharts.js",
			"vendor/pivottable/apex.js",
		])
			->set("vendorStyles", [
				"vendor/codemirror/codemirror.css",
				"vendor/codemirror/hint.css",
				"vendor/pivottable/pivot.css"
			])
			->set("scripts", [
				"js/steps.js"
			])
			->set("styles", [
				"css/folder.css"
			]);



		$data['folders'] = $this->folder->get_folders();

		$this->page("reports/create", $data);
	}


	private function stepOne()
	{
		echo json_encode($this->report->getQuery(post("database"), post("sql")));
	}

	private function saveReport() {
		dd(post());
	}
}
