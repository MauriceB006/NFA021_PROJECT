
<?php 
require_once 'check_auth.php';
require_once "cookies.php";
// Handle cookie consent
$showCookieConsent = true;
if (isset($_COOKIE['cookie_consent'])) {
    $showCookieConsent = false;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cookie_action'])) {
    $cookieConsent = false;
    $preferenceCookies = false;
    $analyticsCookies = false;
    
    if ($_POST['cookie_action'] === 'accept_all') {
        $cookieConsent = true;
        $preferenceCookies = true;
        $analyticsCookies = true;
    } elseif ($_POST['cookie_action'] === 'save_settings') {
        $cookieConsent = true;
        $preferenceCookies = isset($_POST['preference_cookies']);
        $analyticsCookies = isset($_POST['analytics_cookies']);
    } elseif ($_POST['cookie_action'] === 'reject_all') {
        $cookieConsent = true;
        $preferenceCookies = false;
        $analyticsCookies = false;
    }
    
    // Set cookies for 1 year
   // When setting cookies:
setcookie('cookie_consent', 'true', [
    'expires' => time() + 365*24*60*60,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// When checking:
$showCookieConsent = empty($_COOKIE['cookie_consent']) || $_COOKIE['cookie_consent'] !== 'true';
    // Redirect to avoid form resubmission
    header('Location: '.$_SERVER['PHP_SELF']);
    exit();
}

// Track route usage if analytics cookies are enabled
if (isset($_GET['route_used']) && ($_COOKIE['cookie_analytics'] ?? 'false') === 'true') {
    $route = $_GET['route_used'];
    $favoriteRoutes = json_decode($_COOKIE['favorite_routes'] ?? '{}', true);
    $favoriteRoutes[$route] = ($favoriteRoutes[$route] ?? 0) + 1;
    setcookie('favorite_routes', json_encode($favoriteRoutes), time() + 365*24*60*60, '/');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ACTC - Public Transport</title>
  <!--map-->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  
  <!-- favicon -->
  <link rel="shortcut icon" href="assets\images_v5\ACTC LOGO -02 SMALL.png" type="image/svg+xml">

  <!-- custom css link -->
  <link rel="stylesheet" href="assets\css\styleV4.css">

  <!-- google font link -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=Rubik:wght@400;500;600;700&display=swap"
    rel="stylesheet">
  <!-- preload images -->
  
</head>

<body id="top">

  <!-- HEADER -->
  <header class="header" data-header>
    <div class="container">
  
      <h1>
        <a href="indexV51.php">
          <img src="assets\images_v5\ACTC LOGO -02 SMALL.png" class="logo" alt="ACTC Public Transport">
        </a>
      </h1>
  
      <div class="header-user" style="color: white; font-weight: bold;">
        <?php if (isset($_SESSION['user_id'])): ?>
          <div class="user-profile">
            <span class="user-greeting">
              <ion-icon name="person-circle-outline"></ion-icon>
              Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (ID: <?php echo $_SESSION['user_id']; ?>)
            </span>
            <div class="user-dropdown">
              <a href="logout.php">Logout</a>
            </div>
          </div>
        <?php else: ?>
          <a href="sign_in.php" class="login-button">
            <span class="login-icon">
              <ion-icon name="person-circle-outline"></ion-icon>
            </span>
            <span class="login-text">Sign In</span>
          </a>
        <?php endif; ?>
      </div>
  
      <nav class="navbar" data-navbar>
        <!-- Navigation links -->
        <ul class="navbar-list">
          <li class="navbar-item">
            <a href="pages\searchplanner.html" class="navbar-link" data-nav-link>
              <span>Plan</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="#updates-map" class="navbar-link" data-nav-link>
              <span>Lines</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="pages\about.html" class="navbar-link" data-nav-link>
              <span>About</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="pages\contactV3.html" class="navbar-link" data-nav-link>
              <span>Contact</span>
            </a>
          </li>
          <!-- <li class="navbar-item">
            <a href="pages\career.php" class="navbar-link" data-nav-link>
              <span>Career</span>
            </a>
          </li> -->
        </ul>
      </nav>
  
      <button class="nav-open-btn" aria-label="Open menu" data-nav-toggler>
        <ion-icon name="menu-outline"></ion-icon>
      </button>
  
      <div class="overlay" data-nav-toggler data-overlay></div>
  
    </div>
  </header>

  <main>
    <article>
      <!-- HERO SECTION -->
      <section class="hero hero--full" aria-label="home" id="home"
        style="background-image: url('assets/images_v5/2 IMG_7419.jpg')">
        <div class="container">
          <div class="hero-content">
            <h2 class="h1 hero-title">
              <span class="span">Where Every</span> Mile Matters
            </h2>

            <p class="hero-text">
              <strong>Yalla, hop on The Public Transport Buses! From souk adventures to Corniche cruises-w ba3dna faster than your cousin's dabke moves!""</strong>
            </p>
          
            <a href="pages\searchplanner.html" class="btn-outline">Discover More</a>

            <img src="assets\images_v5\hero-shape.png" width="116" height="116" loading="lazy"
              class="hero-shape shape-1">

            <img src="assets\images_v5\hero-shape.png" width="116" height="116" loading="lazy"
              class="hero-shape shape-2">
          </div>
        </div>
      </section>

     
      <!-- ABOUT SECTION -->
      <section class="section about" id="about" aria-label="about">
        <div class="container">
            <img src="assets\images_v5\employee.jpg" width="400" height="720" loading="lazy" alt=""
              class="img-cover-1">

            <img src="assets\images_v5\about-shape-2.png" width="500" height="500" loading="lazy" alt=""
              class="abs-img abs-img-2">

          <div class="about-content">
            <p class="section-subtitle">Why Choose Us ?</p>

            <p class="section-text">
              we are dedicated to providing an essential and efficient public transportation service that reflects the dynamic spirit of Lebanon
            </p>
         
            <ul class="about-list">
              <li class="about-item">
                <div class="about-icon">
                  <ion-icon name="chevron-forward"></ion-icon>
                </div>
                <p class="about-text">
                  Providing safe, reliable, and efficient transportation services.
                </p>
              </li>

              <li class="about-item">
                <div class="about-icon">
                  <ion-icon name="chevron-forward"></ion-icon>
                </div>
                <p class="about-text">
                  Prioritizing comfort, punctuality, and accessibility for all passengers.
                </p>
              </li>

              <li class="about-item">
                <div class="about-icon">
                  <ion-icon name="chevron-forward"></ion-icon>
                </div>
                <p class="about-text">
                  Supporting the daily mobility needs of residents and visitors.
                </p>
              </li>

              <li class="about-item">
                <div class="about-icon">
                  <ion-icon name="chevron-forward"></ion-icon>
                </div>
                <p class="about-text">
                  Reflecting a commitment to quality and excellence in every journey.
                </p>
              </li>

              <li class="about-item">
                <div class="about-icon">
                  <ion-icon name="chevron-forward"></ion-icon>
                </div>
                <p class="about-text">
                  Honoring Lebanon's pulse with every honk and every turn.
                </p>
              </li>

              <li class="about-item">
                <div class="about-icon">
                  <ion-icon name="chevron-forward"></ion-icon>
                </div>
                <p class="about-text">
                  Where Lebanon's traffic turns into a celebration of tarab.
                </p>
              </li>
            </ul>

            <a href="pages\searchplanner.html" class="btn">Learn More</a>
          </div>
        </div>
      </section>

      <!-- FEATURE SECTION -->
      <section class="section feature" aria-label="feature" style="background-color: rgb(243, 248, 253);">
        <div class="container">
          <div class="title-wrapper">
            <div>
              <p class="section-subtitle">Routes & Stops</p>
              <h2 class="h2 section-title">Our Latest Bus Routes and Stops.</h2>
              <p class="section-text">
                Providing frequent stops to ensure accessibility for all passengers.
              </p>
            </div>
            <a href="pages\timetable.php" class="btn">View Schedule</a>
          </div>
          <h1>Bus Route Journey</h1>
          <select id="route-select" onchange="startJourney()">
            <option value="">Select a Line</option>
            <option value="B1">Line B1</option>
            <option value="ML3">Line ML3</option>
            <option value="B3">Line B3</option>
            <option value="B2">Line B2</option>
          </select>
          <div id="road">
            <span id="bus">🚌</span>
            <div id="stops">
              <!-- Stops will be dynamically updated -->
            </div>
          </div>
          <!-- Button to Display Prices -->
          <button class="btn" onclick="showPrice()">View Price</button>
          
          <!-- Price Display Section -->
          <div id="price-display" style="margin-top: 20px; display: none;">
            <h2>Fare Information</h2>
            <p id="price-text">Select a line to see its fare.</p>
          </div>
        </div>
      </section>
      
      <!-- UPDATES SECTION -->
      <section class="section updates-map" class="updates-map" id="updates-map" aria-label="updates-map"
        style="background-image: url('assets\images_v5\inside2.jpg'); background-repeat: no-repeat;; background-size: cover; background-position: center;">
        <div class="container">
          <div class="title-wrapper">
            <h1 class="section-subtitle" style="font-size: 100px;">Bus Line Map</h1>
            <h2 class="h2 section-title" style="color: rgb(134, 166, 192);">Explore all bus lines, stops, and schedules in one place.</h2>
          </div>

          <iframe src="https://www.google.com/maps/d/u/0/embed?mid=1hnN01yhLhJ2NdzCbcGjPo9g7o4dNyo8&ehbc=2E312F&noprof=1" width="100%" height="600"></iframe>


        </div>
      </section>
      <!-- PROJECT SECTION -->
      <section class="section project" aria-label="project">
        <div class="container">
          <p class="section-subtitle">Communication</p>
          <h2 class="h2 section-title">Engagement Featured</h2>
          <p class="section-text">
            Offering a platform for passengers to provide feedback or ask questions enhances user satisfaction.
          </p>

          <ul class="project-list">
            <li class="project-item">
              <div class="project-card">
                <figure class="card-banner img-holder" style="--width: 397; --height: 352;">
                  <img src="assets\images_v5\report.jpg" width="397" height="352" loading="lazy"
                    alt="Warehouse inventory" class="img-cover">
                </figure>

                <button class="action-btn" aria-label="View image">
                  <ion-icon name="expand-outline"></ion-icon>
                </button>

                <div class="card-content">
                  <p class="card-tag"> Passenger Reporting System</p>
                  <h3 class="h3">
                    <a href="pages\reportV3.html" class="card-title">Report drivers, incidents, or misconduct</a>
                  </h3>
                  <a href="pages\reportV3.html" class="card-link">click here</a>
                </div>
              </div>
            </li>

            <li class="project-item">
              <div class="project-card">
                <figure class="card-banner img-holder" style="--width: 397; --height: 352;">
                  <img src="assets\images_v5\pay.jpg" width="397" height="352" loading="lazy"
                    alt="Warehouse inventory" class="img-cover">
                </figure>

                <button class="action-btn" aria-label="View image">
                  <ion-icon name="expand-outline"></ion-icon>
                </button>

                <div class="card-content">
                  <p class="card-tag">Payment & Bus Card</p>
                  <h3 class="h3">
                    <a href="pages/paymentV3.PHP" class="card-title">Apply for a bus card</a>
                  </h3>
                  <a href="pages/paymentV3.PHP" class="card-link">click here</a>
                </div>
              </div>
            </li>

            <li class="project-item">
              <div class="project-card">
                <figure class="card-banner img-holder" style="--width: 397; --height: 352;">
                  <img src="assets\images_v5\opinion.jpg" width="397" height="352" loading="lazy"
                    alt="Warehouse inventory" class="img-cover">
                </figure>

                <button class="action-btn" aria-label="View image">
                  <ion-icon name="expand-outline"></ion-icon>
                </button>

                <div class="card-content">
                  <p class="card-tag">Reviews & Ratings</p>
                  <h3 class="h3">
                    <a href="pages\reviewV3.html" class="card-title">Share your experience with us</a>
                  </h3>
                  <a href="pages\reviewV3.html" class="card-link">click here</a>
                </div>
              </div>
            </li>

            <li class="project-item">
              <div class="project-card">
                <figure class="card-banner img-holder" style="--width: 397; --height: 352;">
                  <img src="assets\images_v5\bus2.jpg" width="397" height="352" loading="lazy"
                    alt="Warehouse inventory" class="img-cover">
                </figure>

                <button class="action-btn" aria-label="View image">
                  <ion-icon name="expand-outline"></ion-icon>
                </button>

                <div class="card-content">
                  <p class="card-tag"> Lost Items</p>
                  <h3 class="h3">
                    <a href="pages\lostV3.html" class="card-title">Report lost items on our buses.</a>
                  </h3>
                  <a href="pages\lostV3.html" class="card-link">click here</a>
                </div>
              </div>
            </li>

            <li class="project-item">
              <div class="project-card">
                <figure class="card-banner img-holder" style="--width: 397; --height: 352;">
                  <img src="assets\images_v5\employee2.jpg" width="397" height="352" loading="lazy"
                    alt="Warehouse inventory" class="img-cover">
                </figure>

                <button class="action-btn" aria-label="View image">
                  <ion-icon name="expand-outline"></ion-icon>
                </button>

                <div class="card-content">
                  <p class="card-tag">Contact Us</p>
                  <h3 class="h3">
                    <a href="pages\contactV3.html" class="card-title">Get in touch with us for any inquiries or support.</a>
                  </h3>
                  <a href="pages\contactV3.html" class="card-link">click here</a>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </section>

      <?php
      if (isset($_GET['review']) && $_GET['review'] === 'success') {
          echo '<div class="alert alert-success">Thank you for your review!</div>';
      }
      ?>

      <!-- NEWSLETTER SECTION -->
      <section class="section newsletter" aria-label="newsletter">
        <div class="container">
          <figure class="newsletter-banner img-holder">
            <!-- REMOVED AS PER ACTC REQUEST -->
          </figure>

          <!-- <div class="newsletter-content">
            <h2 class="h2 section-title">Subscribe for offers and news</h2>
            <form action="" class="newsletter-form">
              <input type="email" name="email_address" placeholder="Enter Your Email" aria-label="email"
                class="email-field">
              <button type="submit" class="newsletter-btn">Subscribe Now</button>
            </form>
          </div> -->
        </div>
      </section>
    </article>
  </main>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <div class="footer-top section">
        <div class="footer-brand">
          <a href="indexV51.php">
            <img src="assets\images_v5\ACTC LOGO -01.png" class="logo" alt="ACTC Public Transport">
          </a>

          <p class="footer-text">
            Empowering journeys, building connections, and driving progress every step of the way.
          </p>

          <ul class="social-list">
            <li>
              <a href="https://www.facebook.com/people/Ahdab-Commuting-Trading-Company-Public-Transport/61562826866056/" class="social-link">
                <ion-icon name="logo-facebook"></ion-icon>
              </a>
            </li>

            <li>
              <a href="https://www.instagram.com/actcpt.lb/?hl=en" class="social-link">
                <ion-icon name="logo-instagram"></ion-icon>
              </a>
            </li>

            <li>
              <a href="https://www.youtube.com/watch?v=CBHuCte5bdg" class="social-link">
                <ion-icon name="logo-youtube"></ion-icon>
              </a>
            </li>
          </ul>
        </div>

        <ul class="footer-list">
          <li>
            <p class="footer-list-title">Quick Links</p>
          </li>
          <li>
            <a href="pages\searchplanner.html" class="footer-link">Lines</a>
          </li>
          <li>
            <a href="pages\about.html" class="footer-link">About</a>
          </li>

          <li>
            <a href="pages\faq.html" class="footer-link">FAQ</a>
          </li>
          <li>
            <a href="pages\contactV3.html" class="footer-link">Contact Us</a>
          </li>
        </ul>

        <ul class="footer-list">
          <li>
            <p class="footer-list-title">Community</p>
          </li>
          
          <li>
            <a href="pages\privacy.html" class="footer-link">Privacy Policy</a>
          </li>
          <li>
            <a href="pages\terms.html" class="footer-link">Terms & Condition</a>
          </li>
             <li>
            <a href="admin_redirect.php" class="footer-link">Admins</a>

          </li>
        </ul>
      </div>

      <div class="footer-bottom">
        <p class="copyright">
          &copy; 2025 ACTC-Transport Publique. All Rights Reserved  <a href="#" class="copyright-link"></a>
        </p>
      </div>
    </div>
  </footer>

  <!-- BACK TO TOP -->
  <a href="#top" class="back-top-btn" aria-label="Back to top" data-back-top-btn>
    <ion-icon name="chevron-up"></ion-icon>
  </a>

  <!-- custom js link -->
  <script src="assets/js/scriptV3.js" defer></script>
<script>
// Function to set cookie
function setCookie(name, value, days) {
  let expires = "";
  if (days) {
    const d = new Date();
    d.setTime(d.getTime() + (days*24*60*60*1000));
    expires = "; expires=" + d.toUTCString();
  }
  document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

// Function to get cookie
function getCookie(name) {
  const nameEQ = name + "=";
  const ca = document.cookie.split(';');
  for(let i=0; i < ca.length; i++) {
    let c = ca[i];
    while(c.charAt(0)==' ') c = c.substring(1,c.length);
    if(c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

// Show popup if cookie not set
window.onload = function() {
  if (!getCookie("cookiesAccepted")) {
    document.getElementById("cookie-popup").style.display = "flex";
  }

  document.getElementById("accept-cookies").onclick = function() {
    setCookie("cookiesAccepted", "yes", 365);
    document.getElementById("cookie-popup").style.display = "none";
  };
};
</script>

  <!-- ionicon link -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <!-- Cookie Consent Popup -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($showCookieConsent): ?>
        setTimeout(function() {
            document.getElementById('cookie-popup').style.display = 'block';
        }, 3000);
    <?php endif; ?>
    
    function startJourney() {
        const routeSelect = document.getElementById('route-select');
        const selectedRoute = routeSelect.value;

        <?php
        $analyticsEnabled = ($_COOKIE['cookie_analytics'] ?? 'false') === 'true';
        ?>
        
        const analyticsEnabled = <?php echo json_encode($analyticsEnabled); ?>;
        
        if (selectedRoute) {
            if (analyticsEnabled) {
                window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?route_used=' + encodeURIComponent(selectedRoute);
            } else {
                // Continue with normal function (no redirect)
                console.log("Journey started without analytics.");
            }
        }
    }

    window.startJourney = startJourney;
});

 function scrollBanners(direction) {
  // Select the correct scrollable container
  const container = document.querySelector('.horizontal-scroll-wrapper');

  // Amount to scroll
  const scrollAmount = 300; // pixels

  // Scroll left or right depending on the direction
  container.scrollBy({
    left: direction * scrollAmount,
    behavior: 'smooth' // for smooth scrolling
  });
}

</script>
</body>
</html>
