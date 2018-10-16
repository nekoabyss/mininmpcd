<?php
include_once "const.php";

$db = new mysqli();

header('Content-Type: application/json');
error_reporting(0);
$response = [
    'query' => $_GET,
    'name' => $tab_name,
    //'id' => 0,
    'total' => 0,
    'results' => array(),
    'response' => ['status' => 'failed'],
];

// get query result, encode as json, and print
try {
    $db->connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
    if ($db->connect_error) {
        throw new Exception($db->connect_error);
    }

    // get and prepare params
    //$response['query'] = $_GET;

    $params = [];
    if (isset($_GET['distributorName'])) $params[] = "distributor_name LIKE '%" . $db->escape_string($_GET['distributorName']) . "%'";
    if (isset($_GET['distributorId'])) $params[] = "distributor_id = " . $db->escape_string($_GET['distributorId']);

    // prepare and execute query statement
    $sql = "SELECT tab_name FROM distributor";
    if (sizeof($params) > 0) {
        $sql .= " WHERE " . implode(" AND ", $params);
    }
    //echo $sql;

    if ($tab = $db->query($sql)) {
        $tab_name = $tab->fetch_assoc()['tab_name'];

        $sql = "SELECT * FROM $tab_name";
        $sql = $db->escape_string($sql);

//        $sql2 = "SELECT distributor_id FROM distributor WHERE tab_name LIKE $tab_name";
  //      $sql2 = $db->escape_string($sql2);
    //    $sql2_result = $db->query($sql2);
      //  $id = $sql2_result->fetch_assoc();


        echo $sql2;

        if ($query_result = $db->query($sql)) {
            while ($item = $query_result->fetch_assoc()) {
                $response['results'][] = $item;

            }
            $response['name'] = $tab_name;
            //$response['id'] = $id;
            $response['total'] = sizeof($response['results']);
            $response['response']['status'] = 'success';
        }
    } else {
        throw new Exception($db->error);
    }
} catch (Exception $err) {
    $response['response']['error_message'] = $err->getMessage();
} finally {
    // close db connection
    $db->close();
}



echo json_encode($response, JSON_PRETTY_PRINT);