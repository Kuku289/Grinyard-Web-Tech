<?php

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmers Management</title>
    <link rel="stylesheet" href="../assets/css/farmer_styles.css">
</head>
<body>
    <div class="container">
        <h1>Farmers Management</h1>
        
        <div class="add-farmer-section">
            <h2>Add New Farmer</h2>
            <form id="farmerForm">
                <input type="text" id="firstName" placeholder="First Name" required>
                <input type="text" id="lastName" placeholder="Last Name" required>
                <input type="email" id="email" placeholder="Email" required>
                <input type="text" id="farmLocation" placeholder="Farm Location">
                <input type="text" id="farmType" placeholder="Farm Type">
                <button type="submit">Add Farmer</button>
            </form>
        </div>

        <div class="farmers-list">
            <h2>Farmers List</h2>
            <table id="farmersTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Farm Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="farmersTableBody">
                    <!-- Farmers will be dynamically added here -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="../assets/js/farmers.js"></script>
</body>
</html>