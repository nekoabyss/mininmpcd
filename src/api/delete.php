<?php

function delete ($_params) {
    $response = [
        'query' => $_params,
        'name' => '',
        'response' => ['status' => 'failed'],
    ];

    // get query result, encode as json, and print
    try {
        $db = getDatabaseConnector();

        // get and prepare params
        $query_params = [];
        if (isset($_params['delete_name'])) {
            $query_params[] = "strTable = '" . $db->escape_string($_params['delete_name']) . "'";
        }

        if (!sizeof($query_params)) {
            throw new RuntimeException('param not match');
        }

        // prepare and execute query statement
        $sql = "SELECT strTable FROM tb_distributor";
        if (sizeof($query_params) > 0) {
            $sql .= " WHERE " . implode(" AND ", $query_params);
        }

        $tab_name = $_params['delete_name'];
        $response['name'] = $db->escape_string($tab_name);

        if ($tab = $db->query($sql)) {
            if ($tab->num_rows) {
                //echo 'yayy unicornnn';
                $sql =
                    "DROP TABLE $tab_name;" .
                    "DELETE FROM tb_distributor WHERE strTable = '$tab_name'";
                echo 'table deleted';

                if ($db->multi_query($sql)) {
                    while ($db->more_results()) {
                        if (!$db->next_result()) {
                            throw new Exception($db->error);
                        }
                    }
                    $response['response']['status'] = 'success';
                } else {
                    throw new Exception($db->error);
                }
            } else {
                //echo 'unicorn has died';
                $response['response']['status'] = 'table not there';
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
