<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 02/11/2018
 * Time: 22:16
 */

namespace excel;

use PhpOffice\PhpSpreadsheet\Reader\Exception;

require 'lib/vendor/autoload.php';

class Excel
{
    /**
     * @param $filePath
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public static function readExcel($filePath)
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
//        $reader->setLoadSheetsOnly(["Sheet 1", "My special sheet"]);
        try {
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            echo '<table>' . PHP_EOL;
            foreach ($worksheet->getRowIterator() as $row) {
                echo '<tr>' . PHP_EOL;
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                //    even if a cell value is not set.
                // By default, only cells that have a value
                //    set will be iterated.
                foreach ($cellIterator as $cell) {
                    $s =  '<td>';
                    $value =  $cell->getValue();
                    if(strpos($value, '&')) {
                        $arr = explode("&", $value);
                        $index = str_replace('"', '', str_replace('=', '', $arr[0]));
//                        $arr = preg_split('/(?=[^A-Z]+)(?<=[^0-9])/',$index);
                        echo $index;
                            $id = $worksheet->getCell($index)->getValue();
                            $s .= $id . str_replace('"', '', $arr[1]);


                    }else {
                        $s .= $value;
                    }

                        echo $s . '</td>' . PHP_EOL;
                }
                echo '</tr>' . PHP_EOL;
            }
            echo '</table>' . PHP_EOL;

        } catch (Exception $e) {
            $e->getTrace();
        }


    }
}