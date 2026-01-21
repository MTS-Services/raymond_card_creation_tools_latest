<?php 
require_once '../Middleware/Authentication.php';

$auth = new Authentication ;

header('Location: dashboard.php');
exit;

