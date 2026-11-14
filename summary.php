
<?php
session_start();

// Start Over: destroy the session and return to registration
if (isset($_GET['restart'])) {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();

    header('Location: register.php');
    exit;
}

// Check that both registration and survey data exist
if (
    !isset($_SESSION['registration']) ||
    !isset($_SESSION['survey'])
) {
    header('Location: register.php');
    exit;
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$registration = $_SESSION['registration'];
$survey = $_SESSION['survey'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration Summary</title>

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
    padding: 40px 15px;
}

.container {
    max-width: 750px;
    margin: auto;
}

.header {
    background: linear-gradient(135deg, #173b72, #2874c8);
    color: white;
    padding: 35px;
    border-radius: 15px 15px 0 0;
    text-align: center;
}

.header h1 {
    margin-bottom: 12px;
}

.header p {
    color: #e4edfa;
    line-height: 1.6;
}

.content {
    background: white;
    padding: 30px;
    border-radius: 0 0 15px 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}

.section {
    margin-bottom: 28px;
}

.section h2 {
    color: #173b72;
    font-size: 20px;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eaf1fa;
}

.info-row {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #edf0f5;
}

.info-row span:first-child {
    color: #64748b;
    font-weight: bold;
}

.info-row span:last-child {
    text-align: right;
    overflow-wrap: anywhere;
}

.interest-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.interest {
    background: #eaf1fa;
    color: #1d4e89;
    padding: 9px 13px;
    border-radius: 20px;
    font-size: 14px;
}

.actions {
    margin-top: 25px;
    text-align: center;
}

button {
    padding: 13px 25px;
    border: none;
    border-radius: 8px;
    background: #2463a9;
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #173b72;
}

footer {
    text-align: center;
    color: #718096;
    font-size: 13px;
    margin-top: 25px;
}
</style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Thank You, <?= e($registration['name']) ?>!</h1>
        <p>Your registration and survey have been completed successfully.</p>
    </div>

    <div class="content">

        <div class="section">
            <h2>Personal Information</h2>

            <div class="info-row">
                <span>Full Name</span>
                <span><?= e($registration['name']) ?></span>
            </div>

            <div class="info-row">
                <span>Email</span>
                <span><?= e($registration['email']) ?></span>
            </div>

            <div class="info-row">
                <span>Age</span>
                <span><?= e($registration['age']) ?></span>
            </div>

            <div class="info-row">
                <span>Gender</span>
                <span><?= e($registration['gender']) ?></span>
            </div>
        </div>

        <div class="section">
            <h2>Survey Results</h2>

            <div class="info-row">
                <span>Favorite Language</span>
                <span><?= e($survey['language']) ?></span>
            </div>

            <div class="info-row">
                <span>Experience Level</span>
                <span><?= e($survey['level']) ?></span>
            </div>
        </div>

        <div class="section">
            <h2>Your Interests</h2>

            <div class="interest-list">
                <?php foreach ($survey['interests'] as $interest): ?>
                    <span class="interest"><?= e($interest) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="actions">
            <a href="summary.php?restart=1">
                <button type="button">Start Over</button>
            </a>
        </div>

    </div>

    <footer>
        Student Registration & Survey System | Homework 3
    </footer>

</div>
</body>
</html>
