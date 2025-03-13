<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Login Page - AU Equipment Management System</title>

    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg,rgb(240, 26, 186) 0%,rgb(20, 20, 206) 100%);
        }
        .login-container {
            display: flex;
            width: 800px;
            height: 500px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            overflow: hidden;
        }
        .welcome-section {
            width: 50%;
            background: url('picture/try.jpg') no-repeat center center/cover;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            padding: 40px;
        }
        .login-section {
            width: 50%;
            background-color: #fff;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .login-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            width: 100%;
            margin-top: 10px;
        }
        .login-header h2 {
            margin: 0;
            font-size: 35px;
        }
        .login-header .logo {
            width: 80px;
            height: 80px;
        }
        .input-field {
            width: 100%;
            padding: 5px;
            margin-bottom: 15px;
            border: none;
            border-bottom: 2px solid rgb(46, 19, 200);
            font-size: 16px;
            box-sizing: border-box;
        }
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg,rgb(217, 47, 47),rgb(37, 2, 235));
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 25px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg,rgb(37, 2, 235),rgb(217, 47, 47));
        }
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-options a {
            color: #D92F93;
            text-decoration: none;
        }
        .form-options a:hover {
            text-decoration: underline;
        }
        .register-link {
            margin-top: 15px;
            text-align: center;
        }
        .register-link a {
            color: #D92F93;
            text-decoration: none;
        }
    </style>
</head>

<body>
<?php
$error_message = "";

if (isset($_POST['btnlogin'])) {
    require_once "config.php";

    $sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND status = 'ACTIVE'";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $accounts = mysqli_fetch_array($result, MYSQLI_ASSOC);
                session_start();

                $_SESSION['username'] = $accounts['username'];
                $_SESSION['usertype'] = $accounts['usertype'];
                header("location:equipment-management.php");
                exit();

            } else {
                $error_message = "Incorrect login details or account is inactive.";
            }
        }
    } else {
        $error_message = "Error on the select statement.";
    }
}
?>

<script>
    window.onload = function() {
        var errorMessage = "<?php echo $error_message; ?>";
        if (errorMessage !== "") {
            alert(errorMessage);
        }
    };
</script>

<div class = "login-container">
    <div class = "welcome-section">
        <h1>Equipment Management System</h1>
        <p>Sign in to continue access ></p>
    </div>

    <div class = "login-section">
        <div class = "login-header">
            <h2>Sign In</h2>
            <img src = "picture/Arellano_University_logo.png" alt = "Logo" class = "logo">
        </div>

        <form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "POST">
            <label for = "txtusername">Username:</label>
            <input type = "text" name = "txtusername" id = "txtusername" class = "input-field" required>
            <label for = "txtpassword">Password:</label>
            <input type = "password" name = "txtpassword" id = "txtpassword" class = "input-field" required>

            <div class = "form-options">
                <div>
                    <input type = "checkbox" id = "remember" name=  "remember">
                    <label for = "remember">Remember Me</label>
                </div>

                <a href = "#">Forgot Password?</a>
            </div>

            <button type = "submit" name = "btnlogin" class = "btn-login">Continue</button>
        </form>

        <div class = "register-link">
            <p>Don't have an account? <a href = "#">Register here</a></p>
        </div>
    </div>
</div>
</body>
</html>
