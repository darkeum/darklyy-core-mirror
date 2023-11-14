<?php

namespace Catalog\Stock;

use Boot\Support\Carbon;

abstract class Parser
{

    static $tempFiles = ROOT . "usr/modules/catalog/Stock/TempFiles/";

    abstract public function parse_();
    abstract public function getManufacture();

    static function parse(){
        $class = new static;
        $class->catalogManufactureStockParserUpdate(["work" => 1]);
        try {
            $class->parse_();
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
        $class->catalogManufactureStockParserUpdate(["work" => 0, "last_date" => \Carbon\Carbon::now()]);
    }

    static function client()
    {
        return new \GuzzleHttp\Client([
            "cookies" => true,
            "verify" => false,
        ]);
    }

    public function findCatalog($name, $sku)
    {
        if (empty($name) && empty($sku)) {
            return [];
        } else {
            return \Catalog_v2\Model\Catalog::where("man", $this->getManufacture())->where(function ($query) use ($sku, $name) {
                if (!empty($sku)) {
                    $query->orWhere("sku", $sku)->orWhere("sku2", $sku)->orWhere("sku3", $sku);
                }
                if (!empty($name)) {
                    $query->orWhere("title", $name);
                }
            })->get();
        }
    }

    public function updateOrCreate($cid, $count, $date)
    {
        return \Catalog\Model\CatalogManufactureStock::updateOrCreate(
            ["cid" => $cid, "model_type" => get_class($this),],
            ["count" => $count, "date" => $date,],
        );
    }

    static function prepareValue($value){
        $new_value = $value;
        $new_value = trim($new_value, '&nbsp;');
        $new_value = html_entity_decode($new_value);
        $new_value = trim($new_value);
        return $new_value;
    }

    public function catalogManufactureStockParserUpdate(Array $data){
        return \Catalog\Model\CatalogManufactureStockParser::where("model_type", get_class($this))->update($data);
    }

    static function dumpData($sku, $name, $count, $catalog){
        dump("Артикул: {$sku}; Наименование: {$name}; Количество: {$count}; Найденное количество: ".count($catalog));
    }

    public function emailDownload():object
    {
        if (!isset($this->email)) throw new \Exception("Email not found!");
        if (!isset($this->password)) throw new \Exception("Password not found!");
        if (!isset($this->from)) throw new \Exception("From not found!");

        $imap = imap_open($this->mailbox, $this->email, $this->password);
        $mails_id = imap_search($imap, "FROM {$this->from}");

        $email = (object)[
            "date" => null,
            "files_patch" => [],
        ];
        
        if ($mails_id) {
            rsort($mails_id);
            $mail_id = $mails_id[0];

            $header = imap_headerinfo($imap, $mail_id);
            $email->date = Carbon::parse($header->date)->format("Y-m-d");
            
            /* get mail structure */
            $structure = imap_fetchstructure($imap, $mail_id);

            $attachments = array();

            /* if any attachments found... */
            if (isset($structure->parts) && count($structure->parts)) {
                for ($i = 0; $i < count($structure->parts); $i++) {
                    $attachments[$i] = array(
                        'is_attachment' => false,
                        'filename' => '',
                        'name' => '',
                        'attachment' => ''
                    );

                    if ($structure->parts[$i]->ifdparameters) {
                        foreach ($structure->parts[$i]->dparameters as $object) {
                            if (strtolower($object->attribute) == 'filename') {
                                $attachments[$i]['is_attachment'] = true;
                                $attachments[$i]['filename'] = $object->value;
                            }
                        }
                    }

                    if ($structure->parts[$i]->ifparameters) {
                        foreach ($structure->parts[$i]->parameters as $object) {
                            if (strtolower($object->attribute) == 'name') {
                                $attachments[$i]['is_attachment'] = true;
                                $attachments[$i]['name'] = $object->value;
                            }
                        }
                    }

                    if ($attachments[$i]['is_attachment']) {
                        $attachments[$i]['attachment'] = imap_fetchbody($imap, $mail_id, $i + 1);

                        /* 3 = BASE64 encoding */
                        if ($structure->parts[$i]->encoding == 3) {
                            $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                        }
                        /* 4 = QUOTED-PRINTABLE encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                            $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                        }
                    }
                }
            }

            /* iterate through each attachment and save it */
            foreach ($attachments as $attachment) {
                if ($attachment['is_attachment'] == 1 && (!empty($attachment["name"]) || !empty($attachment["filename"]))) {
                    $filename = $attachment['name'];
                    if (empty($filename)) $filename = $attachment['filename'];

                    $file_patch = static::$tempFiles . $filename;
                    $files_patch[] = $file_patch;
                    $fp = fopen($file_patch, "w+");
                    fwrite($fp, $attachment['attachment']);
                    fclose($fp);
                }
            }
            $email->files_patch = $files_patch;
        }

        imap_close($imap);
        return $email;
    }
}
