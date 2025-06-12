<?php
class IpValidatorModel {
    public function validate($ip) {
        if (empty($ip)) {
            return ['valid' => false, 'message' => 'IP-адреса не може бути порожньою'];
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return ['valid' => true, 'message' => '✅ Валідний IPv4'];
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return ['valid' => true, 'message' => '✅ Валідний IPv6'];
        }

        return ['valid' => false, 'message' => '❌ Невалідний IP'];
    }

    public function checkAdminAccess() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin';
    }
}