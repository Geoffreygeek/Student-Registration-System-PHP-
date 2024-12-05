<?php
// Database connection settings
$host = "localhost";
$username = "root";
$password = "";
$db_name = "connect2";

// Connect to the database
$conn = new mysqli($host, $username, $password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the form if it is submitted (for registration)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Get data from the registration form
    $name = $_POST['name'];
    $regno = $_POST['regno'];
    $course_id = $_POST['course'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO students (name, regno, course_id, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $regno, $course_id, $email, $hashed_password);

    // Execute the statement
    if ($stmt->execute()) {
        $message = "Student registered successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Process the form if it is submitted (for login)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Get data from the login form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind SQL statement to check user credentials
    $stmt = $conn->prepare("SELECT password FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($stored_password);

    // If user exists, verify the password
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $stored_password)) {
            // Redirect to the search_delete.php page upon successful login
            header("Location: search_delete.php");
            exit();
        } else {
            $message = "Invalid login credentials.";
        }
    } else {
        $message = "No account found with that email.";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup Toggle</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 10px;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            width: 100%;
            max-width: 350px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .form-container h2 {
            font-size: 22px;
            color: #4A90E2;
            margin-bottom: 15px;
        }

        .form-container label {
            font-size: 14px;
            color: #555;
            margin-bottom: 6px;
            text-align: left;
            display: block;
        }

        .form-container input[type="text"], .form-container input[type="email"], .form-container input[type="password"], .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .form-container input[type="text"]:focus, .form-container input[type="email"]:focus, .form-container input[type="password"]:focus, .form-container select:focus {
            border-color: #4A90E2;
            outline: none;
        }

        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            padding: 10px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-container .message {
            margin-top: 15px;
            font-size: 14px;
            color: #e74c3c;
        }

        .toggle-btn {
            margin-top: 15px;
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .toggle-btn:hover {
            background-color: #45a049;
        }

        .form-container.register-form {
            display: none;
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 15px;
                max-width: 90%;
            }
        }
    </style>
</head>
<body>

    <!-- Login Form -->
    <div class="form-container login-form" id="login-form">
        <h2>Login</h2>
        <form action="" method="post">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login" name="login">
        </form>
        <button class="toggle-btn" onclick="toggleForm()">Switch to Registration</button>
    </div>

    <!-- Registration Form -->
    <div class="form-container register-form" id="register-form">
        <h2>Register</h2>

        <!-- Display success or error message -->
        <?php if (isset($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <form action="" method="post">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="regno">Registration Number</label>
            <input type="text" id="regno" name="regno" required>

            <label for="course">Course</label>
            <select id="course" name="course" required>
                <option value="1">Computer Science</option>
                <option value="2">Information Technology</option>
                <option value="3">Engineering</option>
                <option value="4">Mathematics</option>
            </select>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Register" name="register">
        </form>
        <button class="toggle-btn" onclick="toggleForm()">Switch to Login</button>
    </div>

    <script>
        function toggleForm() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');

            // Toggle visibility of login and register forms
            loginForm.classList.toggle('login-form');
            loginForm.classList.toggle('register-form');
            registerForm.classList.toggle('login-form');
            registerForm.classList.toggle('register-form');
        }
    </script>

</body>
</html>
