<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgeo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee count
$employeeCountQuery = "SELECT COUNT(DISTINCT username) AS employee_count FROM user_sessions";
$employeeCountResult = $conn->query($employeeCountQuery);
$employeeCount = ($employeeCountResult->num_rows > 0) ? $employeeCountResult->fetch_assoc()['employee_count'] : 0;

// Fetch user growth data (monthly for a year)
$userGrowthQuery = "
    SELECT DATE_FORMAT(session_start, '%Y-%m') AS month, COUNT(DISTINCT username) AS user_count 
    FROM user_sessions 
    GROUP BY month
    ORDER BY month ASC";
$userGrowthResult = $conn->query($userGrowthQuery);

$growthMonths = [];
$growthCounts = [];
while ($row = $userGrowthResult->fetch_assoc()) {
    $growthMonths[] = $row['month'];
    $growthCounts[] = (int)$row['user_count'];
}

// LSTM Dynamic Training (simplified for demonstration)
$trainedWeights = [];
$iterations = 100;

if (isset($_POST['add_growth'])) {
    $newGrowth = (int)$_POST['new_growth'];
    $growthCounts[] = $newGrowth;

    // Simulate LSTM training (placeholder logic)
    $trainedWeights = [];
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

    $serializedModel = serialize($trainedWeights);
    $storeModelQuery = "
        INSERT INTO lstm_models (model_name, model_data) 
        VALUES ('user_growth', '$serializedModel')
        ON DUPLICATE KEY UPDATE model_data = '$serializedModel'";
    $conn->query($storeModelQuery);
}

$modelQuery = "SELECT model_data FROM lstm_models WHERE model_name = 'user_growth'";
$modelResult = $conn->query($modelQuery);
$trainedWeights = ($modelResult->num_rows > 0) ? unserialize($modelResult->fetch_assoc()['model_data']) : [];
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
            padding-top: 20px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px 20px;
        }
        .sidebar ul li a {
            color: inherit;
            text-decoration: none;
        }
        .sidebar ul li a:hover {
            color: #ffffff;
        }
        .card {
            background-color: #242424;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            color: #e0e0e0;
        }
        .card h5 {
            margin-bottom: 10px;
            color: #007bff;
        }
        footer {
            text-align: center;
            padding: 15px;
            background-color: #1e1e2f;
            color: #b0bec5;
        }
        .equal-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%; /* Ensure cards take up the full height of the parent */
}

.interactive-card {
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.interactive-card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

.details {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 123, 255, 0.9);
    color: white;
    padding: 10px;
    transform: translateY(100%);
    transition: transform 0.3s ease;
    text-align: center;
}

.interactive-card:hover .details {
    transform: translateY(0);
}

/* Ensures equal card height across all rows */
.row.g-4 > div {
    display: flex;
}

.row.g-4 > div > .card {
    flex: 1;
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
          <li><a href="#"><i class="uil uil-users-alt"></i> Users</a></li>
          <li><a href="lstm.php"><i class="uil uil-setting"></i> Get Insights</a></li>
          <li><a href="registration.php"><i class="uil uil-signout"></i> Logout</a></li>
        </ul>
      </nav>
        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
    <h1 class="mt-4">Model Insights</h1>
    <div class="row g-4">
    <!-- Total Employees Card -->
    <div class="col-lg-4 col-md-6">
        <div class="card p-4 shadow-sm text-center interactive-card equal-card">
            <h5 class="mb-3 text-primary">Total Active Employees</h5>
            <h2 class="display-4 fw-bold text-light"><?= $employeeCount ?></h2>
            <!-- <div class="details">
                <p>Total active employees currently tracked by the system.</p>
            </div> -->
        </div>
    </div>


<!-- LSTM Model Output Card -->
<div class="col-lg-4 col-md-6">
    <div class="card p-4 shadow-sm text-center interactive-card equal-card">
        <h5 class="mb-3 text-success" style="font-size: 1.25rem;">LSTM Model Output</h5>
        <div class="d-flex flex-wrap justify-content-center">
            <?php foreach ($trainedWeights as $weight): ?>
                <span class="badge bg-secondary mx-2 my-2" style="font-size: 1.25rem; padding: 8px 16px; border-radius: 12px;"><?= $weight ?></span>
            <?php endforeach; ?>
        </div>
        <!-- <div class="details mt-3">
            <p style="font-size: 1rem;">Each point represents the model's calculated weight for predicting user growth trends.</p>
        </div> -->
    </div>
</div>



    <!-- User Growth Input Card -->
    <div class="col-lg-4 col-md-12">
        <div class="card p-4 shadow-sm text-center interactive-card equal-card">
            <h5 class="mb-3 text-warning">Add User Growth</h5>
            <form method="POST">
                <input type="number" name="new_growth" class="form-control mb-3" placeholder="Enter Growth Value">
                <button type="submit" class="btn btn-primary w-100" name="add_growth">Add Growth</button>
            </form>
           
        </div>
    </div>
</div>


    <!-- User Growth Chart -->
    <div class="mt-5">
        <div class="card p-4 shadow-sm">
            <h5 class="mb-3 text-center text-info">User Growth Over Time</h5>
            <canvas id="userGrowthChart"></canvas>
        </div>
    </div>
</main>

    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 GeoAt | Designed by TechVentures</p>
</footer>

<script>
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    const userGrowthChart = new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($growthMonths) ?>,
            datasets: [{
                label: 'User Growth',
                data: <?= json_encode($growthCounts) ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Month: ${context.label}, Users: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Number of Users'
                    }
                }
            }
        }
    });
</script>
<script>
document.querySelectorAll('.interactive-card').forEach(card => {
    card.addEventListener('mouseover', () => {
        const details = card.querySelector('.details');
        if (details) {
            const title = card.querySelector('h5').textContent;
            if (title.includes('LSTM Model Output')) {
                details.textContent = 'This shows the weights used by the model for predicting growth trends.';
            } else if (title.includes('Total Active Employees')) {
                details.textContent = 'Shows the total number of employees currently being tracked.';
            } else if (title.includes('Add User Growth')) {
                details.textContent = 'Allows you to input growth data to analyze user trends further.';
            }
        }
    });
});
</script>

</body>
</html>
