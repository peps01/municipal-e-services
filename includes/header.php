<!-- includes/topbar.php -->
<div class="top-bar">
    <form action="upload_profile.php" method="POST" enctype="multipart/form-data" class="profile-upload-form">
        <label for="profile-upload">
            <img src="<?php echo $profilePic . '?t=' . time(); ?>" id="profile-pic-large">
        </label>
        <input 
            type="file"
            name="profile_pic"
            id="profile-upload"
            accept="image/*"
            onchange="this.form.submit()"
        >
    </form>
</div>