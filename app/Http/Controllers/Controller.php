<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('main');
    }

    public function download($hash)
    {
        $path = base64_decode($hash);

        switch (env("FILE_DRIVER")) {
            case 'azure':
                $url = "https://" . env('AZURE_ACCOUNT_NAME') . ".blob.core.windows.net" . $path;
                return redirect($url);
                break;

            case 'files':
            default:
                $file_path = storage_path("files");
                $path = storage_path("files/$path");
                $real_path = realpath($path);
                $name = basename($real_path);

                if ($real_path !== false && strpos($path, $file_path) === 0 && file_exists($real_path)) {
                    return response()->download($real_path, $name);
                }
                break;
        }

        return abort(404);
    }
}
