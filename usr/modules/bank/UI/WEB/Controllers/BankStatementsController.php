<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Bank\UI\WEB\Controllers;

use Carbon\Carbon;
use Bank\Models\BankUpload;
use Boot\System\Office\OfficeSupport;
use Darkeum\ClientBankExchange\Parser;
use Boot\Controller\AbstractController;
use Boot\System\Office\Word\TemplateProcessor;

class BankStatementsController extends AbstractController
{
    static function index()
    {
        return view('bank::manager.statements');
    }

    public function download($id)
    {
        $bank_upload = BankUpload::with("org_.organization")->where("id", $id)->first();
        $br = '</w:t><w:br/><w:t>';
        $p = new Parser(ROOT . $bank_upload->file);
        $document = new TemplateProcessor(ROOT . 'usr/modules/bank/Resources/Templates/bank.docx');

        $items = [];
        $debit_count = 0;
        $credit_count = 0;
        $nf = function ($value) {
            return number_format($value, 2, ",", " ");
        };
        foreach ($p->documents as $item) {
            $is_credit = $bank_upload->org_->organization->inn == $item->ПолучательИНН;
            if ($is_credit) {
                $credit_count++;
            } else {
                $debit_count++;
            }
            $sum = $nf($item->Сумма);
            $date = empty($item->ДатаСписано) ? $item->ДатаПоступило : $item->ДатаСписано;
            $items[] = [
                "date" => Carbon::parse($date)->format("d.m.Y"),
                "debit" => "{$item->ПлательщикРасчСчет}{$item->ПолучательИНН}{$br}{$item->Плательщик}",
                "credit" => "{$item->ПолучательСчет}{$br}{$item->ПолучательИНН}{$br}{$item->Получатель}",
                "debit_sum" => $is_credit ? "" : $sum,
                "credit_sum" => $is_credit ? $sum : "",
                "numb" => $item->Номер,
                "bo" => $item->ВидОплаты,
                "bank" => "БИК {$item->ПолучательБИК}, {$br}{$item->ПолучательБанк1}",
                "comment" => $item->НазначениеПлатежа,
            ];
        }
        $document->setValue('rschet', $p->remainings->РасчСчет);
        $document->setValue('org_name', $bank_upload->org_->organization->name);
        $date_start = Carbon::parse($p->remainings->ДатаНачала)->locale('ru')->isoFormat('DD MMMM YYYY');
        $date_end = Carbon::parse($p->remainings->ДатаКонца)->locale('ru')->isoFormat('DD MMMM YYYY');
        $document->setValue('date_between', "{$date_start} г. по {$date_end} г.");
        $document->cloneRowAndSetValues('date', $items);

        $document->setValue('debit_count', $debit_count);
        $document->setValue('credit_count', $credit_count);
        $document->setValue('count_total', $debit_count + $credit_count);

        $document->setValue('debit_incoming', $nf(0));
        $document->setValue('credit_incoming', $nf($p->remainings->НачальныйОстаток));
        $document->setValue('date_incoming', $date_start);

        $document->setValue('debit_total', $nf($p->remainings->ВсегоСписано));
        $document->setValue('credit_total', $nf($p->remainings->ВсегоПоступило));

        $document->setValue('debit_outgoing', $nf(0));
        $document->setValue('credit_outgoing', $nf($p->remainings->КонечныйОстаток));
        $document->setValue('date_outgoing', $date_end);

        $filename = storage_path($id . '-' . time() . '.docx');

        $document->saveAs($filename);

        OfficeSupport::get_file($filename);
    }
}