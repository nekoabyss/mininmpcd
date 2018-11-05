<?php
include_once "const.php";

function create ($_params) {
    $db = new mysqli();

    $response = [
        'query' => $_params,
        'name' => '',
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
        if (isset($_params['register'])) {
            $params[] = "tab_name = '" . $db->escape_string($_params['register']) . "'";
        }

        if (!sizeof($params)) {
            throw new RuntimeException('param not match');
        }

        // prepare and execute query statement
        $sql = "SELECT tab_name FROM distributor";
        if (sizeof($params) > 0) {
            $sql .= " WHERE " . implode(" AND ", $params);
        }
        //echo $sql;
        $tab_name = $_params['register'];
        $response['name'] = $db->escape_string($tab_name);

        if ($tab = $db->query($sql)) {
            if ($tab->num_rows) {
                //echo 'yayy unicornnn';
                $response['response']['status'] = 'table is already there';
            } else {
                //echo 'unicorn has died';
                $name = str_replace('table', '', $tab_name);
                $id = strval(time()); //random number
                $sql =
                    "INSERT INTO distributor (distributor_id, distributor_name, tab_name)" .
                    "VALUES ('$id', '$name', '$tab_name');" .
                    "CREATE TABLE $tab_name (" .
                    "  item_code int(65) NOT NULL," .
                    "  tpu_code int(65) NOT NULL," .
                    "  gtin int(65) NOT NULL," .
                    "  item_name_en varchar(255) NOT NULL," .
                    "  item_name_th varchar(255) NOT NULL," .
                    "  item_brand varchar(255) NOT NULL," .
                    "  price_per_unit float(65, 2) NOT NULL," .
                    "  base_unit_qty int(65) NOT NULL," .
                    "  base_unit varchar(255) NOT NULL," .
                    "  pack_unit_qty int(65) NOT NULL," .
                    "  pack_unit varchar(255) NOT NULL," .
                    "  packsize_desc varchar(255) NOT NULL," .
                    "  price_currency varchar(255) NOT NULL," .
                    "  remark varchar(255)," .
                    "  isActive boolean NOT NULL," .
                    "  primary key (item_code)" .
                    ")";
                echo 'table created';

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