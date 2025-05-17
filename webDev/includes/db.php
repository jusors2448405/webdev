<?php
/**
 * Database adapter file that includes the appropriate database connection
 * based on environment
 */

// Use MySQL for XAMPP (localhost) environment
require_once __DIR__ . '/db_mysql.php';