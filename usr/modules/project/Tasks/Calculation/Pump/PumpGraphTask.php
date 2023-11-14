<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Valve Calculation Task
*/

namespace Project\Tasks\Calculation\Pump;

use Boot\Abstracts\Tasks\Task;
use Boot\Support\Numb;
use Catalog_v2\Model\Pump;
use Darkeum\JpGraph\Text\Text;
use Darkeum\JpGraph\Graph\Graph;
use Darkeum\JpGraph\Graph\MGraph;
use Darkeum\JpGraph\Plot\LinePlot;
use Catalog_v2\Model\Catalog;

class PumpGraphTask extends Task
{

	protected $priority;
	protected $current_x;
	protected $current_y;
	protected $id;
	protected $chart;
	protected $points;
	protected $max_y;
	protected $frequency = false;
	protected $use_pump_station = false;
	protected $max_x = 0;
	protected $width = 640;
	protected $height = 320;
	protected $sub_height = 100;
	protected $directory = null;
	protected $work_mark;
	protected $pump_switch;
	protected $pump_count;
	protected $pump_reserve;
	protected $pump_reserve_count;
	protected $super_mark_qh;
	protected $last_graph = 'QH';
	protected $line_color = 'cornflowerblue';


	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct()
	{
		new Graph();
	}

	/**
	 * Обработчик задачи
	 *
	 * @param  mixed $request
	 * @return array
	 */
	public function run($data, $generate = true)
	{
		$this->priority = $data['priority'];
		$this->current_x = $data['q'];
		$this->current_y = $data['h'];
		$this->work_mark = $data['work_mark'] ?? 'A1';
		$this->pump_switch = $data['pump_switch'] ?? 0;
		$this->pump_count = $data['pump_count'] ?? 1;
		$this->id = $data['id'];
		$this->directory = $data['direction'] ?? null;
		$this->pump_reserve = $data['pump_reserve'] ?? 0;
		$this->pump_reserve_count = $data['pump_reserve_count'] ?? 1;
		$this->frequency = $data['frequency'] ?? false;
		$this->use_pump_station = $data['use_pump_station'] ?? false;
		$this->prepareSize($data);
		return $this->graphGenerate($generate);
	}

	public function getCharts()
	{
		$charts = $this->chart;
		$pump_multi = [];
		foreach ($charts['pump_multi'] as $key => $value) {
			foreach ($value as $key_c => $value_c) {
				foreach ($value_c as $value_cс) {
					$pump_multi[$key_c][] = $value_cс;
				}
			}
		}
		$pump_one = [];
		foreach ($charts['pump_one'] as $key_one => $value_one) {
			foreach ($value_one as $value_c_one) {
				$pump_one[$key_one][] = $value_c_one;
			}
		}

		$pump = [];
		foreach ($charts['pump'] as $key_one => $value_one) {
			foreach ($value_one as $value_c_one) {
				$pump[$key_one][] = $value_c_one;
			}
		}

		$pump_reserve = [];
		if (isset($charts['pump_reserve'])) {
			foreach ($charts['pump_reserve'] as $key => $value) {
				foreach ($value as $key_c => $value_c) {
					foreach ($value_c as $value_cс) {
						$pump_reserve[$key_c][] = $value_cс;
					}
				}
			}
		}

		$calc = [];
		foreach ($charts['calc']['x'] as $key_calc => $value_calc) {
			$calc[] = [
				'x' => $value_calc,
				'y' => $charts['calc']['y'][$key_calc],
			];
		}

		$charts['pump_multi'] = $pump_multi;
		$charts['pump_one'] = $pump_one;
		$charts['pump_reserve'] = $pump_reserve;
		$charts['pump'] = $pump;
		$charts['calc'] = $calc;
		return $charts;
	}

	public function getPoints()
	{
		$this->points = [
			'current' => [
				'x' => (float)$this->current_x,
				'y' => (float)$this->current_y,
			],
			'super_mark' => [
				'QH' => $this->super_mark_qh,
			]
		];
		return $this->points;
	}

	protected function prepareSize()
	{
		if (isset($data['width'])) {
			$this->width = $data['width'];
		}
		if (isset($data['height'])) {
			$this->width = $data['height'];
		}
		if (isset($data['sub_height'])) {
			$this->width = $data['sub_height'];
		}
	}

