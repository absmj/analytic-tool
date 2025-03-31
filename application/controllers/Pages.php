<?php
require_once APPPATH . "custom/BaseController.php";

class Pages extends BaseController
{
	protected $view = '';

	public function __construct()
	{
		parent::__construct();
		// autorize();

		$this->load->model("Page_model", "page");
		$this->title = "Səhifələr";
	}

	public function index()
	{
		if (ENVIRONMENT != 'development') {
			$username = strtolower($_SESSION['user_login']);
			if (!in_array($username, ['khanalid', 'shahriyara', 'alasgara', 'abbasm', 'veliis', 'zahraag'])) {
				die("Sizin icazəniz yoxdur");
			}
		}
		$data['pages'] = $this->page->list();
		// echo BaseResponse::ok("Success", $data['pages']);
		// exit;
		$this->page("index", $data);
	}

	public function get($id, $filter = 0)
	{
		$this->load->model("Chart_model", "chart");
		$this->load->model("Report_model", "report");
		$page = $this->page->get($id);
		$data['access'] = json_decode($page['access'] ?? '[]', 1);

		if (isset($data['access']['ldap']) && !empty($data['access']['ldap']) && !userHasAccess($data['access']['ldap'])) {
			show_error("Access denied", 403);
		}

		foreach ($data['access']['special'] ?? [] as $key => &$value) {
			$value = $_SESSION[$key];
		}

		$report = $this->report->get($page['report_id']);
		$fieldsMap = json_decode($report['fields_map'], 1);
		$data['columns'] = array_values(array_filter($this->report->columns($report['report_table']), function ($item) {
			return !preg_match('/[_]*id/ui', $item['column_name']);
		}));
		$data['filters'] = $this->report->getFieldDistinctValues($report['report_table'], ($fieldsMap));
		$charts = $this->chart->findByPageId($page['id']);

		$data['page'] = $page;
		$fieldMaps = json_decode($page['fields_map'], 1);
		$data['fieldMaps'] = array_flip($fieldMaps ?? []);
		$data['page'] = $page;

		foreach ($charts as $chart) {
			$grid = json_decode($chart['grid'], 1);
			unset($grid['content']);
			$data['charts'][] = [
				"id" => $chart['id'],
				"type" => $chart['chart_type'],
				"slice" => json_decode($chart['slice'], 1),
				"grid" => $grid,
				"options" => json_decode($chart['chart_options'], 1)
			];
		}

		$this->set("styles", [
			"css/folder.css"
		])
			->set("vendorStyles", [
				"vendor/pivottable/pivot.css",
				"vendor/datatables/datatables.css",
				"vendor/gridstack/gridstack.css",
				"vendor/select2/select2.css",
			])
			->set("vendorScripts", [
				"vendor/datatables/datatables.js",
				"vendor/pivottable/pivottable.js",
				"vendor/gridstack/gridstack.js",
				"vendor/chart.js/chart.umd.js",
				"vendor/select2/select2.js",
			])
			->set("vendorScripts", [
				"js/functions.js",
				"js/charts/chart-js.js"
			])
			->set("scripts", [
				// "js/chart-visual.js",
				"js/functions.js",
				// "js/dashboard-create.js",
			]);

		$this->set("vendorScripts", [
			"vendor/codemirror/codemirror.js",
			"vendor/codemirror/autorefresh.js",
			"vendor/codemirror/match-brackets.js",
			"vendor/codemirror/js/javascript.js",
		]);

		$this->page("dashboards/index", $data, false);
	}

