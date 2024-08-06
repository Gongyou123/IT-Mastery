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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if a file was uploaded
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        // File was uploaded successfully, handle the upload
        $profile_picture = $_FILES['profile_picture']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);

        // Move the uploaded file to the target location
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Retrieve other form fields
            $age = $_POST['age'];
            $date_of_birth = $_POST['date_of_birth'];
            $gender = $_POST['gender'];
            $address = $_POST['address'];
            $contact_number = $_POST['contact_number'];
            $email = $_POST['email'];
            $skills = $_POST['skills'];
            $parent_guardian_info = $_POST['parent_guardian_info'];
            $parent_guardian_contact = $_POST['parent_guardian_contact'];
            $reason = $_POST['reason'];

            // Insert into student_profiles table
            $sql_student_profiles = "INSERT INTO student_profiles 
                    (student_id, profile_picture, age, date_of_birth, gender, address, contact_number, email, skills, parent_guardian_info, parent_guardian_contact, reason)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_student_profiles = $conn->prepare($sql_student_profiles);
            $stmt_student_profiles->bind_param("isisssssssss", $student_id, $profile_picture, $age, $date_of_birth, $gender, $address, $contact_number, $email, $skills, $parent_guardian_info, $parent_guardian_contact, $reason);

            // Insert into teacher_student_profiles table
            $sql_teacher_student_profiles = "INSERT INTO teacher_student_profiles 
                    (student_id, profile_picture, age, date_of_birth, gender, address, contact_number, email, skills, parent_guardian_info, parent_guardian_contact, reason)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_teacher_student_profiles = $conn->prepare($sql_teacher_student_profiles);
            $stmt_teacher_student_profiles->bind_param("isisssssssss", $student_id, $profile_picture, $age, $date_of_birth, $gender, $address, $contact_number, $email, $skills, $parent_guardian_info, $parent_guardian_contact, $reason);

            // Execute both statements
            if ($stmt_student_profiles->execute() && $stmt_teacher_student_profiles->execute()) {
                echo "Profile completed successfully!";
                header("Location: student_home.php");
            } else {
                echo "Error: " . $stmt_student_profiles->error;
                echo "Error: " . $stmt_teacher_student_profiles->error;
            }

            $stmt_student_profiles->close();
            $stmt_teacher_student_profiles->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "No file uploaded or an error occurred during upload.";
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Student Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200vh;
            color: #333;
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
            color: #2c3e50;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 14px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #4cae4c;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            color: #5cb85c;
            text-decoration: none;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 100%;
            }

            h2 {
                font-size: 24px;
            }

            input[type="submit"] {
                padding: 12px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Complete Your Profile</h2>
        <form action="complete_student_profile.php" method="post" enctype="multipart/form-data">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" required>
            
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>
            
            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" required>
            
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            
            <label for="address">Address:</label>
            <textarea id="address" name="address" required></textarea>
            
            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="skills">Skills:</label>
            <textarea id="skills" name="skills" required></textarea>
            
            <label for="parent_guardian_info">Parent/Guardian Information:</label>
            <textarea id="parent_guardian_info" name="parent_guardian_info" required></textarea>
            
            <label for="parent_guardian_contact">Parent/Guardian Contact Number:</label>
            <input type="text" id="parent_guardian_contact" name="parent_guardian_contact" required>
            
            <label for="reason">Reason for using this system:</label>
            <textarea id="reason" name="reason" required></textarea>
            
            <input type="submit" value="Complete Profile">
        </form>
        <a href="student_home.php">Back to Home</a>
    </div>
</body>
</html>
