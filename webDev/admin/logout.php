<?php
session_start();
require_once '../includes/auth.php';

// Log the user out
logout();

// Redirect to login page
header('Location: login.php');
exit;
