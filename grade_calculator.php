
<?php
// ==========================================
// HOMEWORK 2: STUDENT GRADE CALCULATOR
// ==========================================

// Student data: associative and indexed arrays
$students = [
    ['name' => 'Alice Nguyen', 'scores' => [85, 92, 78, 90]],
    ['name' => 'Bob Tran', 'scores' => [70, 65, 80, 72]],
    ['name' => 'Charlie Le', 'scores' => [100, 100, 100, 100]],
    ['name' => 'David Pham', 'scores' => [0, 0, 0, 0]],
    ['name' => 'Emma Vo', 'scores' => [90, 90, 90, 90]]
];

// Function 1: Calculate average using a for loop
function calculateAverage($scores) {
    $total = 0;

    for ($i = 0; $i < count($scores); $i++) {
        $total += $scores[$i];
    }

    return round($total / count($scores), 1);
}

// Function 2: Assign letter grade
function assignGrade($average) {
    if ($average >= 90) {
        return 'A';
    } elseif ($average >= 80) {
        return 'B';
    } elseif ($average >= 70) {
        return 'C';
    } elseif ($average >= 60) {
        return 'D';
    } else {
        return 'F';
    }
}

// Calculate results for every student
$results = [];

foreach ($students as $student) {
    $average = calculateAverage($student['scores']);

    $results[] = [
        'name' => $student['name'],
        'scores' => $student['scores'],
        'average' => $average,
        'highest' => max($student['scores']),
        'lowest' => min($student['scores']),
        'grade' => assignGrade($average)
    ];
}

// Sort by average score in descending order
usort($results, function ($a, $b) {
    return $b['average'] <=> $a['average'];
});

// Escape output for safe HTML display
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Count students and calculate class average
$totalStudents = count($results);
$classTotal = 0;

foreach ($results as $result) {
    $classTotal += $result['average'];
}

$classAverage = round($classTotal / $totalStudents, 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grade Calculator</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #263247;
            padding: 40px 20px;
        }

        .container {
            max-width: 1150px;
            margin: auto;
        }

        .header {
            background: linear-gradient(135deg, #173b72, #2874c8);
            color: white;
            padding: 35px;
            border-radius: 16px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .header p {
            color: #e4edfa;
            line-height: 1.6;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
        }

        .summary-card p {
            color: #718096;
            margin-bottom: 10px;
        }

        .summary-card h2 {
            color: #1d4e89;
            font-size: 28px;
        }

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        .table-card h2 {
            margin-bottom: 20px;
            color: #173b72;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th {
            background: #eaf1fa;
            color: #173b72;
            padding: 15px 12px;
            text-align: center;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #e8edf4;
            text-align: center;
        }

        tbody tr:hover {
            background: #f7faff;
        }

        td:nth-child(2) {
            text-align: left;
            font-weight: 600;
        }

        .grade {
            display: inline-block;
            min-width: 35px;
            padding: 7px 10px;
            border-radius: 7px;
            font-weight: bold;
        }

        .grade-a {
            color: #166534;
            background: #dcfce7;
        }

        .grade-b {
            color: #1d4ed8;
            background: #dbeafe;
        }

        .grade-c {
            color: #92400e;
            background: #fef3c7;
        }

        .grade-d {
            color: #9a3412;
            background: #ffedd5;
        }

        .grade-f {
            color: #b91c1c;
            background: #fee2e2;
        }

        .note {
            margin-top: 20px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
        }

        footer {
            text-align: center;
            margin-top: 25px;
            color: #718096;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            body {
                padding: 20px 10px;
            }

            .summary {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Student Grade Calculator</h1>
        <p>Calculate student scores, averages, rankings, and letter grades.</p>
    </div>

    <div class="summary">
        <div class="summary-card">
            <p>Total Students</p>
            <h2><?= $totalStudents ?></h2>
        </div>

        <div class="summary-card">
            <p>Class Average</p>
            <h2><?= number_format($classAverage, 1) ?></h2>
        </div>
    </div>

    <div class="table-card">
        <h2>Student Results</h2>

        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Scores</th>
                    <th>Average</th>
                    <th>Highest</th>
                    <th>Lowest</th>
                    <th>Grade</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($results as $index => $student): ?>
                    <?php
                        $gradeClass = 'grade-' . strtolower($student['grade']);
                    ?>

                    <tr>
                        <td><?= $index + 1 ?></td>

                        <td><?= e($student['name']) ?></td>

                        <td><?= e(implode(', ', $student['scores'])) ?></td>

                        <td><strong><?= number_format($student['average'], 1) ?></strong></td>

                        <td><?= $student['highest'] ?></td>

                        <td><?= $student['lowest'] ?></td>

                        <td>
                            <span class="grade <?= e($gradeClass) ?>">
                                <?= e($student['grade']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="note">
            <strong>Grade Scale:</strong>
            A: 90–100 |
            B: 80–89 |
            C: 70–79 |
            D: 60–69 |
            F: Below 60
            <br>
            Students are ranked by average score, from highest to lowest.
        </div>
    </div>

    <footer>
        Student Grade Calculator | Homework 2 | PHP
    </footer>

</div>
</body>
</html>
