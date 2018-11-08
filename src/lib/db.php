<?php
include_once 'const.php';

function getDatabaseConnector() {
    $db = new mysqli();

    $db->connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
    if ($db->connect_error) {
        throw new Exception($db->connect_error);
    } else if (!$db->set_charset("utf8")) {
        throw new Exception('Can not set db encoding to UTF8 ' . $db->error);
    }

    return $db;
}