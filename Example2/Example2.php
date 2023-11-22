<?php

require '../PRC.php';

class Example2 extends PRC 
{
    function __construct()
    {
      $this->tbl['name'] = 'example2';

      parent::__construct();

      $this->tbl['cols'] = array(
        'id' => array(
          'type' => 'text',
          'display as' => 'ID',
          'is display' => array(
            'on listing' => true,
            'on view' => true,
            'on add' => false,
            'on edit' => false,
          ),
          'is required' => true,
        ),
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
      );
    } // __construct()

    function __destruct() {
        parent::__destruct();
    } // __destruct()
} // class