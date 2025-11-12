<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vinimaya - Smart Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css" rel="stylesheet">
    <link href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/public/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- ✅ COMPLETELY REMOVE navigation from header -->
    <!-- Navigation will be included in individual pages as needed -->

    <!-- Alerts -->
    <?php include VIEW_PATH . '/partials/alerts.php'; ?>

    <main class="container-fluid py-4">