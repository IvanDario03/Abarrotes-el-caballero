<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    // Ruta válida tanto si se llama desde /frontend/views/* como desde /backend/controllers/*
    header("Location: ../../frontend/views/login.php");
    exit;
}
