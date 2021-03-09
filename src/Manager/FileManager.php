<?php

namespace App\Manager;

class FileManager {

    public function explodeFileToArray($filePath)
    {
        $fileRow = explode("\r\n", $filePath);
        return $this->explodeFileColownToArray($fileRow);
    }

    public function explodeFileColownToArray($fileRow)
    {
        $fileData = [];

        foreach($fileRow as $row) {
            array_push($fileData, str_getcsv($row, ","));
        }

        if(empty($fileData[count($fileData) - 1])) {
            array_pop($fileData);
        }

        return $fileData;
    }
}