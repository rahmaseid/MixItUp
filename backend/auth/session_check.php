<?php
header('Content-Type: application/json');
session_start(); // required!

$response = [
    "loggedIn" => isset($_SESSION["user_id"]),
    "userId" => $_SESSION["user_id"] ?? null
];

//echo json_encode($response);
?>