<div class="res-nav" id="sidebar">
    <div class="logo">
        <img src="../assets/img/municipal-logo.jpg" alt="Municipal">
        <h2>Municipal</h2>
    </div>


    <div class="menu">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="profile_content.php">My Profile</a></li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle">Services ▾</a>

                <ul class="dropdown-menu">

                    <?php

        $query = "SELECT * FROM services";
        $result = mysqli_query($conn, $query);

        while($row = mysqli_fetch_assoc($result)){
        ?>

                        <li>
                            <a href="../public/services/service.php?slug=<?php echo $row['slug']; ?>" class="ajax-link">
                                <?php echo $row['name']; ?>
                            </a>
                        </li>

                        <?php } ?>

                </ul>
            </li>
            <li><a href="notification_content.php">Notifications</a></li>

            <li><a href="request_content.php">My Request</a></li>

            <li><a href="complaints_content.php">Complaints</a></li>

        </ul>
    </div>
    <!-- Logout at bottom -->
    <div class="logout-section">
        <hr>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>

</div>