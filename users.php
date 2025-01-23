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

// Initialize message variables
$message = '';
$messageType = '';

// Handle form submissions for adding or editing users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $role = $conn->real_escape_string($_POST['role']);

        // Check for existing user
        $checkQuery = "SELECT * FROM geoloc WHERE email = '$email'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            // Delete the existing user with the same email
            $deleteQuery = "DELETE FROM geoloc WHERE email = '$email'";
            $conn->query($deleteQuery);
        }

        // Insert the new user
        $insertQuery = "INSERT INTO geoloc (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        if ($conn->query($insertQuery)) {
            $message = "User added successfully!";
            $messageType = "success";
        } else {
            $message = "Error adding user: " . $conn->error;
            $messageType = "danger";
        }
    }

    if (isset($_POST['edit_user'])) {
        $userId = $conn->real_escape_string($_POST['id']);
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $role = $conn->real_escape_string($_POST['role']);

        // Check for existing user with the same email but different ID
        $checkQuery = "SELECT * FROM geoloc WHERE email = '$email' AND id != '$userId'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            // Delete the existing user with the same email
            $deleteQuery = "DELETE FROM geoloc WHERE email = '$email'";
            $conn->query($deleteQuery);
        }

        // Update the user with the new details
        $updateQuery = "UPDATE geoloc SET username = '$username', email = '$email', role = '$role' WHERE id = '$userId'";
        if ($conn->query($updateQuery)) {
            $message = "User details updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating user details: " . $conn->error;
            $messageType = "danger";
        }
    }
}

// Fetch users with working details
$usersQuery = "
    SELECT 
        u.id,
        u.username,
        u.email,
        u.role,
        IFNULL(SEC_TO_TIME(SUM(us.session_duration)), '00:00:00') AS total_working_hours,
        IFNULL(SEC_TO_TIME(AVG(us.session_duration)), '00:00:00') AS avg_session_duration,
        COUNT(us.session_start) AS active_sessions
    FROM geoloc u
    LEFT JOIN user_sessions us ON u.username = us.username
    GROUP BY u.id, u.username, u.email, u.role
    ORDER BY u.username
";

$usersResult = $conn->query($usersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/uicons/1.0.0/css/uicons-regular-rounded.min.css" rel="stylesheet">
    <style>
        /* Sidebar */
        .sidebar {
            background-color: #343a40;
            height: 100vh;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            padding-top: 20px;
            width: 220px;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 10px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            display: flex;
            align-items: center;
            padding: 10px 20px;
            transition: background 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #495057;
            border-radius: 5px;
        }

        .sidebar ul li a i {
            margin-right: 10px;
        }

        /* Main Content */
        main {
            margin-left: 240px;
            padding: 20px;
        }

        .table-container {
            margin-top: 30px;
        }

        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }

        /* Footer */
        footer {
            background-color: #343a40;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        footer p {
            margin: 0;
        }

        /* Modal Buttons */
        .modal-footer button {
            margin-right: 10px;
        }
    </style>
</head>
<body>

        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div style="display: flex; align-items: center;">
                    <img src="Group 34056.png" alt="Admin" width="50">
                    <h2 style="margin: 0 0 0 15px;">eoAt</h2>
                </div>
            </div>
            <center><h5 class="mt-3">Admin</h5></center>
            <p>____________________________</p>
            <ul class="nav flex-column">
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
        <main>
            
        <div class="container">
        <h1 class="text-center">User Management</h1>
        <?php if ($message): ?>
    <div id="alertBox" class="alert alert-<?= $messageType ?>"><?= $message ?></div>
    <script>
        setTimeout(() => {
            const alertBox = document.getElementById('alertBox');
            if (alertBox) {
                alertBox.style.transition = "opacity 0.5s ease";
                alertBox.style.opacity = "0";
                setTimeout(() => alertBox.remove(), 500); // Remove element after fade-out
            }
        }, 3000); // 3 seconds delay
    </script>
<?php endif; ?>

                <div class="table-container">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Total Working Hours</th>
                                <th>Avg. Session Duration</th>
                                <th>Active Sessions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($user = $usersResult->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['total_working_hours']) ?></td>
                    <td><?= htmlspecialchars($user['avg_session_duration']) ?></td>
                    <td><?= htmlspecialchars($user['active_sessions']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="populateEditForm(<?= htmlspecialchars(json_encode($user)) ?>)">Edit</button>
                    </td>
                </tr>
            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal">Edit User Details</button>
                </div>
            </div>
        </main>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form method="POST" action="">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" name="username" id="username" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" name="email" id="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" name="password" id="password" required>
    </div>
    <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <input type="text" class="form-control" name="role" id="role" required>
    </div>
    <button type="submit" class="btn btn-primary" name="add_user">Submit</button>
</form>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form method="POST" action="">
    <input type="hidden" name="id" id="editUserId">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" name="username" id="editUsername" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" name="email" id="editEmail" required>
    </div>
    <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <input type="text" class="form-control" name="role" id="editRole" required>
    </div>
    <button type="submit" class="btn btn-primary" name="edit_user">Save Changes</button>
</form>

                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 GeoAt Admin Panel. All rights reserved.</p>
        <p>Designed by <a href="#" class="text-warning">TechVentures</a></p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
   function populateEditForm(user) {
    if (!user) {
        alert("Invalid user data.");
        return;
    }
    document.getElementById('editUserId').value = user.id || '';
    document.getElementById('editUsername').value = user.username || '';
    document.getElementById('editEmail').value = user.email || '';
    document.getElementById('editRole').value = user.role || '';
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

</script>
</body>
</html>







