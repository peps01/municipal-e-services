<?php
class Report {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getStaffDashboardStats() {
        $query = "
            SELECT 
                COUNT(*) AS total_applications,
                SUM(CASE WHEN current_status = 'Filed' THEN 1 ELSE 0 END) AS filed,
                SUM(CASE WHEN current_status = 'Under Review' THEN 1 ELSE 0 END) AS under_review,
                SUM(CASE WHEN current_status = 'Approved' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN current_status = 'Rejected' THEN 1 ELSE 0 END) AS rejected,
                SUM(CASE WHEN current_status = 'Returned' THEN 1 ELSE 0 END) AS returned,
                SUM(CASE WHEN current_status = 'Pending Approval' THEN 1 ELSE 0 END) AS pending_approval
            FROM applications
        ";

        $result = $this->conn->query($query);

        if ($result) {
            return $result->fetch_assoc();
        } else {
            return [
                'total_applications' => 0,
                'filed' => 0,
                'under_review' => 0,
                'approved' => 0,
                'rejected' => 0,
                'returned' => 0,
                'pending_approval' => 0 // default value for pending approval
            ];
        }
    }
}
