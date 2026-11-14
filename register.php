
<?php
session_start();

// Escape output
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$errors = [];
$name = '';
$email = '';
$age = '';
$gender = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $age = trim($_POST['age'] ?? '');
    $gender = $_POST['gender'] ?? '';

    // Validate full name
    if ($name === '') {
        $errors['name'] = 'Please enter your full name.';
    }

    // Validate email
    if ($email === '') {
        $errors['email'] = 'Please enter your email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    // Validate password
    if (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    // Confirm password
    if ($confirmPassword !== $password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Validate age
    if ($age === '' || !filter_var($age, FILTER_VALIDATE_INT)) {
        $errors['age'] = 'Please enter a valid age.';
    } elseif ((int)$age < 10 || (int)$age > 100) {
        $errors['age'] = 'Age must be between 10 and 100.';
    }

    // Validate gender
    if (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
        $errors['gender'] = 'Please select your gender.';
    }

    // If there are no errors, save data and redirect
    if (empty($errors)) {
        $_SESSION = [];

        $_SESSION['registration'] = [
            'name' => $name,
            'email' => $email,
            'age' => (int)$age,
            'gender' => $gender
        ];

        header('Location: survey.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration</title>

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

.field {
    margin-bottom: 20px;
}

label, .field-title {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
}

input:not([type="radio"]) {
    width: 100%;
    padding: 12px;
    border: 1px solid #d4dce8;
    border-radius: 8px;
    font-size: 15px;
}

input:focus {
    outline: none;
    border-color: #2874c8;
}

.radio-group {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.radio-group label {
    font-weight: normal;
}

.error {
    color: #dc2626;
    font-size: 13px;
    margin-top: 6px;
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

.step {
    margin-bottom: 20px;
    color: #64748b;
    font-size: 14px;
}
</style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Student Registration</h1>
        <p>Please complete the form to continue to the student survey.</p>
    </div>

    <div class="form-card">

        <p class="step">Step 1 of 3 — Personal Information</p>

        <form method="POST" action="">

            <div class="field">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name"
                    value="<?= e($name) ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <p class="error"><?= e($errors['name']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                    value="<?= e($email) ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <p class="error"><?= e($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                    minlength="6" required>
                <?php if (isset($errors['password'])): ?>
                    <p class="error"><?= e($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password"
                    name="confirm_password" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <p class="error"><?= e($errors['confirm_password']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="age">Age</label>
                <input type="number" id="age" name="age"
                    min="10" max="100" value="<?= e($age) ?>" required>
                <?php if (isset($errors['age'])): ?>
                    <p class="error"><?= e($errors['age']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <span class="field-title">Gender</span>

                <div class="radio-group">
                    <?php foreach (['Male', 'Female', 'Other'] as $option): ?>
                        <label>
                            <input type="radio" name="gender"
                                value="<?= e($option) ?>"
                                <?= $gender === $option ? 'checked' : '' ?>>
                            <?= e($option) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <?php if (isset($errors['gender'])): ?>
                    <p class="error"><?= e($errors['gender']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit">Continue to Survey →</button>

        </form>
    </div>
</div>
</body>
</html>
