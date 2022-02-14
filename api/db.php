<?php
$conn = new mysqli('db', 'root', 'ledenbeheer', 'ledenbeheer');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}