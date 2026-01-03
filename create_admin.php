<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Delete existing admin if exists
User::where('email', 'admin@example.com')->delete();

// Create admin user with faster hashing
$admin = User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('12345678'),
]);

echo "Admin user created successfully!\n";
echo "Email: admin@example.com\n";
echo "Password: 12345678\n";
echo "Login speed optimized!\n";
