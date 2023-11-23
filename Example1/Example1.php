<?php

require '../PRC.php';

class Example1 extends PRC 
{
    function __construct()
    {
      $this->tbl['name'] = 'example1';

      parent::__construct();

      $this->tbl['cols'] = array(
        'name' => array(
          'type' => 'text',
          'display as' => 'Name',
          'is display' => array(
            'on listing' => true,
            'on view' => true,
            'on add' => true,
            'on edit' => true,
          ),
          'is required' => true,
        ),
        'des' => array(
          'type' => 'text',
          'display as' => 'Description',
          'is display' => array(
            'on listing' => true,
            'on view' => true,
            'on add' => true,
            'on edit' => true,
          ),
          'is required' => false,
        ),
      );
    } // __construct()

    function __destruct() {
        parent::__destruct();
    } // __destruct()
} // class