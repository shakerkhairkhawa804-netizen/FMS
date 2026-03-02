<?php
// یوازې که session نه وي فعال شوی، شروع یې کړه
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// که کارونکی لاګین نه وي، login.php ته redirect
if (!isset($_SESSION['username'])) {
    header('Location: /home/app/login.php');
    exit;
}
?>