	/**
	 * getPumpChart
	 *
	 * @param  mixed $id
	 * @return void
	 */
	protected function getPumpChart($id, $x_coefficient)
	{
		$pumps = Pump::where("cid", $id)->orderBy("type_of_instalation")->orderBy("work_line")->get();
		$chart_data = [];
		foreach ($pumps as $pump) {
			$correct_mark = [];
			$max_x = 0;
			$max_y = 0;
			foreach ($pump->data as $mark) {
				$correct_x = $x_coefficient * Numb::prepareNumeric(reset($mark));
				$correct_y = Numb::prepareNumeric(end($mark));
				if ($pump->type == 'QH' && $this->use_pump_station) {
					if ($correct_y - 2 >= 0) {
						$correct_mark[] = ['x' => $correct_x, 'y' => $correct_y - 2];
						if ($correct_x > $max_x) {
							$max_x = $correct_x;
							$max_mark_x = ['x' => $correct_x, 'y' => $correct_y - 2];
						}
						if ($correct_y - 2 > $max_y) {
							$max_y = $correct_y - 2;
							$max_mark_y = ['x' => $correct_x, 'y' => $correct_y - 2];
						}
					}
				} else {
					$correct_mark[] = ['x' => $correct_x, 'y' => $correct_y];
					if ($correct_x > $max_x) {
						$max_x = $correct_x;
						$max_mark_x = ['x' => $correct_x, 'y' => $correct_y];
					}
					if ($correct_y > $max_y) {
						$max_y = $correct_y;
						$max_mark_y = ['x' => $correct_x, 'y' => $correct_y];
					}
				}
			}
			$chart_data[$pump->type][$pump->work_line]['marks'] = $correct_mark;
			$chart_data[$pump->type][$pump->work_line]['max_x'] = $max_x;
			$chart_data[$pump->type][$pump->work_line]['max_y'] = $max_y;
			$chart_data[$pump->type][$pump->work_line]['max_mark_x'] = $max_mark_x;
			$chart_data[$pump->type][$pump->work_line]['max_mark_y'] = $max_mark_y;
			if ($pump->type == 'QH') {
				$this->max_y = $max_y;
			}
			if ($pump->type == 'QH') {
				$this->max_x = $max_x;
			}
		}
		if (isset($chart_data['QP2'])) {
			$this->last_graph = 'QP2';
		}
		if (isset($chart_data['NPSH'])) {
			$this->last_graph = 'NPSH';
		}
		if (isset($chart_data['QE'])) {
			$this->last_graph = 'QE';
		}
		return $chart_data;
	}


	protected function getWorkLineChart($current_x, $current_y, $max_y, $max_x)
	{
		$chart_data = [];
		for ($y = 0; $y <= $max_y + 2; $y += 0.1) {
			$x = sqrt($y / ($current_y / pow($current_x, 2)));
			$chart_data['x'][] = $x;
			$chart_data['y'][] = $y;
			if ($max_x <= $x) {
				break;
			}
		}
		$chart_data['key'] = 0;
		$chart_data['type'] = 'calc';
		return $chart_data;
	}


	protected function findIntersectionSqr($marks, $current_x, $current_y)
	{
		$superX = 0;
		$superY = 0;
		$a = $current_y / pow($current_x, 2);

		foreach ($marks as $key => $mark) {
			if (isset($marks[$key + 1])) {
				$x1 = $mark['x'];
				$y1 = $mark['y'];
				$x2 = $marks[$key + 1]['x'];
				$y2 = $marks[$key + 1]['y'];
				if (($x1 - $x2) == 0) {
					continue;
				}
				$k = ($y1 - $y2) / ($x1 - $x2);
				$b = $y2 - $k * $x2;
				$aa = $a;
				$bb = -$k;
				$cc = -$b;
				$d = ($bb * $bb) - (4 * $aa * $cc);
				$superX = (-$bb + sqrt($d)) / (2 * $aa);
				$superY = $k * $superX + $b;
				if ($superX >= $x1 && $superX <= $x2 && $superY <= $y1 && $superY >= $y2 && $superX > $current_x && $superY > $current_y) {
					return ['x' => $superX, 'y' => $superY];
				}
			}
		}

		return ['x' => $superX, 'y' => $superY];
	}

	protected function findIntersection($marks, $x)
	{
		$y = 0;
		foreach ($marks as $key => $mark) {
			if (isset($marks[$key + 1])) {
				$x1 = $mark['x'];
				$y1 = $mark['y'];
				$x2 = $marks[$key + 1]['x'];
				$y2 = $marks[$key + 1]['y'];
				if ($x1 <= $x && $x2 >= $x) {
					$y = ($y2 - $y1) * (($x - $x1) / ($x2 - $x1)) + $y1;
					break;
				}
			}
		}
		return ['x' => $x, 'y' => $y];
	}

