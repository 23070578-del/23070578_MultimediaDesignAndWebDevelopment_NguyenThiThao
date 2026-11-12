
<?php
// ==================================================
// Homework 1 - Personal Portfolio
// Name: Nguyen Thi Thao
// Student ID: 23070578
// Date: 2026-11-12
// ==================================================

// 1. Personal information
$fullName = "Nguyễn Thị Thảo";
$studentID = "23070578";
$major = "MIS";
$university = "VNUIS";

$bio = "I am an Management Information Systems student who enjoys learning new technologies and improving my programming skills. I am interested in web development, database systems, and digital innovation.";

// String concatenation (Requirement: at least 2 places)
$pageTitle = $fullName . " | Personal Portfolio";
$welcomeMessage = "Hello, I'm " . $fullName . ".";

// 2. Education history
$education = [
    [
        "school" => "VNUIS",
        "degree" => "Bachelor's Degree in Information Systems",
        "period" => "2023 - Present",
        "description" => "Currently studying information systems, programming, databases, and business technology."
    ],
    [
        "school" => "LE VAN HUU High School",
        "degree" => "High School Education",
        "period" => "2020 - 2023",
        "description" => "Completed high school education and developed a foundation for further study."
    ]
];

// 3. Technical skills
$skills = [
    ["name" => "HTML", "level" => "Beginner", "percent" => 40],
    ["name" => "CSS", "level" => "Beginner", "percent" => 40],
    ["name" => "PHP", "level" => "Beginner", "percent" => 35],
    ["name" => "SQL & MySQL", "level" => "Beginner", "percent" => 40],
    ["name" => "JavaScript", "level" => "Beginner", "percent" => 30]
];

// 4. Hobbies and interests
$hobbies = [
    ["icon" => "📚", "name" => "Reading"],
    ["icon" => "🌍", "name" => "Learning Languages"],
    ["icon" => "🎵", "name" => "Listening to Music"],
    ["icon" => "💻", "name" => "Exploring Technology"]
];

// 5. Dynamic timestamp
date_default_timezone_set("Asia/Ho_Chi_Minh");
$lastUpdated = date("d M Y, H:i");

