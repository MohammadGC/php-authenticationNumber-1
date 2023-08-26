<?php
session_start();
require_once "connection.php"; //connection

if (isset($_GET["message"]) && !empty($_GET["message"])) {
    $message = $_GET["message"]; //A message that we sent from the registration page
}

if (isset($_SESSION['user'])) { //prevent the removal of values in the form
    unset($_SESSION['user']);
}




if (
    isset($_POST['email']) && $_POST['email'] !== ''
    && isset($_POST['password']) && $_POST['password'] !== ''
) {
    global $pdo;
    $query = 'SELECT * FROM authentication.users WHERE email = ?';
    $statement = $pdo->prepare($query);
    $statement->execute([$_POST['email']]);
    $user = $statement->fetch();

    if ($user !== false) {
        if (password_verify($_POST['password'], $user["password"])) {
            // $_SESSION['user'] = $user->email;    
            $message = "You successfully entered";
            // redirect('panel');
        } else {

            $error = 'failed password ';
        }
    } else {
        $error = 'failed email address';
    }
} else {
    if (!empty($_POST))
        $error = 'all fild required';
}


?>

<!doctype html>
<html lang="en">

<head>
    <title>login form</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="css/style.css">

</head>

<body class="img js-fullheight" style="background-image: url(images/bg.jpg);">
    <?= isset($message) ? "<h4 class='alert alert-success' role='alert'>" . $message . "</h4>" : "", isset($error) ? "<h4 class='alert alert-success' role='alert'>" . $error . "</h4>" : "" ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center mb-5">
                <h2 class="heading-section">login form</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-wrap p-0">
                    <h3 class="mb-4 text-center">have created an accunt</h3>
                    <form action="#" method="POST" class="signin-form">
                        <div class="form-group">
                            <input type="text" name="email" class="form-control" placeholder=" Email ">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="password" name="password">


                        </div>
                        <div class="form-group">
                            <button type="submit" class="form-control btn btn-primary submit px-3">Login</button>
                        </div>
                        <div class="form-group d-md-flex">
                            <div class="w-50">
                                <label class="checkbox-wrap checkbox-primary"> remember me
                                    <input type="checkbox" checked>
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="w-50 text-md-right">
                                <a href="#" style="color: #fff">forget password</a>
                            </div>
                        </div>
                    </form>
                    <p class="w-100 text-center">&mdash; login with &mdash;</p>
                    <div class="social d-flex text-center">
                        <a href="#" class="px-2 py-2 mr-md-1 rounded"><span class="ion-logo-facebook mr-2"></span> Facebook</a>
                        <a href="#" class="px-2 py-2 ml-md-1 rounded"><span class="ion-logo-twitter mr-2"></span> Twitter</a>
                        <a href="#" class="px-2 py-2 ml-md-1 rounded"><span class="ion-logo-twitter mr-2"></span> Gmail</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>

</body>

</html>