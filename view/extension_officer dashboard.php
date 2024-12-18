<?php
session_start();

// Check if user is logged in and is an extension officer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'extension_officer') {
    header("Location: Grinyard_index.php");
    exit();
}

// Include database connection
require_once '../db/config.php';

// Fetch extension officer's details
$userId = $_SESSION['user_id'];
$officerQuery = "
    SELECT * FROM extension_officers 
    WHERE extension_officer_id = ?
";
$stmt = $conn->prepare($officerQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$officerResult = $stmt->get_result();
$officer = $officerResult->fetch_assoc();

// Fetch upcoming appointments
$appointmentsQuery = "
    SELECT a.appointment_id, a.appointment_date, a.status, 
           f.first_name AS farmer_first_name, 
           f.last_name AS farmer_last_name,
           f.farm_location
    FROM appointments a
    JOIN farmers f ON a.farmer_id = f.farmer_id
    WHERE a.officer_id = ? 
    ORDER BY a.appointment_date ASC
    LIMIT 5
";
$appointmentsStmt = $conn->prepare($appointmentsQuery);
$appointmentsStmt->bind_param("i", $userId);
$appointmentsStmt->execute();
$appointmentsResult = $appointmentsStmt->get_result();

// Fetch officer ratings and performance metrics
$ratingsQuery = "
    SELECT 
        COUNT(*) as total_ratings,
        AVG(rating) as average_rating,
        SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as high_ratings
    FROM officer_ratings
    WHERE extension_officer_id = ?
";
$ratingsStmt = $conn->prepare($ratingsQuery);
$ratingsStmt->bind_param("i", $userId);
$ratingsStmt->execute();
$ratingsResult = $ratingsStmt->get_result();
$performanceMetrics = $ratingsResult->fetch_assoc();

// Fetch recent rated appointments
$recentRatingsQuery = "
    SELECT 
        a.appointment_id, 
        f.first_name AS farmer_first_name, 
        f.last_name AS farmer_last_name,
        ofr.rating,
        ofr.review_text,
        a.appointment_date
    FROM officer_ratings ofr
    JOIN appointments a ON ofr.appointment_id = a.appointment_id
    JOIN farmers f ON a.farmer_id = f.farmer_id
    WHERE ofr.extension_officer_id = ?
    ORDER BY a.appointment_date DESC
    LIMIT 5
";
$recentRatingsStmt = $conn->prepare($recentRatingsQuery);
$recentRatingsStmt->bind_param("i", $userId);
$recentRatingsStmt->execute();
$recentRatingsResult = $recentRatingsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extension Officer Dashboard - Grinyard</title>
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
                    Extension Officer Dashboard
                </h1>
                <a href="../actions/logout.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Logout
                </a>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gray-700 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4 text-blue-300">My Profile</h2>
                    <div class="space-y-3">
                        <p class="text-white">
                            <strong class="text-blue-300">Name:</strong> 
                            <?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?>
                        </p>
                        <p class="text-white">
                            <strong class="text-blue-300">Email:</strong> 
                            <?php echo htmlspecialchars($officer['email']); ?>
                        </p>
                        <p class="text-white">
                            <strong class="text-blue-300">Specialization:</strong> 
                            <?php echo htmlspecialchars($officer['specialization']); ?>
                        </p>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4 text-blue-300">Performance Metrics</h2>
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-400">Average Rating</p>
                                <div class="flex items-center">
                                    <?php 
                                    $avgRating = $performanceMetrics['average_rating'] ?? 0;
                                    for ($i = 1; $i <= 5; $i++): 
                                    ?>
                                        <span class="text-2xl <?= $i <= $avgRating ? 'text-yellow-400' : 'text-gray-500' ?>">★</span>
                                    <?php endfor; ?>
                                    <span class="ml-2 text-white"><?= number_format($avgRating, 1) ?></span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Total Ratings</p>
                                <p class="text-lg font-bold text-white">
                                    <?= $performanceMetrics['total_ratings'] ?? 0 ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">High Ratings (4-5 stars)</p>
                                <p class="text-lg font-bold text-white">
                                    <?= $performanceMetrics['high_ratings'] ?? 0 ?> 
                                    <span class="text-sm text-gray-400">
                                        (<?= $performanceMetrics['total_ratings'] > 0 
                                            ? number_format(($performanceMetrics['high_ratings'] / $performanceMetrics['total_ratings']) * 100, 1) 
                                            : 0 ?>%)
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-300">Upcoming Appointments</h2>
                <?php if ($appointmentsResult->num_rows > 0): ?>
                    <div class="space-y-3">
                        <?php while ($appointment = $appointmentsResult->fetch_assoc()): ?>
                            <div class="bg-gray-700 p-3 rounded-lg">
                                <p class="font-medium text-white">
                                    <?php echo htmlspecialchars($appointment['farmer_first_name'] . ' ' . $appointment['farmer_last_name']); ?>
                                </p>
                                <p class="text-sm text-gray-400">
                                    Location: <?php echo htmlspecialchars($appointment['farm_location']); ?>
                                </p>
                                <p class="text-sm text-gray-400">
                                    Date: <?php echo date('F j, Y', strtotime($appointment['appointment_date'])); ?>
                                </p>
                                <p class="text-sm <?php 
                                    echo strtolower($appointment['status']) == 'pending' ? 'text-yellow-400' : 
                                         (strtolower($appointment['status']) == 'confirmed' ? 'text-green-400' : 'text-red-400');
                                ?>">
                                    Status: <?php echo htmlspecialchars($appointment['status']); ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No upcoming appointments</p>
                <?php endif; ?>
            </div>

            <div class="mt-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-300">Recent Ratings</h2>
                <?php if ($recentRatingsResult->num_rows > 0): ?>
                    <div class="space-y-3">
                        <?php while ($rating = $recentRatingsResult->fetch_assoc()): ?>
                            <div class="bg-gray-700 p-3 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="font-medium text-white">
                                        <?php echo htmlspecialchars($rating['farmer_first_name'] . ' ' . $rating['farmer_last_name']); ?>
                                        <span class="text-sm text-gray-400 ml-2">
                                            <?php echo date('F j, Y', strtotime($rating['appointment_date'])); ?>
                                        </span>
                                    </p>
                                    <div class="text-yellow-400">
                                        <?php 
                                        for ($i = 1; $i <= 5; $i++): 
                                            echo $i <= $rating['rating'] ? '★' : '☆';
                                        endfor; 
                                        ?>
                                    </div>
                                </div>
                                <?php if (!empty($rating['review_text'])): ?>
                                    <p class="text-sm text-gray-400 italic">
                                        "<?php echo htmlspecialchars($rating['review_text']); ?>"
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No ratings yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Optional logout confirmation
        document.querySelector('a[href="../actions/logout.php"]').addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>

<?php
// Close statements and connection
$stmt->close();
$appointmentsStmt->close();
$ratingsStmt->close();
$recentRatingsStmt->close();
$conn->close();
?>