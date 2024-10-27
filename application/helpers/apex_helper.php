<?php

class Apex {

    protected $data;
    public $datasets;
    public $labels;
    public $xAxis;
    public $yAxis;
    public $total = 0;
    public $totals = [];
    public $avarage;

    public function __construct($data, $type, $sorting = null, $slice = null, $labels = null, $names = [])
    {
        $this->data = $data;
        // dd($data);
        if($labels == null || empty($labels)) $this->labels = array_values(array_filter(array_keys($data), function($k) { return $k != '_type'; }));
        else $this->labels = $labels;

        foreach($this->labels as &$label) {
            if(empty($label)) {
                $label = "Bilinməyən";
            }
        }
        $labels = [];
        if(is_array($slice)) {
            // dd(array_merge($slice['rows'], $slice['columns']));

        }


        $this->datasets = $this->generateSeries(array_slice($this->data, 1), $type, $sorting, $names);

        if($type == 'card') {

            foreach($data as $key => $val) {

                if(is_array($val)) {

                    foreach($val as $v) {
                        
                        if($v['_type'] == 'TYPE_VAL') {
                            $this->totals[] = $v['_val'];
                        }
                    }
                }
            }

        }


    }


    public function generateSeries($data, $type, $sorting, $names) {
        $series = [];
        $cols = [];
        $vals = [];
        $means = [];
        $colGroup = false;
        // dd($data);
        // Detect cols
        foreach($data as $key => $d) {
            // if(isset($d['_type']) && $d['_type'] == "TYPE_COL") continue;
            if(is_array($d)) {
                foreach($d as $ck => $c) {

                    // dd($c);
                    if(isset($d['_type'])) {
                        $colGroup = true;
                        // continue;
                    }
                    // var_dump($colGroup);
                    if($colGroup) {
                        if(is_array($c)) {
                            foreach($c as $k => $v) {
                                // dd($d);
                                if($k != '_type' && !in_array($ck, $cols)) {
                                    $cols[] = $ck;
                                    $means[$ck] = $k;
                                }
                                if($v == "TYPE_VAL") continue;
                                $vals[$ck][] = (int)$v['_val'];
                                $this->total += (int)($v['_val']);
                            }
                        }

                    } else {
                        if($c == "TYPE_VAL") continue;
                        if(!in_array($ck, $cols)) $cols[] = $ck;
                        $vals[$ck][] = (int)($c['_val']);
                        $this->total += (int)($c['_val']);
                    }
 
                        // $cols = array_keys($c);


                }
            }
        }
        $k = 0;

        if($sorting) {
            natsort($cols);
            natsort($this->labels);
            if($sorting == 2) {
                $cols = array_reverse($cols);
                $this->labels = array_reverse($this->labels);
            }
        }

        if(count($vals) > 0) $this->avarage = $this->total / count($vals);
        // dd($means);
        foreach($cols as $ck => $col) {
            if($sorting) {
                natsort($vals[$col]);
                if($sorting == 2) {
                    $vals[$col] = array_reverse($vals[$col]);
                }
                // dd($vals);
            }



            $series[$k] = [
                "name" => $col,
                "data" => array_values($vals[$col])
            ];


            if(isset($names[$k])) {
                $series[$k]['name'] = $names[$k];
            }

            if($colGroup) {
                if(isset($names[$k])) {
                    $series[$k]['name'] = $names[$k] . " " . $col;
                }
                else{
                    $series[$k]["name"] = $means[$col] . " " . $col;
                }
                $series[$k]["label"] = $col;
            }

            $k++;
        }

        return $series;


        // dd($series);

    }
    
    public function chart() {

    }
}