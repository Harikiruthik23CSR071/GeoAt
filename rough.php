<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeoLoc Attendance</title>
    <link rel ="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- // Google API key to get real time location data -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQ-JTbsBf1D6yayoW3mGvNHo0aJja6ZFE&libraries=places"></script>
</head>
<body>

   
    <nav class="navbar">
        <img src="Group 34056.png" alt="Logo">
        <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">News</a></li>
        </ul>
        <div class="profile"><img src="Profile-PNG-File.png" alt="profile">
            <span>
                <?php if (isset($_SESSION['username'])): ?>
                <p><?php echo $_SESSION['username']; ?>!</p>
                 <?php else: ?>
                   <p>Welcome!</p>
                 <?php endif; ?>
            </span>
        </div>
       <a href="registration.php" style="list-style-type: none;"> <button style='font-size:18px '>Logout <i class='fas fa-power-off'> </i></button></a>
    </nav>
    <div class="container">
        <header>
            <img style="height: 150px;" src="Group 34056.png" alt="Logo">
            <h1>GeoAt Attendance</h1>
        </header>

        <section id="userSection" class="hidden">
        <div id="userCard">
            <div class="user-avatar">
                <img src="Profile-PNG-File.png" alt="User Avatar">
            </div>
            <div class="user-info">
                <h2 id="userName">
                    <?php if (isset($_SESSION['username'])): ?>
                    <p><?php echo $_SESSION['username']; ?>!</p>
                    <?php else: ?>
                    <p>Welcome!</p>
                    <?php endif; ?>
                </h2>
                <p id="userEmail"><i class="fas fa-envelope"></i>
                    <?php if (isset($_SESSION['email'])): ?>
                    <?php echo $_SESSION['email']; ?>
                    <?php else: ?>
                    user@gmail.com
                    <?php endif; ?>
                </p>
                <p id="userLocation"><i class="fas fa-map-marker-alt"></i> Location: <span id="location"></span></p>
                <p id="sessionTime"><i class="fas fa-clock"></i> Session Time: <span id="sessionDuration"></span></p>
                <p id="activeStatus">
                    <i class="fas fa-toggle-on"></i> Active Status:
                    <button id="statusBtnYes" class="hidden">Yes</button>
                    <button id="statusBtnNo" class="hidden">No</button>
                </p>
            </div>
        </div>
    </section>


        <section id="actions">
            <button id="checkInBtn" onclick="checkIn()">Check In</button>
            <button id="checkOutBtn" onclick="checkOut()" class="hidden">Check Out</button>
            <button style="display:none" id="notEmployeeBtn" onclick="notAnEmployee()">Contact Admin</button>
        </section>

        <section id="contacts">
            <h2>Contacts & Helpline</h2>
            <p><i class="fas fa-user-shield"></i> Contact Admin: <a href="mailto:admin@example.com">admin@GeoAt.com</a></p>
            <p><i class="fas fa-phone-alt"></i> Helpline: <a href="tel:+1-234-567-890">+91 9342696026</a></p>
        </section>

        <footer>
            <p>&copy; 2024 GeoAt Attendance</p>
            <a href="#" style="list-style-type: none;">Terms & conditions</a>
        </footer>
    </div>
    <script>
    // Coordinates for the rectangular geofenced area (set by admin)
    const geoFence = {}; // Geofence will be dynamically fetched from the database

let sessionStartTime;
let sessionTimer;

