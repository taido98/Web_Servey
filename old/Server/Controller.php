<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 18:33
 */

namespace controller;


abstract class Controller
{
    protected $tableName;

    public function __construct($t)
    {
        $this->tableName = $t;
    }

}