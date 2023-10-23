<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Reports extends BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Report_model", "report");
	}

	public function create()
	{
		if (isPostRequest()) {
			header("Content-Type: application/json");
			switch (post('step')) {
				case 0:
					return $this->stepOne();
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
			"vendor/pivottable/pivottable.js"
		])->set("vendorStyles", [
			"vendor/codemirror/codemirror.css",
			"vendor/codemirror/hint.css",
			"vendor/pivottable/pivot.css"
		])
			->set("scripts", [
				"js/steps.js"
			]);



		$this->page("reports/create");
	}


	private function stepOne()
	{
		echo json_encode($this->report->getQuery(post("database"), post("sql")));
	}
}
