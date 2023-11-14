<?php

namespace Project\Controller;

use \Boot\Controller\AbstractController;
use \Boot\Services\Request;
use \Project\Grid\BlocksGrid;
use \Boot\Models\Attach;
use GuzzleHttp\Client;
use \Project\Model\Blocks;
use \Project\BlocksFTP;

class BlocksController extends AbstractController{

    static function index(){
        $grid = new BlocksGrid;
        display('@project/manager/index.html.twig', [
            'grid' =>  $grid->render(),
        ]);
    }

    static function create(){
        display('@project/manager/create.html.twig', [
            "type" => Blocks::getType(),
            "view" => Blocks::getView(),
        ]);
    }

    // static function store(Request $request){
        
    // }

    // static function show($id){
       
    // }

    // static function edit($id){
        
    // }

    // static function update(Request $request, $id){
        
    // }

    static function destroy($id){
        $block = Blocks::findOrFail($id);
        $block->delete();
        echo $block->toJson();
    }

    static function parse(Request $request){
        $files = $request->files->get("files");
        $data = [];
        foreach($files as $file){
            $originalName = $file->getClientOriginalName();
            $path = pathinfo($originalName);
            $filename = $path["filename"];
            $parse_data = \Project\Model\Blocks::parseFilename($filename);
            $data[] = [
                "filename" => $filename,
                "type" => $parse_data["type"],
                "code" => $parse_data["code"],
                "view" => $parse_data["view"],
            ];
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    static function download(Request $request){
        $id = $request->query->get("id");
        $view = $request->query->get("view");
        $type = $request->query->get("type");
        $block = Blocks::findOrFail($id);
        $download_list = [];
        switch($view){
            case "3d":
                if($type == "dwg"){
                    $name = "3d";
                    $download_list[] = [
                        "path" => $block->$name,
                        "view" => $name,
                    ];
                }
            break;
            case "2d":
                $list2d = Blocks::get2dList();
                switch($type){
                    case "dwg":
                        foreach($list2d as $item){
                            if(!empty($block->$item)) $download_list[] = [
                                "path" => $block->$item,
                                "view" => $item,
                            ];
                        }
                    break;
                    case "preview":
                        foreach($list2d as $item){
                            $name = "{$item}_preview";
                            if(!empty($block->$name)) $download_list[] = [
                                "path" => $block->$name,
                                "view" => $item,
                            ];
                        }
                    break;
                }
            break;
        }
        if($download_list){
            $local_download_list = [];
            foreach($download_list as $item){
                $BlocksFTP = new BlocksFTP;
                $BlocksFTP->remote_file = "/files/{$item["path"]}";
                $BlocksFTP->init_local_file($block, $item);
                $local_download_list[] = [
                    "path" => $BlocksFTP->local_file,
                    "filename" => BlocksFTP::get_block_name($block, $item),
                ];
                $BlocksFTP->get();
            }
            #Если выдавать один файл без архива
            // if(count($download_list) == 1){
            //     BlocksFTP::get_local_file($local_download_list[0]["path"]);
            // } else{
                
            // }
            $zip_file = ROOT."files/blocks/{$id}/".uniqid().".zip";
            $zip = new \ZipArchive();
            $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            foreach($local_download_list as $item){
                $zip->addFile($item["path"], $item["filename"]);
            }
            $zip->close();
            foreach($local_download_list as $item){
                unlink($item["path"]);
            }
            BlocksFTP::get_local_file($zip_file);
        }
    }
}