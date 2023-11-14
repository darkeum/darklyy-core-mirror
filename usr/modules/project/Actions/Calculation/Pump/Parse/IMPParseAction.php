<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Heat Calculation class
*/

namespace Project\Actions\Calculation\Pump\Parse;

use Boot\Support\Str;
use Boot\Support\Numb;
use Catalog_v2\Model\Catalog;
use Boot\Support\Facades\Http;
use Catalog_v2\Model\Pump;
use Catalog_v2\Model\PumpLinks;

class IMPParseAction
{

	protected $token;
	protected $coefficients = null;

	public function run($images)
	{
		$result = [];
		// $items = Catalog::where('man', 271)->whereNotNull('sku_UdmurtSnab')->get();
		$items = PumpLinks::where('status', 1)->get();
		$bseResponse = Http::get("http://www.pumps.si/Pumps/app/getHome?&theneName=undefined");
		$this->token = $bseResponse->cookies()->getCookieByName('JSESSIONID')->getValue();
		foreach ($items as $item) {
			// dump($item->link);
			$result = [];
			// $pump_id = $item->sku_UdmurtSnab;
			// PumpLinks::create([
			// 	'cid' => $item->id,
			// 	'link' => "http://www.pumps.si/Pumps/app/get%C4%8CrpalkaHtml?co={$pump_id}&co={$pump_id}",
			// 	'status' => 1,
			// ]);
			// $pump_id = $item->sku_UdmurtSnab;
			// $pump_id = '0190';
			//получаем токен авторизации

			//получаем данные насоса
			// $response = Http::withCookies(['JSESSIONID' => $this->token], 'pumps.si')->get("http://www.pumps.si/Pumps/app/get%C4%8CrpalkaHtml?co={$pump_id}&co={$pump_id}");
			$response = Http::withCookies(['JSESSIONID' => $this->token], 'pumps.si')->get($item->link);
			//QH и КПД
			$first_graph = $this->replaceBadQuotes(Str::betweenFirst($response->body(), '<svg', '</svg>'));
			preg_match('/<rect x="(\d+\.\d+)" y="(\d+\.\d+)" width="(\d+\.\d+)" height="(\d+\.\d+)"  fill="#fdfdfd" ><\/rect>/', $first_graph, $graph_info);
			preg_match_all('/<polyline points="([\d\.\,\s]+)"  fill-opacity="0\.04"  stroke="#39a8d6/', $first_graph, $QH_array);
			preg_match_all('/<polyline points="([\d\.\,\s]+)"  fill-opacity="0\.04"  stroke="#a0a0a0/', $first_graph, $QE_array);
			if (isset($graph_info[3])) {
				$this->coefficients = $this->getCoefficients($graph_info[3], $graph_info[4]);
				foreach ($QH_array[1] as $graph) {
					$result['QH'][] = $this->prepareMarks($graph, $graph_info);
				}
				foreach ($QE_array[1] as $graph) {
					$result['QE'][] = $this->prepareMarks($graph, $graph_info);
				}
				//мощность
				$second_graph =  $this->replaceBadQuotes(Str::of($response->body())->remove('<svg' . Str::betweenFirst($response->body(), '<svg', '</svg>') . '</svg>')->betweenFirst('<svg', '</svg>'));
				preg_match('/<rect x="(\d+\.\d+)" y="(\d+\.\d+)" width="(\d+\.\d+)" height="(\d+\.\d+)"  fill="#fdfdfd" ><\/rect>/', $second_graph, $graph_info);
				preg_match_all('/<polyline points="([\d\.\,\s]+)"  fill-opacity="0\.04"  stroke="#d0d0d0/', $second_graph, $QP2_array);

				$this->coefficients = $this->getCoefficients($graph_info[3], $graph_info[4]);
				foreach ($QP2_array[1] as $graph) {
					$result['QP2'][] = $this->prepareMarks($graph, $graph_info, 'QP2');
				}
				Pump::where('cid', $item->cid)->forceDelete();
				foreach ($result as $type => $lines) {
					foreach ($lines as $key => $marks) {
						$data = [];
						foreach ($marks as $mark) {
							$data[] = (object)["Q" => str_replace(".", ",", $mark['Q']), "H" => str_replace(".", ",", $mark['H'])];
						}
						$element = [
							"cid" => $item->cid,
							"uid" => 79,
							"uid_change" => 79,
							"data" => $data,
							"type_of_instalation" => 0,
							"work_line" => $key + 1,
							"type" => $type,
						];
						Pump::create($element);
					}
				}
				PumpLinks::find($item->id)->update(["status" => 2]);
				dump($item->cid);
				sleep(1);
			} else {
				dump('Пауза');
				sleep(5);
				$bseResponse = Http::get("http://www.pumps.si/Pumps/app/getHome?&theneName=undefined");
				$this->token = $bseResponse->cookies()->getCookieByName('JSESSIONID')->getValue();
			}
		}
	}

	protected function replaceBadQuotes($value): string
	{
		$result = Str::replace($value, '«', '"');
		return (string) Str::replace($result, '»', '"');
	}

	protected function prepareMarks($marks_string, $graph_info, $type = 'QH'): array
	{
		$marks = explode(' ', $marks_string);
		$result = [];
		foreach ($marks as $mark) {
			$coord = explode(',', $mark);
			if ($type == 'QH') {
				$realCoord = $this->getRealCoord($coord, $graph_info);
			} else {
				$realCoord = $this->getRealCoordQP($coord, $graph_info);
			}
			$result[] = [
				'Q' => $realCoord['X'],
				'H' => $realCoord['Y']
			];
		}
		return $result;
	}


	protected function getCoefficients($w, $h): array
	{
		$x = 158;
		$y = 54;
		$response = Http::withCookies(['JSESSIONID' => $this->token], 'pumps.si')->get("http://www.pumps.si/Pumps/app/getTooltipText?x={$x}&y={$y}&w={$w}&h={$h}");
		$result = [];
		$result['X'] = Str::betweenFirst($response->body(), '<div>Q = ', 'm3h<br>') / ($x);
		$result['Y'] = Numb::prepareNumeric(Str::betweenFirst($response->body(), '<br>H = ', 'm</div>')) / ($h - $y);
		return $result;
	}

	protected function getRealCoord($mark, $graph_info): array
	{
		$x = $mark[0] - $graph_info[1];
		$y = $mark[1] - $graph_info[2];
		$h = $graph_info[4];
		$result['X'] = round($this->coefficients['X'] * $x, 2);
		$result['Y'] = round($this->coefficients['Y'] * ($h - $y), 2);
		return $result;
	}

	protected function getRealCoordQP($mark, $graph_info): array
	{
		$x = $mark[0] - $graph_info[1];
		$y = $mark[1] - $graph_info[2];
		$result['X'] = round(4 / (242.46727464482893 - 70) * $x, 2);
		$result['Y'] = round(100 / 119 * (125 - $y), 2);
		return $result;
	}
}
