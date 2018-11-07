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

    const TeacherName = 'Giảng viên';
    const TeacherId = 'Mã cán bộ';
    const LectureLocation = 'Giảng đường';
    const SubSubjectId = 'Lớp môn học';
    const SubjectId = 'Mã lớp môn học';
    const SubjectName = 'Môn học';
    const TinChi = 'Số tín chỉ';
    const Data = "data";
    /**
     * @param $filePath
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @return array data [[Mã sinh viên/Tên đăng nhập    Mật khẩu    Họ và tên    VNU email    Khóa đào tạo]
     * ...]
     * ]
     */
    public static function readStudentExcel($filePath)
    {
        $data = [];
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
//        $reader->setLoadSheetsOnly(["Sheet 1", "My special sudentheet"]);
        try {
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $i = 0;

            foreach ($worksheet->getRowIterator() as $row) {

                if ($i >= 1) {
                    $rowValue = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE);
                    // This loops through all cells,
                    //    even if a cell value is not set.
                    // By default, only cells that have a value
                    //    set will be iterated.
                    $j = 0;
                    foreach ($cellIterator as $cell) {

                        if ($j >= 1) {
                            $value = $cell->getValue();
                            if($j === 1) {
                                $value = preg_replace('/[^0-9]/', '', $value);
                            }
                            if (strpos($value, '&')) {
                                $arr = explode("&", $value);
                                $index = str_replace('"', '', str_replace('=', '', $arr[0]));
//                        $arr = preg_split('/(?=[^A-Z]+)(?<=[^0-9])/',$index);
                                $id = $worksheet->getCell($index)->getValue();
                                array_push($rowValue, trim($id) . trim(str_replace('"', '', $arr[1])));


                            } else {
                                if($value != null) {

                                    array_push($rowValue, trim($value));
                                }

                            }
                        }
                        ++$j;
                    }
                    if(count($rowValue) >= 1) {
                        array_push($data, $rowValue);
                    }
                }
                ++$i;

            }
        } catch (Exception $e) {
            $e->getTrace();
        }

        return $data;
    }

    public static function readTeacherExcel($filePath)
    {
        $data = [];
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
//        $reader->setLoadSheetsOnly(["Sheet 1", "My special sudentheet"]);
        try {
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $i = 0;

            foreach ($worksheet->getRowIterator() as $row) {

                if ($i >= 1) {
                    $rowValue = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE);
                    // This loops through all cells,
                    //    even if a cell value is not set.
                    // By default, only cells that have a value
                    //    set will be iterated.
                    $j = 0;
                    foreach ($cellIterator as $cell) {

                        if ($j >= 1) {
                            $value = $cell->getValue();
                            if (strpos($value, '&')) {
                                $arr = explode("&", $value);
                                $index = str_replace('"', '', str_replace('=', '', $arr[0]));
//                        $arr = preg_split('/(?=[^A-Z]+)(?<=[^0-9])/',$index);
                                $id = $worksheet->getCell($index)->getValue();
                                array_push($rowValue, trim($id) . trim(str_replace('"', '', $arr[1])));


                            } else {
                                if($value != null) {

                                    array_push($rowValue, trim($value));
                                }

                            }
                        }
                        ++$j;
                    }
                    if(count($rowValue) >= 1) {
                        array_push($data, $rowValue);
                    }
                }
                ++$i;

            }
        } catch (Exception $e) {
            $e->getTrace();
        }

        return $data;
    }

    /**
     * @param $filePath
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public static function readSubjectExcel($filePath)
    {
        $data = array();
        $data[Excel::Data] = array();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        //        $reader->setLoadSheetsOnly(["Sheet 1", "My special sudentheet"]);
        try {
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $isGetData = false;
            $isGetRowData = false;
            $i = 1;
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();

                $cellIterator->setIterateOnlyExistingCells(FALSE);
                $rowValue = [];
                $j = 1;
                foreach ($cellIterator as $cell) {
                    $value = $cell->getValue();
                    if(!$isGetData) {
                        if(str_replace(":","", $value) == Excel::LectureLocation) {
                            $data[Excel::LectureLocation] = trim($worksheet->getCellByColumnAndRow($j+1, $i)->getValue());

//                            echo "lecture location" . $data[Excel::LectureLocation] ."<br>";

                        }
                        if(str_replace(":","", $value) == Excel::TeacherId)
                        {
                            $data[Excel::TeacherId] = trim($worksheet->getCellByColumnAndRow($j+1, $i)->getValue());

//                            echo "teacher id" . $data[Excel::TeacherId] ."<br>";
                        }
                        if(str_replace(":","", $value) == Excel::TeacherName)
                        {
                            $data[Excel::TeacherName] = trim($worksheet->getCellByColumnAndRow($j+2, $i)->getValue());
//                            echo "teacher name:" . $data[Excel::TeacherName] ."<br>";
                        }

                        if(str_replace(":","", $value) == Excel::SubSubjectId)
                        {
                            $data[Excel::SubSubjectId] = trim($worksheet->getCellByColumnAndRow($j+2, $i)->getValue());
                            $data[Excel::SubjectId] = explode(" ", $data[Excel::SubSubjectId])[0];
//                            echo "sub subject id" . $data[Excel::SubSubjectId] ."<br>";

                        }

                        if(str_replace(":","", $value) == Excel::SubjectName)
                        {
                            $data[Excel::SubjectName] = trim($worksheet->getCellByColumnAndRow($j+2, $i)->getValue());
//                            echo "ten mon hoc" . $data[Excel::SubjectName] ."<br>";

                        }
                        if(str_replace(":","", $value) == Excel::TinChi)
                        {
                            $data[Excel::TinChi] = trim($worksheet->getCellByColumnAndRow($j+1, $i)->getValue());
//                            echo "Số tín chỉ:" . $data[Excel::TinChi] ."<br>";

                        }
                    } else {
                        if($j === 1 && $value != null && $isGetRowData === false) {
                            $isGetRowData = true;

                        }
                        if($isGetRowData && $j >= 2 && $value != null) {
                            array_push($rowValue, trim($value));

                        } else {
//                            echo $value == null;
                        }

                    }

                    if( $value == "STT") {
                        $isGetData = true;
//                        echo $value ."<br>";
                        break;
                    }

                    ++$j;

                }
                $isGetRowData = false;
                ++$i;
                if(count($rowValue) >= 1) {
                    array_push($data[Excel::Data], $rowValue);
                }


            }
        } catch
        (Exception $e) {
            print_r($e->getTrace());
        }

        return $data;
    }
}