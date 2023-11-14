<?php

namespace Catalog\Stock\Provider;

use Boot\Support\Carbon;
use Boot\System\Office\Spreadsheet\SpreadsheetSupport;
use \Catalog\Stock\Parser;

use PhpExtended\Endpoint\HttpEndpoint;
use PhpExtended\Endpoint\HttpJsonEndpoint;
use PhpExtended\HttpMessage\Response;
use PhpExtended\HttpMessage\StringStream;
use PhpExtended\MegaNzApi\MegaEndpoint;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Ebara extends Parser
{
    public function getManufacture(){
        return 116;
    }

    public function parse_()
    {
        // $urlFragment = "https://mega.nz/folder/vEpgATxA#6ZRmPG5aPXCbmBl8si5ouA";
        $urlFragment = static::getMegaLink();

        if (!empty($urlFragment)) {

            // $file_patch = static::$tempFiles."stock 15.06.22.XLSX";
            $file_patch = static::downloadFile($urlFragment);

            if($file_patch){
                $filename = basename($file_patch);
                preg_match('/\d{2}.\d{2}.\d{2}/Uis', $filename, $matches);
                $date = $matches[0];
                $date = Carbon::createFromFormat("d.m.y", $date);
                $date = $date->format("Y-m-d");
                $excel = new SpreadsheetSupport;
                $excel->init_file(["tmp_name" => $file_patch]);
                $data = $excel->excel_to_array();
                $len = count($data) - 1;
                for($i = 1; $i < $len; $i++){
                    $item = $data[$i];
                    $sku =   static::prepareValue($item[0]);
                    $name =  static::prepareValue($item[1]);
                    $count = (double)static::prepareValue($item[4] < 0 ? 0 : $item[4]);
                    $catalog = $this->findCatalog($name, $sku);
                    static::dumpData($sku, $name, $count, $catalog);
                    foreach($catalog as $item){
                        $this->updateOrCreate($item->id, $count, $date);
                    }
                }
                $this->catalogManufactureStockParserUpdate(["last_date_file" => $date]);
                unlink($file_patch);
            }
        }
    }

    static function getMegaLink(){
        $client = static::client();
        $content = $client->request("GET", "https://www.ebaraeurope.ru/%D0%BD%D0%B0-%D1%81%D0%BA%D0%BB%D0%B0%D0%B4%D0%B5/")->getBody();
        preg_match_all('|<a href="([\w:\/#\.]*)" target="__blank" class="sn-button --primary mt-5">|Uis', $content, $matches);
        return $matches[1][0] ?? null;
    }

    static function downloadFile($urlFragment){
        $url = explode("/", $urlFragment);
        $client = static::getClientMega();
        $mega = new MegaEndpoint(new HttpJsonEndpoint(new HttpEndpoint($client)), $url[4]);
        $root = $mega->getRootNodeInfo();
        $files = $mega->getChildren($root);
        if (isset($files[0])) {
            $file = $files[0];
            $file_name = $file;

            $file_name = $file->getAttributes()->getName();
            $mega->downloadSynchronize($file, static::$tempFiles);
            return static::$tempFiles.$file_name;
        }
        return false;
    }

    static function getClientMega()
    {
        return new class implements ClientInterface
        {

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                echo date('Y-m-d H:i:s') . " SENDING\n";
                echo $request->getMethod() . ' ' . $request->getUri()->__toString() . "\n";
                $headers = [];

                foreach ($request->getHeaders() as $headerKey => $headerArr) {
                    echo "\t" . $headerKey . ': ' . \implode(', ', $headerArr) . "\n";
                    $headers[] = $headerKey . ': ' . \implode(', ', $headerArr);
                }

                echo "BODY: " . $request->getBody()->__toString() . "\n";

                $context = \stream_context_create(['http' => [
                    'protocol_version' => 1.1,
                    'method' => $request->getMethod(),
                    'header' => \implode("\r\n", $headers) . "\r\n",
                    'content' => $request->getBody()->__toString(),
                ]]);

                $http_response_header = [];

                $data = (string) \file_get_contents($request->getUri()->__toString(), false, $context);

                echo date('Y-m-d H:i:s') . " RECEIVED\n";
                $gzdecode = false;

                foreach ($http_response_header as $headerval) {
                    if (stripos($headerval, 'Content-Encoding: gzip') !== false) {
                        $gzdecode = true;
                    }
                }

                if ($gzdecode)
                    $data = \gzdecode($data);

                // \var_dump($data);

                return (new Response())->withBody(new StringStream($data));
            }
        };
    }
}
