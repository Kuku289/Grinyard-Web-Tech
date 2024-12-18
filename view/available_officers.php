<?php
session_start();

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: LOGIN_Grinyard.php");
    exit();
}

// Include database connection
require_once '../db/config.php';

// Fetch available extension officers
$officerQuery = "
    SELECT extension_officer_id, first_name, last_name, specialization 
    FROM extension_officers
";
$officersResult = $conn->query($officerQuery);

// Check if query was successful
if ($officersResult === false) {
    // Handle query error
    error_log("Query failed: " . $conn->error);
    die("An error occurred while fetching extension officers.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Grinyard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold text-green-700 mb-6">Book Appointment with Extension Officer</h1>

            <form action="../actions/book_appointment.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2" for="officer">
                        Select Extension Officer
                    </label>
                    <select 
                        name="extension_officer_id" 
                        id="officer" 
                        required 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                        <option value="">Choose an Extension Officer</option>
                        <?php while ($officer = $officersResult->fetch_assoc()): ?>
                            <option value="<?php echo $officer['extension_officer_id']; ?>">
                                <?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?> 
                                - <?php echo htmlspecialchars($officer['specialization']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-2" for="appointment_date">
                        Preferred Appointment Date
                    </label>
                    <input 
                        type="date" 
                        name="appointment_date" 
                        id="appointment_date" 
                        required 
                        min="<?php echo date('Y-m-d'); ?>"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-2" for="description">
                        Appointment Description
                    </label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="4" 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Briefly describe the purpose of your appointment"
                    ></textarea>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition duration-300"
                    >
                        Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>