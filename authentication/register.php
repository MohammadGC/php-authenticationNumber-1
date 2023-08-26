<?php
session_start();
require_once "connection.php";



if (isset($_SESSION["old"])) {
    unset($_SESSION["temporary_old"]);
}
if (isset($_SESSION["old"])) {
    $_SESSION["temporary_old"] = $_SESSION["old"];
    unset($_SESSION["old"]);
}

$params = [];
$params = !isset($_GET) ? $params : array_merge($params, $_GET);
$params = !isset($_POST) ? $params : array_merge($params, $_POST);
$_SESSION["old"] = $params;
unset($params);

function old($name)
{
    if (isset($_SESSION["temporary_old"][$name]) && !empty($_SESSION["temporary_old"][$name])) {
        return $_SESSION["old"][$name];
    } else {
        return null;
    }
}


$nameError = $familyError = $userError = $passwordError = $conPasswordError = $agreeFormErr =
    $duplicateUser =  $emailError = "";
$name = $family = $user = $password = $conPassword = $hashed_password = $ok = $email = "";

$clsformName = $clsformfamily = $clsformuser = $clsformpassword = $clsformConPassword = $clsformAgree = $clsformEmail = "";
function validateInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    if (empty($_POST['first_name'])) {
        $clsformName = "is-invalid";
        $nameError = 'required first name';
    } else {
        $name = validateInput($_POST['first_name']);
        if (!preg_match("#^[ا-یa-zA-Zء-ي ]+$#u", $name)) {
            $nameError = 'The entered name should only contain letters and distance';
            $clsformName = "is-invalid";
        } else {
            $clsformName = " is-valid";
        }
    }

    if (empty($_POST['last_name'])) {
        $clsformfamily = "is-invalid";
        $familyError = "Please enter the last name";
    } else {
        $family = validateInput($_POST['last_name']);

        if (!preg_match("#^[ا-یa-zA-Zء-ي ]+$#u", $family)) {
            $clsformfamily = "is-invalid";
            $familyError = "The entered last name should only contain letters and distance";
        } else {
            $clsformfamily = "is-valid";
        }
    }
    if (empty($_POST['user_name'])) {
        $userError = "Please enter a user name";
        $clsformuser = "is-invalid";
    } else {
        $user = validateInput($_POST['user_name']);

        if (!preg_match("/^[a-zA-Z ]+$/", $user)) {
            $userError = "The entered User name should only contain letters and distance";
            $clsformuser = "is-invalid";
        } else {
            $clsformuser = "is-valid";
        }
    }

    if (empty($password) || strlen($password) < 8) {
        $clsformpassword = "is-invalid";
        $passwordError = "Password is compulsory and should not be the number of characters less than 8 characters";
    } elseif (strlen($_POST["password"]) >= 8) {
        $password = validateInput($_POST['password']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $clsformpassword = "is-valid";
    }

    if (empty($_POST['conPassword'])) {//repeat password
        $conPaswordError = "Please enter a valid password";
        $clsformConPassword = "is-invalid";
    } elseif (!password_verify($_POST['conPassword'], $hashed_password)) {
        $conPasswordError = "The entered password does not match";
        $clsformConPassword = "is-invalid";
    } else {
        $conPassword = validateInput($_POST['conPassword']);
        $clsformConPassword = "is-valid";
    }

    if (empty($_POST['email'])) {
        $emailError = 'required email address';
        $clsformEmail = "is-invalid";
    } else {
        $email = validateInput($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = 'The logged email does not have the correct format';
            $clsformEmail = "is-invalid";
        } else {
            $query = "SELECT * FROM `users` WHERE `email` = ? ";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // The email address of the user is present in the database.
                $duplicateUser = "You have already registered";
            }
            $clsformEmail = "is-valid";
        }
    }


    if (!isset($_POST["agreeForm"])) {
        $agreeFormErr = "Please enable agreement with site rules";
        $clsformAgree = "is-invalid";
    } else {
        $clsformAgree = "is-valid";
    }

    if (
        empty($nameError) && empty($familyError) && empty($userError) && empty($passwordError) && empty($conPasswordError) && empty($agreeFormErr) && empty($duplicateUser) &&
        empty($emailError)
    ) {


        $query = "INSERT INTO `users` SET `first_name` = ?,`last_name` = ?,`user_name` = ?,`password` = ?,`email` = ?,`created_at` = NOW();) ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$first_name, $last_name, $user_name, $hashed_password, $email]);
        $user = $stmt->fetch();


        header("location:login.php?message=" . "Your registration has been successfully completed. Please login.");//send a notification to login page
        die;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="css/style.home.css">

</head>

<body>

    <div class="background">
        <div class="container">
            <form class="row g-3 " action="register.php" method="POST">
                <div class="col-md-4">
                    <label for="validationServer01" class="form-label">Name </label>
                    <input type="text" class="form-control   <?= $clsformName ?>" name="first_name" id="validationServer01" value="<?= old("first_name") ?>">
                    <div class="text-danger">
                        <?php
                        echo $nameError;
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="validationServer02" class="form-label">Last Name </label>
                    <input type="text" class="form-control  <?= $clsformfamily ?>" name="last_name" id="validationServer02" value="<?= old("last_name") ?>">
                    <div class="valid-feedback">
                        <?php
                        echo $familyError;
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="validationServerUsername" class="form-label">User Name </label>
                    <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend3">@</span>
                        <input type="text" class="form-control <?= $clsformuser ?> " name="user_name" id="validationServerUsername" aria-describedby="inputGroupPrepend3 validationServerUsernameFeedback" value="<?= old("user_name") ?>">
                        <div class="text-danger">
                            <?php
                            echo $userError;
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="validationServer03" class="form-label">Password </label>
                    <input type="password" class="form-control <?= $clsformpassword ?> " name="password" id="validationServer03" aria-describedby="validationServer03Feedback" value="<?= old("password") ?>">
                    <div id="validationServer03Feedback" class="text-danger">
                        <?php
                        echo $passwordError;
                        ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="validationServer03" class="form-label"> Repeat the password</label></label>
                    <input type="password" class="form-control <?= $clsformConPassword ?> " name="conPassword" id="validationServer03" aria-describedby="validationServer03Feedback" value="<?= old("conPassword") ?>">
                    <div id="validationServer03Feedback" class="text-danger">
                        <?php
                        echo $conPasswordError;
                        ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="validationServer03" class="form-label">Email</label></label>
                    <input type="text" class="form-control <?= $clsformEmail ?> " name="email" id="validationServer03" aria-describedby="validationServer03Feedback" value="<?= old("email") ?>">
                    <div id="validationServer03Feedback" class="text-danger">
                        <?php
                        echo $emailError;
                        ?>
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input <?= $clsformAgree ?>" type="checkbox" value="" name="agreeForm" id="invalidCheck3" aria-describedby="invalidCheck3Feedback">
                        <label class="form-check-label" for="invalidCheck3">
                            Agree to the rules
                        </label>
                        <div id="invalidCheck3Feedback" class="text-danger">
                            <?php
                            echo $agreeFormErr;
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Submit</button>
                    <div class="text-danger">
                        <?php
                        echo $duplicateUser;
                        ?>
                    </div>
                    <div class="text-success">
                        <?php
                        echo $ok;
                        ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>

</html>