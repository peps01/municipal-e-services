<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Handle staff filter (via GET)
$filter_staff = isset($_GET['filter_staff']) ? $mysqli->real_escape_string($_GET['filter_staff']) : '';

// Fetch staff users
$staffResult = $mysqli->query("
    SELECT user_id, full_name, email, phone, created_at
    FROM users
    WHERE role_id = 2
    ORDER BY created_at DESC
");

// Fetch change logs (only for staff)
$logsQuery = "
    SELECT 
        s.status,
        s.changed_at,
        a.type AS application_type,
        u_app.full_name AS application_owner,
        u_staff.full_name AS staff_name
    FROM statuses s
    JOIN applications a ON s.app_id = a.app_id
    JOIN users u_app ON a.user_id = u_app.user_id
    JOIN users u_staff ON s.changed_by = u_staff.user_id
    WHERE u_staff.role_id = 2
";

if ($filter_staff !== '') {
    $logsQuery .= " AND s.changed_by = '$filter_staff'";
}

$logsQuery .= " ORDER BY s.changed_at DESC";
$logsResult = $mysqli->query($logsQuery);

// Check if an edit is requested
$editUser = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $editResult = $mysqli->query("SELECT * FROM users WHERE user_id = $edit_id AND role_id = 2");
    if ($editResult && $editResult->num_rows > 0) {
        $editUser = $editResult->fetch_assoc();
    }
}

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $userId = (int)$_POST['user_id'];
    $fullName = $mysqli->real_escape_string($_POST['full_name']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $phone = $mysqli->real_escape_string($_POST['phone']);

    $mysqli->query("
        UPDATE users 
        SET full_name = '$fullName', email = '$email', phone = '$phone'
        WHERE user_id = $userId AND role_id = 2
    ");
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff List & Change Logs</title>
  <link rel="stylesheet" href="/municipal-e-services/assets/css/staff_list.css">
  <style>
    @media (max-width: 768px) {
      .tracker-container {
        width: 100%;
        max-width: 100%;
        padding: 1rem;
        height: auto;
      }
  body {
    align-items: flex-start; /* Allow full vertical scroll */
    padding: 0.1rem;
  }

  .container,
  .tracker-container {
    width: 90vw;
    height: auto;
    padding: 1rem;
    margin: 0 auto;
    overflow: visible;
  }

  table {
    display: block;
    overflow-x: auto;
    width: 100%;
  }
      table, thead, tbody, th, td, tr {
        display: block;
      }

      table thead {
        display: none;
      }

      table tr {
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 1rem;
        background: #fff;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      }

      table td {
        padding: 0.5rem 0;
        text-align: left;
        position: relative;
        padding-left: 50%;
      }

      table td::before {
        position: absolute;
        top: 0.5rem;
        left: 1rem;
        width: 45%;
        white-space: nowrap;
        font-weight: bold;
        color: #004aad;
      }

      table td:nth-child(1)::before { content: "Name"; }
      table td:nth-child(2)::before { content: "Email"; }
      table td:nth-child(3)::before { content: "Phone"; }
      table td:nth-child(4)::before { content: "Created"; }
      table td:nth-child(5)::before { content: "Action"; }

      select {
        width: 100%;
      }

      .application-card {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="tracker-container">
    <div class="top-bar">
      <a href="admin_dashboard.php" class="back-link">&larr; Back to Admin Dashboard</a>
      <a href="register_staff.php" class="right-link">Register Staff  &rarr;</a>
    </div>

    <h1>Staff Users</h1>
    <div id="staffTableContainer"></div>

    <h2>Change Logs</h2>
    <select id="filterStaff">
      <option value="">-- All Staff --</option>
      <?php
      $staffList = $mysqli->query("SELECT user_id, full_name FROM users WHERE role_id = 2 ORDER BY full_name ASC");
      while ($staff = $staffList->fetch_assoc()):
      ?>
        <option value="<?php echo $staff['user_id']; ?>" <?php echo ($filter_staff == $staff['user_id']) ? 'selected' : ''; ?>>
          <?php echo htmlspecialchars($staff['full_name']); ?>
        </option>
      <?php endwhile; ?>
    </select>
    <div id="logsContainer"></div>
  </div>

  <div class="modal" id="editModal" style="display: none;">
    <div class="modal-content">
      <h3>Edit Staff Details</h3>
      <form id="editForm">
        <input type="hidden" name="user_id">
        <label>Full Name:</label>
        <input type="text" name="full_name" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Phone:</label>
        <input type="text" name="phone" required>
        <button type="submit" id="saveChangesBtn">Save Changes</button>
        <button type="button" class="close-btn" id="closeModalBtn">Cancel</button>
      </form>
    </div>
  </div>

  <div id="notificationContainer"></div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const staffTable = document.getElementById('staffTableContainer');
      const logsDiv = document.getElementById('logsContainer');
      const filter = document.getElementById('filterStaff');
      const editModal = document.getElementById('editModal');
      const editForm = document.getElementById('editForm');
      const notificationContainer = document.getElementById('notificationContainer');

      const fetchStaff = () => fetch('staff_list_ajax.php?type=staff')
        .then(res => res.json())
        .then(data => {
          staffTable.innerHTML = `<table>
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Created</th><th>Action</th></tr></thead><tbody>
            ${data.map(u => `<tr>
              <td>${u.full_name}</td><td>${u.email}</td><td>${u.phone}</td><td>${u.created_at}</td>
              <td><button data-user='${JSON.stringify(u)}' class='editBtn'>Edit</button></td>
            </tr>`).join('')}
            </tbody></table>`;
          document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', () => openModal(JSON.parse(btn.dataset.user)));
          });
        });

      const fetchLogs = (staffId = '') => fetch(`staff_list_ajax.php?type=logs&staff_id=${staffId}`)
        .then(res => res.json())
        .then(data => {
          logsDiv.innerHTML = data.map(l => `
            <div class="application-card">
              <p><strong>${l.staff_name}</strong> changed <strong>${l.application_type}</strong> for <strong>${l.application_owner}</strong> to <strong>${l.status}</strong> at ${l.changed_at}</p>
            </div>
          `).join('') || '<p>No logs found.</p>';
        });

      filter.addEventListener('change', () => fetchLogs(filter.value));

      editForm.addEventListener('submit', e => {
        e.preventDefault();
        const fd = new FormData(editForm);
        fetch('staff_list_ajax.php', { method: 'POST', body: fd })
          .then(res => res.json())
          .then(res => {
            showNotification(res.success ? 'Saved!' : 'Error saving.', res.success);
            if (res.success) {
              closeModal();
              fetchStaff();
            }
          });
      });

      document.getElementById('closeModalBtn').addEventListener('click', closeModal);

      function openModal(user) {
        editForm.user_id.value = user.user_id;
        editForm.full_name.value = user.full_name;
        editForm.email.value = user.email;
        editForm.phone.value = user.phone;
        editModal.style.display = 'flex';
      }

      function closeModal() {
        editModal.style.display = 'none';
      }

      function showNotification(msg, ok) {
        const el = document.createElement('div');
        el.className = `notification-message ${ok ? 'notification-success' : 'notification-error'}`;
        el.innerText = msg;
        notificationContainer.appendChild(el);
        setTimeout(() => el.remove(), 3000);
      }

      fetchStaff();
      fetchLogs();
    });
  </script>
</body>
</html>
