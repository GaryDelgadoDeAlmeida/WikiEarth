<?php

namespace App\Manager;

class FileManager {

    public function explodeFileToArray($filePath)
    {
        $fileRow = explode("\r\n", $filePath);

        // If in the file there is no "\r\n" then it probably have only "\n"
        if(!\is_array($fileRow) || (!is_array($fileRow[0]))) {
            $fileRow = explode("\n", $filePath);
        }
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