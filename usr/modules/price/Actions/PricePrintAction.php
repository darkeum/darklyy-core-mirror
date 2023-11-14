<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\Actions;

use Boot\Support\Currency;
use Price\Models\CatalogPrice;
use Boot\Abstracts\Actions\Action;
use Price\Models\CatalogPriceDetail;
use Boot\System\Office\Spreadsheet\IOFactory;

class PricePrintAction extends Action
{
	public function run($id)
	{
		$price = CatalogPrice::where('id', $id)->with('type')->first();
		if (!empty($price)) {
			$currency = Currency::get($price->type->currency)->short();
			$reader = IOFactory::createReader("Xlsx");
			$spreadsheet = $reader->load(module_path('Resources' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'price.xlsx'));
			$items = CatalogPriceDetail::where('price_id', $id)->with('catalog')->get();

			if (!empty($items)) {
				$baseRow = 1;
				$row_count = 0;
				foreach ($items as $item) {
					$row_count++;
					$row = $baseRow + $row_count;
					$spreadsheet->getActiveSheet()->insertNewRowBefore($row, 1);
					$spreadsheet->getActiveSheet()->setCellValue('A' . $row, $row_count)
						->setCellValue('B' . $row, $item->cid)
						->setCellValue('C' . $row, $item->catalog->sku)
						->setCellValue('D' . $row, $item->catalog->title)
						->setCellValue('E' . $row, $price->type->title)
						->setCellValue('F' . $row, $item->sum)
						->setCellValue('G' . $row, $currency);
				}
			}

			$oWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="' . __('price::show.title', ['numb' => $id]) . '.xlsx"');
			header('Cache-Control: max-age=0');
			$oWriter->save('php://output');
			exit;
		} else {
			abort('404');
		}
	}
}
