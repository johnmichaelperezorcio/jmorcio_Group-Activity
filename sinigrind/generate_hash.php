<?php
// Choose a strong password for your admin
$plainPassword = "manager";

// Generate a bcrypt hash (the $2y$10$... format)
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

// Show the hash so you can copy it into your SQL INSERT
echo $hash;