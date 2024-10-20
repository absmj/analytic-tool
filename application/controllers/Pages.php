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
		echo BaseResponse::ok("Success", $data['pages']);
		exit;
		$this->page("index", $data);
	}

	public function get($id, $filter = 0)
	{
		$this->load->model("Chart_model", "chart");
		$this->load->model("Report_model", "report");
		$this->load->helper(["pivot", "apex"]);
		$page = $this->page->get($id);

		$charts = $this->chart->findByPageId($page['id']);

		$data['page'] = $page;

		// dd($page);
		if($page['report_table']) {
			$result = $this->report->getReportData($page['report_table'], null, json_decode($page['params'] ?? '[]', 1),array_keys(json_decode($page['fields_map'] ?? '[]', 1)), $this->input->get());
		}

		$pivotting = $this->makingChart($charts, $result);
		$rows = $pivotting[0];
		$data['charts'] = $pivotting[1];
		$fieldMaps = json_decode($page['fields_map'], 1);
		$filters = $this->report->getFieldDistinctValues($page['report_table'], $fieldMaps, $page['unique_field']);
		$data['fieldMaps'] = array_flip($fieldMaps);
		$data['filters'] = $filters;
		echo BaseResponse::ok("Success", $data);
		exit;
		// dd($charts_[0]);
		ksort($rows);
		foreach ($rows as &$row) {
			ksort($row);
		}

		$data['rows'] = $rows;
		$data['page'] = $page;

		$data['template'] = $page['template'];
		if($this->input->get()) {
			echo BaseResponse::ok("Success", ["view" => $this->load->view("pages/dashboards/templates/".$data['template'], $data, true)]);
			exit;
		} else {
			$fieldMaps = json_decode($page['fields_map'], 1);
			$filters = $this->report->getFieldDistinctValues($page['report_table'], $fieldMaps, $page['unique_field']);
			$data['fieldMaps'] = array_flip($fieldMaps);
			$data['filters'] = $filters;
			$this->set("styles", [
				"css/folder.css"
			])
				->set("vendorStyles", [
					"vendor/pivottable/pivot.css",
					"vendor/datatables/datatables.css"
				])
				->set("vendorScripts", [
					"vendor/datatables/datatables.min.js",
					"js/functions.js",
					"js/mock-data.js",
					"js/template.js",
					"vendor/pivottable/pivottable.js"
				])
				->set("vendorScripts", [
					"js/functions.js",
					"js/mock-data.js"
				])
				->set("scripts", [
					"js/chart-visual.js",
				]);
			// dd($data);
			echo BaseResponse::ok("Success", $data);
			// $this->page("dashboards/templates/index", $data, false);
		}



		// dd($data['charts']);
		// dd($data['rows']);

		
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

	private function makingChart($charts, $data) {
		$charts_ = []; $rows = [];
		foreach ($charts as $chart) {
			$day = null; $month = null; $year = null;
			$chart['row_index'] = (int)$chart['row_index'];
			// var_dump($chart['row_index']);
			if (is_int((int)$chart['row_index']) && count($data ?? []) > 0) {
				$slice = json_decode($chart['slice'], 1);
				$pivot = PHPivot::create($data);
				foreach(array_merge($slice['rows'], $slice['columns']) ?? [] as $r) {
					if($r['uniqueName'] == 'Measures') continue;

					if(preg_match("/\..*$/mui", $r['uniqueName'])){
						$part = preg_replace("/(.*?)(\..*)$/mui", "$1", $r['uniqueName']);
						foreach($data as &$d) {
							if(preg_match("/\.Day$/mui", $r['uniqueName'])){
								$day = preg_replace("/(.*\/)(\d{1,2})(\/.*)/mui", "$2", $d[$part]);
								$d[$r['uniqueName']] = $day;
							} else if(preg_match("/\.Month$/mui", $r['uniqueName'])){
								$month = preg_replace("/(\d{1,2})(.*)(\/.*)/mui", "$1", $d[$part]);
								$d[$r['uniqueName']] = $month;
							}  else if(preg_match("/\.Year$/mui", $r['uniqueName'])){
								$year = preg_replace("/(.*)(\/.*\/)(\d{1,2})/mui", "$3", $d[$part]);
								$d[$r['uniqueName']] = $year;
							}
						}
					}
				}

				$pivot = PHPivot::create($data);

				foreach($slice['rows'] as $r) {
					$pivot->setPivotRowFields($r['uniqueName']);
				}
				foreach($slice['columns'] ?? [] as $r) {
					if($r['uniqueName'] == 'Measures') continue;


					$pivot->setPivotColumnFields($r['uniqueName']);
				}

				foreach($slice['measures'] ?? [] as $r) {
					// $pivot->setPivotColumnFields($r['uniqueName']);
					$pivot->setPivotValueFields($r['uniqueName'], $r['aggregation'] == 'count' ? PHPivot::PIVOT_VALUE_COUNT : PHPivot::PIVOT_VALUE_SUM);
				}
	
				foreach($slice['filters'] ?? [] as $r) {
					$pivot->addFilter($r['uniqueName'], $r['filter']['members']);
				}

				$sorting = null;
				if(isset($slice['sorting'])) {
					if(isset($slice['sorting']['column'])) {
						// dd(1);
						$sorting = $slice['sorting']['column']['type'] == "asc" ? PHPivot::SORT_ASC : PHPivot::SORT_DESC;
					}
					if(isset($slice['sorting']['row'])) {
						$sorting = $slice['sorting']['row']['type'] == "asc" ? PHPivot::SORT_ASC : PHPivot::SORT_DESC;
					}
				}
				$pivotData = $pivot->generate()->toArray();

				// dd($pivotData);
				$chart['options'] = json_decode($chart['chart_options'], 1);
				// dd($chart['options']['labels']);
				$apex = new Apex($pivotData, $chart['chart_type'], $sorting, $slice, $chart['options']['labels'] ?? null, $chart['options']['series'] ?? []);
				$chart['options']['data'] = ['datasets' => $apex->datasets];
				// dd($month);
				if($month){
					$mounth = array_map("mounthConverter", $apex->labels);
					// $chart['options']['xaxis'] = [];
					$chart['options']['data']['labels'] = $mounth;	
				} else {
					$chart['options']['data']['labels'] = $apex->labels;
				}
				$chart['options']['type'] = $chart['chart_type'];

				// $chart['options']['xaxis']['type'] = "category";

				// preg_match_all("/\{\{(.*?)\}\}/u", $chart['options']['title']['text'] ?? '', $matches);

				// foreach($matches[1] as $match) {
				// 	if(!is_null($apex->{$match})) {
				// 		$chart['options']['title']['text'] = preg_replace("/\{\{".$match."\}\}/u", $apex->{$match}, $chart['options']['title']['text']);
				// 	}
				// }

				$rows[$chart['row_index']][] = $chart;
				$charts_[] = $chart;
			}

		}
		// dd($charts_);
		return [$rows, $charts_];
	}
	
}
