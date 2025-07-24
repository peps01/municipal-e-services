<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="/municipal-e-services/assets/register.css">
  <style>
    html,
body {
    min-height: 100vh;
    /* Ensures at least the viewport height */
    width: 100vw;
    overflow: hidden;
    /* Prevent scrolling */
}

  </style>
  <script>
    function showMessage(msg, type) {
      const el = document.getElementById('registerMsg');
      el.innerText = msg;
      el.className = `msg ${type}`;
      el.style.display = 'block';
      setTimeout(() => { el.style.display = 'none'; }, 3000);
    }

    async function registerUser(e) {
      e.preventDefault();
      const form = document.getElementById('registerForm');
      const formData = new FormData(form);

      const res = await fetch('controllers/UserController.php', {
        method: 'POST',
        body: formData
      });

      const data = await res.json();
      showMessage(data.message, data.success ? 'success' : 'error');

      if (data.success && data.redirect) {
        setTimeout(() => {
          window.location.href = data.redirect;
        }, 3000);
      }
    }
  </script>
</head>
<body>
<section class="login-container">
  <div class="login-box">
    <h2>Create Your Account</h2>
    <form id="registerForm" onsubmit="registerUser(event)">
      <input type="hidden" name="action" value="register">

      <label for="full_name">Full Name</label>
      <input type="text" name="full_name" id="full_name" placeholder="Enter your full name" required>

      <label for="email">Email</label>
      <input type="email" name="email" id="email" placeholder="Enter your email" required>

      <label for="phone">Phone</label>
      <input type="text" name="phone" id="phone" placeholder="Enter your phone number" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" placeholder="Create a password" required>

      <button type="submit">Register</button>

      <div class="extra-links">
        <a href="login.php">Already have a account?</a>
      </div>
    </form>
    <div id="registerMsg" class="msg"></div>
  </div>
</section>
</body>
</html>
