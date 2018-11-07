<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 06/11/2018
 * Time: 22:26
 */

namespace database;

abstract class ActionDBType {
    const NIL = -1;
    const INSERT = 0;
    const SELECT = 1;
    const UPDATE = 2;
    const DELETE = 3;
}


//class CriteriaDB extends DB
//{
//
//    public function __construct($dsn, $username, $password, $tableName, $columnNames, $differentIndexColumn)
//    {
//        parent::__construct($dsn, $username, $password, $tableName, $columnNames, $differentIndexColumn);
//
//    }
//
//    /**
//     * @param $data = ['idClass', 'idStudent' , 'data':[[criteriaName(string), value(int)], [], []]
//     */
//    public function insertIntoTable($data)
//    {
//
//        $criteriaData = $data['data'];
//
//
//        $serveyFormDB = new DB($this->dsn, $this->username, $this->password,
//            'serveyForm', ['idClass', 'idStudent'], [0, 1]);
//
//        for ($i = 0; $i < count($criteriaData); ++$i) {
////            $serveyData = $serveyFormDB->selectData(['id'], ['idClass'=>$data['idClass'],
////                ['idStudent'=> ]]);
//        }
//
//
//
//            $this->execute();
//
//
//    }
//
//
//
//
//
//}