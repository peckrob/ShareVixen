<?php

namespace App\Libraries;

abstract class FilesHandler {
    abstract public function getFiles($load_path, $query);
}
