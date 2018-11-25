<?php
include_once "../lib/db.php";

function query ($_params) {
    if (isset($_params['distributorName']) || isset($_params['distributorId'])) {
        return _queryByDistributor($_params);
    } elseif (isset($_params['tpu']) || isset($_params['gtin'])) {
        return _queryByCode($_params);
    } else {
        $response = _initResponse($_params);
        $response['response']['error_message'] = "required parameters not included";
        return $response;
    }
}

function _initResponse($_params) {
    return [
        'query' => $_params,
        'name' => '',
        //'id' => 0,
        'total' => 0,
        'results' => array(),
        'response' => ['status' => 'failed'],
        'timestamp' => '',
    ];
}

function _queryByDistributor($_params) {
    $response = _initResponse($_params);

    // get query result, encode as json, and print
    try {
        $db = getDatabaseConnector();
        // get and prepare params

        $query_params = [];
        if (isset($_params['distributorName'])) {
            $query_params[] = "strDistributorName LIKE '%" . $db->escape_string($_params['distributorName']) . "%'";
        }
        if (isset($_params['distributorId'])){
            $query_params[] = "strDistributorId LIKE '%" . $db->escape_string($_params['distributorId']) . "%'";
        }

        // prepare and execute query statement
        $sql = "SELECT * FROM tb_distributor";
        if (sizeof($query_params) > 0) {
            $sql .= " WHERE " . implode(" AND ", $query_params);
        }

        if ($tab = $db->query($sql)) {
            $r = $tab->fetch_assoc();

            if (!$r) {
                throw new Exception('distributor not found');
            }

            $tab_name = $r['strTable'];
            $response['name'] =  $r[''];
            $response['timestamp'] = $r['strUpdateItemData'];
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

function _queryByCode($_params) {
    $response = _initResponse($_params);

    // get query result, encode as json, and print
    try {
        $db = getDatabaseConnector();
        // get and prepare params
        $sql = "SELECT * FROM tb_distributor";

        if ($tab = $db->query($sql)) {
            $tables = array();

            while ($item = $tab->fetch_assoc()) $tables[] = $item['strTable'];

            // prepare and execute query statement
            $query_params = [];
            if (isset($_params['tpu'])) {
                $query_params[] = "tpu_code = " . $db->escape_string($_params['tpu']);
            }
            if (isset($_params['gtin'])) {
                $query_params[] = "gtin = " . $db->escape_string($_params['gtin']);
            }
            $param = implode(" OR ", $query_params);
            $tables = array_map(function ($table) use ($param) { return "SELECT '$table' AS distributor, $table.* FROM $table WHERE $param"; }, $tables);
            $sql = "SELECT * FROM (" . implode(" UNION ", $tables) . ") t ORDER BY t.item_code";

            if ($query_result = $db->query($sql)) {
                while ($item = $query_result->fetch_assoc()) {
                    $response['results'][] = $item;
                }

                unset($response['name']);
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