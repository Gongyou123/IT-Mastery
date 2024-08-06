<?php
session_start();



$teacher_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check application status
$sql = "SELECT status FROM teacher_applications WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();

// Check if profile is completed
$sql = "SELECT * FROM teacher_profiles WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Home</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ece9e6, #ffffff);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            color: #2c3e50;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .header {
            width: 100%;
            background-color: #3498db;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
            animation: slideDown 1s ease-in-out;
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }

        .header a {
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            transition: background-color 0.3s;
            font-size: 16px;
            margin: 0 10px;
        }

        .header a:hover {
            background-color: #2980b9;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
            animation: fadeInUp 1s ease-in-out;
        }

        @keyframes fadeInUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        h2 {
            font-size: 24px;
            color: #34495e;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        a, input[type="submit"] {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 12px 24px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 16px;
            margin: 5px 0;
        }

        a:hover, input[type="submit"]:hover {
            background-color: #2980b9;
        }

        label {
            display: block;
            font-size: 16px;
            margin: 10px 0 5px;
            text-align: left;
        }

        input[type="file"] {
            padding: 10px;
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        .logout-button {
            background-color: #e74c3c;
            margin-top: 20px;
        }

        .logout-button:hover {
            background-color: #c0392b;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 100%;
            }

            h2 {
                font-size: 20px;
            }

            p {
                font-size: 16px;
            }

            a, input[type="submit"] {
                font-size: 14px;
                padding: 10px 20px;
            }

            label {
                font-size: 14px;
            }

            .header a {
                font-size: 14px;
                padding: 10px;
                margin: 0 5px;
            }
        }
     </style>
</head>
<body>
    <?php if ($profile): ?>
    <div class="header">
        <a href="select_courses.php">Select Courses</a>
        <a href="teacher_profile.php">View Profile</a>
        <a href="search_students.php">Search and Add Students</a>
        <a href="view_student_requests.php">View Student Requests</a>
        <form action="logout.php" method="post" style="display: inline;">
            <input type="submit" value="Logout" class="logout-button">
        </form>
    </div>
    <?php endif; ?>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?></h2>

        <?php if (!$application): ?>
            <p>You need to apply first.</p>
            <form action="apply.php" method="post" enctype="multipart/form-data">
                <label for="resume">Upload Resume:</label>
                <input type="file" id="resume" name="resume" required><br>
                <input type="submit" name="apply" value="Apply">
            </form>
        <?php elseif ($application['status'] == 'pending'): ?>
            <p>Your application is under review. Please wait for admin's response.</p>
        <?php elseif ($application['status'] == 'accepted' && !$profile): ?>
            <p>Your application has been accepted. Proceed to complete your profile.</p>
            <a href="complete_profile.php">Complete Profile</a>
        <?php elseif ($application['status'] == 'accepted' && $profile): ?>
            <p>Profile completed. Proceed to select courses.</p>
        <?php else: ?>
            <p>Your application was rejected. Please contact admin for further details.</p>
            <form action="reapply.php" method="post">
                <label for="resume">Upload Another Resume:</label>
                <input type="file" id="resume" name="resume">
                <br>
                <input type="submit" name="reapply" value="Reapply">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
