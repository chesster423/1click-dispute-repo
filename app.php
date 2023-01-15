<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
require 'vendor/autoload.php';
include "lib/DB.php";

session_start();

foreach (glob("services/*.php") as $services)
{
    include $services;
}

include 'controller/BaseController.php';
foreach (glob("controller/*.php") as $controller)
{
	if ($controller != 'controller/BaseController.php') {
		include $controller;
	}
}
