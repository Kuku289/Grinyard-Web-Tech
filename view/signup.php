<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grinyard Registration</title>
    <style>
        body {
            background-image: url('../assets/img/farmland2.jpeg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative; 
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

        .container {
            text-align: left;
            z-index: 1; 
        }

        p, h1 {
            color: white;
        }

        form {
            display: flex;
            flex-direction: column;
            background-color: rgba(255, 255, 255, 0.1); 
            padding: 20px;
            border-radius: 5px;
            border: 1px solid rgba(255, 255, 255, 0.279); 
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5); 
            width: 300px; 
        }

        input, select {
            margin-bottom: 10px; 
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            margin-top: 10px;
        }

        .error {
            color: red;
            font-size: 12px;
            margin-top: -8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="color: white;">GRINYARD REGISTRATION</h2>
        
        <form id="registrationForm" method="POST" action="../actions/register__process.php">
            <p>First Name</p>
            <input type="text" id="firstName" name="first_name" placeholder="Enter first name" required>
            <span id="firstNameError" class="error"></span>

            <p>Last Name</p>
            <input type="text" id="lastName" name="last_name" placeholder="Enter last name" required>
            <span id="lastNameError" class="error"></span>

            <p>Email</p>
            <input type="email" id="email" name="email" placeholder="Enter email" required>
            <span id="emailError" class="error"></span>

            <p>Password</p>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
            <span id="passwordError" class="error"></span>

            <p>Confirm Password</p>
            <input type="password" id="confirmPassword" placeholder="Confirm password" required>
            <span id="confirmPasswordError" class="error"></span>

            <p>Role</p>
            <select name="role" id="role" required>
                <option value="farmer">Farmer</option>
                <option value="extension_officer">Extension Officer</option>
                <option value="admin">Admin</option>
            </select>

            <!-- Additional fields for specific roles -->
            <div id="farmerFields" style="display:none;">
                <p>Farm Location</p>
                <input type="text" name="farm_location" placeholder="Enter farm location">
                
                <p>Farm Type</p>
                <input type="text" name="farm_type" placeholder="Enter farm type">
            </div>

            <div id="extensionOfficerFields" style="display:none;">
                <p>Specialization</p>
                <input type="text" name="specialization" placeholder="Enter specialization">
            </div>

            <input type="submit" value="Register">

            <p style="color:white; text-align:center;">
                Already have an account? <a href="LOGIN_Grinyard.php" style="color:lightblue;">LOGIN</a>
            </p>
        </form>
    </div>

    <script>
        // Role-specific fields display
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            document.getElementById('farmerFields').style.display = 
                role === 'farmer' ? 'block' : 'none';
            document.getElementById('extensionOfficerFields').style.display = 
                role === 'extension_officer' ? 'block' : 'none';
        });

        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            let firstName = document.getElementById('firstName').value;
            let lastName = document.getElementById('lastName').value;
            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;
            let confirmPassword = document.getElementById('confirmPassword').value;

            let firstNameError = document.getElementById('firstNameError');
            let lastNameError = document.getElementById('lastNameError');
            let emailError = document.getElementById('emailError');
            let passwordError = document.getElementById('passwordError');
            let confirmPasswordError = document.getElementById('confirmPasswordError');

            let isValid = true;

            // Clear previous error messages
            firstNameError.textContent = '';
            lastNameError.textContent = '';
            emailError.textContent = '';
            passwordError.textContent = '';
            confirmPasswordError.textContent = '';

            // First name validation
            if (!firstName.trim()) {
                firstNameError.textContent = 'First name is required.';
                isValid = false;
            }

            // Last name validation
            if (!lastName.trim()) {
                lastNameError.textContent = 'Last name is required.';
                isValid = false;
            }

            // Email validation
            if (!email.trim()) {
                emailError.textContent = 'Email is required.';
                isValid = false;
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                emailError.textContent = 'Please enter a valid email address.';
                isValid = false;
            }

            // Password validation
            if (!password) {
                passwordError.textContent = 'Password is required.';
                isValid = false;
            } else {
                if (password.length < 8) {
                    passwordError.textContent = 'Password must be at least 8 characters long.';
                    isValid = false;
                }
                if (!/[A-Z]/.test(password)) {
                    passwordError.textContent = 'Password must contain at least one uppercase letter.';
                    isValid = false;
                }
                if (!/\d{3}/.test(password)) {
                    passwordError.textContent = 'Password must include at least three digits.';
                    isValid = false;
                }
                if (!/[!@#$%^&*]/.test(password)) {
                    passwordError.textContent = 'Password must contain at least one special character.';
                    isValid = false;
                }
            }

            // Confirm password validation
            if (!confirmPassword) {
                confirmPasswordError.textContent = 'Please confirm your password.';
                isValid = false;
            } else if (password !== confirmPassword) {
                confirmPasswordError.textContent = 'Passwords do not match.';
                isValid = false;
            }

            // Prevent form submission if any validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>


