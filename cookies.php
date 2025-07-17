<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if cookies were accepted
$cookiesAccepted = false;
if (isset($_COOKIE['cookiesAccepted']) && $_COOKIE['cookiesAccepted'] === 'yes') {
    $cookiesAccepted = true;
}

// Handle cookie acceptance via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cookie_action'])) {
    if ($_POST['cookie_action'] === 'accept') {
        setcookie('cookiesAccepted', 'yes', [
            'expires' => time() + 365 * 24 * 60 * 60,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        $cookiesAccepted = true;
    }
    // Redirect to prevent resubmission
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .cookie-popup-overlay {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff;
            padding: 20px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: none;
            z-index: 9999;
        }
        .cookie-popup-box {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .cookie-popup-box p {
            margin: 0;
            flex: 1;
            min-width: 300px;
            font-size: 14px;
            color: #333;
        }
        .cookie-popup-box a {
            color: #0066cc;
            text-decoration: underline;
        }
        .cookie-buttons {
            display: flex;
            gap: 10px;
        }
        .cookie-popup-box button {
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        #accept-cookies {
            background-color: #007bff;
            color: white;
            border: none;
        }
        #reject-cookies {
            background-color: transparent;
            color: #007bff;
            border: 1px solid #007bff;
        }
        #accept-cookies:hover {
            background-color: #0056b3;
        }
        #reject-cookies:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

<?php if (!$cookiesAccepted): ?>
<div id="cookie-popup" class="cookie-popup-overlay">
    <div class="cookie-popup-box">
        <p>We use cookies to ensure you get the best experience on our website. <a href="pages/privacy.html" target="_blank">Learn more</a></p>
        <div class="cookie-buttons">
            <button id="reject-cookies">Reject</button>
            <button id="accept-cookies">Accept All</button>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = name + "=" + value + ";expires=" + d.toUTCString() + ";path=/;SameSite=Lax" + 
                     (location.protocol === 'https:' ? ";Secure" : "");
}

document.addEventListener("DOMContentLoaded", function() {
    const cookiePopup = document.getElementById("cookie-popup");
    
    if (cookiePopup) {
        document.getElementById("accept-cookies").addEventListener("click", function() {
            setCookie("cookiesAccepted", "yes", 365);
            cookiePopup.style.display = "none";
            
            // Optional: Submit via form for PHP to handle
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = '<input type="hidden" name="cookie_action" value="accept">';
            document.body.appendChild(form);
            form.submit();
        });
        
        document.getElementById("reject-cookies").addEventListener("click", function() {
            // For rejection, we still set a cookie to remember the choice
            setCookie("cookiesAccepted", "no", 365);
            cookiePopup.style.display = "none";
        });
    }
});
</script>

</body>
</html>
