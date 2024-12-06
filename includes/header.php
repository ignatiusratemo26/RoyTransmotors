<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoyTransmotors</title>
    <link rel="stylesheet" href="../css/styles.css">

</head>
<body>
    <header>
        <h1>RoyTransmotors Management System</h1>
        <p>This system allows you to manage customers, jobs, payments, depots, vehicles, and staff efficiently.</p>
        <nav>
        <?php if (basename($_SERVER['PHP_SELF']) !== 'dashboard.php'): ?>
            <a href="../views/dashboard.php">Go to Dashboard</a>
        <?php endif; ?>
        </nav>

    </header>
    <main>
