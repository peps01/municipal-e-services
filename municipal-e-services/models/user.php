<?php
class User {
    private $db;

    public function __construct($mysqli) {
        $this->db = $mysqli;
    }

    public function emailOrPhoneExists($email, $phone) {
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ? OR phone = ? LIMIT 1");
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function register($full_name, $email, $phone, $password, $role_id = 3) {
        if ($this->emailOrPhoneExists($email, $phone)) {
            return 'exists';
        }

        $stmt = $this->db->prepare("INSERT INTO users (full_name, email, phone, password, role_id) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) return false;

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("ssssi", $full_name, $email, $phone, $hashed_password, $role_id);
        return $stmt->execute();
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT user_id, full_name, password, role_id FROM users WHERE email = ? LIMIT 1");
        if (!$stmt) return false;

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }
}
?>