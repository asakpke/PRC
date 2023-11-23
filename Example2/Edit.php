<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require 'Example2.php';

$example2 = new Example2();
$example2->edit($_GET['id']);