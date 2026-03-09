<?php
function initialize_database()
{

    $conn = mysqli_connect(DATABASE_HOST_NAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
    if (!$conn) {
        die("connection failed : " . mysqli_connect_error());
    }

    return $conn;
}