	protected function graphTemplate($parameter = [])
	{
		$graph  = new Graph($parameter['width'] ?? $this->width, $parameter['height'] ?? $this->height, 'auto');
		$graph->img->SetAntiAliasing(false);
		$graph->SetMargin($parameter['margin']['left'] ?? 65, $parameter['margin']['right'] ?? 30, $parameter['margin']['top'] ?? 0, $parameter['margin']['bottom'] ?? 0);
		if (isset($parameter['title'])) {
			$graph->title->Set($parameter['title']);
			$graph->title->SetAlign('left');
			$graph->title->SetFont(FF_VERDANA, FS_BOLD, 12);
		}
		$graph->SetScale('linlin');
		$graph->SetFrame(false);
		$graph->SetBox(true, 'black', 2);
		$graph->SetMarginColor('white');
		$graph->SetColor('lightyellow');
		//Сетка
		$graph->xgrid->Show();
		//настройка оси Y
		$graph->yaxis->HideTicks(false, false);
		$graph->yaxis->scale->ticks->SetColor('red', 'black');
		$graph->yaxis->scale->ticks->SetDirection(SIDE_LEFT);
		$graph->yaxis->scale->ticks->SetSize(7, 5);
		$graph->yaxis->scale->ticks->SetWeight(1.7);
		$graph->yaxis->SetTitle($parameter['yaxis']['title'] ?? '');
		$graph->yaxis->SetTickSide(SIDE_LEFT);
		$graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL, 13);

