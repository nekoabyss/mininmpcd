<?php

switch ($_SERVER['SERVER_ADDR']) {
    case '172.26.4.93':
        define("MYSQL_HOST", "127.0.0.1");
        define("MYSQL_PORT", "3306");
        define("MYSQL_DATABASE", "edi");
        define("MYSQL_USER", "mahidol");
        define("MYSQL_PASSWORD", "edi2018");
        break;
    case '172.18.0.2':
    default:
        define("MYSQL_HOST", "us-cdbr-iron-east-01.cleardb.net");
        define("MYSQL_PORT", "3306");
        define("MYSQL_DATABASE", "heroku_bf2cd70c758fafb");
        define("MYSQL_USER", "bc0e5f61020993");
        define("MYSQL_PASSWORD", "82e00dcf");
}
