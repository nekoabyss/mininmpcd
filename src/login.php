<?php
include_once "api/const.php";

session_start();

if (sizeof($_POST) > 0) {
    // create db connector
    $db = new mysqli();
    try {
        $db->connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
        if ($db->connect_error) {
            throw new Exception($db->connect_error);
        }
        // query with username-hashedPassword
        $username = $db->escape_string($_POST['username']);
        $password = $db->escape_string($_POST['password']);
        $sql = "SELECT * FROM users WHERE username = '$username' AND password_digest = '$password'";
        if ($query_result = $db->query($sql)) {
            $user = $query_result->fetch_assoc();
            if (!isset($_SESSION['user_id'])) {
                 $_SESSION['user_id'] = $user['id'];
            }
        }
    } finally {
        // close db connection
        $db->close();
    }
}


if (isset($_SESSION['user_id'])) {
    echo 'loginnnnn';
    die();
//    http_redirect('./index.php');
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</head>
<body>

<div class="container">
    <h2>Login Form</h2>

    <form id="login-form" method="post">

        <div class="container">
<!--            username-->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Username</span>
                </div>
                <input type="text" class="form-control" name="username" value="<?php echo $_POST['username'] ?>"/>
            </div>
<!--            password-->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Password</span>
                </div>
                <input type="password" class="form-control" name="password" value=""/>
            </div>
            <button type="button" class="btn btn-success btn-lg" onclick="document.getElementById('login-form').reset()">Reset</button>
            <button type="submit" class="btn btn-outline-danger btn-sm">Login</button>

        </div>

    </form>

</div>
</body>
</html>