		$graph->ygrid->SetFill(true, 'white', 'white');
		if (isset($parameter['xaxis']['HideLabels']) && $parameter['xaxis']['HideLabels']) {
			$graph->xaxis->HideLabels();
		}
		if (isset($parameter['name'])) {
			$txt = new Text($parameter['name']['text']);
			$txt->SetPos($parameter['name']['x'] ?? 50, $parameter['name']['y'] ?? 30);
			$txt->SetColor('black');
			$txt->SetFont(FF_VERDANA, FS_NORMAL, 10);
			$graph->AddText($txt);
		}
		$graph->xaxis->scale->SetAutoMax($this->max_x + 1);
		$graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 13);
		return $graph;
	}


	protected function kpdMarkShow($marks, $mark_kpd, $showTitle = false)
	{
		return  $this->markShow([
			'y' => $this->findIntersection($marks, $mark_kpd['x'])['y'],
			'x' => $mark_kpd['x'],
			'format' => $showTitle ? 'Эфф. ' . round($mark_kpd['y'], 2) . '%%' : '',
		]);
	}

	protected function superMarkShow($mark)
	{
		return  $this->markShow([
			'y' => $mark['y'],
			'x' => $mark['x'],
			'type' => 'MARK_CUSTOM',
			'mark_scale' => 0.45,
			'width' => 20,
			'fill_color' => 'white',
		]);
	}

	protected function currentMarkShow($current_x, $current_y)
	{
		return  $this->markShow([
			'y' => $current_y,
			'x' => $current_x,
			'type' => 'MARK_CUSTOM',
			'mark_scale' => 0.6,
			'text_align' => 'right',
			'width' => 20,
			'fill_color' => 'white',
			'format' => $this->work_mark ?? 'A1',
		]);
	}


	protected function markShow($parameter = [])
	{
		$linePlot = new LinePlot([$parameter['y']], [$parameter['x']]);
		if (isset($parameter['type']) && $parameter['type'] == 'MARK_CUSTOM') {
			$linePlot->mark->SetType(MARK_IMG, ROOT . 'usr/modules/project/Resources/Templates/mark.png', $parameter['mark_scale'] ?? 0.5);
		} else {
			$linePlot->mark->SetType($parameter['type'] ?? MARK_SQUARE);
		}
		$linePlot->mark->SetFillColor($parameter['fill_color'] ?? $this->line_color);
		$linePlot->mark->SetColor($parameter['color'] ?? 'black');
		$linePlot->mark->SetWidth($parameter['width'] ?? 10);
		$linePlot->value->SetFormat($parameter['format'] ?? '');
		$linePlot->value->Show();
		$linePlot->value->SetAlign($parameter['text_align'] ?? 'left', $parameter['text_valign'] ?? '');
		$linePlot->value->SetColor($parameter['text_color'] ?? $this->line_color);
		$linePlot->value->SetFont(FF_ARIAL, $parameter['text_style'] ?? FS_BOLD,  $parameter['text_size'] ?? 10);
		$linePlot->value->SetMargin($parameter['text_margin'] ?? 14);
		return $linePlot;
	}

	protected function lineShow($type = 'x_line', $mark = [], $line = [])
	{
		if ($type == 'x_line') {
			$linePlot = new LinePlot(array_fill(0, 2, $mark['y']),  [0, $this->max_x + 2]);
		} else {
			$linePlot = new LinePlot([0, $line['max_y'] + 0.5], array_fill(0, 2, $mark['x']));
		}
		$linePlot->SetColor($this->line_color);
		return $linePlot;
	}

	protected function graphLineGenerate(Graph $graph, $chart, $priority, $parameter = [])
	{
		foreach ($chart as $key => $line) {
			$x_array = [];
			$y_array = [];
			foreach ($line['marks'] as $mark) {
				$x_array[] = $mark['x'];
				$y_array[] = $mark['y'];
			}
			$linePlot = new LinePlot($y_array, $x_array);
			if ($priority == $key) {
				$linePlot->SetColor($parameter['color_priority'] ?? 'forestgreen');
			} else {
				$linePlot->SetColor($parameter['color'] ?? 'gray');
			}
			$linePlot->SetWeight($parameter['weight'] ?? 1);
			$linePlot->SetStyle($parameter['style'] ?? "solid");
			$graph->Add($linePlot);
			if (isset($parameter['title'])) {
				$graph->Add($this->markShow([
					'x' => max($x_array) + (0.03 * max($x_array)),
					'y' => min($y_array) - (0.03 * min($y_array)),
					'fill_color' => 'white',
					'color' => 'white',
					'format' => $parameter['title'],
					'text_align' => 'center',
					'text_valign' => 'top',
					'text_margin' => 1,
					'width' => 1,
					'text_color' => $priority == $key ? ($parameter['color_priority'] ?? 'forestgreen') : $parameter['color'] ?? 'black',
					'text_style' => FS_NORMAL,
					'text_size' => 8,
				]));
			}
		}
		return $graph;
	}

	protected function graphAreaGenerate(Graph $graph, $chart_type, $priority)
	{
		$parameter_main = [];
		if ($chart_type == 'QH' && $this->pump_switch == 1) {
			$title_sum = 'A;';
			for ($i = 1; $i < $this->pump_count; $i++) {
				$title_sum .= 'A;';
			}
			$parameter_main['title'] = 'Σ ' . $title_sum;
		}
		$parameter_main['weight'] = 1.5;
		$graph = $this->graphLineGenerate($graph, $this->chart['pump'][$chart_type], $priority, $parameter_main);
		if ($this->pump_switch == 1) {
			$parameter_one = [];
			if ($chart_type == 'QH' && $this->pump_switch == 1) {
				$pump_info = Catalog::find($this->id);
				$marka = '';
				if ($pump_info->man != 270) {
					$marka = $pump_info->attribute['127'] . ': ' ?? '';
				}
				$manufacture = $pump_info->catalog_manufacture->name ?? '';
				$parameter_one['title'] = 'A: ' . $manufacture . ' ' . $marka . $pump_info->sku;
			}
			if (isset($this->chart['pump_multi'])) {
				foreach ($this->chart['pump_multi'] as $key => $value) {
					$parameter_multi = [];
					if ($key == 1) {
						$parameter_one['color_priority'] = 'darkolivegreen4';
						$parameter_one['color'] = 'gray';
						$parameter_multi = $parameter_one;
					} else {
						if ($chart_type == 'QH') {
							$title_sum = 'A;';
							for ($i = 1; $i < $key; $i++) {
								$title_sum .= 'A;';
							}
							$parameter_multi['title'] = 'Σ ' . $title_sum;
						}
						$parameter_multi['color_priority'] = 'darkgray';
						$parameter_multi['color'] = 'darkgray';
					}
					$graph = $this->graphLineGenerate($graph, $value[$chart_type], $priority, $parameter_multi);
				}
			}
		}
		if ($this->pump_reserve == 1) {
			$parameter = [
				'color_priority' => 'darkgray',
				'color' => 'gray',
				'weight' => '2',
				'style' => "dashed"
			];
			foreach ($this->chart['pump_reserve'] as $value_reserve) {
				// dd($this->chart['pump_reserve']);
				$graph = $this->graphLineGenerate($graph, $value_reserve[$chart_type], $priority, $parameter);
			}
			
		}
		$kpdLine = false;
		//проверяем график КПД и если есть, то запоминаем наибольшие КПД      
		if (isset($this->chart['pump']['QE'][$priority])) {
			$kpdLine = true;
			$mark_kpd = $this->chart['pump']['QE'][$priority]['max_mark_y'];
		}
		//устанавливаем точки пересечения
		foreach ($this->chart['pump'][$chart_type] as $key => $line) {
			if ($priority == $key) {

				//получем точку пересечения
				if (!$this->frequency) {
					if ($chart_type == 'QH') {
						$super_mark = $this->findIntersectionSqr($line['marks'], $this->current_x, $this->current_y);
						$this->super_mark_qh = $super_mark;
					} else {
						$super_mark = $this->findIntersection($line['marks'], $this->super_mark_qh['x']);
					}
					$graph->Add($this->superMarkShow($super_mark));
					//ставим точку максимального КПД
					if ($kpdLine) {
						$graph->Add($this->kpdMarkShow($line['marks'], $mark_kpd, ($chart_type == 'QH' ? true : false)));
					}
				} else {
					if ($chart_type == 'QH') {
						$super_mark = [
							'x' => $this->current_x,
							'y' => $this->current_y,
						];
						$this->super_mark_qh = $super_mark;
					} else {
						$super_mark = $this->findIntersection($line['marks'], $this->super_mark_qh['x']);
					}
				}



				// dd($markForLine);
				//строим прямые пересечения    
				$x_line = $this->lineShow('x_line', $super_mark, $line);
				$graph->Add($x_line);
				$graph->Add($this->lineShow('y_line', $super_mark, $line));
			}
		}
		if ($chart_type == 'QH') {
			//ставим исходную точку 
			$graph->Add($this->currentMarkShow($this->current_x, $this->current_y));
			//строим расчетный график 
			$linePlot = new LinePlot($this->chart['calc']['y'], $this->chart['calc']['x']);
			$linePlot->SetColor('navy');
			$linePlot->SetWeight(1.5);
			$linePlot->SetStyle("solid");
			// dd($graph);
			$graph->Add($linePlot);
			if (false) {
				//добавляем текст
				$txt = new Text('◄ Область применения ►');
				$txt->SetPos(48, 300);
				$txt->SetColor('darkred');
				$txt->SetFont(FF_ARIAL, FS_NORMAL, 14);
				$txt->SetBox('white', 'black', false, 0);
				$graph->AddText($txt);
			}
			//добавляем текст

		}
		//точка пересечения на осях
		$txt = new Text((string) round($super_mark['y'], 2));
		$txt->SetScalePos(0, $super_mark['y'] - $super_mark['y'] * 0.02);
		$txt->SetColor($this->line_color);
		$txt->SetAlign('right', 'center');
		$txt->SetFont(FF_ARIAL, FS_NORMAL, 13);
		$txt->SetBox('white', 'black', false, 0);
		$graph->AddText($txt);

		if ($this->last_graph == $chart_type) {
			$txt = new Text((string) round($super_mark['x'], 2));
			$txt->SetScalePos($super_mark['x'], 0);
			$txt->SetColor($this->line_color);
			$txt->SetAlign('center', 'top');
			$txt->SetFont(FF_ARIAL, FS_NORMAL, 10);
			$txt->SetBox('white', 'black', false, 0);
			$graph->AddText($txt);
		}
		return $graph;
	}

	public function graphGenerate($generate = true)
	{
		if ($generate) {
			$resultGraph = new MGraph();
		}
		$resultXPos = 3;
		$resultYPos = 3;
		$resultHeight = 0;
		$resultWidth = $this->width;

		$current_x = $this->current_x;
		$current_y = $this->current_y;
		$id = $this->id;

		$priority = 1;
		if ($this->pump_switch == 0) {
			$this->chart['pump'] = $this->getPumpChart($id, 1);
		} else {
			$this->chart['pump_one'] = $this->getPumpChart($id, 1);
			if ($this->pump_count == 1) {
				$this->chart['pump_multi'][1] = $this->getPumpChart($id, 1);
			}
			for ($i = 1; $i <= ($this->pump_count - 1); $i++) {
				$this->chart['pump_multi'][$i] = $this->getPumpChart($id, $i);
			}
			$this->chart['pump'] = $this->getPumpChart($id, $this->pump_count);
		}
		if ($this->pump_reserve == 1) {
			for ($i=1; $i <= $this->pump_reserve_count; $i++) { 
				$this->chart['pump_reserve'][$i] = $this->getPumpChart($id, ($this->pump_switch == 1 ? $this->pump_count + $i : 2));
			}
			
		}

		$check_distance = 10000;
		//поиск приоритетной линии 
		foreach ($this->chart['pump']['QH'] as $key => $line) {
			$s_mark = $this->findIntersectionSqr($line['marks'], $current_x, $current_y);
			$distance = sqrt(pow($s_mark['x'] - $current_x, 2) + pow($s_mark['y'] - $current_y, 2));
			if (($s_mark['x'] >= $current_x) && ($s_mark['y'] >= $current_y) && ($distance < $check_distance)) {
				$check_distance = $distance;
				$priority = $key;
			}
		}

		$this->chart['calc'] = $this->getWorkLineChart($current_x, $current_y, $this->chart['pump']['QH'][$priority]['max_y'], $this->chart['pump']['QH'][$priority]['max_x']);

		$margin_bottom = 0;
		if ($this->last_graph == 'QH') {
			$margin_bottom = 20;
		}

		//строим график напора и расчетны график
		$graph1 = $this->graphTemplate([
			'title' => 'Рабочее поле',
			'xaxis' => [
				'HideLabels' => ($this->last_graph == 'QH') ? false : true
			],
			'yaxis' => [
				'title' => 'H/m'
			],
			'name' => [
				'text' => 'Напор',
				'x' => '70'
			],
			'margin' => ['bottom' => $margin_bottom],
		]);
		$graph1 = $this->graphAreaGenerate($graph1, 'QH', $priority);
		if ($generate) {
			$resultGraph->Add($graph1, $resultXPos, $resultYPos);
			$resultHeight += $this->height;



			//построение графика QP2 (kW)
			if (isset($this->chart['pump']['QP2'])) {
				$margin_bottom = 0;
				if ($this->last_graph == 'NPSH') {
					$margin_bottom = 20;
				}
				$graph2 = $this->graphTemplate([
					'height' => $this->sub_height,
					'xaxis' => [
						'HideLabels' => ($this->last_graph == 'QP2') ? false : true
					],
					'yaxis' => [
						'title' => 'P1/kW'
					],
					'name' => [
						'text' => 'Мощность на валу P2',
						'y' => '10',
						'x' => '70'
					],
					'margin' => ['bottom' => $margin_bottom],
				]);
				$graph2 = $this->graphAreaGenerate($graph2, 'QP2', $priority);
				$resultGraph->Add($graph2, $resultXPos, $resultHeight);
				$resultHeight += $this->sub_height;
			}


			//построение графика NPSH
			if (isset($this->chart['pump']['NPSH'])) {
				$margin_bottom = 0;
				if ($this->last_graph == 'NPSH') {
					$margin_bottom = 20;
				}
				$graph3 = $this->graphTemplate([
					'height' => $this->sub_height,
					'xaxis' => [
						'HideLabels' => ($this->last_graph == 'NPSH') ? false : true
					],
					'yaxis' => [
						'title' => 'NPSH/m'
					],
					'name' => [
						'text' => 'NPSH - значения',
						'y' => '10',
						'x' => '70'
					],
					'margin' => ['bottom' => $margin_bottom],
				]);
				$graph3 = $this->graphAreaGenerate($graph3, 'NPSH', $priority);
				$resultGraph->Add($graph3, $resultXPos, $resultHeight);
				$resultHeight += $this->sub_height;
			}
			if (isset($this->chart['pump']['QE'])) {
				//построение графика QE
				$margin_bottom = 0;
				if ($this->last_graph == 'QE') {
					$margin_bottom = 20;
				}
				$graph4 = $this->graphTemplate([
					'height' => $this->sub_height,
					'yaxis' => [
						'title' => 'η/%'
					],
					'name' => [
						'text' => 'Гидравлический КПД',
						'y' => '10',
						'x' => '70'
					],
					'margin' => ['bottom' => $margin_bottom],
				]);
				$graph4 = $this->graphAreaGenerate($graph4, 'QE', $priority);
				$resultGraph->Add($graph4, $resultXPos, $resultHeight);
				$resultHeight += $this->sub_height;
			}

			$resultGraph->Stroke($this->directory);
			return [
				'height' => $resultHeight,
				'width' => $resultWidth,
				'directory' => $this->directory,
			];
		}
		return $this;
	}
}
