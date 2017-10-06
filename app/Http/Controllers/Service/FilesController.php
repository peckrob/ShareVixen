<?php

namespace App\Http\Controllers\Service;

use Cache;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class FilesController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $api_return = [];

        $query = $request->input("q");
        $load_path = urldecode($request->input("p"));
        $load_level = (int)$request->input("l");

        $file_handler = "FilesFilesHandler";
        switch (env("FILE_DRIVER")) {
            case 'azure':
                $file_handler = "App\Libraries\AzureFilesHandler";
                break;

            default:
                $file_handler = "App\Libraries\FilesFilesHandler";
                break;
        }

        $file_object = new $file_handler();
        $api_return = $file_object->getFiles($load_path, $query);

        $array = array();
        foreach ($api_return as $key => $value) {
            array_set($array, $key, $value);
        }

        $array = array_values($array);

        if ($load_level > 0) {
            $foo = function(&$array) use (&$foo, $load_level) {
                foreach ($array as $key => &$value) {
                    if (!empty($value["indent_level"]) && $value["indent_level"] > $load_level) {
                        unset($array[$key]);
                        continue;
                    }

                    if (!empty($value["nodes"])) {
                        $foo($value["nodes"]);
                    }
                }
            };

            $foo($array);
        }

        $sort = function($a, $b) {
            $ret = 0;

            if (!isset($a["nodes"]) && !isset($b["nodes"])) {
                $ret = 0;
            }

            if (isset($a["nodes"]) && !isset($b["nodes"])) {
                $ret = -1;
            }

            if (!isset($a["nodes"]) && isset($b["nodes"])) {
                $ret = 1;
            }

            if ($ret == 0) {
                $ret = (int)($a["title"] > $b["title"]);
            }

            return $ret;
        };

        $strip_and_sort = function($array) use (&$strip_and_sort, $sort) {
            if (!empty($array["title"])) {
                return $array;
            }

            foreach ($array as $key => &$value) {
                if (!empty($value["nodes"])) {
                    $value["nodes"] = array_values($value["nodes"]);

                    // Magic.
                    usort($value["nodes"], $sort);

                } else {
                    $value = $strip_and_sort($value);
                }
            }

            usort($array, $sort);

            return $array;
        };

        $api_return = $strip_and_sort($array);

        return response()->json($api_return);
    }
}
