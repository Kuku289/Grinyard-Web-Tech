<?php
?>

<?php
session_start();
// Display login errors if any
if (isset($_SESSION['login_errors'])) {
    echo '<div class="error-messages">';
    foreach ($_SESSION['login_errors'] as $error) {
        echo '<p style="color: red;">' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
    // Clear the errors after displaying
    unset($_SESSION['login_errors']);
}
?>
<!-- Rest of your existing login HTML -->








<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grinyard_login</title>

    <style>
        body {
            background-image: url('../assets/img/farmland2.jpeg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); 
            z-index: -1; 
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.171); 
            padding: 10px;
            border-radius: 5px;
            border: 1px solid rgb(255, 255, 255); 
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5); 
            width: 300px;
        }

        input[type="email"], input[type="password"], .auth-buttons button {
            width: 80%; /* Same width for inputs and buttons */
            margin-bottom: 5px; /* Reduced margin for shorter form */
            padding: 8px; /* Reduced padding */
            border: none;
            border-radius: 3px;
            font-size: 14px; /* Reduced font size */
        }

        input[type="submit"] {
            width: 85.5%; /* Same width for the submit button */
            padding: 8px; /* Reduced padding */
            margin-top: 4px;
            margin-bottom: 10px; /* Reduced margin */
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            font-size: 14px; /* Reduced font size */
        }
        
        .account-message {
            margin-top: 5px; /* Reduced space between submit and account message */
        }

        .auth-buttons {
            display: flex;
            flex-direction: column;
            width: 80%; 
        }

        .auth-buttons button {
            padding: 8px; /* Reduced padding */
            border: none;
            border-radius: 5px;
            font-size: 14px; /* Reduced font size */
            cursor: pointer;
            width: 100%;
        }

        .google-button {
            background-color: #db4437;
            color: white;
            
        }

        .microsoft-button {
            background-color: #2b579a;
            color: white;
            
        }
        
    </style>
</head>
<body>

    <form action="../actions/user_login.php" method="POST">
        <!-- LOGIN Text -->
        <i style="color: rgba(255, 255, 255, 0.7); font-size: 24px; margin-bottom: 20px;">LOGIN</i>
        
        <!-- Email and Password Fields -->
        <p style="color:white">Email</p>
        <!-- Change these lines -->
        <input type="email" name="email" placeholder="Enter your email">

        <p style="color:white">Password</p>
        <input type="password" name="password" placeholder="Enter your password">

        <p>Role</p>
            <select name="role" id="role" required>
                <option value="farmer">Farmer</option>
                <option value="extension_officer">Extension Officer</option>
                <option value="admin">Admin</option>
            </select>

        <input type="submit" value="Submit">

        

        <!-- Account message comes after the form elements -->
        <div class="account-message">
            <p style="color:blue;">Don't have an account? <a href="signup.php">SIGN UP</a></p>
        </div>
    </form>
    <script src="../assets/js/login.js"></script>

</body>
</html>
