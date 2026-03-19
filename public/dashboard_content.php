

        <!-- Dashboard Stats -->
        <div class="dashboard-stats">

            <div class="card">
                <h3>Pending</h3>
                <p>
                    <?php echo $pending_count ?? 0; ?>
                </p>
            </div>

            <div class="card">
                <h3>Approved</h3>
                <p>
                    <?php echo $approved_count ?? 0; ?>
                </p>
            </div>

            <div class="card">
                <h3>Ready for Pickup</h3>
                <p>
                    <?php echo $ready_count ?? 0; ?>
                </p>
            </div>

            <div class="card">
                <h3>Completed</h3>
                <p>
                    <?php echo $completed_count ?? 0; ?>
                </p>
            </div>

        </div>

        <!-- Quick Actions -->
        <h2>Quick Actions</h2>
        <div class="quick-actions">

            <a href="services.php" class="action-btn">Request Service</a>
            <a href="my_requests.php" class="action-btn">Track Requests</a>
            <a href="profile.php" class="action-btn">Edit Profile</a>

        </div>

        <!-- Recent Requests -->
        <h2>Recent Requests</h2>
        <table class="requests-table">
            <tr>
                <th>Service</th>
                <th>Date</th>
                <th>Status</th>
            </tr>

            <?php if(!empty($recent_requests)): ?>
            <?php foreach($recent_requests as $req): ?>
            <tr>
                <td>
                    <?php echo htmlspecialchars($req['service_name']); ?>
                </td>
                <td>
                    <?php echo date("M d, Y", strtotime($req['created_at'])); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($req['status']); ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="3">No requests yet</td>
            </tr>
            <?php endif; ?>

        </table>

        <!-- Notifications -->
        <h2>Notifications</h2>
        <ul class="notifications">
            <?php if(!empty($notifications)): ?>
            <?php foreach($notifications as $note): ?>
            <li>
                <?php echo htmlspecialchars($note['message']); ?>
            </li>
            <?php endforeach; ?>
            <?php else: ?>
            <li>No notifications</li>
            <?php endif; ?>
        </ul>

        <!-- Announcements -->
        <h2>Announcements</h2>
        <div class="announcements">

            <?php if(!empty($announcements)): ?>
            <?php foreach($announcements as $ann): ?>
            <div class="announcement">
                <h4>
                    <?php echo htmlspecialchars($ann['title']); ?>
                </h4>
                <p>
                    <?php echo htmlspecialchars($ann['content']); ?>
                </p>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p>No announcements available.</p>
            <?php endif; ?>

        </div>
