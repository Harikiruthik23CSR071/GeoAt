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
    <style> body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        thead {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }

        header h1 {
            margin: 0;
        }

        section {
            padding: 20px;
            margin: 20px 0;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }

        ul li::before {
            
            position: absolute;
            left: 0;
            color: #28a745;
            font-size: 16px;
        }

        .two-columns {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .two-columns > div {
            flex: 1;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }

        .social-media {
            margin-top: 10px;
        }

        .social-media a {
            margin-right: 10px;
            text-decoration: none;
            font-size: 20px;
            color: #333;
            transition: color 0.3s;
        }

        .social-media a:hover {
            color: #007bff;
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .records-table thead {
            background-color: #007bff;
            color: #fff;
        }

        .records-table th, .records-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .records-table tbody tr:hover {
            background-color: #f1f1f1;
        }

       

</style>
</head>
<body>

<nav class="navbar">
        <img src="Group 34056.png" alt="Logo">
        <ul>
            <li><a href="#about">About Us</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contacts">Contact Us</a></li>
            <li><a href="#news">News</a></li>
            <li><a href="#records">Records</a></li>
        </ul>
        <div class="profile">
            <img src="Profile-PNG-File.png" alt="profile">
            <span>
                <?php if (isset($_SESSION['username'])): ?>
                <p><?php echo $_SESSION['username']; ?>!</p>
                <?php else: ?>
                <p>Welcome!</p>
                <?php endif; ?>
            </span>
        </div>
        <button  style="font-size: 18px; cursor: pointer;" onclick="logout()"> Logout <i class="fas fa-power-off"></i></button>

<script>
    function logout() {
        // Redirect the user to the registration.php page
        window.location.href = "registration.php";
    }
</script>
    </nav>

    <div class="container">
        <header>
            <img style="height: 150px;" src="Group 34056.png" alt="Logo">
            <h1>GeoAt Attendance</h1>
        </header>

        <section id="userSection" class="hidden">
        <div id="userCard">
            <div class="user-avatar">
                <img src="employee.png" alt="User Avatar">
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

        <footer class="footer1">
            <p>&copy; 2024 GeoAt Attendance</p>
            <a href="#" style="list-style-type: none;">Terms & conditions</a>
        </footer>
    </div>
    <section id="about">
        <h2>About Us</h2>
        <p>
            Welcome to <strong>GeoAt Attendance</strong>, your trusted platform for seamless geolocation-based attendance
            tracking. Our system is designed to enhance productivity, ensure accountability, and simplify workforce
            management.
        </p>
        <p>
            With cutting-edge features like real-time tracking, session analytics, and detailed reporting, we aim to
            revolutionize how you manage attendance in your organization.
        </p>
    </section>

    <div class="two-columns">
        <section id="services">
            <h2>Our Services</h2>
            <ul>
                <li>Real-time attendance tracking with geolocation precision.</li>
                <li>Customizable session management for flexible work policies.</li>
                <li>Insightful analytics and reporting to optimize your team's performance.</li>
                <li>Secure data storage ensuring compliance and privacy.</li>
            </ul>
        </section>

        <section id="contacts">
            <h2>Contact Us</h2>
            <p>We’re here to help! Reach out to us anytime:</p>
            <div class="contact-details">
                <p><i class="fas fa-user-shield"></i> Admin: <a href="mailto:admin@geoat.com">admin@geoat.com</a></p>
                <p><i class="fas fa-phone-alt"></i> Helpline: <a href="tel:+91-9342696026">+91 9342696026</a></p>
                <div class="social-media">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </section>
    </div>

    <section id="news">
        <h2>Latest News</h2>
        <p>Stay updated with our exciting features and announcements!</p>
        <ul>
            <li>Upcoming feature: Automated break time tracking and reminders.</li>
            <li>New partnership with global HR platforms for seamless integration.</li>
            <li>Free webinar: "Enhancing Workforce Management with GeoAt Attendance."</li>
        </ul>
    </section>

    <section id="records">
        <h2>Session Records</h2>
        <div id="userRecords">
            <?php
            $conn = new mysqli("localhost", "root", "", "dbgeo","3307");

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
            $sql = "SELECT session_start, session_end, session_duration FROM user_sessions WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<table class='records-table'>
                        <thead>
                            <tr>
                                <th>Session Start</th>
                                <th>Session End</th>
                                <th>Session Duration</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['session_start']}</td>
                            <td>{$row['session_end']}</td>
                            <td>{$row['session_duration']}</td>
                          </tr>";
                }
                echo "</tbody>
                    </table>";
            } else {
                echo "<p>No records found.</p>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>
    </section>

   
</section>

   </section>
</body>
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
<script>document.querySelectorAll('.navbar a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href').substring(1);
        document.getElementById(targetId).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
</script>
</html>