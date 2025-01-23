<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgeo";
$port=3307;

$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total number of check-ins
$checkInCountQuery = "SELECT COUNT(*) AS total_checkins FROM user_sessions WHERE session_start IS NOT NULL";
$checkInCountResult = $conn->query($checkInCountQuery);
$totalCheckIns = 0;

if ($checkInCountResult && $checkInCountResult->num_rows > 0) {
    $row = $checkInCountResult->fetch_assoc();
    $totalCheckIns = $row['total_checkins'];
}

// Fetch average session duration
$avgSessionQuery = "SELECT SEC_TO_TIME(AVG(session_duration)) AS avg_duration FROM user_sessions WHERE session_duration IS NOT NULL";
$avgSessionResult = $conn->query($avgSessionQuery);
$avgSessionDuration = "0 Sec";

if ($avgSessionResult && $avgSessionResult->num_rows > 0) {
    $row = $avgSessionResult->fetch_assoc();
    $avgSessionDuration = $row['avg_duration'] ?: "0 Sec";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GeoAt - Statistical Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uicons/1.0.0/css/uicons-solid-rounded.min.css">
  <link rel="stylesheet" href="Admin.css">
  <link rel="stylesheet" href="admintable.css">
  <style>
    /* General Styling */
    body {
      font-family: 'Inter', sans-serif;
      background-color: #121212;
      color: #e0e0e0;
    }

    a {
      text-decoration: none;
      color: #e0e0e0;
    }

    a:hover {
      color: #007bff;
    }

    h1, h5 {
      color: #ffffff;
    }

    /* Sidebar Styling */
    .sidebar {
      height: 100vh;
      position: fixed;
      background-color: #1e1e2f;
      padding-top: 20px;
    }

    .sidebar-header {
      text-align: center;
    }

    /* .sidebar-header img {
      border-radius: 50%;
      border: 2px solid #007bff;
    } */

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
    }

    .sidebar ul li a:hover {
      color: #ffffff;
    }

    /* Card Styling */
    .card {
      border-radius: 10px;
      background-color: #242424;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      text-align: center;
    }

    .card h5 {
      font-weight: 500;
    }

    /* Footer Styling */
    footer {
      text-align: center;
      padding: 15px 0;
      background-color: #1e1e2f;
      color: #b0bec5;
      position: relative;
      bottom: 0;
      width: 100%;
    }

    footer a {
      color: #007bff;
    }

    footer a:hover {
      text-decoration: underline;
    }
    .sidebar ul li a i {
  color: #ffffff; /* Makes the icons white */
  margin-right: 10px; /* Adds some space between the icon and text */
}

.sidebar ul li a:hover i {
  color: #007bff; /* Changes icon color on hover to match your theme */
}

  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
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
      <!-- Main Content -->
      <main class="col-md-10 ms-sm-auto px-md-4">
        <h1 class="mt-4">Statistics Dashboard</h1>
        <div class="row my-4">
          <!-- Cards -->
          <div class="col-md-3">
    <div class="card p-3">
        <h5>Check-ins</h5>
        <h2><?php echo $totalCheckIns; ?></h2>
        <p>Number of Successful Check-ins</p>
    </div>
</div>

      <div class="col-md-3">
               <div class="card">
                    <h5>Avg. Session Duration</h5>
                    <h2><?php echo $avgSessionDuration; ?></h2>
                    <p>Average Session Time</p>
                </div>
            </div> 
          <div class="col-md-3">
            <div class="card p-3">
              <h5>Attendance Compliance</h5>
              <h2>85%</h2>
              <p>Employees Within Geofence</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card p-3">
              <h5>Bounce Rate</h5>
              <h2>59%</h2>
              <p>Users Leaving Early</p>
            </div>
          </div>
        </div>

        <!-- Charts -->
        <div class="row">
          <div class="col-md-6">
            <canvas id="visitsChart"></canvas>
          </div>
          <div class="col-md-6">
            <canvas id="userTypeChart"></canvas>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-6">
            <canvas id="trafficSourcesChart"></canvas>
          </div>
          <div class="col-md-6">
            <canvas id="campaignsChart"></canvas>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <p>&copy; 2024 GeoAt | Designed with <span style="color: red;">❤</span> by <a href="#">TechVentures</a></p>
  </footer>

  <script>
    // Visits by Week Chart
    const visitsChart = document.getElementById('visitsChart').getContext('2d');
    new Chart(visitsChart, {
      type: 'line',
      data: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
        datasets: [{
          label: 'Visits',
          data: [5000, 7000, 10000, 8000, 12000],
          borderColor: '#007bff',
          tension: 0.3
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });

    // User Type Chart
    const userTypeChart = document.getElementById('userTypeChart').getContext('2d');
    new Chart(userTypeChart, {
      type: 'bar',
      data: {
        labels: ['New', 'Returning'],
        datasets: [{
          label: 'Users',
          data: [68, 32],
          backgroundColor: ['#28a745', '#ffc107']
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });

    // Traffic Sources Chart
    const trafficSourcesChart = document.getElementById('trafficSourcesChart').getContext('2d');
    new Chart(trafficSourcesChart, {
      type: 'pie',
      data: {
        labels: ['Direct', 'Organic', 'Referral', 'Social'],
        datasets: [{
          data: [24, 55, 9, 12],
          backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8']
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });

    // Campaigns Chart
    const campaignsChart = document.getElementById('campaignsChart').getContext('2d');
    new Chart(campaignsChart, {
      type: 'bar',
      data: {
        labels: ['Campaign 1', 'Campaign 2', 'Campaign 3'],
        datasets: [{
          label: 'Conversion Rate',
          data: [18, 12, 10],
          backgroundColor: '#007bff'
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });
     document.getElementById('logout').addEventListener('click', function () {
      document.getElementById('popup-overlay').style.display = 'flex';
      document.getElementById('main-content').classList.add('blurred');
    });

    function confirmLogout() {
      window.location.href = "registration.php"; // Redirect to registration page
    }

    function cancelLogout() {
      document.getElementById('popup-overlay').style.display = 'none';
      document.getElementById('main-content').classList.remove('blurred');
    }
  </script>
</body>
</html>
