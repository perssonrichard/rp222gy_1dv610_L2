<?php

/**
 * REMOTE SERVER
 */
// $dbServerName = "localhost";
// $dbUsername = "persglgr_root";
// $dbPassword = "s.Ki~F@zE@6L";
// $dbName = "persglgr_loginsystem";

/**
 * LOCAL SERVER
 */
$dbServerName = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "loginsystem";

$dbconnection = mysqli_connect($dbServerName, $dbUsername, $dbPassword, $dbName);
