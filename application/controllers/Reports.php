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
		$this->load->model("Cron_model", "cron");
		$this->load->model("Query_model", "query");
		$this->load->model("File_model", "file");
		$this->load->model("Cron_model", "cron");
	}

	public function index()
	{
		$data['reports'] = $this->report->list();
		$this->page("reports/index", $data);
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
		try {
			if(!empty($data)) {
				$cronJob = $this->cron->getByJob(post('cron_job'));
				if(empty($cronJob)) {
					$cronJob = $this->cron->insert([
						"job" => post('cron_job'),
						"title" => post("cron_title")
					]);
				} else {
					$cronJob = $cronJob["id"];
				}
				
				$query = $this->query->insert([
					"sql" => post("sql"),
					"db" => post("database"),
					"cron_id" => $cronJob
				]);

				$report = $this->report->insert([
					"name" => post("name"),
					"query_id" => $query,
					"folder_id" => post("report_folder")
				]);

				// File operation
				$folder = APPPATH . "reports";
				$report_folder = $folder . DIRECTORY_SEPARATOR . preg_replace("/\s*>\s*/", DIRECTORY_SEPARATOR, post('folder_name'));
				$report_name = preg_replace("/[^a-zA-Z0-9-_]/m", "_", post("name"));
				$csv = $report_folder . DIRECTORY_SEPARATOR . $report_name. $report .  ".csv";
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
					
					$file = $this->file->insert([
						"query_id" => $query,
						"name" => $report_name,
						"location" => $report_folder
					]);

					echo BaseResponse::ok("Hesabat yaradıldı!", $file . "::" . $csv);
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
