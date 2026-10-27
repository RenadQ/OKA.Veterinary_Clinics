<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login & Sign Up</title>
  <link rel="stylesheet" href="signin.css" />
</head>
<body>
  <div class="container">
    <!-- Sign Up Form -->
    <div class="form-container sign-up-container">
      <form id="signup-form">
        <h1>Create Account</h1>
        <span>or use your email for registration:</span>

        <input type="text" name="name" placeholder="Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="text" name="phone" placeholder="Phone Number" required />
        <input type="password" id="password" name="password" placeholder="Password" required oninput="checkPasswordStrength()" />
        
        <!-- Password strength -->
        <div id="password-strength">
          <span id="strength-text"></span>
          <div id="strength-bar"></div>
        </div>

        <label>
          <input type="checkbox" id="receive-ads" name="receive_ads" />
          I want to receive promotional emails and updates.
        </label>

        <button type="submit">Sign Up</button>
        <button type="button" onclick="goToHomePage()">Cancel</button>
        <div id="error-container" style="color:red; margin-top: 8px;"></div>
      </form>
    </div>

    <!-- Sign In Form -->
    <div class="form-container sign-in-container">
      <form id="login-form">
        <h1>Sign In</h1>
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <a href="#" onclick="showEmailForm()">Forgot your password?</a>
        <button type="submit">Sign In</button>
        <button type="button" onclick="goToHomePage()">Back</button>
        <div id="error-container-login" style="color:red; margin-top: 8px;"></div>
        <label>
        <input type="checkbox" name="remember"> Remember Me
    </label>
      </form>
    </div>

    <!-- Modals -->
    <div class="modal" id="forgot-password-form">
      <div class="modal-content">
        <span class="close" onclick="hideAllPopups()">&times;</span>
        <h2>Forgot Password</h2>
        <input type="email" id="reset-email" placeholder="Enter your email" required />
        <div id="email-error" class="error"></div>
        <button onclick="showCodeForm()">Next</button>
      </div>
    </div>

    <div class="modal" id="code-verification-form">
      <div class="modal-content">
        <span class="close" onclick="hideAllPopups()">&times;</span>
        <h2>Enter Code</h2>
        <p>Enter the verification code:</p>
        <input type="text" id="reset-code" placeholder="Code" required />
        <div id="reset-error" class="error"></div>
        <button onclick="verifyCode()">Verify</button>
      </div>
    </div>

    <div class="modal" id="new-password-form">
      <div class="modal-content">
        <span class="close" onclick="hideAllPopups()">&times;</span>
        <h2>Set New Password</h2>
        <input type="password" id="new-password" placeholder="New Password" required />
        <input type="password" id="confirm-password" placeholder="Confirm Password" required />
        <div id="update-error" class="error"></div>
        <button onclick="updatePassword()">Update Password</button>
      </div>
    </div>

    <!-- Overlay Panels -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Welcome Back!</h1>
          <p>Login with your personal info</p>
          <button class="ghost" id="signIn">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Hello, Be OKA Petstore Friend!</h1>
          <p>Enter your personal details and start your journey with us</p>
          <button class="ghost" id="signUp">Sign Up</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function goToHomePage() {
      window.location.href = "homepage.php";
    }
  </script>
  <script src="signin.js"></script>
</body>
</html>
<!-- ===============================
//    Authors:
//    Team member  
//    Renad Alkahtani
//    ===============================  -->