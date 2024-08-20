<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKILLSPHERE-Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container">
        <div class="gif-side"></div>
        <div class="login-side">
            <form class="login" action="login.php" method="POST">
                <b><h2 style="font-size: 40px;">Welcome to SKILLSPHERE</h2></b><br><br>
                <div class="login__field">
                    <i class="login__icon fas fa-user"></i>
                    <input type="text" class="login__input" name="email" placeholder="Email" autocomplete="off" required>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-lock"></i>
                    <input type="password" class="login__input" name="password" placeholder="Password" autocomplete="off" required>
                </div>
                <button type="submit" class="button login__submit" name="login">
                    <span class="button__text">Log In Now</span>
                    <i class="button__icon fas fa-chevron-right"></i>
                </button><br><br>
                <h4>Don't have an account <a href="student_dashboard/registration.php">Enroll Now</a></h4><br>
                <center>
                    <h3>
                        <a href="index.php">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                    </h3>
                </center>        
            </form>
        </div>
    </div>
</body>
</html>

<?php
session_start();
include("dbase.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Function to check user credentials and return user data if found
    function checkUser($table, $email, $passwordField, $password) {
        global $conn;
        $sql = "SELECT * FROM $table WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (isset($user[$passwordField]) && password_verify($password, $user[$passwordField])) {
                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Check admin login
    $adminUser = checkUser("admin", $email, "password", $password);
    if ($adminUser) {
        $_SESSION['admin_email'] = $adminUser['email'];
        header("Location: admin_home.php");
        exit;
    }

    // Check teacher login
    $teacherUser = checkUser("teacher", $email, "Password", $password);
    if ($teacherUser) {
        $_SESSION['teacher_email'] = $teacherUser['Email'];
        header("Location: teacher_home.php");
        exit;
    }

    // Check student login
    $studentUser = checkUser("student", $email, "password", $password);
    if ($studentUser) {
        // Additional check for students in the payments table
        $sql_payments = "SELECT * FROM payments WHERE email = ?";
        $stmt_payments = $conn->prepare($sql_payments);
        $stmt_payments->bind_param("s", $email);
        $stmt_payments->execute();
        $result_payments = $stmt_payments->get_result();
        if ($result_payments->num_rows > 0) {
            $_SESSION['student_email'] = $studentUser['email'];
            header("Location: student_home.php");
            exit;
        } else {
            // If student email not found in payments table
            echo "Student email not found in payments table.";
        }
    }

    // If none of the above matches, display appropriate error message
    echo "Invalid email or password.";
}

$conn->close();
?>