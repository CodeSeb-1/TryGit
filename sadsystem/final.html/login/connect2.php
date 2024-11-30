<?php
$con = mysqli_connect("localhost", "root", "", "appointmentlog");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}