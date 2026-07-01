<?php
$rootPath = dirname(__DIR__, 3);

include $rootPath . '/koneksi.php';
/** @var mysqli  */
require_once $rootPath . '/app/config/session.php';

$_SESSION = array();

session_destroy();

header("Location: /login.php");
exit();