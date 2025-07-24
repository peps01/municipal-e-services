<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="/municipal-e-services/assets/login.css">
  <script src="/municipal-e-services/assets/js/login.js"></script>
</head>
<body>
<section class="login-container">
    <div class="login-box">
      <h2> <img src="/municipal-e-services/assets/icons/hall.png" class="hall_icon"> Login to MuniServe</h2>
      <form id="loginForm" onsubmit="loginUser(event)">
        <input type="hidden" name="action" value="login">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required />

        <button type="submit">Login</button>
        <div class="extra-links">
          <a href="register.php">Create Account</a>
          <a href="#"> || Forgot Password?</a>
        </div>
      </form>
      <div id="loginMsg" class="msg"></div>
    </div>
  </section>
</body>
</html>

