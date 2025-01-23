<?php
session_start();

// Database connection
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "dbgeo";
$port=3307;

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname,$port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMessage = ""; // Initialize an empty success message

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['setCoordinates'])) {
    // Get form data with validation
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $topLeftLat = isset($_POST['top_left_lat']) ? $_POST['top_left_lat'] : '';
    $topLeftLon = isset($_POST['top_left_lon']) ? $_POST['top_left_lon'] : '';
    $bottomRightLat = isset($_POST['bottom_right_lat']) ? $_POST['bottom_right_lat'] : '';
    $bottomRightLon = isset($_POST['bottom_right_lon']) ? $_POST['bottom_right_lon'] : '';

    if (!empty($email) && is_numeric($topLeftLat) && is_numeric($topLeftLon) && is_numeric($bottomRightLat) && is_numeric($bottomRightLon)) {
        // Check if the user exists in the geoloc table
        $query = "SELECT id FROM geoloc WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];

            // Insert coordinates into the coordinates table
            $stmt = $conn->prepare("INSERT INTO coordinates (user_id, top_left_lat, top_left_lon, bottom_right_lat, bottom_right_lon) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("idddd", $user_id, $topLeftLat, $topLeftLon, $bottomRightLat, $bottomRightLon);

            if ($stmt->execute()) {
                $successMessage = "Coordinates set successfully!"; // Set success message
            } else {
                echo "<script>alert('Error setting coordinates: " . $stmt->error . "');</script>";
            }
        } else {
            echo "<script>alert('User not found');</script>";
        }
    } else {
        echo "<script>alert('Please fill all fields correctly');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Coordinates Locker</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    
body {
      font-family: 'Inter', sans-serif;
      background-color: #121212;
      color: #e0e0e0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .sidebar {
      height: 100vh;
      position: fixed;
      background-color: #1e1e2f;
      padding-top: 20px;
    }

    .sidebar-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      padding: 15px 20px;
    }

    .sidebar ul li a {
      font-weight: 500;
      display: flex;
      align-items: center;
      color: #b0bec5;
      text-decoration: none;
    }

    .sidebar ul li a:hover {
      color: #ffffff;
    }

    .sidebar ul li a i {
      margin-right: 10px;
    }

    .main-container {
      margin-left: 220px;
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .card {
      padding: 30px;
      width: 50%;
      background-color: #242424;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .card h1 {
      color: #ffffff;
      text-align: center;
      margin-bottom: 20px;
    }

    .btn-primary {
      width: 100%;
    }

    footer {
      text-align: center;
      padding: 15px 0;
      background-color: #1e1e2f;
      color: #b0bec5;
    }

    footer a {
      color: #007bff;
    }

    footer a:hover {
      text-decoration: underline;
    }
    .success-message {
      color: #28a745;
      font-weight: bold;
      margin-bottom: 15px;
      text-align: center;
    }
  </style>
</head>
<body>
<nav class="col-md-2 d-none d-md-block sidebar">
        <div class="sidebar-header">
          <div style="display: flex; align-items: center;">
            <img src="Group 34056.png" alt="Admin" width="65">
            <h2 style="margin: 0 0 0 15px;">eoAt</h2>
          </div>
        </div>
        <center><h5 class="mt-3">Admin</h5></center>
        <p>____________________________</p>
        <ul>
          <li><a href="Admin.php"><i class="uil uil-dashboard"></i> Dashboard</a></li>
          <li><a href="setcoordinates.php"><i class="uil uil-map-marker"></i> Set Coordinates</a></li>
          <li><a href="editcoordinates.php"><i class="uil uil-map-marker"></i>Edit Coordinates</a></li>
          <li><a href="statisticalMapping.php"><i class="uil uil-chart"></i> Statistics</a></li>
          <li><a href="users.php"><i class="uil uil-users-alt"></i> Users</a></li>
          <li><a href="lstm.php"><i class="uil uil-setting"></i> Get Insights</a></li>
          <li><a href="registration.php"><i class="uil uil-signout"></i> Logout</a></li>
        </ul>
      </nav>
<div class="main-container">
  <div class="card">
    <h1>Coordinates Locker</h1>
    <?php if (!empty($successMessage)): ?>
    <div class="success-message" id="successMessage">
        <?= $successMessage ?>
    </div>
    <script>
        setTimeout(function() {
            const successMessageElement = document.getElementById('successMessage');
            if (successMessageElement) {
                successMessageElement.style.transition = 'opacity 0.5s';
                successMessageElement.style.opacity = '0';
                setTimeout(() => successMessageElement.remove(), 500); // Removes the element after fading out
            }
        }, 3000); // 3 seconds delay
    </script>
<?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">User Email</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="top_left_lat" class="form-label">Top Left Latitude</label>
        <input type="number" step="0.0001" id="top_left_lat" name="top_left_lat" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="top_left_lon" class="form-label">Top Left Longitude</label>
        <input type="number" step="0.0001" id="top_left_lon" name="top_left_lon" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="bottom_right_lat" class="form-label">Bottom Right Latitude</label>
        <input type="number" step="0.0001" id="bottom_right_lat" name="bottom_right_lat" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="bottom_right_lon" class="form-label">Bottom Right Longitude</label>
        <input type="number" step="0.0001" id="bottom_right_lon" name="bottom_right_lon" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary" name="setCoordinates">Set Coordinates</button>
    </form>
  </div>
</div>
<footer>
  <p>&copy; 2024 GeoAt Solutions. All rights reserved. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>








 