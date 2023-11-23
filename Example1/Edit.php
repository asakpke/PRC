<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require 'Example1.php';

$example1 = new Example1();
$example1->edit($_GET['id']);