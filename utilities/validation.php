<?php

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
    // Password must be at least 8 characters long and contain at least one number
    return strlen($password) >= 8 && preg_match('/[0-9]/', $password);
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function validateAppointmentStatus($status) {
    $valid_statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
    return in_array($status, $valid_statuses);
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function validateFarmType($type) {
    // Add your farm types here
    $valid_types = [
        'Crop Farming',
        'Livestock Farming',
        'Mixed Farming',
        'Poultry Farming',
        'Fish Farming',
        'Other'
    ];
    return in_array($type, $valid_types);
}

function validateSpecialization($specialization) {
    // Add your specialization types here
    $valid_specializations = [
        'Crop Production',
        'Animal Health',
        'Soil Science',
        'Plant Protection',
        'Agricultural Engineering',
        'Livestock Management',
        'Aquaculture',
        'Other'
    ];
    return in_array($specialization, $valid_specializations);
}
?>
