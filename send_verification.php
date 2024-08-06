<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'user_registration');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check user type based on selection
    if ($user_type == 'teacher') {
        $sql = "SELECT email FROM teachers WHERE email = ?";
    } elseif ($user_type == 'student') {
        $sql = "SELECT email FROM students WHERE email = ?";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a verification code
        $verification_code = rand(100000, 999999);

        // Store the verification code in the session
        session_start();
        $_SESSION['verification_code'] = $verification_code;
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = $user_type;

        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jamesoyay841@gmail.com'; // Your Gmail address
            $mail->Password   = 'hyen ujau mhja yqux'; // Your Gmail password or App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('jamesoyay841@gmail.com', 'ernie');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            $mail->Body    = 'Your verification code is: ' . $verification_code;

            $mail->send();
            echo 'Verification code has been sent to your email.';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        // Redirect to the verification code page
        header("Location: verification_code.php");
        exit();
    } else {
        echo 'Email address not found.';
    }

    $stmt->close();
    $conn->close();
}
?>
