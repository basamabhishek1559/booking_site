<?php

// Get JSON data from POST request
$data = json_decode(file_get_contents('php://input'), true);

// Connect to Oracle database
$connection = oci_connect('username', 'password', 'localhost/XE');

if (!$connection) {
    $error = oci_error();
    echo "Failed to connect to Oracle: " . $error['message'];
    exit;
}

// Prepare SQL statement
$sql = "INSERT INTO bookings (name, email, date, time) VALUES (:name, :email, TO_DATE(:date, 'YYYY-MM-DD'), TO_TIMESTAMP(:time, 'HH24:MI'))";
$statement = oci_parse($connection, $sql);

// Bind parameters
oci_bind_by_name($statement, ':name', $data['name']);
oci_bind_by_name($statement, ':email', $data['email']);
oci_bind_by_name($statement, ':date', $data['date']);
oci_bind_by_name($statement, ':time', $data['time']);

// Execute SQL statement
if (oci_execute($statement)) {
    echo "Booking saved successfully!";
} else {
    $error = oci_error($statement);
    echo "Failed to save booking: " . $error['message'];
}

// Close connection
oci_free_statement($statement);
oci_close($connection);

?>
