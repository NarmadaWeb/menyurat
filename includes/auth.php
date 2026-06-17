<?php
require_once __DIR__ . '/functions.php';

function require_login() {
    if (!is_logged_in()) {
        redirect(base_url('login.php'), 'Silakan login terlebih dahulu.', 'error');
    }
}

function require_role($role_name) {
    require_login();
    if (!has_role($role_name)) {
        http_response_code(403);
        die("403 Forbidden: Anda tidak memiliki akses ke halaman ini.");
    }
}

function authenticate($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = ? AND u.status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role_name'] = $user['role_name'];
        return true;
    }
    return false;
}
?>
