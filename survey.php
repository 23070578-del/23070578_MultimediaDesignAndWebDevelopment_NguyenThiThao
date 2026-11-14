
<?php
session_start();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Check registration session
if (!isset($_SESSION['registration'])) {
    header('Location: register.php');
    exit;
}

$languages = ['PHP', 'JavaScript', 'Python', 'Java', 'C++', 'Other'];

$levels = ['Beginner', 'Intermediate', 'Advanced'];

$interestOptions = [
    'Web Development',
    'Mobile Apps',
    'Data Science',
    'Cybersecurity',
    'AI / Machine Learning',
    'Game Development'
];

$errors = [];
$language = '';
$level = '';
$interests = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $language = $_POST['language'] ?? '';
    $level = $_POST['level'] ?? '';
    $interests = $_POST['interests'] ?? [];

    // Validate language
    if (!in_array($language, $languages, true)) {
        $errors['language'] = 'Please select a valid programming language.';
    }

    // Validate experience level
    if (!in_array($level, $levels, true)) {
        $errors['level'] = 'Please select your experience level.';
    }

    // Validate interests
    if (!is_array($interests)) {
        $interests = [];
    }

    $interests = array_values(array_intersect(
        $interests,
        $interestOptions
    ));

    if (empty($interests)) {
        $errors['interests'] = 'Please select at least one interest.';
    }

    // Save survey data into the existing session
    if (empty($errors)) {

        $_SESSION['survey'] = [
            'language' => $language,
            'level' => $level,
            'interests' => $interests
        ];

        header('Location: summary.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Survey</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: #f2f5fb;
    color: #263247;
    padding: 35px 15px;
}

.container {
    max-width: 650px;
    margin: auto;
}

.header {
    background: linear-gradient(135deg, #173b72, #2874c8);
    color: white;
    padding: 30px;
    border-radius: 15px 15px 0 0;
}

.header h1 {
    margin-bottom: 10px;
}

.header p {
    color: #e4edfa;
    line-height: 1.5;
}

.form-card {
    background: white;
    padding: 30px;
    border-radius: 0 0 15px 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}

.step {
    color: #64748b;
    font-size: 14px;
    margin-bottom: 25px;
}

.field {
    margin-bottom: 25px;
}

.field-title {
    display: block;
    font-weight: bold;
    margin-bottom: 12px;
}

select {
    width: 100%;
    padding: 12px;
    border: 1px solid #d4dce8;
    border-radius: 8px;
    font-size: 15px;
    background: white;
}

.option {
    display: block;
    margin: 12px 0;
    font-weight: normal;
}

.option input {
    margin-right: 8px;
}

.error {
    color: #dc2626;
    font-size: 13px;
    margin-top: 8px;
}

button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 8px;
    background: #2463a9;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #173b72;
}
</style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Student Survey</h1>
        <p>Tell us about your programming interests and experience.</p>
    </div>

    <div class="form-card">

        <p class="step">Step 2 of 3 — Programming Survey</p>

        <form method="POST" action="">

            <div class="field">
                <label class="field-title" for="language">
                    Favorite Programming Language
                </label>

                <select id="language" name="language" required>
                    <option value="">-- Select a language --</option>

                    <?php foreach ($languages as $item): ?>
                        <option value="<?= e($item) ?>"
                            <?= $language === $item ? 'selected' : '' ?>>
                            <?= e($item) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if (isset($errors['language'])): ?>
                    <p class="error"><?= e($errors['language']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <span class="field-title">Experience Level</span>

                <?php foreach ($levels as $item): ?>
                    <label class="option">
                        <input type="radio" name="level"
                            value="<?= e($item) ?>"
                            <?= $level === $item ? 'checked' : '' ?>>
                        <?= e($item) ?>
                    </label>
                <?php endforeach; ?>

                <?php if (isset($errors['level'])): ?>
                    <p class="error"><?= e($errors['level']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <span class="field-title">Your Interests</span>

                <?php foreach ($interestOptions as $item): ?>
                    <label class="option">
                        <input type="checkbox" name="interests[]"
                            value="<?= e($item) ?>"
                            <?= in_array($item, $interests, true) ? 'checked' : '' ?>>
                        <?= e($item) ?>
                    </label>
                <?php endforeach; ?>

                <?php if (isset($errors['interests'])): ?>
                    <p class="error"><?= e($errors['interests']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit">Submit Survey →</button>

        </form>
    </div>
</div>
</body>
</html>
