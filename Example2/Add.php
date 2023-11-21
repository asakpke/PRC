<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require 'Example2.php';

$example1 = new Example2();
$example1->add();