<?php
$con = mysqli_connect("localhost", "root", "", "appointment");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
