<?php
if (isset($_POST['register'])) {
    $fullname = $_POST['fullname'];
    $contact_number = $_POST['contact_number'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $conn = new mysqli('localhost', 'root', '', 'user_registration');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO teachers (fullname, contact_number, gender, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $fullname, $contact_number, $gender, $email, $password);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $success = false;
        $error_message = $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Registration</title>
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
        }

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h2 {
            font-size: 28px;
            color: #34495e;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        input, select {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .button {
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #2980b9;
        }

        .success, .error {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .continue-button {
            background-color: #2ecc71;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .continue-button:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Teacher Registration</h2>
        <?php if (isset($success) && $success): ?>
            <p class="success">Registration successful!</p>
            <button class="continue-button" onclick="location.href='login.php'">Continue</button>
        <?php else: ?>
            <?php if (isset($success) && !$success): ?>
                <p class="error">Error: <?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form action="" method="post">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" required>

                <label for="contact_number">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" required>

                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <input type="submit" name="register" value="Register" class="button">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
