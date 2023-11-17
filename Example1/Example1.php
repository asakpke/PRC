<?php

require '../PRC.php';

class Example1 extends PRC 
{
    function __construct()
    {
      parent::__construct();

      $this->tblName = 'example1';
      $this->auth = "WHERE user_id = {$_SESSION['user_id']}";
      $this->tblCols = array(
        'id' => array(
          'display as' => 'ID',
          'is display' => array(
            'on listing' => true,
            'on add' => false,
            'on edit' => false,
          ),
        ),
        'name' => array(
          'display as' => 'Name',
          'is display' => array(
            'on listing' => true,
            'on add' => true,
            'on edit' => true,
          ),
        ),
        'date' => array(
          'display as' => 'Date',
          'is display' => array(
            'on listing' => true,
            'on add' => true,
            'on edit' => true,
          ),
        ),
      );
    } // __construct()

    function __destruct() {
        parent::__destruct();
    } // __destruct()
} // class