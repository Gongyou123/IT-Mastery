<?php
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type']; // Retrieve the selected user type

    $conn = new mysqli('localhost', 'root', '', 'user_registration');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user = null;

    // Check user type based on selection
    if ($user_type == 'teacher') {
        $sql = "SELECT * FROM teachers WHERE email = ?";
    } elseif ($user_type == 'student') {
        $sql = "SELECT * FROM students WHERE email = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user_type;
            $_SESSION['fullname'] = $user['fullname'];

            // Redirect based on user type
            if ($user_type == 'teacher') {
                header("Location: teacher_home.php");
            } elseif ($user_type == 'student') {
                header("Location: student_home.php");
            }
            exit();
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "User not found.";
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
    <title>Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap');

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
            background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('background/pic.jpg');
            background-position: center;
            background-size: cover;
            transition: 0.5s ease;
        }
        .form_box {
            width: 380px;
            height: 450px;
            margin: 6% auto;
            padding: 5px;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            overflow: hidden;
        }
        .btn_box {
            width: 220px;
            margin: 35px auto;
            position: relative;
            box-shadow: 0 0 20px 9px #1b1f2955;
            border-radius: 30px;
        }
        .toggle_btn {
            padding: 10px 30px;
            cursor: pointer;
            background: transparent;
            border: 0;
            outline: none;
            position: relative;
            transition: 0.3s;
            font-weight: 600;
        }
        .toggle_btn:hover {
            color: #fff;
        }
        #btn {
            top: 0;
            left: 0;
            position: absolute;
            width: 110px;
            height: 100%;
            border-radius: 30px;
            background: #f66335;
            transition: 0.5s;
            font-weight: 600;
        }
        .social_icons {
            text-align: center;
        }
        .social_icons .icon {
            width: 20px;
            height: 20px;
            margin: 0 12px;
            box-shadow: 0 0 20px 9px #1b1f2955;
            cursor: pointer;
            border-radius: 50%;
            color: #000;
            padding: 5px;
            background: transparent;
            transition: 0.2s;
        }
        .social_icons .icon:hover {
            background: #f66335;
            color: #fff;
        }
        .form_div {
            width: 100%;
            margin-top: 10px;
            position: relative;
            height: 48px;
            margin-bottom: 1.5rem;
        }
        .input_group {
            top: 180px;
            position: absolute;
            width: 280px;
            transition: 0.5s;
        }
        .form_input {
            font-size: 1rem;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            box-shadow: 0 0 20px 9px #1b1f2955;
            border-radius: .5rem;
            outline: none;
            padding: 1rem;
            background: transparent;
            z-index: 1;
            transition: 0.3s;
        }
        .form_label {
            position: absolute;
            left: 1rem;
            top: 1rem;
            padding: 0 0.25rem;
            background-color: transparent;
            color: #000;
            font-size: 1rem;
            transition: 0.3s;
        }
        .form_input:focus + .form_label {
            top: -0.5rem;
            left: 0.8rem;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 500;
            z-index: 10;
            background-color: #f66335;
            border-radius: 10px;
            padding: 0 5px;
        }
        .form_input:not(:placeholder-shown).form_input:not(:focus) + .form_label {
            top: -0.5rem;
            left: 0.8rem;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #f66335;
            border-radius: 10px;
            color: #fff;
            z-index: 10;
        }
        .form_input:focus {
            border: 1.5px solid #f66335;
        }
        .submit_btn {
            width: 85%;
            padding: 10px 30px;
            cursor: pointer;
            display: block;
            margin: auto;
            background-color: transparent;
            box-shadow: 0 0 20px 9px #1b1f2955;
            border: 0;
            outline: none;
            font-weight: 600;
            border-radius: 30px;
            transition: 0.5s;
        }
        .submit_btn:hover {
            color: #fff;
            background-color: #f66335;
        }
        .checkbox {
            margin: 20px 10px 30px 0;
            box-shadow: 0 0 20px 5px #1b1f2955;
            accent-color: #f66335;
            cursor: pointer;
        }
        span {
            color: #000;
            font-size: 12px;
            bottom: 68px;
            position: absolute;
        }
        #login {
            left: 50px;
        }
        #register {
            left: 450px;
        }
        #forgot_password {
            bottom: 5px;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form_box">
            <form action="login.php" method="post">
                <h2>Login</h2>
                <div class="form_div">
                    <input type="email" id="email" name="email" class="form_input" placeholder=" " required>
                    <label for="email" class="form_label">Email</label>
                </div>

                <div class="form_div">
                    <input type="password" id="password" name="password" class="form_input" placeholder=" " required>
                    <label for="password" class="form_label">Password</label>
                </div>

                <div class="form_div">
                    <input type="radio" id="teacher" name="user_type" value="teacher" required>
                    <label for="teacher">Teacher</label><br>
                    <input type="radio" id="student" name="user_type" value="student" required>
                    <label for="student">Student</label><br>
                </div>

                <div class="btn_box">
                    <input type="submit" name="login" value="Login" class="submit_btn">
                </div>
            </form>
            <span>Don't have an account? <a href="user_type.html">Register</a></span><br>
            <span id="forgot_password"><a href="forgot_password.php">Forgot Password?</a></span>
        </div>
    </div>
</body>
</html>
