<?php

require '../PRC.php';

class Example1 extends PRC 
{
    function __construct()
    {
      $this->tbl['name'] = 'example1';

      parent::__construct();

      // $this->auth = "WHERE user_id = {$_SESSION['user_id']}"; // we may adjust or fix it later on
      $this->tbl['cols'] = array(
        // 'id' => array(
        //   'type' => 'text',
        //   'display as' => 'ID',
        //   'is display' => array(
        //     'on listing' => false,
        //     'on view' => false,
        //     'on add' => false,
        //     'on edit' => false,
        //   ),
        //   'is required' => true,
        // ),
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
        'desc' => array(
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