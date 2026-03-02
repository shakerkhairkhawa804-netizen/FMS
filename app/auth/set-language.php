<?php
session_start();

$lang = $_GET['lang'] ?? 'en';

$_SESSION['custom_lang'] = $lang;
setcookie('custom_lang', $lang, time() + 86400 * 30, '/');

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