	public function cross_filter($pageId)
	{
		if (isPostRequest()) {
			$this->load->model("Chart_model", "chart");
			$this->load->model("Report_model", "report");
			$this->load->helper(["pivot", "apex"]);
			$post = json_decode(file_get_contents("php://input"), 1);
			$file = $post['file'];
			$requestedLabelId = $post['requestedLabelId'];
			$filter = array_filter($post['cross'], function ($f) {
				return !empty($f);
			});
			$charts = $this->chart->findByPageId($pageId);
			$resultFiltered = [];
			$result = $this->report->getCsvFile($file);
			$result = csv2json($result);
			// dd($filter);
			if (!empty($filter)) {
				foreach ($result as $index => $item) {
					foreach ($filter as $key => $val) {
						if (!in_array($item[$key], $val)) {
							$resultFiltered[] = $item;
						}
					}
				}
			} else $resultFiltered = $result;


			$pivotting = @$this->makingChart($charts, $resultFiltered);
			$result = [];

			foreach ($pivotting[1] as &$chart) {
				foreach ($filter as $f) {
					foreach ($requestedLabelId as $index => $label) {
						$chart['options']['data']['datasets'][$label + 1] = ['name' => $f[$index], "label" => $f[$index], "hidden" => true, "data" => []];
					}
				}
			}

			echo BaseResponse::ok("Success", $pivotting[1]);
			exit;
		}
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

	private function makingChart($charts, $data)
	{
		$charts_ = [];
		$rows = [];
		$datasets = [];
		foreach ($charts as $chart) {
			// if($chart["id"] != '89') continue;
			$day = null;
			$month = null;
			$year = null;
			$chart['row_index'] = (int)$chart['row_index'];
			// var_dump($chart['row_index']);
			if (is_int((int)$chart['row_index']) && count($data ?? []) > 0) {
				$slice = json_decode($chart['slice'], 1);
				// dd($slice);

				foreach (array_merge($slice['rows'] ?? [], $slice['columns'] ?? []) ?? [] as $r) {
					if ($r['uniqueName'] == 'Measures') continue;

					// if(preg_match("/\..*$/mui", $r['uniqueName'])){
					// 	$part = preg_replace("/(.*?)(\..*)$/mui", "$1", $r['uniqueName']);
					// 	foreach($data as &$d) {
					// 		if(preg_match("/\.Day$/mui", $r['uniqueName'])){
					// 			$day = preg_replace("/(.*\/)(\d{1,2})(\/.*)/mui", "$2", $d[$part]);
					// 			$d[$r['uniqueName']] = $day;
					// 		} else if(preg_match("/\.Month$/mui", $r['uniqueName'])){
					// 			$month = preg_replace("/(\d{1,2})(.*)(\/.*)/mui", "$1", $d[$part]);
					// 			$d[$r['uniqueName']] = $month;
					// 		}  else if(preg_match("/\.Year$/mui", $r['uniqueName'])){
					// 			$year = preg_replace("/(.*)(\/.*\/)(\d{1,2})/mui", "$3", $d[$part]);
					// 			$d[$r['uniqueName']] = $year;
					// 		}
					// 	}
					// }
				}

				$pivot = PHPivot::create($data);
				// dd(($pivot));

				foreach ($slice['rows'] ?? [] as $r) {
					$pivot->setPivotRowFields($r['uniqueName']);
				}
				foreach ($slice['columns'] ?? [] as $r) {
					if ($r['uniqueName'] == 'Measures') continue;

					$pivot->setPivotColumnFields($r['uniqueName']);
				}

				foreach ($slice['measures'] ?? [] as $r) {
					$pivot->setPivotValueFields($r['uniqueName'], $r['aggregation'] == 'count' ? PHPivot::PIVOT_VALUE_COUNT : PHPivot::PIVOT_VALUE_SUM);
				}

				foreach ($slice['reportFilters'] ?? [] as $r) {
					foreach ($r['filter']['members'] ?? [] as $member) {
						$pivot->addFilter($r['uniqueName'], explode(".", $member)[1] ?? '');
					}
				}

				$sorting = null;
				if (isset($slice['sorting'])) {
					if (isset($slice['sorting']['column'])) {
						// dd(1);
						$sorting = $slice['sorting']['column']['type'] == "asc" ? PHPivot::SORT_ASC : PHPivot::SORT_DESC;
					}
					if (isset($slice['sorting']['row'])) {
						$sorting = $slice['sorting']['row']['type'] == "asc" ? PHPivot::SORT_ASC : PHPivot::SORT_DESC;
					}
				}
				$pivotData = $pivot->generate()->toArray();
				$chart['options'] = json_decode($chart['chart_options'], 1);
				// dd($chart['options']['labels']);
				$apex = new Apex($pivotData, $chart['chart_type'], $sorting, $slice, $chart['options']['labels'] ?? null, $chart['options']['series'] ?? []);
				$chart['options']['data'] = ['datasets' => []];
				// dd($month);
				if ($month) {
					$mounth = array_map("mounthConverter", $apex->labels);
					// $chart['options']['xaxis'] = [];
					$chart['options']['data']['labels'] = $mounth;
				} else {
					$chart['options']['data']['labels'] = $apex->labels;
				}
				$chart['options']['type'] = $chart['chart_type'];
				$chart['options']['data']['total'] = $apex->totals;

				// $chart['options']['xaxis']['type'] = "category";

				preg_match_all("/\{\{(.*?)\}\}/u", @$chart['options']['options']['plugins']['title']['text'], $matches);

				foreach ($matches[1] as $match) {
					if (!is_null($apex->{$match})) {
						$chart['options']['options']['plugins']['title']['text'] = preg_replace("/\{\{" . $match . "\}\}/u", $apex->{$match}, $chart['options']['options']['plugins']['title']['text']);
					}
				}

				preg_match_all("/\{\{(.*?)\}\}/u", @$chart['options']['options']['plugins']['subtitle']['text'], $matches);

				foreach ($matches[1] as $match) {
					if (!is_null($apex->{$match})) {
						$chart['options']['options']['plugins']['subtitle']['text'] = preg_replace("/\{\{" . $match . "\}\}/u", $apex->{$match}, $chart['options']['options']['plugins']['subtitle']['text']);
					}
				}
				$datasets[] = $chart['options']['data']['datasets'];

				$rows[$chart['row_index']][] = $chart;
				$charts_[] = $chart;
			}
		}
		// dd($charts_);
		return [$datasets, $charts_];
	}


	public function delete($page_id)
	{
		$this->load->model("Chart_model", "chart");
		$this->chart->delete($page_id, "page_id");
		$this->page->delete($page_id, "id");
		echo BaseResponse::ok();
	}
}
