<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_code = $_POST['verification_code'];

    if ($input_code == $_SESSION['verification_code']) {
        // Verification successful, redirect to reset password page
        header("Location: reset_password.php");
        exit();
    } else {
        echo 'Invalid verification code.';
    }
}
?>
