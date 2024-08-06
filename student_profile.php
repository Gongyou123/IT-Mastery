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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <style type="text/css">
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ece9e6, #ffffff);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #2c3e50;
        }

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        h2 {
            font-size: 28px;
            color: #34495e;
            margin-bottom: 20px;
        }

        img {
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .profile-info {
            text-align: left;
            margin: 0 auto;
            max-width: 400px;
        }

        .profile-info strong {
            color: #34495e;
        }

        a {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 12px 24px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 16px;
            margin: 20px 0;
        }

        a:hover {
            background-color: #2980b9;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }

            h2 {
                font-size: 24px;
            }

            .profile-info {
                font-size: 14px;
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
        <h2>Profile of <?php echo $_SESSION['fullname']; ?></h2>

        <?php if ($profile): ?>
            <img src="uploads/<?php echo $profile['profile_picture']; ?>" alt="Profile Picture" width="150">
            <div class="profile-info">
                <p><strong>Age:</strong> <?php echo $profile['age']; ?></p>
                <p><strong>Date of Birth:</strong> <?php echo $profile['date_of_birth']; ?></p>
                <p><strong>Gender:</strong> <?php echo $profile['gender']; ?></p>
                <p><strong>Address:</strong> <?php echo $profile['address']; ?></p>
                <p><strong>Contact Number:</strong> <?php echo $profile['contact_number']; ?></p>
                <p><strong>Email:</strong> <?php echo $profile['email']; ?></p>
                <p><strong>Skills:</strong> <?php echo $profile['skills']; ?></p>
                <p><strong>Parent/Guardian Information:</strong> <?php echo $profile['parent_guardian_info']; ?></p>
                <p><strong>Parent/Guardian Contact:</strong> <?php echo $profile['parent_guardian_contact']; ?></p>
                <p><strong>Reason for using this system:</strong> <?php echo $profile['reason']; ?></p>
            </div>
        <?php else: ?>
            <p>Profile not found. Please complete your profile first.</p>
            <a href="complete_student_profile.php">Complete Profile</a>
        <?php endif; ?>

        <br>
        <a href="student_home.php">Back to Home</a>
    </div>
</body>
</html>
