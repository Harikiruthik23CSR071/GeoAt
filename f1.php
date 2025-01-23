<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgeoat";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for storing query results

if (isset($_SESSION['username'])) {
    $inputUsername = $_SESSION['username'];

    $stmt = $conn->prepare("
        SELECT 
            username, 
            session_start, 
            session_end, 
            session_duration,
            checkout_time
        FROM 
            user_sessions 
        WHERE username = ?
        ORDER BY session_start
    ");
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $singleUserResult = $stmt->get_result();
}

$resultData = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="f1.css">
    <link rel="stylesheet" href="checkbox.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="f1.js"></script>
    

    <title>GeoAt.com</title>
</head>
<!-- Header Section
–––––––––––––––––––––––––––––––––––––––––––––––––– -->

<header id="header">
    <div class="main_nav">
      <div class="container">
        <div class="mobile-toggle"> <span></span> <span></span> <span></span> </div>
        <nav>
          <ul>
            <li><a class="smoothscroll" href="#header">Home</a></li>
            <li><a class="smoothscroll" href="#about">About</a></li>
            <li><a class="smoothscroll" href="#skills">Check In</a></li>
            <li><a class="smoothscroll" href="#record">Record</a></li>
            <li><a class="smoothscroll" href="#contact">Contact</a></li>
            <li><a href="logingeo.php">Logout</a></li>
          </ul>
        </nav>
      </div>
    </div>
    <div class="title">
      <div><span class="typcn typcn-heart-outline icon heading"></span></div>
      <div class="smallsep heading"></div>
      <h1 class="heading"> 
        <img src="Group 34056.png" alt="Logo">
        <span>
        <?php if (isset($_SESSION['username'])): ?>
        <p><?php echo $_SESSION['username']; ?>!</p>
         <?php else: ?>
           <p>Hi!</p>
         <?php endif; ?>
        </span>
      </h1>
      <h2 class="heading">Welcome to</h2>
      <h2 class="heading">GeoAt</h2>
      <a class="smoothscroll" href="#about">
      <div class="mouse">
        <div class="wheel"></div>
      </div>
      </a> </div>
    <a class="smoothscroll" href="#about">
    <div class="scroll-down"></div>
    </a> </header>
    
  <!-- About Section
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->  
    
<section id="about">
    <div class="container">
      <div class="row">
        <h1>About</h1>
        <div class="block"></div>
        <p>Welcome to our Geolocation-Based Attendance solution. We strive to provide a seamless and efficient way to manage attendance using cutting-edge geolocation technology.</p>
      </div>
      <div class="row">
        <div class="six columns">
          <h3><span class="typcn typcn-device-desktop icon"></span>Our Process</h3>
          <p>We regulate and collect attendance data efficiently to ensure accuracy and reliability for all users.</p>
        </div>
        <div class="six columns">
          <h3><span class="typcn typcn-pen icon"></span>Our Approach</h3>
          <p>Our approach is simple and straightforward, making it easy for users to track their attendance without hassle.</p>
        </div>
      </div>
      <div class="row">
        <div class="six columns">
          <h3><span class="typcn typcn-cog-outline icon"></span>Our Goal</h3>
          <p>Our goal is to make attendance management easier, saving time and effort for both users and administrators.</p>
        </div>
        <div class="six columns">
          <h3><span class="typcn typcn-lightbulb icon"></span>Our Mission</h3>
          <p>We believe in transparency between users and their attendance records, fostering trust and accountability.</p>
        </div>
      </div>
    </div>
</section>
  
  <!-- Team Section
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->  
  
  <!-- <section id="team">
    <div class="container">
      <div class="row">
        <h1>Meet the Team</h1>
        <div class="block"></div>
        <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
      </div>
      <div class="row">
        <div class="three columns"> <img src="http://placehold.it/220x220" width="220" height="220" alt=""/>
          <h4>Naruto Uzumaki</h4>
          <p>Creative Director</p>
          <span class="typcn typcn-social-facebook-circular icon"></span><span class="typcn typcn-social-instagram-circular icon"></span><span class="typcn typcn-social-google-plus-circular icon"></span><span class="typcn typcn-social-linkedin-circular icon"></span> </div>
        <div class="three columns"> <img src="http://placehold.it/220x220" width="220" height="220" alt=""/>
          <h4>Sasuke Uchiha</h4>
          <p>Lead Designer</p>
          <span class="typcn typcn-social-facebook-circular icon"></span><span class="typcn typcn-social-instagram-circular icon"></span><span class="typcn typcn-social-google-plus-circular icon"></span><span class="typcn typcn-social-linkedin-circular icon"></span> </div>
        <div class="three columns"> <img src="http://placehold.it/220x220" width="220" height="220" alt=""/>
          <h4>Shikamaru Nara</h4>
          <p>Designer</p>
          <span class="typcn typcn-social-facebook-circular icon"></span><span class="typcn typcn-social-instagram-circular icon"></span><span class="typcn typcn-social-google-plus-circular icon"></span><span class="typcn typcn-social-linkedin-circular icon"></span> </div>
        <div class="three columns"> <img src="http://placehold.it/220x220" width="220" height="220" alt=""/>
          <h4>Sakura Haruno</h4>
          <p>Designer</p>
          <span class="typcn typcn-social-facebook-circular icon"></span><span class="typcn typcn-social-instagram-circular icon"></span><span class="typcn typcn-social-google-plus-circular icon"></span><span class="typcn typcn-social-linkedin-circular icon"></span> </div>
      </div>
    </div>
  </section> -->
  
  <!-- Skills Section
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->  
  
  <section id="skills" style="height: 480px;color:white;background-image: linear-gradient(90deg, rgb(72, 72, 72),rgb(243, 243, 243)),linear-gradient(0deg, rgb(89, 116, 30) 0%, rgb(89, 116, 30) 16%,rgb(112, 136, 32) 16%, rgb(112, 136, 32) 63%,rgb(135, 156, 34) 63%, rgb(135, 156, 34) 66%,rgb(158, 175, 36) 66%, rgb(158, 175, 36) 71%,rgb(181, 195, 38) 71%, rgb(181, 195, 38) 90%,rgb(204, 215, 40) 90%, rgb(204, 215, 40) 100%),linear-gradient(67.5deg, rgb(89, 116, 30) 0%, rgb(89, 116, 30) 16%,rgb(112, 136, 32) 16%, rgb(112, 136, 32) 63%,rgb(135, 156, 34) 63%, rgb(135, 156, 34) 66%,rgb(158, 175, 36) 66%, rgb(158, 175, 36) 71%,rgb(181, 195, 38) 71%, rgb(181, 195, 38) 90%,rgb(204, 215, 40) 90%, rgb(204, 215, 40) 100%),linear-gradient(157.5deg, rgb(89, 116, 30) 0%, rgb(89, 116, 30) 16%,rgb(112, 136, 32) 16%, rgb(112, 136, 32) 63%,rgb(135, 156, 34) 63%, rgb(135, 156, 34) 66%,rgb(158, 175, 36) 66%, rgb(158, 175, 36) 71%,rgb(181, 195, 38) 71%, rgb(181, 195, 38) 90%,rgb(204, 215, 40) 90%, rgb(204, 215, 40) 100%); background-blend-mode:overlay, overlay, overlay, normal;">
    <div class="container">
      <h1>Check In</h1>
      <div class="block">
          <div class="containerr">
            <section class="head">
                <img style="height: 150px;margin-right: 20px;margin-left: -60px;" src="Group 24.png" alt="Logo">
                <h1 style="color: black;margin-right: 20px;margin-left: -80px;">Ready to Lock In</h1>
                <button id="checkOutBtn" onclick="checkOut()" class="hidden">Check Out</button>
            </section>
            <section id="userSection" class="hidden">
              <div id="userCard">
                  <div class="user-avatar">
                      <img src="Profile-PNG-File.png" alt="User Avatar">
                  </div>
                  <div class="user-info">
                      <p id="userName">
                          <?php if (isset($_SESSION['username'])): ?>
                          <p><?php echo $_SESSION['username']; ?>!</p>
                          <?php else: ?>
                          <?php endif; ?>
                      </p>
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
                    </section>
          
          
                    <section id="actions">
                        <button id="checkInBtn" onclick="checkIn()">Check In</button>
                        <button style="display:none" id="notEmployeeBtn" onclick="notAnEmployee()">Contact Admin</button>
                    </section>
                </div>
              </div>
     </div>
   </div>
  </section>
  
  <!-- Portfolio Section
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->  
  
  <section id="record">
    <div class="container">
      <h1>Record</h1>
      <div class="block"></div>
      <div class="row">
      <?php if ($singleUserResult && $singleUserResult->num_rows > 0): ?>
            <h3><?php echo htmlspecialchars($inputUsername); ?>'s Record</h3>
            <table border="1">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>WORKING TIME(secs)</th>
                        <th>checkOutTime</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $singleUserResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['session_duration']); ?></td>
                            <td><?php echo htmlspecialchars($row['checkout_time']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif (isset($inputUsername)): ?>
            <p>No data found for user: <?php echo htmlspecialchars($inputUsername);?></p>
        <?php endif; ?>
      </div>
    </div>
  </section>
  
  <!-- Testimonials Section
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->  
  
  <section id="testimonial">
    <div class="container">
      <div class="quoteLoop">
        <blockquote class="quote"> <img src="http://placehold.it/100x100" width="100" height="100" alt=""/>
          <h5>&nbsp;<br>
            &rdquo;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.&rdquo;<br>
            <small>Steve Bruce, Sed ut perspiciatis unde omnis</small></h5>
        </blockquote>
        <blockquote class="quote"> <img src="http://placehold.it/100x100" width="100" height="100" alt=""/>
          <h5>&nbsp;<br>
            &ldquo;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.&rdquo;<br>
            <small>Tom Jones, Sed ut perspiciatis unde omnis</small></h5>
        </blockquote>
      </div>
    </div>
  </section>
  
  <!-- Contact Section
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->  
  
  <section id="contact">
    <div class="container">
      <h1>Contact</h1>
      <div class="block"></div>
      <form>
        <div class="row">
          <div class="six columns">
            <label for="exampleRecipientInput">Name</label>
            <input class="u-full-width" type="text">
          </div>
          <div class="six columns">
            <label for="exampleEmailInput">Email</label>
            <input class="u-full-width" type="email">
          </div>
        </div>
        <div class="row">
          <label for="exampleMessage">Message</label>
          <textarea class="u-full-width"></textarea>
          <input class="button-primary" type="submit" value="Submit">
        </div>
      </form>
    </div>
  </section>
  
  <!-- Footer Section
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->  
  
  <footer>
    <div class="container">
      <div class="nine columns">
        <p>Geolocation-Based Attendance.com</p>
      </div>
      <div class="three columns"> <span class="typcn typcn-social-facebook-circular socialIcons"></span> <span class="typcn typcn-social-instagram-circular socialIcons"></span> <span class="typcn typcn-social-google-plus-circular socialIcons"></span> <span class="typcn typcn-social-linkedin-circular socialIcons"></span> </div>
    </div>
  </footer>
  <script>
    // Coordinates for the rectangular geofenced area (set by admin)
    const geoFence = {
        topLeft: { latitude: 12.0211, longitude: 77.5252 }, 
        bottomRight: { latitude: 11.2716, longitude: 77.6083 }
    };

    let sessionStartTime;
    let sessionTimer;

    function checkIn() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(validatePosition, showError, { enableHighAccuracy: true });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function validatePosition(position) {
        const { latitude, longitude } = position.coords;

        console.log(`User's latitude: ${latitude}`);
        console.log(`User's longitude: ${longitude}`);
        console.log(`Top Left corner: ${geoFence.topLeft.latitude}, ${geoFence.topLeft.longitude}`);
        console.log(`Bottom Right corner: ${geoFence.bottomRight.latitude}, ${geoFence.bottomRight.longitude}`);

        // Proceed with check-in regardless of location
        document.getElementById('userSection').classList.remove('hidden');
        document.getElementById('checkOutBtn').classList.remove('hidden');
        document.getElementById('checkInBtn').classList.add('hidden');
        document.getElementById('notEmployeeBtn').classList.add('hidden');
        document.getElementById('statusBtnYes').classList.remove('hidden');
        document.getElementById('statusBtnNo').classList.add('hidden');
        sessionStartTime = new Date();
        startTimer();

        document.getElementById('location').innerText = `Lat: ${latitude}, Lon: ${longitude}`;
    }
    // function isInsideRectangle(lat, lon, topLeft, bottomRight) {
    //     const isLatitudeInRange = lat >= bottomRight.latitude && lat <= topLeft.latitude;
    //     const isLongitudeInRange = lon >= topLeft.longitude && lon <= bottomRight.longitude;
    //     return isLatitudeInRange && isLongitudeInRange;
    // }
    // function validatePosition(position) {
    //     const { latitude, longitude } = position.coords;

    //     console.log(`User's latitude: ${latitude}`);
    //     console.log(`User's longitude: ${longitude}`);
    //     console.log(`Top Left corner: ${geoFence.topLeft.latitude}, ${geoFence.topLeft.longitude}`);
    //     console.log(`Bottom Right corner: ${geoFence.bottomRight.latitude}, ${geoFence.bottomRight.longitude}`);

    //     const isInside = isInsideRectangle(latitude, longitude, geoFence.topLeft, geoFence.bottomRight);
    //     console.log(`Is inside geofence: ${isInside}`);

    //     if (isInside) {
    //         // Proceed with check-in
    //     } else {
    //         alert("You are outside the allowed location. Please move within the designated area to check in.");
    //     }
    // }


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

        // Use toLocaleString() for Indian Standard Time (IST)
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
</html>