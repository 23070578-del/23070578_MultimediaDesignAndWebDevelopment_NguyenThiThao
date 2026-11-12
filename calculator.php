
<?php
// =====================================================
// Homework 9 - Robust Calculator
// Name: Nguyen Thi Thao
// Student ID: 23070578
// Date: 2026-11-12
// =====================================================

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();

// Custom Exceptions
class DivisionByZeroException extends Exception {}
class InvalidOperatorException extends Exception {}
class InvalidInputException extends Exception {}

// Initialize session history
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

// Application variables
$firstNumber = '';
$secondNumber = '';
$operator = '+';
$result = null;
$message = '';
$messageType = '';
$expression = '';

$operators = [
    '+' => 'Addition (+)',
    '-' => 'Subtraction (-)',
    '*' => 'Multiplication (*)',
    '/' => 'Division (/)',
    '%' => 'Modulo (%)',
    '**' => 'Power (**)'
];

// Log errors to file
function logCalculatorError($exception, $input)
{
    $date = date('Y-m-d H:i:s');
    $type = get_class($exception);
    $errorMessage = $exception->getMessage();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

    $log = "[$date] $type: $errorMessage | Input: \"$input\" | IP: $ip"
         . PHP_EOL;

    @file_put_contents(
        __DIR__ . '/calculator_errors.log',
        $log,
        FILE_APPEND | LOCK_EX
    );
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Clear History
    if (isset($_POST['clear_history'])) {
        $_SESSION['history'] = [];
        $message = 'Calculation history has been cleared successfully.';
        $messageType = 'warning';

    } else {

        $firstNumber = trim($_POST['first_number'] ?? '');
        $secondNumber = trim($_POST['second_number'] ?? '');
        $operator = $_POST['operator'] ?? '+';

        $expression = $firstNumber . ' ' . $operator . ' ' . $secondNumber;

        try {

            // Validate numeric inputs
            if (
                $firstNumber === '' ||
                $secondNumber === '' ||
                !is_numeric($firstNumber) ||
                !is_numeric($secondNumber)
            ) {
                throw new InvalidInputException(
                    'Please enter valid numeric values for both numbers.'
                );
            }

            // Validate operator
            if (!array_key_exists($operator, $operators)) {
                throw new InvalidOperatorException(
                    'The selected mathematical operator is not supported.'
                );
            }

            $num1 = (float)$firstNumber;
            $num2 = (float)$secondNumber;

            // Prevent division and modulo by zero
            if (
                ($operator === '/' || $operator === '%') &&
                $num2 == 0
            ) {
                throw new DivisionByZeroException(
                    'Division or modulo by zero is not allowed.'
                );
            }

            // Perform calculation
            switch ($operator) {

                case '+':
                    $result = $num1 + $num2;
                    break;

                case '-':
                    $result = $num1 - $num2;
                    break;

                case '*':
                    $result = $num1 * $num2;
                    break;

                case '/':
                    $result = $num1 / $num2;
                    break;

                case '%':
                    $result = fmod($num1, $num2);
                    break;

                case '**':
                    $result = $num1 ** $num2;
                    break;

                default:
                    throw new InvalidOperatorException(
                        'Unsupported operator.'
                    );
            }

            // Check overflow or invalid numeric results
            if (!is_finite($result)) {
                throw new OverflowException(
                    'The calculation result is too large or invalid.'
                );
            }

            // Format result
            if ($operator === '/') {
                $formattedResult = number_format($result, 4, '.', '');
            } else {
                if (floor($result) == $result) {
                    $formattedResult = number_format($result, 0, '.', '');
                } else {
                    $formattedResult = rtrim(
                        rtrim(number_format($result, 10, '.', ''), '0'),
                        '.'
                    );
                }
            }

            // Success message
            $message = 'Calculation completed successfully!';
            $messageType = 'success';

            // Save calculation history
            array_unshift($_SESSION['history'], [
                'expression' => $expression,
                'result' => $formattedResult,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            // Keep latest 50 records
            $_SESSION['history'] = array_slice(
                $_SESSION['history'],
                0,
                50
            );

        } catch (InvalidInputException $e) {

            $message = $e->getMessage();
            $messageType = 'error';
            logCalculatorError($e, $expression);

        } catch (InvalidOperatorException $e) {

            $message = $e->getMessage();
            $messageType = 'error';
            logCalculatorError($e, $expression);

        } catch (DivisionByZeroException $e) {

            $message = $e->getMessage();
            $messageType = 'error';
            logCalculatorError($e, $expression);

        } catch (OverflowException $e) {

            $message = $e->getMessage();
            $messageType = 'error';
            logCalculatorError($e, $expression);

        } catch (Throwable $e) {

            $message = 'An unexpected error occurred. Please try again.';
            $messageType = 'error';
            logCalculatorError($e, $expression);
        }
    }
}

// Escape output safely
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$history = $_SESSION['history'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Smart Calculator | Homework 9</title>

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
            min-height: 100vh;
            padding: 45px 20px;
        }

        .container {
            max-width: 1050px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.2);
        }

        .header h1 {
            font-size: 34px;
            font-weight: 800;
            letter-spacing: -1px;
            color: #17243b;
        }

        .header p {
            color: #718096;
            font-size: 15px;
            margin-top: 8px;
        }

        .header-tag {
            display: inline-block;
            margin-top: 15px;
            padding: 6px 13px;
            border-radius: 30px;
            background: #e8efff;
            color: #2859c5;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* Main Layout */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 0.85fr;
            gap: 22px;
            align-items: start;
        }

        .card {
            background: white;
            border: 1px solid #e9edf5;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 8px 30px rgba(25, 42, 70, 0.06);
        }

        .card-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
        }

        .heading-icon {
            width: 40px;
            height: 40px;
            background: #edf3ff;
            color: #2563eb;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .card-heading h2 {
            font-size: 18px;
            font-weight: 700;
        }

        .card-heading p {
            color: #94a3b8;
            font-size: 12px;
            margin-top: 4px;
        }

        /* Result Display */
        .result-box {
            background: linear-gradient(135deg, #172554, #1e40af);
            border-radius: 15px;
            padding: 24px;
            color: white;
            margin-bottom: 26px;
            position: relative;
            overflow: hidden;
        }

        .result-box::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 50%;
            right: -45px;
            top: -65px;
        }

        .result-label {
            color: #bfdbfe;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin-bottom: 12px;
        }

        .result-expression {
            color: #dbeafe;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .result-value {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -1px;
            overflow-wrap: anywhere;
        }

        .result-placeholder {
            color: #bfdbfe;
            font-size: 23px;
            font-weight: 600;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 9px;
        }

        .form-control {
            width: 100%;
            height: 49px;
            padding: 0 15px;
            border: 1px solid #dce3ee;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            color: #17243b;
            background: #fbfcfe;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        select.form-control {
            cursor: pointer;
        }

        /* Buttons */
        .btn {
            border: none;
            border-radius: 10px;
            height: 49px;
            padding: 0 18px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            width: 100%;
            background: #2563eb;
            color: white;
            box-shadow: 0 5px 12px rgba(37, 99, 235, 0.18);
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 7px 15px rgba(37, 99, 235, 0.24);
        }

        .btn-clear {
            background: #fff1f2;
            color: #e11d48;
            height: 36px;
            font-size: 11px;
            padding: 0 12px;
        }

        .btn-clear:hover {
            background: #ffe4e6;
        }

        /* Messages */
        .message {
            padding: 13px 15px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .message.success {
            color: #166534;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
        }

        .message.error {
            color: #b91c1c;
            background: #fee2e2;
            border: 1px solid #fecaca;
        }

        .message.warning {
            color: #92400e;
            background: #fef3c7;
            border: 1px solid #fde68a;
        }

        /* History */
        .history-card {
            margin-top: 22px;
        }

        .history-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
        }

        .history-title {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .history-title h2 {
            font-size: 17px;
        }

        .history-count {
            background: #edf3ff;
            color: #2563eb;
            padding: 4px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        thead {
            background: #f8fafc;
        }

        th {
            padding: 13px 15px;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            white-space: nowrap;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #edf0f5;
            font-size: 12px;
            color: #475569;
        }

        tbody tr:hover {
            background: #f8faff;
        }

        .history-result {
            color: #15803d;
            font-weight: 700;
        }

        .empty-history {
            text-align: center;
            padding: 35px 15px;
            color: #94a3b8;
        }

        .empty-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .empty-history p {
            font-size: 13px;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
            margin-top: 28px;
        }

        /* Responsive */
        @media (max-width: 760px) {
            body {
                padding: 25px 14px;
            }

            .main-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 27px;
            }

            .card {
                padding: 21px;
            }

            .result-value {
                font-size: 30px;
            }
        }

        @media (max-width: 420px) {
            .history-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Header -->
    <header class="header">
        <div class="brand">
            <div class="brand-icon">✦</div>
            <h1>Smart Calculator</h1>
        </div>

        <p>A reliable calculator with advanced error handling</p>

        <div class="header-tag">HOMEWORK 09 · PHP APPLICATION</div>
    </header>

    <!-- Main Section -->
    <div class="main-grid">

        <!-- Calculator -->
        <section class="card">

            <div class="card-heading">
                <div class="heading-icon">▦</div>
                <div>
                    <h2>Calculator Workspace</h2>
                    <p>Enter your values and select an operation</p>
                </div>
            </div>

            <?php if ($message !== ''): ?>
                <div class="message <?= e($messageType) ?>">
                    <span>
                        <?= $messageType === 'success' ? '✓' :
                            ($messageType === 'error' ? '!' : 'i') ?>
                    </span>
                    <?= e($message) ?>
                </div>
            <?php endif; ?>

            <!-- Result -->
            <div class="result-box">
                <div class="result-label">CALCULATION RESULT</div>

                <?php if ($result !== null): ?>

                    <div class="result-expression">
                        <?= e($expression) ?> =
                    </div>

                    <div class="result-value">
                        <?= e($formattedResult) ?>
                    </div>

                <?php else: ?>

                    <div class="result-placeholder">
                        Ready to calculate
                    </div>

                <?php endif; ?>
            </div>

            <!-- Calculator Form -->
            <form method="POST" action="">

                <div class="form-group">
                    <label for="first_number">First Number</label>

                    <input
                        type="text"
                        id="first_number"
                        name="first_number"
                        class="form-control"
                        placeholder="Enter first number"
                        value="<?= e($firstNumber) ?>"
                        inputmode="decimal"
                    >
                </div>

                <div class="form-group">
                    <label for="operator">Mathematical Operator</label>

                    <select
                        id="operator"
                        name="operator"
                        class="form-control"
                    >
                        <?php foreach ($operators as $symbol => $label): ?>
                            <option
                                value="<?= e($symbol) ?>"
                                <?= $operator === $symbol ? 'selected' : '' ?>
                            >
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="second_number">Second Number</label>

                    <input
                        type="text"
                        id="second_number"
                        name="second_number"
                        class="form-control"
                        placeholder="Enter second number"
                        value="<?= e($secondNumber) ?>"
                        inputmode="decimal"
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Calculate &nbsp; →
                </button>

            </form>

        </section>

        <!-- Information Panel -->
        <section class="card">

            <div class="card-heading">
                <div class="heading-icon">⚙</div>
                <div>
                    <h2>Calculator Features</h2>
                    <p>Supported mathematical operations</p>
                </div>
            </div>

            <div class="form-group">
                <label>Available Operations</label>

                <div class="table-wrapper">
                    <table>
                        <tbody>
                            <tr>
                                <td><strong>Addition</strong></td>
                                <td>+</td>
                            </tr>
                            <tr>
                                <td><strong>Subtraction</strong></td>
                                <td>−</td>
                            </tr>
                            <tr>
                                <td><strong>Multiplication</strong></td>
                                <td>×</td>
                            </tr>
                            <tr>
                                <td><strong>Division</strong></td>
                                <td>÷</td>
                            </tr>
                            <tr>
                                <td><strong>Modulo</strong></td>
                                <td>%</td>
                            </tr>
                            <tr>
                                <td><strong>Power</strong></td>
                                <td>**</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <label>System Capabilities</label>

                <div class="message success">
                    ✓ &nbsp; Input validation
                </div>

                <div class="message success">
                    ✓ &nbsp; Custom exception handling
                </div>

                <div class="message success">
                    ✓ &nbsp; Error logging
                </div>

                <div class="message success">
                    ✓ &nbsp; Session-based history
                </div>

            </div>

            <div class="message warning">
                <span>i</span>
                Division and modulo by zero are not allowed.
            </div>

        </section>

    </div>

    <!-- Calculation History -->
    <section class="card history-card">

        <div class="history-header">

            <div class="history-title">
                <div class="heading-icon">◷</div>

                <div>
                    <h2>Calculation History</h2>
                    <p style="color:#94a3b8; font-size:11px; margin-top:4px;">
                        Your recent calculations in this session
                    </p>
                </div>

                <span class="history-count">
                    <?= count($history) ?> RECORDS
                </span>
            </div>

            <form method="POST" action="">
                <button
                    type="submit"
                    name="clear_history"
                    value="1"
                    class="btn btn-clear"
                    onclick="return confirm('Are you sure you want to clear all history?');"
                >
                    Clear History
                </button>
            </form>

        </div>

        <?php if (empty($history)): ?>

            <div class="empty-history">
                <div class="empty-icon">◷</div>
                <p>No calculations yet.</p>
                <p>Your successful calculations will appear here.</p>
            </div>

        <?php else: ?>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Expression</th>
                            <th>Result</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($history as $index => $item): ?>

                            <tr>
                                <td><?= $index + 1 ?></td>

                                <td>
                                    <?= e($item['expression']) ?>
                                </td>

                                <td class="history-result">
                                    <?= e($item['result']) ?>
                                </td>

                                <td>
                                    <?= e($item['timestamp']) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>
                </table>

            </div>

        <?php endif; ?>

    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>Smart Calculator · PHP Exception Handling & Session Management</p>
        <p style="margin-top:6px;">Homework 09 · Information Systems</p>
    </footer>

</div>

</body>
</html>
