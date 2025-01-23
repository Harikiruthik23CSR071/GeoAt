<?php
// Database connection
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgeo";
$port=3307;

$conn = new mysqli($servername, $username, $password, $dbname,$port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$isAdmin = false;
$currentUser = $_SESSION['email'] ?? null;

if ($currentUser) {
    // Ensure the 'role' column exists in your database
    $adminCheckQuery = "SELECT * FROM geoloc WHERE email = '$currentUser' AND role = 'admin'";
    try {
        $adminCheckResult = $conn->query($adminCheckQuery);
        $isAdmin = $adminCheckResult->num_rows > 0;
    } catch (mysqli_sql_exception $e) {
        die("Error: " . $e->getMessage());
    }
}

// Your remaining code here...

// Fetch user list for dropdown
$userQuery = "SELECT username, email FROM geoloc";
$userResult = $conn->query($userQuery);
$users = [];
while ($row = $userResult->fetch_assoc()) {
    $users[] = $row;
}

// Initialize variables for selected user insights
$selectedUser = $_POST['selected_user'] ?? null;
$growthCounts = [];
$growthMonths = [];
$trainedWeights = [];
$userSuggestions = [];
$userInconsistencies = [];

if ($selectedUser) {
    // Fetch user growth data
    $userGrowthQuery = "
        SELECT DATE_FORMAT(session_start, '%Y-%m') AS month, COUNT(session_duration) AS user_count
        FROM user_sessions
        WHERE username = '$selectedUser'
        GROUP BY month
        ORDER BY month ASC";
    $userGrowthResult = $conn->query($userGrowthQuery);

    while ($row = $userGrowthResult->fetch_assoc()) {
        $growthMonths[] = $row['month'];
        $growthCounts[] = (int)$row['user_count'];
    }

    if (count($growthCounts) > 0) {
        // Simulate LSTM training
        $iterations = 100;
        $learningRate = 0.01;
        $bias = 0.1;

        for ($i = 0; $i < $iterations; $i++) {
            $weights = [];
            foreach ($growthCounts as $index => $value) {
                $prediction = ($index > 0) ? $growthCounts[$index - 1] * $bias : $value;
                $error = $value - $prediction;
                $weight = $error * $learningRate;
                $weights[] = $weight;
            }
            $trainedWeights = $weights;
        }

        // Analyze user data for inconsistencies
        foreach ($growthCounts as $monthIndex => $count) {
            if ($count === 0) {
                $userInconsistencies[] = "No activity in {$growthMonths[$monthIndex]}";
            }
        }

        // Provide suggestions
        if (count($userInconsistencies) > 0) {
            $userSuggestions[] = "Consider engaging the user more during inactive months.";
        }
        if (max($growthCounts) - min($growthCounts) > 50) {
            $userSuggestions[] = "User engagement is inconsistent. Consider regular check-ins.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeoAt - Statistical Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            color: #e0e0e0;
        }
        .sidebar {
            background-color: #1e1e2f;
            color: #b0bec5;
            height: 100vh;
            position: fixed;
            width: 250px;
            padding: 20px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin-bottom: 15px;
        }
        .sidebar ul li a {
            color: inherit;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
        }
        .sidebar ul li a:hover {
            background-color: #33364d;
            color: #ffffff;
        }
        .main-content {
            margin-left: 270px;
            padding: 20px;
        }
        .card {
            background-color: #242424;
            border: none;
            border-radius: 10px;
            padding: 30px;
            color: #e0e0e0;
        }
        footer {
            text-align: center;
            padding: 15px;
            background-color: #1e1e2f;
            color: #b0bec5;
        }
        canvas {
            max-width: 100%;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="text-center mb-4">
        <img src="Group 34056.png" alt="Admin" width="65">
        <h3>GeoAt</h3>
        <p class="text-muted">Admin Panel</p>
    </div>
    <ul>
        <li><a href="Admin.php">Dashboard</a></li>
        <li><a href="setcoordinates.php">Set Coordinates</a></li>
        <li><a href="editcoordinates.php">Edit Coordinates</a></li>
        <li><a href="statisticalMapping.php">Statistics</a></li>
        <li><a href="users.php">Users</a></li>
        <li><a href="lstm.php">Get Insights</a></li>
        <li><a href="registration.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1 class="mb-4">LSTM Model Insights</h1>

    <?php if ($isAdmin): ?>
        <div class="alert alert-warning">Admins are restricted from viewing insights.</div>
    <?php else: ?>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="selected_user" class="form-label">Select User</label>
                <select id="selected_user" name="selected_user" class="form-select">
                    <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['username']) ?>" <?= $selectedUser == $user['username'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View Insights</button>
        </form>

        <?php if ($selectedUser): ?>
            <div class="card mt-4 p-4">
    <h5 class="text-center text-info">LSTM Model Output for <?= htmlspecialchars($selectedUser) ?></h5>
    <p class="text-muted text-center mb-4">
        The following boxes represent the weights computed during LSTM model training, reflecting user engagement patterns over time.
    </p>
    <div class="d-flex flex-wrap justify-content-center gap-3">
        <?php foreach ($trainedWeights as $index => $weight): ?>
            <div class="card p-3 text-center" style="width: 150px; height: 150px; background-color: #007bff; color: white; border-radius: 10px;">
                <h6 class="mb-2">Iteration <?= $index + 1 ?></h6>
                <p class="mb-1" style="font-size: 24px; font-weight: bold;"><?= round($weight, 2) ?></p>
                <small><?= $weight > 0 ? 'Growth observed' : 'Decline observed' ?></small>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="mt-4 text-center">
        <strong>Insights:</strong><br>
        - Higher weights indicate periods of significant growth in user engagement.<br>
        - Lower or negative weights suggest a drop in user activity.<br>
        - Regular monitoring and tailored strategies are recommended to maintain consistent growth.
    </p>
</div>



            <div class="card mt-4">
                <h5 class="text-center text-success">User Growth Over Years</h5>
                <canvas id="userGrowthChart"></canvas>
            </div>
        <?php else: ?>
            <div class="card">
                <h5 class="text-center text-danger">No records found for the selected user.</h5>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2024 GeoAt | Designed by TechVentures</p>
</footer>

<script>
    const ctx = document.getElementById('userGrowthChart').getContext('2d');
    const userGrowthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_map(fn($month) => date("Y", strtotime($month)), $growthMonths)) ?>,
            datasets: [{
                label: 'User Growth',
                data: <?= json_encode($growthCounts) ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: '#e0e0e0' } }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Year', color: '#e0e0e0' },
                    ticks: { color: '#e0e0e0' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' }
                },
                y: {
                    title: { display: true, text: 'User Count', color: '#e0e0e0' },
                    ticks: { color: '#e0e0e0' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' }
                }
            }
        }
    });
</script>
</body>
</html>
