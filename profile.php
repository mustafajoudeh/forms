
<?php

require_once __DIR__ . '/Registration.php';

session_start();

if (!isset($_SESSION['registration'])) {
    header('Location: index.php');
    exit;
}

$registration = $_SESSION['registration'];

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Registration Successful</h1>

    <p><strong>Full Name:</strong> <?= e($registration->name) ?></p>
    <p><strong>Email:</strong> <?= e($registration->email) ?></p>
    <p><strong>Mobile:</strong> <?= e($registration->mobile) ?></p>
    <p><strong>Governorate:</strong> <?= e($registration->governorate) ?></p>
    <p><strong>Track:</strong> <?= e($registration->track) ?></p>

    <p><strong>Skills:</strong>
        <?= e(implode(', ', $registration->skills)) ?>
    </p>

    <p><strong>Message:</strong>
        <?= e($registration->message !== '' ? $registration->message : 'No message provided') ?>
    </p>

    <p><strong>Saved in Session:</strong> Yes</p>

    <a href="index.php">Back to Registration</a>
</div>

</body>
</html>
