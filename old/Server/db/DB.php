<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 06/11/2018
 * Time: 22:14
 */

namespace database;

class DataBase
{

    public $db;
    private $dsn, $username, $password;


    /**
     * DB constructor.
     * @param $dsn
     * @param $username
     * @param $password
     */
    public function __construct($dsn, $username, $password)
    {
        $this->db = null;
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    public function initDB() {
        $this->db = new \PDO($this->dsn, $this->username, $this->password);
    }

    public function closeDB() {
        $this->db = null;
    }


//    private function insert() {
//        $sql = 'INSERT INTO '. $this->tableName . '(';
//        for($i = 0; $i < count($this->columnNames); ++$i) {
//            $sql .= $this->columnNames[$i];
//            if($i < count($this->columnNames) -1) {
//                $sql .= ", ";
//            }
//        }
//        $sql .= ')  SELECT * FROM (SELECT ';
//        for($i = 0; $i < count($this->columnNames); ++$i) {
//            $sql .= ":".$this->columnNames[$i];
//            if($i < count($this->columnNames) -1) {
//                $sql .= ",";
//            }
//        }
////
//        $sql .= ') AS tmp WHERE NOT EXISTS (SELECT * FROM ' .$this->tableName . ' WHERE ' ;
//
//
//        for($i = 0; $i < count($this->differentIndexColumn); ++$i) {
//            $sql .= $this->columnNames[$this->differentIndexColumn[$i]] .' = ' . $this->data[$this->differentIndexColumn[$i]] ;
//            if($i < count($this->differentIndexColumn) -1) {
//                $sql .= ' AND ';
//            }
//        }
//        $sql .= ') LIMIT 1;';
////
////        print_r("sql insert query: ". $sql);
//        $stmt = $this->db->prepare($sql);
//        for($i = 0; $i < count($this->columnNames); ++$i) {
////            print_r($columnNames[$i] .", " . ($data[$i]));
//            $stmt->bindParam(':'.($this->columnNames[$i]), $this->data[$i]);
//        }
//
//        $stmt->execute();
//    }
//
//    /**
//     *
//     */
//    public function execute()
//    {
//        try {
//            $this->db = new \PDO($this->dsn, $this->username, $this->password);
//            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
//
//            $this->db->beginTransaction();
//
//
//            $this->query();
//
//
//            $this->db->commit();
//        } catch (\PDOException $e) {
//            if ($this->db !== null) {
//                $this->db->rollBack();
//            }
//            throw new \PDOException($e);
//        }
//        finally {
//            if ($this->db !== null) {
//                $this->db = null;
//            }
//
//        }
//    }
//
//    /**
//     * @param $data
//     */
//    protected function insertData($data) {
//        $this->actionDBType = ActionDBType::INSERT;
//        $this->data = $data;
//        $this->execute();
//        $this->actionDBType = ActionDBType::NIL;
//    }
//
//
//    private function select() {
//
//        $fields = '';
//        $compare = '';
//
//        if(count($this->selectField) <= 0) {
//            $fields = '*';
//        } else {
//            for($i = 0; $i < count($this->selectField); ++$i) {
//                $fields .= $this->selectField[$i];
//                if($i < count($this->selectField) - 1) {
//                    $fields .= ', ';
//                }
//            }
//        }
//
//        for($i = 0; $i < count($this->selectField); ++$i) {
//            $compare .= $this->comparation[$i][0] . '= :'.$this->comparation[$i][0];
//            if($i < count($this->selectField) - 1) {
//                $compare .= 'AND ';
//            }
//        }
//
//
//        $sql = 'SELECT ' . $fields . ' FROM ' .  $this->tableName . ' WHERE ' . $compare;
//        $stmt = $this->db->prepare($sql);
//
//        echo "sql select query: " . $sql .'<br>';
//
//        for($i = 0; $i < count($this->selectField); ++$i) {
//            $stmt->bindParam(':'. $this->comparation[$i][0], $this->comparation[$i][1]);
//        }
//
//
//        $stmt->execute();
//        $this->retSelectData = $stmt->fetchAll();
//    }
//
//    /**
//     * @param $selectField
//     * @param $comparation
//     */
//    public function selectData($selectField, $comparation) {
//        $this->comparation = $comparation;
//        $this->selectField = $selectField;
//        $this->actionDBType = ActionDBType::SELECT;
//        $this->execute();
//        $this->actionDBType = ActionDBType::NIL;
//    }

}