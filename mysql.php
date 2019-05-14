<?php

$host = "localhost";
$username = "root";
$password = "test123";
$dbname = "invite_service";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE invites (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, token VARCHAR(12) NOT NULL, used TINYINT(1), void TINYINT(1), expiry DATETIME, created DATETIME)";

echo $conn->query($sql) === TRUE ? "Table created successfully\n" : "Error creating table: " . $conn->error . "\n";

$conn->close();
