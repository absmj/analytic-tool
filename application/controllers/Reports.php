<?php
require_once APPPATH . "custom/BaseController.php";

class Reports extends BaseController
{
	protected $view = "reports";

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
		$this->load->helper("pivot");
		autorize();
		if (ENVIRONMENT != 'development') {
			$username = strtolower($_SESSION['user_login']);
			if (!in_array($username, ['khanalid', 'shahriyara', 'alasgara', 'abbasm', 'zahraag'])) {
				die("Sizin icazəniz yoxdur");
			}
		}
	}

	public function index()
	{
		$data['reports'] = $this->report->list();
		$this->page("index", $data);
		// echo BaseResponse::ok("Success", $data['reports']);
	}

	public function create()
	{
		if (isPostRequest()) {
			try {
				$data = $this->report->run(post('db'), post('sql'), post('params') ?? []);

				if (post("step") == 0) {
					if (empty($data)) {
						echo BaseResponse::ok("Hesabatın nəticəsi boşdur", $data, StatusCodes::HTTP_NO_CONTENT);
					} else {
						$keys = array_keys($data[0]);
						$filteredKeys = array_values(array_filter($keys, function ($k) {
							return $k != 'id';
						}));
						echo BaseResponse::ok("Hesabatın nəticəsi uğurludur", $filteredKeys);
					}
				} else {
					$this->saveReport($data, null, false, false);
				}
			} catch (Exception $e) {
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


	public function get($report_id)
	{
		$data['report'] = $this->report->get($report_id);
		echo BaseResponse::ok("Hesabatın nəticəsi uğurludur", $data['report']);
	}

	public function run($report_id, $isCron = false)
	{
		$report = $this->report->get($report_id);

		$reportData = $this->report->run($report['db'], $report['sql'], $report['params'] != 'null' ? json_decode($report['params'], 1) : []);

		$csv = tempnam(sys_get_temp_dir(), 'csv');
		$header = array_keys($reportData[0]);
		foreach ($reportData as &$r) {
			foreach ($r as $rk => $v) {
				if (preg_match("/date$/i", $v)) {
					$timestamp = strtotime($v);
					$r[$rk . ".Date"] = date('d', $timestamp);
					$r[$rk . ".Month"] = date('m', $timestamp);
					$r[$rk . ".Year"] = date('Y', $timestamp);
					$r[$rk . ".Timestamp"] = $timestamp;
				}
			}
		}
		$fp = fopen($csv, 'w');
		fputcsv($fp, $header);

		if ($csv) {
			foreach ($reportData as $d) fputcsv($fp, $d);
			$location = $this->report->upload($csv);

			$this->file->update($report['file_id'], ['name' => $location['name'], 'location' => $location['uniqueFileId']]);
		}

		$this->report->deleteFile($report['file']);

		$job = [
			"query_id" => $report['query_id'],
			"report_id" => $report['id'],
			"is_cron" => $isCron
		];
		$this->job->insert($job);
		echo BaseResponse::ok("Success");
	}


	public function delete($report_id)
	{
		$reportData = $this->report->get($report_id);
		if (isset($reportData['file']) || (isset($reportData['file']) && empty($reportData['file']))) {
			try {
				$this->report->deleteFile($reportData['file']);
			} catch (Error $e) {
			}
		}

		$data = $this->report->update($report_id, ['is_deleted' => true]);
		echo BaseResponse::ok("Hesabat silindi", $data);
	}

	public function update($report_id)
	{
		$report = $this->report->run(post('db'), post('sql'), post('params') ?? []);
		$reportData = $this->report->get($report_id);
		if (post('step') == 1)
			$this->saveReport($report, $report_id, false, true, $reportData);
		else {
			$keys = array_keys($report[0]);
			$filteredKeys = array_values(array_filter($keys, function ($k) {
				return $k != 'id';
			}));
			echo BaseResponse::ok("Success", $filteredKeys);
		}
	}

	private function saveReport($data, $base = null, $isCron = false, $toFile = false, $reportData = null)
	{
		try {
			if (!empty($data)) {
				if (!post('cron_frequency')) {
					$cronJob = $this->cron->insert([
						"job" => post('cron_id'),
						"title" => post("cron_title")
					]);
				} else {
					$cronJob = post('cron_frequency');
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

				if (!$toFile) {
					$reportInsert['report_table'] = camelToSnake(post('report_table'));
				}

				if ($base) {
					$this->report->update($base, $reportInsert);
					$report = $base;
				} else {
					$report = $this->report->insert($reportInsert);
				}

				if ($base > 0) {
					$this->report->update($base, ['is_deleted' => true]);
				}

				$job = [
					"query_id" => $query,
					"report_id" => $report,
					"is_cron" => $isCron,

				];

				if ($toFile) {
					$csv = tempnam(sys_get_temp_dir(), 'csv');

					// File operation
					if (isset($reportData['file']) || (isset($reportData['file']) && empty($reportData['file']))) {
						try {
							$this->report->deleteFile($reportData['file']);
						} catch (Error $e) {
						}
					}
					$header = array_keys($data[0]);
					$fp = fopen($csv, 'w');
					fputcsv($fp, $header);

					if ($csv) {
						foreach ($data as $d) fputcsv($fp, $d);
						$location = $this->report->upload($csv)['data'];
						fclose($fp);
						unlink($csv);
						if (isset($location['uniqueField'])) {
							if (!isset($reportData['file']) || (isset($reportData['file']) && empty($reportData['file']))) {
								$file = $this->file->insert([
									"name" => post('name'),
									"location" => $location['uniqueField'],
									"folder_id" => post("report_folder"),
									"type" => "csv"
								]);
								$job['file_id'] = $file;
							} else {
								$this->file->update($reportData['file_id'], ['name' => $reportData['name'], 'location' => $location['uniqueField']]);
								$job['file_id'] = $reportData['file_id'];
							}
						}
					} else {
						throw new Exception($csv . " adlı fayl yaradıla bilmədi. İcazə parametlərinə nəzər yetirin.", 403);
					}
				} else {
					$this->report->createOrInsertOrUpdateReport(post('db'), post('report_table'), post('unique'), post("sql"));
				}

				$this->job->insert($job);
				echo BaseResponse::ok("Hesabat " . ($base > 0 ? 'redaktə edildi' : 'yaradıldı') . "!");
			} else {
				throw new Exception("Nəticə boşdur", 400);
			}
		} catch (Exception $e) {
			echo BaseResponse::error("Hesabatın yaradılması zamanı xəta baş verdi! " . $e->getMessage(), $e->getCode());
		}
	}

	public function restore($id, $old)
	{
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

	public function db_list()
	{
		echo BaseResponse::ok("Success", dblist());
	}

	public function csv($location)
	{
		$csv = $this->report->getCsvFile($location);
		// header("Content-Type: text/csv");
		echo $csv;
		exit;
	}
}
