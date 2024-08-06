<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update application status to 'pending' for reapplication
$sql = "UPDATE teacher_applications SET status = 'pending' WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$stmt->close();

// Check if the teacher wants to submit a new resume or keep the existing one
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["resume_option"])) {
    $resume_option = $_POST["resume_option"];

    // If the teacher chooses to submit a new resume
    if ($resume_option == "new_resume") {
        // Handle new resume submission
        if (isset($_FILES["resume"])) {
            $resume = $_FILES["resume"];
            $resume_name = $resume["name"];
            $resume_tmp_name = $resume["tmp_name"];
            $resume_error = $resume["error"];

            if ($resume_error === UPLOAD_ERR_OK) {
                $resume_destination = "resumes/" . $resume_name;
                move_uploaded_file($resume_tmp_name, $resume_destination);

                // Insert new resume record
                $sql_insert_resume = "INSERT INTO teacher_resumes (teacher_id, resume_path) VALUES (?, ?)";
                $stmt_insert_resume = $conn->prepare($sql_insert_resume);
                $stmt_insert_resume->bind_param("is", $teacher_id, $resume_destination);
                $stmt_insert_resume->execute();
                $stmt_insert_resume->close();
            }
        }
    } elseif ($resume_option == "existing_resume") {
        // Check if a file is uploaded when keeping existing resume
        if (!empty($_FILES["resume"]["name"])) {
            // Display error message and redirect back to teacher home page
            $_SESSION['error'] = "You chose to keep the existing resume, please do not upload a file.";
            header("Location: teacher_home.php");
            exit();
        }
    }
}

$conn->close();

header("Location: teacher_home.php"); // Redirect to teacher home page after reapplying
exit();
?>
