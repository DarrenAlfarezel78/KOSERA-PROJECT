<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = 'H4p!zh4km4l';
$DB_NAME = 'kosera_db';

function getConnection() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;

    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_error) {
        die('Koneksi database gagal: ' . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}
