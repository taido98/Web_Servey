<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 09:57
 */

namespace database;


class ClassDB extends DB
{
    public function __construct($dsn, $username, $password, $tableName, $columnNames, $differentIndexColumn)
    {
        parent::__construct($dsn, $username, $password, $tableName, $columnNames, $differentIndexColumn);
    }
}