<?php
include_once "../lib/db.php";
include_once "query.php";
include_once "create.php";
include_once "delete.php";
include_once "version.php";
include_once "medicine.php";

$db = new mysqli();

header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

$action = $_GET['action'];
unset($_GET['action']);

try {
    switch ($action) {
        case 'query':
            $response = query($_GET);
            break;
        case 'create':
            $response = create($_GET);
            break;
        case 'delete':
            $response = delete($_GET);
            break;
        case 'version':
            $response = version($_GET);
            break;
        case 'med':
            $response = version($_GET);
        default:
            throw new Exception('');
    }
} catch (Exception $err) {
    $response = $err->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);