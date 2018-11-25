<?php
include_once "../lib/db.php";

function version ($_params) {
    $response = [
        'query' => $_params,
        'info' => array(),
        'response' => ['status' => 'failed'],
        'timestamp' => '',
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
        $sql = "SELECT * FROM tb_distributor";
        if (sizeof($query_params) > 0) {
            $sql .= " WHERE " . implode(" AND ", $query_params);
        }

        if ($tab = $db->query($sql)) {
                $r = $tab->fetch_assoc();
                $response['info'][] = $r;
                $response['timestamp'] = $r['strUpdateItemData'];
                if ($r != NULL){
                    $response['response']['status'] = 'success';

                } else {
                    $response['response']['error_message'] = 'target does not exist';
                }
        } else {
            $error = $db->error;
            if (strrpos($error, "doesn't exist")) {
                throw new Exception('target does not exist');
            } else {
                throw new Exception($error);
            }
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
