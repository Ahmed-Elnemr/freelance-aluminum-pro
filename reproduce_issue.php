<?php

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Mock a user exists
// We assume 'client@example.com' exists or we can create one if needed, but for 'exists' rule to pass it must exist.
// If it fails 'exists', the message is 'selected email is invalid'.
// The user says "email exists" (which sounds like 'unique' error).

// Let's first see what the 'exists' and 'unique' messages are in Arabic.
app()->setLocale('ar');

$messages = [
    'email.exists' => __('validation.exists', ['attribute' => 'البريد الإلكتروني']),
    'email.unique' => __('validation.unique', ['attribute' => 'البريد الإلكتروني']),
    'password.required' => __('validation.required', ['attribute' => 'كلمة المرور']),
];

echo "Expected messages:\n";
print_r($messages);

// Now let's try to validate a payload without password
$data = [
    'email' => 'client@example.com',
    'otp' => '1111',
    // 'password' is missing
];

$rules = [
    'email' => 'required|email|exists:users,email',
    'otp' => 'required|numeric',
    'password' => 'required|string|min:4|confirmed',
];

$validator = Validator::make($data, $rules);

if ($validator->fails()) {
    echo "\nValidation errors (missing password):\n";
    print_r($validator->errors()->all());
} else {
    echo "\nValidation passed (unexpectedly).\n";
}

// Now let's try with a non-existent email to see the 'exists' error
$data2 = [
    'email' => 'nonexistent@example.com',
    'otp' => '1111',
    'password' => '123456',
    'password_confirmation' => '123456'
];

$validator2 = Validator::make($data2, $rules);
if ($validator2->fails()) {
    echo "\nValidation errors (non-existent email):\n";
    print_r($validator2->errors()->all());
}
