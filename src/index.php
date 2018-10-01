<?php
include_once "lib/db.php";

header('Content-Type: application/json');

$response = [
    'results' => array(),
    'response' => ['status' => 'failed'],
];

// get and prepare params
$response['query'] = $_GET;

$params = [];
if (isset($_GET['distributorName'])) $params[] = "distributorName LIKE %" . $_GET['distributorName'] . "%";
if (isset($_GET['distributorId'])) $params[] = "distributor_id = " . $_GET['distributorId'];

// prepare and execute query statement
$sql = "SELECT tab_name FROM distributor";
if (sizeof($params) > 0) {
    $sql .= " WHERE " . implode(" AND ", $params);
}
$sql = $db->escape_string($sql);

// get query result, encode as json, and print
$tab = $db->query($sql);
if ($tab) {
    $tab_name = $tab->fetch_assoc()['tab_name'];

    $sql = "SELECT * FROM $tab_name";
    $sql = $db->escape_string($sql);

    if ($query_result = $db->query($sql)) {
        while ($item = $query_result->fetch_assoc()) {
            $response['results'][] = $item;
        }
        $response['response']['status'] = 'success';
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);

// close db connection
$db->close();