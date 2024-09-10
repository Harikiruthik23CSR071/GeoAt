<?php
if(isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['crmpassword'])) {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $des      = $_POST['password'];
    $password = $_POST['crmpassword'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'dbgeo');
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT * FROM geoloc WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $username_exists = $result->num_rows > 0;
        $stmt->close();

        // Check if email exists
        $stmt = $conn->prepare("SELECT * FROM geoloc WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $email_exists = $result->num_rows > 0;
        $stmt->close();

        if ($username_exists) {
            echo "<script>alert('Username already exists!');</script>";
        } elseif ($email_exists) {
            echo "<script>alert('Email already registered!');</script>";
        } else {
            // Insert new record
            $stmt = $conn->prepare("INSERT INTO geoloc (username, email, password, crmpassword) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $des, $password);

            $execval = $stmt->execute();
            if ($execval === TRUE) {
                echo "<script>alert('Registration successful!');</script>";
            } else {
                echo "<script>alert('Error: " . $conn->error . "');</script>";
            }

            $stmt->close();
        }
    }
} else {
   
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Sign up</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background-image: linear-gradient(340deg, rgba(76, 76, 76,0.02) 0%, rgba(76, 76, 76,0.02) 34%,transparent 34%, transparent 67%,rgba(142, 142, 142,0.02) 67%, rgba(142, 142, 142,0.02) 73%,rgba(151, 151, 151,0.02) 73%, rgba(151, 151, 151,0.02) 100%),linear-gradient(320deg, rgba(145, 145, 145,0.02) 0%, rgba(145, 145, 145,0.02) 10%,transparent 10%, transparent 72%,rgba(35, 35, 35,0.02) 72%, rgba(35, 35, 35,0.02) 76%,rgba(69, 69, 69,0.02) 76%, rgba(69, 69, 69,0.02) 100%),linear-gradient(268deg, rgba(128, 128, 128,0.02) 0%, rgba(128, 128, 128,0.02) 5%,transparent 5%, transparent 76%,rgba(78, 78, 78,0.02) 76%, rgba(78, 78, 78,0.02) 83%,rgba(224, 224, 224,0.02) 83%, rgba(224, 224, 224,0.02) 100%),linear-gradient(198deg, rgba(25, 25, 25,0.02) 0%, rgba(25, 25, 25,0.02) 36%,transparent 36%, transparent 85%,rgba(180, 180, 180,0.02) 85%, rgba(180, 180, 180,0.02) 99%,rgba(123, 123, 123,0.02) 99%, rgba(123, 123, 123,0.02) 100%),linear-gradient(90deg, rgb(255,255,255),rgb(255,255,255));

            background-repeat: no-repeat;
            background-size: cover;
        }
        .signup-form {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            background-color: #111;
            color: #111;
            font-family: Helvetica;
            border-radius: 8px;
            box-shadow:inset 0px 0px 10px rgba(0, 0, 0, 0.086);
            background: transparent;
            backdrop-filter: blur(10px);
        }

        .form-group {
            position: relative;
        }

        .form-group .bi {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .form-group input {
            padding-right: 2.5rem;
            background: transparent;
            backdrop-filter: blur(10px);
            border-radius: 9px;
            border-width: 2px;
            border-color:   rgba(0, 0, 0, 0.086);
        }

        .login-link {
            margin-left: 10px;
            /* Adjust margin to space out from the button */
        }

        .btn-custom-lg {
            padding: 0.75rem 1.5rem;
            /* Increase padding for a larger button */
            font-size: 1.125rem;
            /* Increase font size */
            border-radius: 0.375rem;
            /* Optional: adjust border radius if needed */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="signup-form">
            <h2 class="text-center mb-4">Sign Up</h2>
            <form id="signupForm" onsubmit="return validate()" method="post" action="signupgeo.php">
                <div class="mb-3 form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <input type="text" id="username" name="username" class="form-control"
                            placeholder="Enter your username">
                        <span class="bi bi-person"></span>
                    </div>
                </div>
                <div class="mb-3 form-group">
                    <label for="email" class="form-label">Email address</label>
                    <div class="input-group">
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email">
                        <span class="bi bi-envelope"></span>
                    </div>
                </div>
                <div class="mb-3 form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Enter your password">
                        <span class="bi bi-lock"></span>
                    </div>
                </div>
                <div class="mb-3 form-group">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" id="confirmPassword" name="crmpassword" class="form-control"
                            placeholder="Confirm your password">
                        <span class="bi bi-lock"></span>
                    </div>
                </div>
                <p>If any discrepancies <a href="contact.html" style="text-decoration: none !important;">contact</a></p>
                <div class="d-flex justify-content-between align-items-center">

                    <button type="submit" class="btn btn-primary btn-custom-lg">Sign Up</button>
                    <p>Already registered?<a href="logingeo.php" class="login-link"  style="text-decoration: none !important;">Log in</a></p>
                </div>
            </form>
        </div>
    </div>
    <script>
        function validate() {
            username = document.getElementById("username").value;
            email = document.getElementById("email").value;
            password = document.getElementById("password").value;
            confirmPassword = document.getElementById("confirmPassword").value;

            if (username == "" || username == " ") {
                alert("Username must not be empty")
                return false
            }
            else if (email == "" || email == " ") {
                alert("E-mail must not be empty")
                return false
            }
            else if (password == "") {
                alert("Password must not be empty")
                return false
            }
            else if (password.length < 8) {
                alert("Password must contain atleast 8 characters")
                return false
            }
            else if (confirmPassword == "") {
                alert("Confirm Password is empty")
                return false
            }
            else if (password != confirmPassword) {
                alert("Password Mismatch")
                return false
            }
            else {
                // alert("Registered successfull")
                return true
            }
        }
    </script>
</body>

</html>