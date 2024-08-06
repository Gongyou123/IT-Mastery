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

// Inside the existing PHP code
$sql = "SELECT t.fullname AS teacher_name, c.course_name, r.request_date
        FROM teacher_student_requests r
        JOIN teachers t ON r.teacher_id = t.id
        JOIN courses c ON r.course_id = c.id
        WHERE r.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teacher Requests</title>
    <style>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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

        a {
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

        a:hover {
            background-color: #2980b9;
        }

        .logout-button {
            background-color: #e74c3c;
        }

        .logout-button:hover {
            background-color: #c0392b;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }

            h2 {
                font-size: 24px;
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
        <h2>Teacher Requests</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Teacher Name</th>
                    <th>Course</th>
                    <th>Date</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No requests from teachers.</p>
        <?php endif; ?>
        <a href="student_home.php">Back to Home</a>
        <br><br>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
