@php
    $parser = function ($html, $class_a, $class_i, $text = true) {
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        //$dom->encoding = 'UTF-8';
        $xpath = new DOMXPath($dom);
        $body = $xpath->query('/html/body/*');

        // Если первый тег a, а в нем есть i то
        $a = $body[0];

        //if ($a->nodeName == 'a') {
        $a->setAttribute('class', $class_a . $a->getAttribute('class'));

         $i = $a->childNodes[0];
         if ($i->nodeName == 'i') {
             $i->setAttribute('class', $class_i . $i->getAttribute('class'));
         }

         if (!$text) {
             $text = $a->textContent;
             $a->setAttribute('title', $text);
             $a->textContent = '';
             $a->appendChild($i);
         }
        //}

        //dd($dom->saveHTML($body->item(0)));

        return $dom->saveHTML($body->item(0));
    };
@endphp

@if ($type ?? false and $type == 'drop')
    {!! $parser($html, 'dropdown-item ', 'me-50 ') !!}
@endif

@if ($type ?? false and $type == 'btn')
    {!! $parser($html, 'btn btn-sm btn-outline-secondary ', '', false) !!}
@endif