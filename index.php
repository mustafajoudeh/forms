
<?php
require_once __DIR__ . '/Registration.php';
session_start();



// Default values
$values = [
    'name' => '',
    'email' => '',
    'mobile' => '',
    'governorate' => '',
    'track' => '',
    'skills' => [],
    'message' => ''
];

$errors = [];
$registration = null;

function clean($value)
{
    return trim($value);
}

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Collect and clean input
    $values['name'] = clean($_POST['name'] ?? '');
    $values['email'] = clean($_POST['email'] ?? '');
    $values['mobile'] = clean($_POST['mobile'] ?? '');
    $values['governorate'] = clean($_POST['governorate'] ?? '');
    $values['track'] = clean($_POST['track'] ?? '');
    $values['message'] = clean($_POST['message'] ?? '');

    $submittedSkills = $_POST['skills'] ?? [];

    // Only accept skills sent as an array
    if (is_array($submittedSkills)) {
        $values['skills'] = array_map(
            'clean',
            $submittedSkills
        );
    }

    // 2. Validate full name
    if ($values['name'] === '') {
        $errors['name'] = 'Full name is required.';
    }

    // 3. Validate email
    if ($values['email'] === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    // 4. Validate mobile (Jordanian mobile format)
    if ($values['mobile'] === '') {
        $errors['mobile'] = 'Mobile is required.';
    } elseif (!preg_match('/^(?:\+962|00962|0)7[789]\d{7}$/', $values['mobile'])) {
        $errors['mobile'] = 'Enter a valid Jordanian mobile number.';
    }

    // 5. Validate governorate
    $allowedGovernorates = ['Amman', 'Irbid', 'Aqaba', 'Zarqa'];

    if (!in_array($values['governorate'], $allowedGovernorates, true)) {
        $errors['governorate'] = 'Please select a valid governorate.';
    }

    // 6. Validate track
    $allowedTracks = ['Full Stack', 'Frontend', 'Backend'];

    if (!in_array($values['track'], $allowedTracks, true)) {
        $errors['track'] = 'Please select a track.';
    }

    // 7. Validate skills
    $allowedSkills = ['HTML', 'CSS', 'JavaScript'];

    $values['skills'] = array_values(array_unique(array_intersect(
        $values['skills'],
        $allowedSkills
    )));

    if (count($values['skills']) === 0) {
        $errors['skills'] = 'Select at least one skill.';
    }

    // 8. Validate terms
    if (($_POST['agree'] ?? '') !== 'yes') {
        $errors['agree'] = 'You must accept the terms.';
    }

    // 9. If there are no errors, create object and save data
    if (empty($errors)) {

        $registration = new Registration($values);

        $_SESSION['registration'] = $registration;

        // Save the name in a cookie for 30 days
        setcookie('saved_name', $registration->name, [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Redirect to prevent accidental resubmission
        header('Location: profile.php');
        exit;
    }
}

// Read saved name cookie
$savedName = $_COOKIE['saved_name'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Registration Form</h1>

    <?php if ($savedName !== ''): ?>
        <p class="saved">
            Welcome back, <?= e($savedName) ?>!
        </p>
    <?php endif; ?>

    <form action="index.php" method="post">

        <label for="name">Full Name</label>
        <input type="text" id="name" name="name"
               value="<?= e($values['name']) ?>">
        <span class="error"><?= e($errors['name'] ?? '') ?></span>

        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               value="<?= e($values['email']) ?>">
        <span class="error"><?= e($errors['email'] ?? '') ?></span>

        <label for="mobile">Mobile</label>
        <input type="tel" id="mobile" name="mobile"
               placeholder="0791234567"
               value="<?= e($values['mobile']) ?>">
        <span class="error"><?= e($errors['mobile'] ?? '') ?></span>

        <label for="governorate">Governorate</label>
        <select id="governorate" name="governorate">
            <option value="">Choose a governorate</option>
            <?php foreach (['Amman', 'Irbid', 'Aqaba', 'Zarqa'] as $gov): ?>
                <option value="<?= e($gov) ?>"
                    <?= $values['governorate'] === $gov ? 'selected' : '' ?>>
                    <?= e($gov) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="error"><?= e($errors['governorate'] ?? '') ?></span>

        <label>Track</label>
        <?php foreach (['Full Stack', 'Frontend', 'Backend'] as $track): ?>
            <label class="inline">
                <input type="radio" name="track"
                       value="<?= e($track) ?>"
                       <?= $values['track'] === $track ? 'checked' : '' ?>>
                <?= e($track) ?>
            </label>
        <?php endforeach; ?>
        <span class="error"><?= e($errors['track'] ?? '') ?></span>

        <label>Skills you already have</label>
        <?php foreach (['HTML', 'CSS', 'JavaScript'] as $skill): ?>
            <label class="inline">
                <input type="checkbox" name="skills[]"
                       value="<?= e($skill) ?>"
                       <?= in_array($skill, $values['skills'], true) ? 'checked' : '' ?>>
                <?= e($skill) ?>
            </label>
        <?php endforeach; ?>
        <span class="error"><?= e($errors['skills'] ?? '') ?></span>

        <label for="message">Why do you want to join? (optional)</label>
        <textarea id="message" name="message" rows="4"><?= e($values['message']) ?></textarea>

        <label class="inline terms">
            <input type="checkbox" name="agree" value="yes"
                   <?= isset($_POST['agree']) && $_POST['agree'] === 'yes' ? 'checked' : '' ?>>
            I agree to the academy terms
        </label>
        <span class="error"><?= e($errors['agree'] ?? '') ?></span>

        <button type="submit">Register</button>
    </form>
</div>

</body>
</html>
