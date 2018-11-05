<?php
include_once "const.php";
include_once "query.php";
include_once "create.php";

$db = new mysqli();

header('Content-Type: application/json');
error_reporting(0);

$action = $_GET['action'];
unset($_GET['action']);

switch ($action) {
    case 'query':
        $response = query($_GET);
        break;
    case 'create':
        $response = create($_GET);
        break;
    default:
        throw new Exception('');
}

echo json_encode($response, JSON_PRETTY_PRINT);