document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form');
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');

    // Function to create and display error messages
    function showError(input, message) {
        // Remove any existing error messages
        const existingError = input.nextElementSibling;
        if (existingError && existingError.classList.contains('error-message')) {
            existingError.remove();
        }

        // Create error message element
        const errorElement = document.createElement('p');
        errorElement.classList.add('error-message');
        errorElement.style.color = 'red';
        errorElement.style.fontSize = '0.8em';
        errorElement.textContent = message;

        // Insert error message after the input
        input.after(errorElement);
        
        // Highlight the input field
        input.style.borderColor = 'red';
    }

    // Function to remove error messages
    function clearError(input) {
        const existingError = input.nextElementSibling;
        if (existingError && existingError.classList.contains('error-message')) {
            existingError.remove();
        }
        input.style.borderColor = '';
    }

    // Email validation function
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Password validation function
    function validatePassword(password) {
        // At least 8 characters
        // Contains at least one uppercase letter, one lowercase letter, 
        // one number, and one special character
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        return passwordRegex.test(password);
    }

    // Email input validation
    emailInput.addEventListener('input', function() {
        clearError(emailInput);
        
        if (emailInput.value.trim() === '') {
            showError(emailInput, 'Email is required');
            return false;
        } else if (!validateEmail(emailInput.value)) {
            showError(emailInput, 'Please enter a valid email address');
            return false;
        }
        return true;
    });

    // Password input validation
    passwordInput.addEventListener('input', function() {
        clearError(passwordInput);
        
        if (passwordInput.value.trim() === '') {
            showError(passwordInput, 'Password is required');
            return false;
        } else if (!validatePassword(passwordInput.value)) {
            showError(passwordInput, 'Password must be at least 8 characters long and include uppercase, lowercase, number, and special character');
            return false;
        }
        return true;
    });

    // Form submission validation
    loginForm.addEventListener('submit', function(event) {
        let isValid = true;

        // Email validation on submit
        if (emailInput.value.trim() === '') {
            showError(emailInput, 'Email is required');
            isValid = false;
        } else if (!validateEmail(emailInput.value)) {
            showError(emailInput, 'Please enter a valid email address');
            isValid = false;
        }

        // Password validation on submit
        if (passwordInput.value.trim() === '') {
            showError(passwordInput, 'Password is required');
            isValid = false;
        } else if (!validatePassword(passwordInput.value)) {
            showError(passwordInput, 'Password must be at least 8 characters long and include uppercase, lowercase, number, and special character');
            isValid = false;
        }

        // If validation passes, allow form submission
        if (isValid) {
            // Optional: You can add a loading state or disable submit button here
            loginForm.querySelector('input[type="submit"]').disabled = true;
            loginForm.querySelector('input[type="submit"]').value = 'Logging in...';
        } else {
            // Prevent form submission if validation fails
            event.preventDefault();
        }
    });
});