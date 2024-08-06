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
$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// Check application status and profile completion
$sql = "SELECT ta.status, tp.teacher_id as profile_completed 
        FROM teacher_applications ta 
        LEFT JOIN teacher_profiles tp ON ta.teacher_id = tp.teacher_id 
        WHERE ta.teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();

// Fetch all courses
$courses_sql = "SELECT * FROM courses";
$courses_result = $conn->query($courses_sql);

// Fetch teacher's courses
$teacher_courses_sql = "SELECT tc.course_id, c.course_name 
                        FROM teacher_courses tc 
                        JOIN courses c ON tc.course_id = c.id 
                        WHERE tc.teacher_id = ?";
$stmt = $conn->prepare($teacher_courses_sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$teacher_courses_result = $stmt->get_result();
$teacher_courses = [];
while ($row = $teacher_courses_result->fetch_assoc()) {
    $teacher_courses[] = $row;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Portal</title>
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
            max-width: 800px;
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
        a, input[type="submit"], button {
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
        a:hover, input[type="submit"]:hover, button:hover {
            background-color: #2980b9;
        }
        label {
            display: block;
            font-size: 16px;
            margin: 10px 0 5px;
            text-align: left;
        }
        input[type="file"], input[type="text"], select {
            padding: 10px;
            margin-bottom: 20px;
            width: calc(100% - 22px);
            box-sizing: border-box;
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #ddd;
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
            a, input[type="submit"], button {
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
    <div class="header">
        <a href="?view=home">Home</a>
        <a href="?view=select_courses">Select Courses</a>
        <a href="?view=profile">View Profile</a>
        <a href="?view=search_students">Search and Add Students</a>
        <a href="?view=view_student_requests">View Student Requests</a>
        <form action="logout.php" method="post" style="display: inline;">
            <input type="submit" value="Logout" class="logout-button">
        </form>
    </div>

    <div class="container">
        <?php if ($view == 'home'): ?>
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
            <?php elseif ($application['status'] == 'accepted' && !$application['profile_completed']): ?>
                <p>Your application has been accepted. Proceed to complete your profile.</p>
                <a href="?view=profile">Complete Profile</a>
            <?php elseif ($application['status'] == 'accepted' && $application['profile_completed']): ?>
                <p>Profile completed. Proceed to select courses.</p>
            <?php else: ?>
                <p>Your application was rejected. Please contact admin for further details.</p>
                <form action="reapply.php" method="post">
                    <label for="resume">Upload Another Resume:</label>
                    <input type="file" id="resume" name="resume"><br>
                    <input type="submit" name="reapply" value="Reapply">
                </form>
            <?php endif; ?>
        <?php elseif ($view == 'select_courses'): ?>
            <h2>Select Courses</h2>
            <form action="select_courses.php" method="post">
                <ul>
                    <?php if ($courses_result && $courses_result->num_rows > 0): ?>
                        <?php while ($row = $courses_result->fetch_assoc()): ?>
                            <li>
                                <img src="logo/<?php echo $row['logo']; ?>" alt="Course Logo" width="100">
                                <p><?php echo $row['course_name']; ?></p>
                                <button type="submit" name="course_id" value="<?php echo $row['id']; ?>">Select Course</button>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>No courses available</li>
                    <?php endif; ?>
                                </ul>
            </form>
        <?php elseif ($view == 'profile'): ?>
            <h2>Teacher Profile</h2>
            <?php if ($application['profile_completed']): ?>
                <p>Your profile has been completed.</p>
            <?php else: ?>
                <p>Your profile is incomplete. Please complete it.</p>
                <form action="update_profile.php" method="post">
                    <label for="first_name">First Name:</label><br>
                    <input type="text" id="first_name" name="first_name" required><br>
                    <label for="last_name">Last Name:</label><br>
                    <input type="text" id="last_name" name="last_name" required><br>
                    <label for="email">Email:</label><br>
                    <input type="text" id="email" name="email" required><br>
                    <label for="phone">Phone:</label><br>
                    <input type="text" id="phone" name="phone" required><br>
                    <input type="submit" name="update_profile" value="Update Profile">
                </form>
            <?php endif; ?>
        <?php elseif ($view == 'search_students'): ?>
            <h2>Search and Add Students</h2>
            <!-- Add your HTML code for searching and adding students here -->
        <?php elseif ($view == 'view_student_requests'): ?>
            <h2>View Student Requests</h2>
            <!-- Add your HTML code for viewing student requests here -->
        <?php endif; ?>
    </div>
</body>
</html>

