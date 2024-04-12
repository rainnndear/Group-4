<?php
session_start();

// Function to generate a random code
function generateRandomCode($length = 6) {
    $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $code = substr(str_shuffle($characters), 0, $length);
    return $code;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get user input from the form
    $email = $_POST["email"];
    $password = $_POST["password"];
    $enteredCode = $_POST["verification_code"];

    // Verify the entered code
    if ($enteredCode === $_SESSION['captcha_code']) {
        // Successful verification

        // Include your database connection
        include('db/connect.php');

        // Use prepared statement to fetch user data by email
        $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // email exists and password is correct
            if (isset($user['status']) && $user['status'] == 2) {
                // Login successful, set session variables
                $_SESSION["UID"] = $user["UID"];
                $_SESSION["email"] = $user["email"];
                // Redirect to a dashboard or home page after successful login
                header("Location: home.php");
                exit();
            } elseif (isset($user['status']) && $user['status'] == 1) {
                // User status is 1, redirect to registration page
                $_SESSION["UID"] = $user["UID"];
                header("Location: register.php");
                exit();
            } else {
                // Unknown status
                echo "<script>alert('Unknown user status!'); history.back();</script>";
            }
        } else {
            // Incorrect email or password
            echo "<script>alert('Incorrect email or password!'); history.back();</script>";
        }
    } else {
        // Failed verification
        echo "<script>alert('Incorrect verification code. Please try again.');</script>";

        // Regenerate a new verification code
        $_SESSION['captcha_code'] = generateRandomCode();

        // Store the entered email in session to keep the input
        $_SESSION['entered_email'] = $email;
    }
} else {
    // Initial page load or when the form is not submitted, generate a new code
    $_SESSION['captcha_code'] = generateRandomCode();

    // Retrieve the stored entered email from session
    $entered_email = isset($_SESSION['entered_email']) ? $_SESSION['entered_email'] : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Information</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
 
</head>
<body>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="container mt-4">
      <div class="row">
        <!-- First Column -->
        <div class="col-md-6">
          <center>
            <h2 class="title">CCSICT FACULTY</h2>
            <h3 class="title2">Seminar / Training / Conference Monitory System</h3>
            <img src="assets/logosystem.png" class="logo" alt="System Image">
          </center>
        </div>

        <!-- Second Column -->
        <div class="col-md-6">
          <div class="card info-card2">
            <div class="card-body2">
              <center>
                <h5 class="card-title">LOG IN</h5>
                <h6>Don't have an Account? <a href="register.php" class="link-primary">Create Here!</a></h6>
              </center>
              <div class="col-12 mb-3">
              <div class="input-group-prepend">
                    <span class="input-group-text">Email:</span>
                  </div>
                <input type="text" class="form-control custom-border" id="email" name="email" placeholder="Enter your email" required>
              </div>
              <div class="col-md-12">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Password:</span>
                  </div>
                  <input type="password" class="form-control custom-border" id="password" name="password" placeholder="Enter your password" aria-label="Password" aria-describedby="password-addon-1" required>
                  <span class="input-group-text password-toggle" id="password-addon-1">
                    <i class="fas fa-eye"></i>
                  </span>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-8 mb-3">
                <center><label for="verification_code">Verify you're not a Robot:</label></center>
                <center><img src="captcha.php" alt="CAPTCHA"></center>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">VERIFICATION CODE</span>
                    </div>
                    <input type="text" class="form-control custom-border" id="verification" name="verification_code" placeholder="Enter the verification code" required>
                    
                  </div>
                </div>
              </div>
              <center>
                <button type="submit" class="btn py-1 px-4" id="login-btn" style="background-color: #ffa908; border: 2px solid #000; font-family: Arial, sans-serif;"> Login
                  <i class="fas fa-angle-right mr-1"></i>
                  <i class="fas fa-angle-right mr-1"></i>
                </button>
              </center>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="iconscript.js"></script>
  <script src="button.js"></script>
</body>

</html>