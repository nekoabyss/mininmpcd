<?php
include_once "const.php";

function query ($_params) {
    $db = new mysqli();

    $response = [
        'query' => $_params,
        'name' => '',
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

        $params = [];
        if (isset($_params['distributorName'])) {
            $params[] = "distributor_name LIKE '%" . $db->escape_string($_params['distributorName']) . "%'";
        }
        if (isset($_params['distributorId'])) $params[] = "distributor_id = " . $db->escape_string($_params['distributorId']);

        if (!sizeof($params)) {
            throw new RuntimeException('param not match');
        }

        // prepare and execute query statement
        $sql = "SELECT tab_name FROM distributor";
        if (sizeof($params) > 0) {
            $sql .= " WHERE " . implode(" AND ", $params);
        }
        //echo $sql;

        if ($tab = $db->query($sql)) {
            //echo json_encode($tab);
            $tab_name = $tab->fetch_assoc()['tab_name'];

            $sql = "SELECT * FROM $tab_name";
            $sql = $db->escape_string($sql);

            if ($query_result = $db->query($sql)) {
                while ($item = $query_result->fetch_assoc()) {
                    $response['results'][] = $item;

                }
                $response['name'] = $tab_name;
                $response['total'] = sizeof($response['results']);
                $response['response']['status'] = 'success';
            } else {
                $error = $db->error;
                if (strpos($error, "doesn't exist")) {
                } else {
                    throw new Exception($error);
                }
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

    return $response;
}

// ตารางกลาง for timestamp
// timestamp
// version
// ftp / putty upload to server ; check
// merge with kai's
// first upload create table
// else add to exist table
// upload update middle table time stamp
//------------------------------------------
// api get version ; timestamp from middle table
// create session ; login