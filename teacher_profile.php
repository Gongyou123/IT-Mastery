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

$message = '';

// Handle drop course request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['drop_course_id'])) {
    $course_id = $_POST['drop_course_id'];

    // Delete the course from teacher_courses
    $sql = "DELETE FROM teacher_courses WHERE teacher_id = ? AND course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $teacher_id, $course_id);
    if ($stmt->execute()) {
        $message = "Course dropped successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Get teacher profile
$sql = "SELECT tp.profile_picture, tp.academic_achievements, tp.skills, tp.contact_details, tp.available_schedule
        FROM teacher_profiles tp 
        WHERE tp.teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();

// Get courses handled by the teacher
$sql = "SELECT c.id, c.course_name 
        FROM teacher_courses tc
        JOIN courses c ON tc.course_id = c.id
        WHERE tc.teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$courses_result = $stmt->get_result();

$courses = [];
while ($row = $courses_result->fetch_assoc()) {
    $courses[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ece9e6, #ffffff);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: #2c3e50;
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
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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

        strong {
            display: block;
            margin-top: 10px;
            font-size: 18px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 20px;
            font-size: 16px;
        }

        button {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #2980b9;
        }

        a.button {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 16px;
            cursor: pointer;
        }

        a.button:hover {
            background-color: #2980b9;
        }

        .message {
            margin-bottom: 20px;
            font-size: 16px;
            color: green;
        }

        .course-networking {
            background-color: #c0392b;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
        }

        .course-information {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
        }

        .course-web {
            background-color: #2980b9;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
        }

        .course-integration {
            background-color: #f39c12;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
        }

        .animation-target {
            animation: fadeIn 1s ease-in-out;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }

            h2 {
                font-size: 24px;
            }

            li {
                font-size: 14px;
            }

            button, a.button {
                font-size: 14px;
                padding: 10px 20px;
            }

            strong {
                font-size: 16px;
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
    <div class="header">
        <a href="select_courses.php">Select Courses</a>
        <a href="teacher_profile.php">View Profile</a>
        <a href="search_students.php">Search and Add Students</a>
        <a href="view_student_requests.php">View Student Requests</a>
        <form action="logout.php" method="post" style="display: inline;">
            <input type="submit" value="Logout" class="logout-button">
        </form>
    </div>

    <div class="container animation-target">
        <h2>Profile of <?php echo $_SESSION['fullname']; ?></h2>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($profile): ?>
            <img src="<?php echo $profile['profile_picture']; ?>" alt="Profile Picture" width="150"><br>
            <strong>Academic Achievements:</strong> <?php echo $profile['academic_achievements']; ?><br>
            <strong>Skills:</strong> <?php echo $profile['skills']; ?><br>
            <strong>Contact Details:</strong> <?php echo $profile['contact_details']; ?><br>
            <strong>Available Schedule:</strong> <?php echo $profile['available_schedule']; ?><br>
            <h3>Courses Handling:</h3>
            <ul>
                <?php foreach ($courses as $course): ?>
                    <li>
                        <?php 
                            // Apply specific styling to certain courses
                            $course_name = strtolower($course['course_name']);
                            if (strpos($course_name, 'networking') !== false) {
                                echo '<a class="course-networking" href="view_course_students.php?course_id=' . $course['id'] . '">' . $course['course_name'] . '</a>';
                            } elseif (strpos($course_name, 'information management') !== false) {
                                echo '<a class="course-information" href="view_course_students.php?course_id=' . $course['id'] . '">' . $course['course_name'] . '</a>';
                            } elseif (strpos($course_name, 'web system') !== false) {
                                echo '<a class="course-web" href="view_course_students.php?course_id=' . $course['id'] . '">' . $course['course_name'] . '</a>';
                            } elseif (strpos($course_name, 'system integration') !== false) {
                                echo '<a class="course-integration" href="view_course_students.php?course_id=' . $course['id'] . '">' . $course['course_name'] . '</a>';
                            } else {
                                echo '<a href="view_course_students.php?course_id=' . $course['id'] . '">' . $course['course_name'] . '</a>';
                            }
                        ?>
                        <form action="teacher_profile.php" method="post" style="display:inline;">
                            <input type="hidden" name="drop_course_id" value="<?php echo $course['id']; ?>">
                            <button type="submit">Drop Course</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Profile not found. Please complete your profile.</p>
            <a href="complete_profile.php" class="button">Complete Profile</a>
        <?php endif; ?>
        <br>
        <a href="teacher_home.php" class="button">Back to Home</a>
    </div>
</body>
</html>
