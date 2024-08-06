<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['email'];
    $user_type = $_SESSION['user_type'];

    if ($new_password == $confirm_password) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        if ($user_type == 'teacher') {
            $sql = "UPDATE teachers SET password = ? WHERE email = ?";
        } elseif ($user_type == 'student') {
            $sql = "UPDATE students SET password = ? WHERE email = ?";
        }

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $hashed_password, $email);
        if ($stmt->execute()) {
            echo 'Password has been reset successfully.';
        } else {
            echo 'Error updating password.';
        }

        // Clear the session
        session_unset();
        session_destroy();
    } else {
        echo 'Passwords do not match.';
    }
}
?>
