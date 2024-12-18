<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Comprehensive login check
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'farmer'
) {
    header("Location: Grinyard_index.php");
    exit();
}

require_once '../db/config.php';

// Use the correct user ID from session
$userId = $_SESSION['user_id'];

// Improved appointment query with LEFT JOIN for ratings
$appointmentQuery = "
    SELECT 
    a.appointment_id, 
    a.appointment_date, 
    a.status, 
    eo.extension_officer_id,
    eo.first_name AS officer_first_name, 
    eo.last_name AS officer_last_name,
    eo.specialization,
    ofr.rating AS rating,
    ofr.review_text AS review
FROM appointments a
JOIN extension_officers eo ON a.officer_id = eo.extension_officer_id
LEFT JOIN officer_ratings ofr 
    ON ofr.appointment_id = a.appointment_id
WHERE a.farmer_id = ?
ORDER BY a.appointment_date DESC
LIMIT 5;

";

try {
    // Prepare and execute the statement with improved error handling
    $stmt = $conn->prepare($appointmentQuery);
    if ($stmt === false) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);

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
    // header("Location: error_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - Grinyard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .star {
            cursor: pointer;
            transition: color 0.3s;
        }

        .star:hover {
            color: #ffd700;
        }
    </style>
</head>

<body class="bg-gray-200">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-gray-800 shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-blue-400">
                    Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!
                </h1>
                <a href="../actions/logout.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Logout
                </a>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gray-700 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4 text-blue-300">Quick Actions</h2>
                    <div class="space-y-3">
                        <a href="../view/available_officers.php" class="block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Book New Appointment
                        </a>
                        <a href="my_appointments.php" class="block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                            View All Appointments
                        </a>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4 text-blue-300">Recent Appointments</h2>
                    <?php if ($appointmentsResult->num_rows > 0): ?>
                        <div class="space-y-3">
                            <?php while ($appointment = $appointmentsResult->fetch_assoc()): ?>
                                <div class="bg-gray-700 p-3 rounded-lg">
                                    <p class="font-medium text-white">
                                        With <?php echo htmlspecialchars($appointment['officer_first_name'] . ' ' . $appointment['officer_last_name']); ?>
                                    </p>
                                    <p class="text-sm text-gray-400">
                                        Date: <?php echo date('F j, Y', strtotime($appointment['appointment_date'])); ?>
                                    </p>
                                    <p class="text-sm <?php
                                                        echo strtolower($appointment['status']) == 'pending' ? 'text-yellow-400' : (strtolower($appointment['status']) == 'confirmed' ? 'text-green-400' : 'text-red-400');
                                                        ?>">
                                        Status: <?php echo htmlspecialchars($appointment['status']); ?>
                                    </p>

                                    <?php
                                    // Check if appointment is completed and not yet rated
                                    if (strtolower($appointment['status']) == 'completed'):
                                        if ($appointment['rating'] === NULL):
                                    ?>
                                            <div class="mt-2">
                                                <button onclick="openRatingModal(
                                                <?php echo $appointment['appointment_id']; ?>, 
                                                <?php echo $appointment['extension_officer_id']; ?>, 
                                                '<?php echo htmlspecialchars($appointment['officer_first_name'] . ' ' . $appointment['officer_last_name']); ?>'
                                            )"
                                                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                                    Rate Officer
                                                </button>
                                            </div>
                                        <?php
                                        else:
                                        ?>
                                            <div class="mt-2 flex items-center">
                                                <span class="text-yellow-400 mr-2">
                                                    <?php
                                                    for ($i = 1; $i <= $appointment['rating']; $i++) {
                                                        echo '★';
                                                    }
                                                    for ($i = $appointment['rating'] + 1; $i <= 5; $i++) {
                                                        echo '☆';
                                                    }
                                                    ?>
                                                </span>
                                                <?php if (!empty($appointment['review'])): ?>
                                                    <span class="text-sm text-gray-400">(with review)</span>
                                                <?php endif; ?>
                                            </div>
                                    <?php
                                        endif;
                                    endif;
                                    ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500">No recent appointments</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Modal -->
    <div id="ratingModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="ratingForm" method="post" action="../actions/officer_rating.php">
                    <input type="hidden" name="appointment_id" id="ratingAppointmentId">
                    <input type="hidden" name="extension_officer_id" id="ratingOfficerId">
                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-blue-300" id="ratingModalTitle">
                                    Rate Extension Officer
                                </h3>
                                <div class="mt-4">
                                    <div class="flex justify-center text-3xl mb-4 text-yellow-400">
                                        <span class="star" data-rating="1">☆</span>
                                        <span class="star" data-rating="2">☆</span>
                                        <span class="star" data-rating="3">☆</span>
                                        <span class="star" data-rating="4">☆</span>
                                        <span class="star" data-rating="5">☆</span>
                                    </div>
                                    <input type="hidden" name="rating" id="selectedRating" required>
                                    <textarea name="review_text" class="w-full p-2 bg-gray-700 text-white rounded" placeholder="Optional: Write a review" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Submit Rating
                        </button>
                        <button type="button" onclick="closeRatingModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRatingModal(appointmentId, officerId, officerName) {
            document.getElementById('ratingModalTitle').textContent = `Rate Extension Officer: ${officerName}`;
            document.getElementById('ratingAppointmentId').value = appointmentId;
            document.getElementById('ratingOfficerId').value = officerId;
            document.getElementById('ratingModal').classList.remove('hidden');
            resetStars();
        }

        function closeRatingModal() {
            document.getElementById('ratingModal').classList.add('hidden');
        }

        // Star rating interaction
        const stars = document.querySelectorAll('.star');
        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                const rating = this.getAttribute('data-rating');
                highlightStars(rating);
            });
            star.addEventListener('mouseout', resetStars);
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                document.getElementById('selectedRating').value = rating;
                highlightStars(rating);
            });
        });

        function highlightStars(rating) {
            stars.forEach(star => {
                const starRating = star.getAttribute('data-rating');
                if (starRating <= rating) {
                    star.textContent = '★';
                } else {
                    star.textContent = '☆';
                }
            });
        }

        function resetStars() {
            const selectedRating = document.getElementById('selectedRating').value;
            stars.forEach(star => {
                const starRating = star.getAttribute('data-rating');
                if (selectedRating && starRating <= selectedRating) {
                    star.textContent = '★';
                } else {
                    star.textContent = '☆';
                }
            });
        }
    </script>
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