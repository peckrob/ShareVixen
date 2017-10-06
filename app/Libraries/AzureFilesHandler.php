<?php

namespace App\Libraries;

use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;

use Cache;

class AzureFilesHandler extends FilesHandler {
    public function getFiles($load_path, $query) {
        $return_files = [];

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

                            if (empty($return_files["$base_array_path" . ".title"])) {
                                $return_files[$rebuilt_path . ".title"] = $p;
                                $return_files[$rebuilt_path . ".indent_level"] = count($rebuilt_path_components);
                                $return_files[$rebuilt_path . ".path"] = $back_path;
                            }
                        }

                        if (empty($return_files[$array_path])) {
                            $return_files[$array_path] = [];
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
                        $return_files[$array_path][] = $data;
                    } else {
                        $return_files[] = $data;
                    }

                } else {
                    $filename = $path;
                }
            }
        }

        return $return_files;
    }
}
