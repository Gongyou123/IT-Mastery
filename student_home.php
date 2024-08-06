<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM student_profiles WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();
$conn->close();

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Home</title>
    <style type="text/css">
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        body {
            font-family: 'Arial', sans-serif;
            background: url('background/pic1.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #2c3e50;
            animation: fadeIn 1s ease-in-out;
        }

        .container {
            background: url('student_profile.jpg') no-repeat center center;
            background-size: cover;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
            background-blend-mode: lighten;
            background-color: rgba(255, 255, 255, 0.8); /* Optional: add a semi-transparent overlay */
            animation: slideInUp 1s ease-in-out;
        }

        h2 {
            font-size: 28px;
            color: #34495e;
            margin-bottom: 20px;
            animation: fadeIn 1.5s ease-in-out;
        }

        p {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
            animation: fadeIn 2s ease-in-out;
        }

        a {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 12px 24px;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 16px;
            margin: 5px 0;
        }

        a:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .logout-button {
            background-color: #e74c3c;
        }

        .logout-button:hover {
            background-color: #c0392b;
        }

        .message {
            font-size: 16px;
            color: green;
            margin-bottom: 20px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }

            h2 {
                font-size: 24px;
            }

            p {
                font-size: 16px;
            }

            a {
                font-size: 14px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h2>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if (!$profile): ?>
            <p>Please complete your profile to proceed.</p>
            <a href="complete_student_profile.php">Complete Profile</a>
        <?php else: ?>
            <p>Your profile is complete. You can now explore the system.</p>
            <a href="view_courses.php">View Courses</a>
            <br><br>
            <a href="student_profile.php">View Profile</a>
            <br><br>
            <a href="view_teacher_request.php">View Teacher Requests</a>
        <?php endif; ?>
        <br><br>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
