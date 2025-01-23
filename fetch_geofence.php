<?php
session_start();
header('Content-Type: application/json');

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgeoat";
$port=3306;

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Check if the connection is successful
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Get the user's email or username from the session
$user_email_or_name = $_SESSION['email'] ?? $_SESSION['username'] ?? null;

if (!$user_email_or_name) {
    echo json_encode(['success' => false, 'message' => 'User is not logged in.']);
    exit();
}

// Step 1: Fetch the `id` from the geoloc table based on the user's email or username
$query_geoloc = "SELECT id FROM geoloc WHERE email = ? OR username = ?";
$stmt_geoloc = $conn->prepare($query_geoloc);
$stmt_geoloc->bind_param("ss", $user_email_or_name, $user_email_or_name);
$stmt_geoloc->execute();
$result_geoloc = $stmt_geoloc->get_result();

if ($result_geoloc->num_rows > 0) {
    $geoloc_data = $result_geoloc->fetch_assoc();
    $geoloc_id = $geoloc_data['id'];

    // Step 2: Use the `id` from geoloc as `user_id` to fetch coordinates
    $query_coordinates = "SELECT * FROM coordinates WHERE user_id = ?";
    $stmt_coordinates = $conn->prepare($query_coordinates);
    $stmt_coordinates->bind_param("i", $geoloc_id);
    $stmt_coordinates->execute();
    $result_coordinates = $stmt_coordinates->get_result();

    if ($result_coordinates->num_rows > 0) {
        $coordinate_data = $result_coordinates->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $coordinate_data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No location data specified.']);
    }

    $stmt_coordinates->close();
} else {
    echo json_encode(['success' => false, 'message' => 'User does not exist in the geoloc table.']);
}

$stmt_geoloc->close();
$conn->close();
?>
