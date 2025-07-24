<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Application</title>
  <link rel="stylesheet" href="/municipal-e-services/assets/css/form.css">
  <script src="/municipal-e-services/assets/js/form.js" defer></script>
</head>
<body>
  <section class="form-container">
    <div class="form-box">
      <a class="back-link" href="../dashboard.php">&larr; Back to Dashboard</a>
      <h2>Submit New Application</h2>
      <form id="applicationForm" enctype="multipart/form-data" onsubmit="submitApplication(event)">
        <input type="hidden" name="action" value="submit_application">
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

        <label>Type:</label>
        <select name="type" required onchange="renderExtraFields(this.value)">
          <option value="">-- Select Type --</option>
          <option>Business Permit / Mayor’s Permit</option>
          <option>Barangay Clearance</option>
          <option>Building Permit</option>
          <option>Permit to Hold an Event / Parade / Rally</option>
          <option>Barangay Business Clearance</option>
          <option>Indigency Certificate</option>
          <option>Residency Certificate</option>
        </select>

        <div id="extraFields"></div>

        <p id="uploadInstruction" style="font-weight:bold;"></p>

        <label>Purpose:</label>
        <textarea name="purpose" rows="4" required></textarea>

        <label>Upload Required Documents (5 slots):</label>
        <input type="file" name="documents[]">
        <input type="file" name="documents[]">
        <input type="file" name="documents[]">
        <input type="file" name="documents[]">
        <input type="file" name="documents[]">

        <button type="submit">Submit</button>
      </form>
    </div>
  </section>

  <div id="appMsg" class="msg"></div>
</body>
</html>
