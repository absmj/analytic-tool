<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Reports extends BaseController {
	public function __construct()
	{
		parent::__construct();
	}
	
	public function create()
	{
		$this->set("vendorScripts", [
			"vendor/codemirror/codemirror.js",
			"vendor/codemirror/sql/sql.js",
			"vendor/codemirror/match-brackets.js",
			"vendor/codemirror/hint.js",
			"vendor/codemirror/sql/hint.js",
			])->set("vendorStyles", [
				"vendor/codemirror/codemirror.css",
				"vendor/codemirror/hint.css"
			])
			->set("scripts", [
				"js/steps.js"
			]);
		
		$this->page("reports/create");
	}
}
