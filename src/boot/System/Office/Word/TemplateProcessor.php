<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Office\Word;

use PhpOffice\PhpWord\TemplateProcessor as PhpWordTemplateProcessor;

class TemplateProcessor extends PhpWordTemplateProcessor
{    
    /**
     * Добавление ссылок
     *
     * @param  mixed $link
     * @return void
     */
    public function addLink($link)
    {
        if ($link->isInternal()) {
            return;
        }
        $linkIndex = $this->getNextRelationsIndex($this->getMainPartName());
        $link_rid_num = $linkIndex - 6;
        $link->setRelationId($link_rid_num);
        $ridLink = 'rId' . $linkIndex;
        $this->addLinkToRelations($this->getMainPartName(),  $ridLink, $link->getSource());
    }
    
    /**
     * Добавления ссылок в структуру файла
     *
     * @param  mixed $partFileName
     * @param  mixed $rid
     * @param  mixed $link
     * @return void
     */
    private function addLinkToRelations($partFileName, $rid, $link)
    {
        $relationTpl = '<Relationship Id="{RID}" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="{LINK}" TargetMode="External"/>';
        $newRelationsTpl = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>';
        $newRelationsTypeTpl = '<Override PartName="/{RELS}" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';

        $xmlLinkRelation = str_replace(array('{RID}', '{LINK}'), array($rid, $link), $relationTpl);

        if (!isset($this->tempDocumentRelations[$partFileName])) {
            $this->tempDocumentRelations[$partFileName] = $newRelationsTpl;
            $xmlRelationsType = str_replace('{RELS}', $this->getRelationsName($partFileName), $newRelationsTypeTpl);
            $this->tempDocumentContentTypes = str_replace('</Types>', $xmlRelationsType, $this->tempDocumentContentTypes) . '</Types>';
        }
        $this->tempDocumentRelations[$partFileName] = str_replace('</Relationships>', $xmlLinkRelation, $this->tempDocumentRelations[$partFileName]) . '</Relationships>';
    }
    
    /**
     * Доработка стандартного метода клонирования строки с возможностью добавления ссылок
     *
     * @param  mixed $search
     * @param  mixed $values
     * @return void
     */
    public function cloneRowAndSetValues($search, $values): void
    {
        $this->cloneRow($search, count($values));

        foreach ($values as $rowKey => $rowData) {
            $rowNumber = $rowKey + 1;
            foreach ($rowData as $macro => $replace) {
                if (is_array($replace)) {
                    $oTextRun = new \PhpOffice\PhpWord\Element\TextRun();
                    $oLink = $oTextRun->addLink($replace['link'], $replace['text'], $replace['style']);
                    $this->addLink($oLink);
                    $this->setComplexValue($macro . '#' . $rowNumber, $oLink);
                } else {
                    $this->setValue($macro . '#' . $rowNumber, $replace);
                }
            }
        }
    }
}
