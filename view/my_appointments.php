<?php
session_start();

// Comprehensive login check
if (!isset($_SESSION['user_id']) || 
    !isset($_SESSION['role']) || 
    $_SESSION['role'] !== 'farmer') {
    header("Location: Grinyard_index.php");
    exit();
}

require_once '../db/config.php';

// Use the correct user ID from session
$userId = $_SESSION['user_id'];

// Comprehensive appointments query with more details
$appointmentQuery = "
    SELECT 
        a.appointment_id, 
        a.appointment_date, 
        a.status, 
        a.description,
        eo.extension_officer_id,
        eo.first_name AS officer_first_name, 
        eo.last_name AS officer_last_name,
        eo.specialization,
        ofr.rating AS rating,
        ofr.review_text AS review
    FROM appointments a
    JOIN extension_officers eo ON a.extension_officer_id = eo.extension_officer_id
    LEFT JOIN officer_ratings ofr ON ofr.appointment_id = a.appointment_id 
                                  AND ofr.farmer_id = ?
    WHERE a.farmer_id = ? 
    ORDER BY a.appointment_date DESC
";

try {
    // Prepare and execute the statement with improved error handling
    $stmt = $conn->prepare($appointmentQuery);
    if ($stmt === false) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("ii", $userId, $userId);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute statement: " . $stmt->error);
    }
    
    $appointmentsResult = $stmt->get_result();
    
    // Additional error checking
    if ($appointmentsResult === false) {
        throw new Exception("Failed to get result: " . $stmt->error);
    }
} catch (Exception $e) {
    // Improved error logging
    error_log("Database Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while fetching appointments: " . $e->getMessage();
    header("Location: error_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Grinyard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .star {
            color: #ffd700;
        }
    </style>
</head>
<body class="bg-gray-200">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-gray-800 shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-blue-400">
                    My Appointments
                </h1>
                <div class="space-x-3">
                    <a href="../actions/book_appointment.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Book New Appointment
                    </a>
                    <a href="farmer_dashboard.php" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-600 text-white p-4 rounded mb-4">
                    <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($appointmentsResult->num_rows > 0): ?>
                <div class="grid gap-4">
                    <?php while ($appointment = $appointmentsResult->fetch_assoc()): ?>
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <h2 class="text-xl font-semibold text-blue-300">
                                    <?php echo htmlspecialchars($appointment['officer_first_name'] . ' ' . $appointment['officer_last_name']); ?>
                                </h2>
                                <span class="<?php 
                                    echo strtolower($appointment['status']) == 'pending' ? 'text-yellow-400' : 
                                         (strtolower($appointment['status']) == 'confirmed' ? 'text-green-400' : 
                                         (strtolower($appointment['status']) == 'completed' ? 'text-blue-400' : 'text-red-400'));
                                ?> font-medium">
                                    <?php echo htmlspecialchars($appointment['status']); ?>
                                </span>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-400"><strong>Date:</strong> 
                                        <?php echo date('F j, Y', strtotime($appointment['appointment_date'])); ?>
                                    </p>
                                    <p class="text-gray-400"><strong>Specialization:</strong> 
                                        <?php echo htmlspecialchars($appointment['specialization']); ?>
                                    </p>
                                    <p class="text-gray-400 mt-2">
                                        <strong>Description:</strong> 
                                        <?php echo htmlspecialchars($appointment['description'] ?: 'No description provided'); ?>
                                    </p>
                                </div>
                                
                                <div>
                                    <?php 
                                    // Check if appointment is completed and not yet rated
                                    if (strtolower($appointment['status']) == 'completed'): 
                                        if ($appointment['rating'] === NULL): 
                                    ?>
                                        <button onclick="openRatingModal(
                                            <?php echo $appointment['appointment_id']; ?>, 
                                            <?php echo $appointment['extension_officer_id']; ?>, 
                                            '<?php echo htmlspecialchars($appointment['officer_first_name'] . ' ' . $appointment['officer_last_name']); ?>'
                                        )" 
                                        class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                            Rate Officer
                                        </button>
                                    <?php 
                                        else: 
                                    ?>
                                        <div class="bg-gray-600 p-3 rounded">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-yellow-400 text-xl">
                                                    <?php 
                                                    for ($i = 1; $i <= $appointment['rating']; $i++) {
                                                        echo '★';
                                                    }
                                                    for ($i = $appointment['rating'] + 1; $i <= 5; $i++) {
                                                        echo '☆';
                                                    }
                                                    ?>
                                                </span>
                                                <span class="text-sm text-gray-400">
                                                    (Your Rating)
                                                </span>
                                            </div>
                                            <?php if (!empty($appointment['review'])): ?>
                                                <p class="text-white italic">
                                                    "<?php echo htmlspecialchars($appointment['review']); ?>"
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    <?php 
                                        endif; 
                                    endif; 
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="bg-gray-700 p-6 rounded-lg text-center">
                    <p class="text-gray-400 text-xl">
                        No appointments found. 
                        <a href="book_appointment.php" class="text-blue-400 hover:underline">
                            Book your first appointment
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Rating Modal (Same as in the dashboard) -->
    <!-- [Insert the rating modal HTML and JavaScript from the previous dashboard code] -->
    <!-- For brevity, I'm not repeating the entire modal code here -->

</body>
</html>

<?php
// Close statements and connection
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?>