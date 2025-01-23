<?php
session_start();

// Database connection
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "dbgeoat";
$port=3306;

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname,$port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMessage = ""; // Initialize an empty success message
$users = [];
$action = ""; // To determine the selected action
$editUser = null; // Store user data for editing

// Fetch all users and their coordinates
$query = "SELECT geoloc.id, geoloc.email, coordinates.top_left_lat, coordinates.top_left_lon, coordinates.bottom_right_lat, coordinates.bottom_right_lon 
          FROM geoloc 
          LEFT JOIN coordinates ON geoloc.id = coordinates.user_id";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['editCoordinatesAction'])) {
        // Edit action triggered
        $action = "edit";
        $editUserId = $_POST['user_id'];

        // Fetch user details for editing
        $query = "SELECT geoloc.id, geoloc.email, coordinates.top_left_lat, coordinates.top_left_lon, coordinates.bottom_right_lat, coordinates.bottom_right_lon 
                  FROM geoloc 
                  LEFT JOIN coordinates ON geoloc.id = coordinates.user_id
                  WHERE geoloc.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $editUserId);
        $stmt->execute();
        $editUser = $stmt->get_result()->fetch_assoc();
    } elseif (isset($_POST['updateCoordinates'])) {
        // Update coordinates
        $userId = $_POST['user_id'];
        $topLeftLat = $_POST['top_left_lat'];
        $topLeftLon = $_POST['top_left_lon'];
        $bottomRightLat = $_POST['bottom_right_lat'];
        $bottomRightLon = $_POST['bottom_right_lon'];

        if (is_numeric($topLeftLat) && is_numeric($topLeftLon) && is_numeric($bottomRightLat) && is_numeric($bottomRightLon)) {
            // Step 1: Delete existing coordinates for the specific user
            $deleteQuery = "DELETE FROM coordinates WHERE user_id = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // Step 2: Insert or update new coordinates
            $insertQuery = "INSERT INTO coordinates (user_id, top_left_lat, top_left_lon, bottom_right_lat, bottom_right_lon)
                            VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("idddd", $userId, $topLeftLat, $topLeftLon, $bottomRightLat, $bottomRightLon);

            if ($stmt->execute()) {
                $successMessage = "Coordinates updated successfully!";
            } else {
                $successMessage = "Error updating coordinates: " . $stmt->error;
            }
        } else {
            $successMessage = "Please fill all fields with valid numeric values.";
        }
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
      margin-left: 300px;
      margin-right: 100px;
      flex: 1;
      padding: 20px;
    }
    .table-responsive {
      margin-top: 20px;
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
  <center><h1>Coordinates Locker</h1></center>
  <?php if (!empty($successMessage)): ?>
    <div id="successMessage" class="alert alert-success"><?= $successMessage ?></div>
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


  <?php if ($action === "edit" && $editUser): ?>
    <form method="POST">
      <input type="hidden" name="user_id" value="<?= $editUser['id'] ?>">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control" id="email" value="<?= htmlspecialchars($editUser['email']) ?>" disabled>
      </div>
      <div class="mb-3">
        <label for="top_left_lat" class="form-label">Top Left Latitude</label>
        <input type="number" step="0.0001" class="form-control" id="top_left_lat" name="top_left_lat" value="<?= htmlspecialchars($editUser['top_left_lat']) ?>" required>
      </div>
      <div class="mb-3">
        <label for="top_left_lon" class="form-label">Top Left Longitude</label>
        <input type="number" step="0.0001" class="form-control" id="top_left_lon" name="top_left_lon" value="<?= htmlspecialchars($editUser['top_left_lon']) ?>" required>
      </div>
      <div class="mb-3">
        <label for="bottom_right_lat" class="form-label">Bottom Right Latitude</label>
        <input type="number" step="0.0001" class="form-control" id="bottom_right_lat" name="bottom_right_lat" value="<?= htmlspecialchars($editUser['bottom_right_lat']) ?>" required>
      </div>
      <div class="mb-3">
        <label for="bottom_right_lon" class="form-label">Bottom Right Longitude</label>
        <input type="number" step="0.0001" class="form-control" id="bottom_right_lon" name="bottom_right_lon" value="<?= htmlspecialchars($editUser['bottom_right_lon']) ?>" required>
      </div>
      <button type="submit" class="btn btn-primary" name="updateCoordinates">Update Coordinates</button>
    </form>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-dark table-hover">
        <thead>
          <tr>
            <th>Email</th>
            <th>Top Left (Lat, Lon)</th>
            <th>Bottom Right (Lat, Lon)</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['top_left_lat']) ?>, <?= htmlspecialchars($user['top_left_lon']) ?></td>
              <td><?= htmlspecialchars($user['bottom_right_lat']) ?>, <?= htmlspecialchars($user['bottom_right_lon']) ?></td>
              <td>
                <form method="POST" style="display:inline-block;">
                  <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                  <button type="submit" name="editCoordinatesAction" class="btn btn-warning btn-sm">Edit</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html

