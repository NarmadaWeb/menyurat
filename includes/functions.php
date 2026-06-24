<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $base = $protocol . "://" . $host . "/";
    return $base . ltrim($path, '/');
}

function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }
    header("Location: " . $url);
    exit;
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function sanitize($data) {
    if ($data === null) return '';
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function has_role($role_name) {
    return isset($_SESSION['role_name']) && $_SESSION['role_name'] === $role_name;
}

function get_village_profile() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM profil_desa LIMIT 1");
    return $stmt->fetch();
}

function format_date($date) {
    if (!$date) return '-';
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $timestamp = strtotime($date);
    $d = date('d', $timestamp);
    $m = $months[(int)date('m', $timestamp)];
    $y = date('Y', $timestamp);
    return "$d $m $y";
}

function generate_registration_number() {
    return 'REG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
}
?>
