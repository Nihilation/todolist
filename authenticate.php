<?php
session_start();
// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
// try connection with information above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_error() ) {
    // if there is an error, stop script and display error.
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// check if form data submitted / exists
if ( !isset($_POST['username'], $_POST['password']) ) {
    // data not retrieved
    exit('Please fill username and password fields');
}

// prevent SQL injection
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();
        // verify passsword
        if (password_verify($_POST['password'], $password)) {
            //create session if login successful
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            header('Location: home.php');
        } else {
            echo 'Incorrect username and/or password';
        }
    } else {
        echo 'Incorrect username and/or password';
    }

    $stmt->close();
}
?>