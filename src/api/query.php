<?php
include_once "../lib/db.php";

function query ($_params) {
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
        $db = getDatabaseConnector();
        // get and prepare params

        $query_params = [];
        if (isset($_params['distributorName'])) {
            $query_params[] = "strDistributorName LIKE '%" . $db->escape_string($_params['distributorName']) . "%'";
        }

        if (isset($_params['distributorId'])) $query_params[] = "strDistributorId = " . $db->escape_string($_params['distributorId']);

        if (!sizeof($query_params)) {
            throw new RuntimeException('param not match');
        }

        // prepare and execute query statement
        $sql = "SELECT strTable FROM tb_distributor";
        if (sizeof($query_params) > 0) {
            $sql .= " WHERE " . implode(" AND ", $query_params);
        }

        if ($tab = $db->query($sql)) {
            $tab_name = $tab->fetch_assoc()['strTable'];

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
                    throw new Exception('target table does not exist');
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
        if (isset($db)) {
            $db->close();
        }
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