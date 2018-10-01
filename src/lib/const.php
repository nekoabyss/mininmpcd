<?php

switch ($_ENV['environment']) {
    case 'production':
        define("MYSQL_HOST", "");
        define("MYSQL_PORT", "");
        define("MYSQL_DATABASE", "");
        define("MYSQL_USER", "");
        define("MYSQL_PASSWORD", "");
        break;
    case 'dev':
    default:
        define("MYSQL_HOST", "us-cdbr-iron-east-01.cleardb.net");
        define("MYSQL_PORT", "3306");
        define("MYSQL_DATABASE", "heroku_bf2cd70c758fafb");
        define("MYSQL_USER", "bc0e5f61020993");
        define("MYSQL_PASSWORD", "82e00dcf");
}
