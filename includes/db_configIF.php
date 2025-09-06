<?php

$host = "sql312.infinityfree.com";
$db   = "if0_38722581_sf_suite_db";
$user = "if0_38722581";
$pass = "sfsuitecapstone";
$charset = "utf8";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enable error handling
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch as associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements (better security)
];

try {
    $pdo = new PDO($dsn, $user, $pass, $opt);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
