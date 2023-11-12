<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Dashboard extends BaseController
{
	public function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
        $this->set("vendorScripts", [
			"vendor/codemirror/codemirror.js",
			"vendor/codemirror/sql/sql.js",
			"vendor/codemirror/match-brackets.js",
			"vendor/codemirror/hint.js",
			"vendor/codemirror/sql/hint.js",
		])
			->set("vendorStyles", [
				"vendor/codemirror/codemirror.css",
			])
			->set("scripts", [
				"js/steps.js"
			])
			->set("styles", [
				"css/folder.css"
			]);
	}

	public function create()
	{
		if (isPostRequest()) {
			try {
				$data = $this->report->run(post('database'), post('sql'));
				if(post('run_or_save') == 'on' && post("step") == 0) {
					if(empty($data)) {
						echo BaseResponse::ok("Hesabatın nəticəsi boşdur", $data, StatusCodes::HTTP_NO_CONTENT);
					} else {
						echo BaseResponse::ok("Hesabatın nəticəsi uğurludur", $data);
					}
				} else {
					$this->saveReport($data);
				}
			} catch(Exception $e) {
				echo BaseResponse::error("Hesabatın icra edilməsi zamanı xəta baş verdi! " . $e->getMessage(), $e->getCode());
			} finally {
				exit;
			}
		}

		$this->set("vendorScripts", [
			"vendor/codemirror/codemirror.js",
			"vendor/codemirror/sql/sql.js",
			"vendor/codemirror/match-brackets.js",
			"vendor/codemirror/hint.js",
			"vendor/codemirror/sql/hint.js",
		])
			->set("vendorStyles", [
				"vendor/codemirror/codemirror.css",
			])
			->set("scripts", [
				"js/steps.js"
			])
			->set("styles", [
				"css/folder.css"
			]);



		$data['folders'] = $this->folder->list();
		$data['crons'] = $this->cron->list();

		$this->page("reports/create", $data);
	}


	private function saveReport($data)
	{
		$folder = APPPATH . "reports";
		$report_folder = $folder . DIRECTORY_SEPARATOR . preg_replace("/\s*>\s*/", DIRECTORY_SEPARATOR, post('folder_name'));
		$report_name = preg_replace("/[^a-zA-Z0-9-_]/m", "_", post("name"));
		try {
			if(!empty($data)) {
				$csv = $report_folder . DIRECTORY_SEPARATOR . $report_name. ".csv";
				$header = array_keys($data[0]);

				// https://stackoverflow.com/a/2303377
				if(!file_exists($report_folder)) 
					mkdir($report_folder, 0777, true);
				
				$fp = fopen($csv, 'w');

				fputcsv($fp, $header);

				if($fp) {
					foreach($data as $d) {
						fputcsv($fp, $d);
					}
					echo BaseResponse::ok("Hesabat yaradıldı!", $csv);
				} else {
					throw new Exception($csv . " adlı fayl yaradıla bilmədi. İcazə parametlərinə nəzər yetirin.", 403);
				}
				
	
				return false;
			} else {
				throw new Exception("Nəticə boşdur", 400);
			}
		}
		catch (Exception $e) {
			echo BaseResponse::error("Hesabatın yaradılması zamanı xəta baş verdi! " . $e->getMessage(), $e->getCode());
		} finally {
			exit;
		}
	}
}