function checkIn() {
    if (navigator.geolocation) {
        // Fetch the user's geofence data before getting their location
        fetchGeoFenceData()
            .then(() => {
                navigator.geolocation.getCurrentPosition(validatePosition, showError, { enableHighAccuracy: true });
            })
            .catch((error) => {
                alert(error.message);
            });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

// Function to fetch geofence data from the database
async function fetchGeoFenceData() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_geofence.php", true);

    return new Promise((resolve, reject) => {
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);

                if (response.success && response.data) {
                    // Assign geofence coordinates
                    geoFence.topLeft = {
                        latitude: parseFloat(response.data.top_left_lat),
                        longitude: parseFloat(response.data.top_left_lon),
                    };
                    geoFence.bottomRight = {
                        latitude: parseFloat(response.data.bottom_right_lat),
                        longitude: parseFloat(response.data.bottom_right_lon),
                    };
                    resolve();
                } else {
                    reject(new Error("No location data specified. Please contact the admin."));
                }
            } else {
                reject(new Error("Failed to fetch geofence data. Please try again later."));
            }
        };

        xhr.onerror = function () {
            reject(new Error("Error fetching geofence data."));
        };

        xhr.send();
    });
}

// Validate the user's current position against the fetched geofence
function validatePosition(position) {
    const { latitude, longitude } = position.coords;

    console.log(`User's latitude: ${latitude}`);
    console.log(`User's longitude: ${longitude}`);
    console.log(`Top Left corner: ${geoFence.topLeft.latitude}, ${geoFence.topLeft.longitude}`);
    console.log(`Bottom Right corner: ${geoFence.bottomRight.latitude}, ${geoFence.bottomRight.longitude}`);

    if (isInsideRectangle(latitude, longitude, geoFence.topLeft, geoFence.bottomRight)) {
        document.getElementById('userSection').classList.remove('hidden');
        document.getElementById('checkOutBtn').classList.remove('hidden');
        document.getElementById('checkInBtn').classList.add('hidden');
        document.getElementById('notEmployeeBtn').classList.add('hidden');
        document.getElementById('statusBtnYes').classList.remove('hidden');
        document.getElementById('statusBtnNo').classList.add('hidden');
        sessionStartTime = new Date();
        startTimer();

        document.getElementById('location').innerText = `Lat: ${latitude}, Lon: ${longitude}`;
    } else {
        alert("You are outside the allowed location. Please move within the designated area to check in.");
    }
}

// Check if the user's current location is inside the geofence
function isInsideRectangle(lat, lon, topLeft, bottomRight) {
    const isLatitudeInRange = lat >= bottomRight.latitude && lat <= topLeft.latitude;
    const isLongitudeInRange = lon >= topLeft.longitude && lon <= bottomRight.longitude;
    return isLatitudeInRange && isLongitudeInRange;
}

// Timer functionality
function startTimer() {
    sessionTimer = setInterval(() => {
        const now = new Date();
        const elapsed = Math.floor((now - sessionStartTime) / 1000);
        document.getElementById('sessionDuration').innerText = formatTime(elapsed);
    }, 1000);
}

function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes}m ${secs}s`;
}

function endSession() {
    const sessionEndTime = new Date();
    const elapsed = Math.floor((sessionEndTime - sessionStartTime) / 1000);

    console.log("Session Start Time:", sessionStartTime);
    console.log("Session End Time:", sessionEndTime);
    console.log("Elapsed Time (in seconds):", elapsed);

    const formattedSessionStartTime = sessionStartTime.toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' });
    const formattedSessionEndTime = sessionEndTime.toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' });

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "end_session.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send(`duration=${elapsed}&sessionEndTime=${formattedSessionEndTime}&sessionStartTime=${formattedSessionStartTime}`);
}


function checkOut() {
    clearInterval(sessionTimer);
    endSession();  // Store the session data on check-out
    document.getElementById('checkInBtn').classList.remove('hidden');
    document.getElementById('checkOutBtn').classList.add('hidden');

    document.getElementById('statusBtnYes').classList.add('hidden');
    document.getElementById('statusBtnNo').classList.remove('hidden');

    document.getElementById('sessionDuration').innerText += ' (Session Ended)';
}

function notAnEmployee() {
    alert('Please contact admin.');
}

function showError(error) {
    switch (error.code) {
        case error.PERMISSION_DENIED:
            alert("Permission to access location was denied. Please enable location permissions and try again.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable. Please ensure your device's location services are enabled.");
            break;
    }
}

</script>
</body>
</html>