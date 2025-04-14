<?php

return [
    // Auth Messages
    'auth' => [
        'login' => 'Login',
        'register' => 'Register',
        'logout' => 'Logout',
        'profile' => 'Profile',
    ],

    // Validation Messages
    'validation' => [
        'mobile' => [
            'required' => 'Please enter your mobile number',
            'format' => 'Mobile number must start with 05 followed by 8 digits',
            'max' => 'Mobile number is too long',
            'unique' => 'This mobile number is already registered'
        ],
        'name' => [
            'required' => 'Please enter your name',
            'string' => 'Name must be text',
            'max' => 'Name cannot be longer than 25 characters',
            'min' => 'Name must be at least 5 characters',
            'format' => 'Name can only contain Arabic letters, English letters, and spaces'
        ],

    ],

    // Response Messages
    'responses' => [
        'success' => 'Success',
        'error' => 'Error',
        'warning' => 'Warning',
        'info' => 'Info',
        'verification_sent' => 'Verification code sent to your mobile',
        'invalid_code' => 'Invalid verification code',
        'enter_code' => 'Please enter verification code'
    ],

    // Common Actions
    'actions' => [
        'save' => 'Save Changes',
        'cancel' => 'Cancel',
        'back' => 'Back',
        'next' => 'Next',
        'submit' => 'Submit'
    ],


];
