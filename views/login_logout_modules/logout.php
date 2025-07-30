<?php
require_once '../../includes/session.php';
require_once '../../controllers/auth.php';
Session::start();
$auth = new Auth();
$auth->logout(); // or Session::destroy(); if that's your logout logic
header('Location: login.php');
exit();