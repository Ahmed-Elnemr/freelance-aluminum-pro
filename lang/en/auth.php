<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Custom validation messages
    'mobile_required' => 'Please enter your mobile number',
    'mobile_format' => 'Mobile number must start with 05 followed by 8 digits',
    'mobile_max' => 'Mobile number is too long',
    'mobile_unique' => 'This mobile number is already registered',

    'name_required' => 'Please enter your name',
    'name_string' => 'Name must be text',
    'name_max' => 'Name cannot be longer than 25 characters',
    'name_min' => 'Name must be at least 5 characters',
    'name_format' => 'Name can only contain Arabic letters, English letters, and spaces',




];
