<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Staff</title>
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

    .top-bar {
    display: flex;
    justify-content: space-between;
    /* Pushes items to the left and right */
    align-items: center;
    padding: 10px;
}

.right-link {
    display: inline-block;
    margin-bottom: 1rem;
    color: #004aad;
    font-weight: 600;
    text-decoration: none;
}

a.right-link:hover {
    text-decoration: underline;
}
a.back-link {
    display: inline-block;
    margin-bottom: 1rem;
    color: #004aad;
    font-weight: 600;
    text-decoration: none;
}

a.back-link:hover {
    text-decoration: underline;
}
  </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Register New Staff Account</h2>
            <form id="staffForm">
                <label>Full Name:</label>
                <input type="text" name="full_name" required>

                <label>Email:</label>
                <input type="email" name="email" required>

                <label>Phone:</label>
                <input type="text" name="phone" required>

                <label>Password:</label>
                <input type="password" name="password" required>

                <input type="hidden" name="action" value="register_staff">

                <button type="submit">Create Staff Account</button>
            </form>
            <div class="top-bar">
                <a href="admin_dashboard.php" class="back-link">&larr; Back</a>
                <a href="staff_home.php" class="right-link">Home  &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Message container -->
    <div id="responseMsg" class="msg"></div>

    <script>
    document.getElementById('staffForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        const response = await fetch('../controllers/AdminController.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        showMessage(result.message, result.success);
        if (result.success) form.reset(); // Optionally reset form
    });

    function showMessage(message, isSuccess) {
        const msgBox = document.getElementById('responseMsg');
        msgBox.textContent = message;
        msgBox.className = 'msg ' + (isSuccess ? 'success' : 'error');
        msgBox.style.display = 'block';

        setTimeout(() => {
            msgBox.style.display = 'none';
        }, 4000);
    }
    </script>
</body>
</html>
