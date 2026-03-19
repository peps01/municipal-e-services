<div class="topbar">

    <!-- Search -->
    <div class="topbar-left">
        <input type="text" class="topbar-search" placeholder="Search...">
    </div>

    <!-- Right side -->
    <div class="topbar-right">

        <!-- Notifications -->
        <div class="notification">
            🔔
        </div>

        <!-- User profile -->
        <div class="user-info">

            <span class="username">
                <?php echo htmlspecialchars($user['first_name']." ".$user['last_name']); ?>
            </span>

            <form action="upload_profile.php" method="POST" enctype="multipart/form-data" class="profile-upload-form">

                <label for="profile-upload">
                    <img src="<?php echo $profilePic . '?t=' . time(); ?>" id="profile-pic">
                </label>

                <input type="file" name="profile_pic" id="profile-upload" accept="image/*" onchange="this.form.submit()">

            </form>

        </div>

    </div>

</div>