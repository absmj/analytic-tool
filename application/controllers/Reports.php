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
		$this->load->model("Job_model", "job");
		$this->load->helper(["pivot_helper", "apex"]);
	}

	public function index()
	{
		$data['reports'] = $this->report->list();
		$this->page("index", $data);
	}

	public function create()
	{
		if (isPostRequest()) {
			try {
				$data = $this->report->run(post('database'), post('sql'), post('params'));
				if(post("step") == 0) {
					if(empty($data)) {
						echo BaseResponse::ok("Hesabatın nəticəsi boşdur", $data, StatusCodes::HTTP_NO_CONTENT);
					} else {
						$keys = array_keys($data[0]);
						$filteredKeys = array_values(array_filter($keys, function($k){ return $k != 'id'; }));
						echo BaseResponse::ok("Hesabatın nəticəsi uğurludur", $filteredKeys);
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
		// dd($data['folders']);
		$data['crons'] = $this->cron->list();

		$this->page("form", $data);
	}

	public function get($report_id) {
		$data['report'] = $this->report->getById($report_id);
	}

	public function edit($report_id) {
		$data['isEdit'] = true;
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
					$this->saveReport($data, $report_id);
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
		$data['report'] = $this->report->get($report_id);
		$data['folders'] = $this->folder->list();

		$data['crons'] = $this->cron->list();
		// dd($data);
		$this->page("form", $data);
	}

	public function delete($report_id) {
		$data = $this->report->update($report_id, ['is_deleted' => true]);
		echo BaseResponse::ok("Hesabat silindi", $data);
	}

	private function saveReport($data, $base = null, $isCron = false, $toFile = false)
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
					"cron_id" => $cronJob,
					"params" => json_encode(post('params')),
					"unique_field" => post("unique"),
					"fields_map" => json_encode(post('fields'))
				]);

				$reportInsert = [
					"name" => post("name"),
					"query_id" => $query,
					"folder_id" => post("report_folder"),
					"base" => $base ?? 0,
				];

				if(!$toFile){
					$reportInsert['report_table'] = camelToSnake(post('report_table'));
				}

				$report = $this->report->insert($reportInsert);

				if($base > 0) {
					$this->report->update($base, ['is_deleted' => true]);
				}

				$job = [
					"query_id" => $query,
					"report_id" => $report,
					"is_cron" => $isCron
				];

				if($toFile) {
					// File operation
					$folder = APPPATH . "reports";
					$report_folder = $folder . DIRECTORY_SEPARATOR . preg_replace("/\s*>\s*/", DIRECTORY_SEPARATOR, post('folder_name'));
					$report_name = post("name")."-".$report;
					$csv = $report_folder . DIRECTORY_SEPARATOR . uniqid() .  ".csv";
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
							"name" => $report_name,
							"location" => $csv,
							"folder_id" => post("report_folder"),
							"type" => "csv"
						]);
						$job['file_id'] = $file;
					} else {
						throw new Exception($csv . " adlı fayl yaradıla bilmədi. İcazə parametlərinə nəzər yetirin.", 403);
					}
				} else {
					$this->report->createOrInsertOrUpdateReport(post('database'),post('report_table'),post('unique'),post("sql"),json_decode(post("params"), 1));
				}

				$this->job->insert($job);
				echo BaseResponse::ok("Hesabat ".($base > 0 ? 'redaktə edildi' : 'yaradıldı')."!");
			} else {
				throw new Exception("Nəticə boşdur", 400);
			}
		}
		catch (Exception $e) {
			echo BaseResponse::error("Hesabatın yaradılması zamanı xəta baş verdi! " . $e->getMessage(), $e->getCode());
		}
	}

	public function restore($id, $old) {
		$restore = $this->report->getById($old);
		$this->report->update($id, ["is_deleted" => true]);
		$report = $this->report->insert([
			"name" => $restore["name"],
			"query_id" => $restore['query'],
			"folder_id" => $restore['folder_id'],
			"base" => $id 
		]);
		echo BaseResponse::ok("Success", $report);
	}
}