// Escape output safely
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($pageTitle) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f3f6fc;
            color: #17243b;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 45px auto;
        }

        /* Navigation */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 20px;
            font-weight: 800;
            color: #1d4ed8;
        }

        .nav-label {
            color: #64748b;
            font-size: 13px;
        }

        /* Hero section */
        .hero {
            background: linear-gradient(135deg, #172554, #2563eb);
            color: white;
            border-radius: 22px;
            padding: 48px;
            display: flex;
            align-items: center;
            gap: 35px;
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.15);
        }

        .profile-placeholder {
            width: 125px;
            height: 125px;
            min-width: 125px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 3px solid rgba(255,255,255,0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 42px;
            font-weight: 800;
        }

        .hero-content .eyebrow {
            color: #bfdbfe;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .hero h1 {
            font-size: 35px;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .hero .major {
            color: #dbeafe;
            font-size: 15px;
            margin-bottom: 16px;
        }

        .hero .bio {
            color: #e2e8f0;
            font-size: 14px;
            max-width: 650px;
        }

        /* General sections */
        .section {
            background: white;
            border: 1px solid #e8edf5;
            border-radius: 18px;
            padding: 28px;
            margin-top: 24px;
            box-shadow: 0 7px 25px rgba(25, 42, 70, 0.04);
        }

        .section-title {
            font-size: 20px;
            font-weight: 800;
            color: #172554;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title span {
            color: #2563eb;
        }

        /* Personal information */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .info-item {
            background: #f8faff;
            border: 1px solid #edf1f7;
            padding: 16px;
            border-radius: 12px;
        }

        .info-label {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-value {
            color: #1e293b;
            font-size: 14px;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        /* Education */
        .education-list {
            display: grid;
            gap: 16px;
        }

        .education-item {
            padding: 18px;
            border-left: 4px solid #2563eb;
            background: #f8faff;
            border-radius: 0 12px 12px 0;
        }

        .education-item h3 {
            font-size: 15px;
            color: #1e293b;
        }

        .education-item .degree {
            color: #2563eb;
            font-size: 13px;
            font-weight: 600;
            margin: 4px 0;
        }

        .education-item .period {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
        }

        .education-item p {
            color: #64748b;
            font-size: 13px;
            margin-top: 8px;
        }

        /* Skills */
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .skill-item {
            background: #f8faff;
            border: 1px solid #edf1f7;
            padding: 17px;
            border-radius: 12px;
        }

        .skill-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 11px;
        }

        .skill-name {
            font-size: 14px;
            font-weight: 700;
        }

        .skill-level {
            color: #2563eb;
            font-size: 11px;
            font-weight: 700;
        }

        .progress {
            height: 7px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
            border-radius: 10px;
        }

        /* Hobbies */
        .hobbies-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        .hobby-item {
            background: #f8faff;
            border: 1px solid #edf1f7;
            border-radius: 13px;
            padding: 20px 12px;
            text-align: center;
        }

        .hobby-icon {
            font-size: 27px;
            margin-bottom: 8px;
        }

        .hobby-name {
            font-size: 12px;
            font-weight: 700;
            color: #334155;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 28px 10px;
            color: #64748b;
            font-size: 12px;
        }

        footer strong {
            color: #2563eb;
        }

        /* Responsive layout */
        @media (max-width: 700px) {
            .container {
                width: 92%;
                margin: 25px auto;
            }

            .hero {
                padding: 30px 22px;
                flex-direction: column;
                text-align: center;
            }

            .hero h1 {
                font-size: 28px;
            }

            .info-grid,
            .skills-grid {
                grid-template-columns: 1fr;
            }

            .hobbies-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .navbar {
                align-items: flex-start;
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">MY PORTFOLIO.</div>
        <div class="nav-label">Personal Profile · 2026</div>
    </nav>

    <!-- Hero / Profile -->
    <header class="hero">

        <div class="profile-placeholder">
            T
        </div>

        <div class="hero-content">
            <div class="eyebrow">WELCOME TO MY PORTFOLIO</div>

            <h1><?= e($welcomeMessage) ?></h1>

            <div class="major">
                <?= e($major) ?> Student
            </div>

            <p class="bio">
                <?= e($bio) ?>
            </p>
        </div>

    </header>

    <main>

        <!-- Personal Information -->
        <section class="section">

            <h2 class="section-title">
                <span>01.</span> Personal Information
            </h2>

            <div class="info-grid">

                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?= e($fullName) ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Student ID</div>
                    <div class="info-value"><?= e($studentID) ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Major</div>
                    <div class="info-value"><?= e($major) ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">University</div>
                    <div class="info-value"><?= e($university) ?></div>
                </div>

            </div>

        </section>

        <!-- Education History -->
        <section class="section">

            <h2 class="section-title">
                <span>02.</span> Education History
            </h2>

            <div class="education-list">

                <?php foreach ($education as $item): ?>

                    <article class="education-item">

                        <h3><?= e($item["school"]) ?></h3>

                        <div class="degree">
                            <?= e($item["degree"]) ?>
                        </div>

                        <div class="period">
                            <?= e($item["period"]) ?>
                        </div>

                        <p><?= e($item["description"]) ?></p>

                    </article>

                <?php endforeach; ?>

            </div>

        </section>

        <!-- Technical Skills -->
        <section class="section">

            <h2 class="section-title">
                <span>03.</span> Technical Skills
            </h2>

            <div class="skills-grid">

                <?php foreach ($skills as $skill): ?>

                    <div class="skill-item">

                        <div class="skill-top">
                            <span class="skill-name">
                                <?= e($skill["name"]) ?>
                            </span>

                            <span class="skill-level">
                                <?= e($skill["level"]) ?>
                            </span>
                        </div>

                        <div class="progress">
                            <div
                                class="progress-bar"
                                style="width: <?= (int)$skill["percent"] ?>%;"
                            ></div>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </section>

        <!-- Hobbies -->
        <section class="section">

            <h2 class="section-title">
                <span>04.</span> Hobbies & Interests
            </h2>

            <div class="hobbies-grid">

                <?php foreach ($hobbies as $hobby): ?>

                    <div class="hobby-item">

                        <div class="hobby-icon">
                            <?= e($hobby["icon"]) ?>
                        </div>

                        <div class="hobby-name">
                            <?= e($hobby["name"]) ?>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </section>

    </main>

    <!-- Footer -->
    <footer>
        <p>Designed and developed by <strong><?= e($fullName) ?></strong></p>

        <p style="margin-top: 8px;">
            Last updated: <strong><?= e($lastUpdated) ?></strong>
        </p>

        <p style="margin-top: 5px;">
            PHP Personal Portfolio · Homework 1
        </p>
    </footer>

</div>

</body>
</html>
