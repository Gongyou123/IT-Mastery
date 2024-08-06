<?php
session_start();

$message = '';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['complete_profile'])) {
    $profile_picture = $_FILES['profile_picture']['tmp_name'];
    $academic_achievements = $_POST['academic_achievements'];
    $skills = $_POST['skills'];
    $contact_details = $_POST['contact_details'];
    $available_schedule = $_POST['available_schedule'];

    if (is_uploaded_file($profile_picture)) {
        $profile_picture_path = 'uploads/' . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($profile_picture, $profile_picture_path);

        $sql = "INSERT INTO teacher_profiles (teacher_id, profile_picture, academic_achievements, skills, contact_details, available_schedule) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $teacher_id, $profile_picture_path, $academic_achievements, $skills, $contact_details, $available_schedule);

        if ($stmt->execute()) {
            $message = "Profile completed successfully! Welcome " . $_SESSION['fullname'];
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Error uploading file.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ece9e6, #ffffff);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 120vh;
            color: #2c3e50;
        }

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        h2 {
            font-size: 28px;
            color: #34495e;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            text-align: left;
        }

        input[type="file"], textarea {
            width: calc(100% - 20px);
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
        }

        input[type="submit"], button {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 12px 24px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
        }

        input[type="submit"]:hover, button:hover {
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

            input[type="file"], textarea {
                width: calc(100% - 20px);
                font-size: 14px;
                padding: 10px;
            }

            input[type="submit"], button {
                font-size: 14px;
                padding: 10px 20px;
            }

            label {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <?php if ($message): ?>
        <div><?php echo $message; ?></div>
    <?php endif; ?>
    <div class="container">
        <h2>Complete Your Profile</h2>
        <form action="complete_profile.php" method="post" enctype="multipart/form-data">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" required><br>

            <label for="academic_achievements">Academic Achievements:</label>
            <textarea id="academic_achievements" name="academic_achievements" required></textarea><br>

            <label for="skills">Skills:</label>
            <textarea id="skills" name="skills" required></textarea><br>

            <label for="contact_details">Contact Details:</label>
            <textarea id="contact_details" name="contact_details" required></textarea><br>

            <label for="available_schedule">Available Schedule:</label>
            <textarea id="available_schedule" name="available_schedule" required></textarea><br>

            <input type="submit" name="complete_profile" value="Complete Profile">
        </form>
        <button onclick="location.href='teacher_home.php'">Back Home</button>
    </div>
</body>
</html>
