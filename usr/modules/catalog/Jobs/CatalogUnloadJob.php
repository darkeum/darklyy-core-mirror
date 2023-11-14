<?php

namespace Catalog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Boot\System\Bus\Dispatchable;
use Illuminate\Bus\Batchable;

//Загрузка с портала
class CatalogUnloadJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 240;

    protected $query;
    protected $offset;
    protected $limit;
    protected $file;
    protected $fields;
    protected $catalog_attribute;
    protected $catalog_certificate;

    public function __construct($query, Int $offset, Int $limit, String $file, array $fields, $catalog_attribute, $catalog_certificate)
    {
        $this->query = $query;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->file = $file;
        $this->fields = $fields;
        $this->catalog_attribute = $catalog_attribute;
        $this->catalog_certificate = $catalog_certificate;
    }

    public function handle()
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
            return;
        }

        $query = \AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade::unserialize($this->query);
        $items = $query->offset($this->offset)->limit($this->limit)->get();

        $fields_list = \Catalog\Controller\CatalogExcelController::get_fields();
        if (file_exists($this->file)) {
            $document = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file);
            $sheet = $document->setActiveSheetIndex(0);
            $columnPosition = $sheet->getHighestRow(); // Начальная координата x
        } else {
            $document = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $document->setActiveSheetIndex(0);

            $columnPosition = 1; // Начальная координата x
            $startLine = 1; // Начальная координата y

            foreach ($this->fields as $field) {
                $f = $fields_list[$field];
                switch ($field) {
                    default:
                        $sheet->setCellValue([$startLine, $columnPosition], "{$f['rus']} {%{$f['field']}%}");
                        $startLine++;
                        break;
                    case "spec":
                        $sheet->setCellValue([$startLine, $columnPosition], 'наименование {%spec_1%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'модель/марка {%spec_2%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'дополнительный производитель {%spec_3%}');
                        $startLine++;
                        break;
                    case "weight":
                        $sheet->setCellValue([$startLine, $columnPosition], 'Вес НЕТТО, кг{%weight_1%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'Вес БРУТТО, кг {%weight_4%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'Длина (глубина), мм {%weight_2%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'Ширина, мм {%weight_6%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'Высота, мм {%weight_7%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'Объем {%weight_3%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'Длина (глубина) упаковки, мм {%weight_8%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'Ширина упаковки, мм {%weight_9%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'Высота упаковки, мм {%weight_10%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'Объем упаковки {%weight_11%}');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], 'Количество в упаковке {%weight_5%}');
                        $startLine++;
                        break;
                    case "certificate":
                        foreach ($this->catalog_certificate as $certificate) {
                            $sheet->setCellValue([$startLine, $columnPosition], "{$certificate->title} {%certificate_{$certificate->id}%}");
                            $startLine++;
                            if ($certificate->date_start == 1) {
                                $sheet->setCellValue([$startLine, $columnPosition], "начало сертификата {%%}");
                                $startLine++;
                            }
                            if ($certificate->date_end == 1) {
                                $sheet->setCellValue([$startLine, $columnPosition], "окончание сертификата {%%}");
                                $startLine++;
                            }
                        }
                        break;
                    case "attribute":
                        foreach ($this->catalog_attribute as $attribute) {
                            $sheet->setCellValue([$startLine, $columnPosition], "{$attribute->name} {%attribute_{$attribute->id}%}");
                            $startLine++;
                        }
                        break;
                }
            }
        }
        foreach ($items as $item) {

            // dd($item);
            $columnPosition++;
            $startLine = 1;
            if (array_search("purchase", $this->fields) || array_search("price", $this->fields)) {
                $price = \Price::getPrice($item->id);
            }
            foreach ($this->fields as $field) {
                $f = $fields_list[$field];
                switch ($field) {
                    default:
                        $sheet->setCellValue([$startLine, $columnPosition], $item->{$f["field"]} ?? "");
                        $startLine++;
                        break;
                    case "category":
                        $sheet->setCellValue([$startLine, $columnPosition], str_replace(',', '', $item->{$f["field"]} ?? ""));
                        $startLine++;
                        break;
                    case "man_name":
                        $sheet->setCellValue([$startLine, $columnPosition], $item->catalog_manufacture?->name);
                        $startLine++;
                        break;
                    case "purchase":
                        $sheet->setCellValue([$startLine, $columnPosition], $price->price_purchase_);
                        $startLine++;
                        break;
                    case "price":
                        $sheet->setCellValue([$startLine, $columnPosition], $price->price_list_);
                        $startLine++;
                        break;
                    case "count":
                        $sheet->setCellValue([$startLine, $columnPosition], $item->stock_detail?->sum);
                        $startLine++;
                        break;
                    case "catalog_groups_id":
                        $sheet->setCellValue([$startLine, $columnPosition], $item->catalog_groups?->name);
                        $startLine++;
                        break;
                    case "spec":
                        $sheet->setCellValue([$startLine, $columnPosition], $item->spec[1] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->spec[2] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->spec[3] ?? '');
                        $startLine++;
                        break;
                    case "weight":
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[1] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[4] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[2] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[6] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[7] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[3] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[8] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[9] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[10] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[11] ?? '');
                        $startLine++;
                        $sheet->setCellValue([$startLine, $columnPosition], $item->weight[5] ?? '');
                        $startLine++;
                        break;
                    case "certificate":
                        foreach ($this->catalog_certificate as $certificate) {
                            $flag_cert = true;
                            $flag_cert_start = true;
                            $flag_cert_end = true;
                            if (!empty($item->catalog_cert)) {
                                $cert_ids = [];
                                $cert_starts = [];
                                $cert_ends = [];
                                foreach ($item->catalog_cert as $value) {
                                    if ($certificate->id == $value->cert->type) {
                                        $cert_ids[] = $value->cert->id;
                                        $cert_starts[] = $value->cert->date_start;
                                        $cert_ends[] = $value->cert->date_end;
                                    }
                                }
                                $sheet->setCellValue([$startLine, $columnPosition], implode(', ', $cert_ids));
                                $startLine++;
                                $flag_cert = false;
                                if ($certificate->date_start == 1) {
                                    $flag_cert_start = false;
                                    $sheet->setCellValue([$startLine, $columnPosition], implode(', ', $cert_starts));
                                    $startLine++;
                                }
                                if ($certificate->date_end == 1) {
                                    $flag_cert_end = false;
                                    $sheet->setCellValue([$startLine, $columnPosition], implode(', ', $cert_ends));
                                    $startLine++;
                                }
                            }
                            if($flag_cert){
                                $startLine++;
                            }
                            if ($certificate->date_start == 1 && $flag_cert_start) {
                                $startLine++;
                            }
                            if ($certificate->date_end == 1 && $flag_cert_end) {
                                $startLine++;
                            }
                        }
                        break;
                    case "attribute":
                        foreach ($this->catalog_attribute as $attribute) {
                            $sheet->setCellValue([$startLine, $columnPosition], $item->attribute[$attribute->id] ?? "");
                            $startLine++;
                        }
                        break;
                }
            }
        }

        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($document, 'Xlsx');
        $objWriter->save($this->file);
        // chmod($file, 0755);
    }
}
