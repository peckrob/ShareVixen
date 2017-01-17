<?php

namespace App\Http\Controllers\Service;

use Cache;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;

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

        try {
            // List blobs.
            $blobs = Cache::remember('blobs', env("AZURE_CACHE_MINUTES"), function () {
                $connectionString = "DefaultEndpointsProtocol=https;AccountName=" . env('AZURE_ACCOUNT_NAME') . ";AccountKey=" . env('AZURE_ACCOUNT_KEY') . ";";
                $client = ServicesBuilder::getInstance()->createBlobService($connectionString);
                $blob_list = $client->listBlobs(env("AZURE_CONTAINER"));
                return $blob_list->getBlobs();
            });

        } catch (ServiceException $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message.PHP_EOL;
            return;
        }

        if (!empty($blobs)) {
            foreach ($blobs as $blob) {
                $back_path = "";

                $name = $blob->getName();

                if (!empty($query)) {
                    if (!stristr($name, $query)) {
                        continue;
                    }
                }

                if (!empty($load_path)) {
                    $back_path = $load_path;

                    if (substr($load_path, -1) != "/") {
                        $load_path .= "/";
                    }

                    if (!preg_match("!^" . preg_quote($load_path) . "!i", $name)) {
                        continue;
                    }

                    $name = str_replace($load_path, "", $name);
                }
                $props = $blob->getProperties();

                $path = explode("/", $name);
                $indent_level = count($path)-1;

                if (count($path) > 0) {
                    $filename = array_pop($path);

                    $base_array_path = implode("_nodes_", $path);
                    $base_array_path = str_replace(".", "", $base_array_path);
                    $base_array_path = str_replace("_", ".", $base_array_path);

                    $array_path = "";
                    if (!empty($base_array_path)) {
                        $array_path = $base_array_path. ".nodes";
                    }

                    if (strlen($array_path) > 0) {
                        $rebuilt_path = "";
                        $rebuilt_path_components = [];

                        foreach ($path as $p) {
                            $rebuilt_path_components[] = str_replace(".", "", $p);
                            $rebuilt_path = implode(".nodes.", $rebuilt_path_components);

                            if (empty($back_path)) {
                                $back_path .= "/$p";
                            } else {
                                $back_path .= $p;
                            }

                            if (substr($back_path, 0, 1) == "/") {
                                $back_path = substr($back_path, 1);
                            }

                            if (empty($api_return["$base_array_path" . ".title"])) {
                                $api_return[$rebuilt_path . ".title"] = $p;
                                $api_return[$rebuilt_path . ".indent_level"] = count($rebuilt_path_components);
                                $api_return[$rebuilt_path . ".path"] = $back_path;
                            }
                        }

                        if (empty($api_return[$array_path])) {
                            $api_return[$array_path] = [];
                        }
                    }

                    // Hide files that start with .
                    if (preg_match("!^\..*!", $filename)) {
                        continue;
                    }

                    // Generate a download URL.
                    $blob_url = $blob->getUrl();
                    $path = parse_url($blob_url, PHP_URL_PATH);
                    $download_url = route('download', ["hash" => base64_encode($path)]);

                    $data = [
                        "title" => $filename,
                        "url" => $download_url,
                        "date" => $props->getLastModified(),
                        "size" => $props->getContentLength(),
                        "indent_level" => $indent_level + 1,
                        "path" => $back_path
                    ];

                    if (strlen($array_path) > 0) {
                        $api_return[$array_path][] = $data;
                    } else {
                        $api_return[] = $data;
                    }

                } else {
                    $filename = $path;
                }
            }
        }

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
