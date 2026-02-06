<?php
// Include helper functions for reading/writing JSON data and escaping output
require_once __DIR__ . '/includes/functions.php';

$employees = readData('employees');
$netResult = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = isset($_POST['employeeId']) ? (int)$_POST['employeeId'] : 0;
    $salaryInput = trim($_POST['salary'] ?? '');

    if ($employeeId <= 0 && $salaryInput === '') {
        $errors[] = "Select an employee or enter a salary.";
    }

    // If employee selected, use their stored salary unless a salary was provided explicitly
    $baseSalary = 0;
    if ($employeeId > 0) {
        foreach ($employees as $e) {
            if ((int)$e['id'] === $employeeId) { $baseSalary = (float)$e['salary']; break; }
        }
    }
    if ($salaryInput !== '') {
        if (!is_numeric($salaryInput)) $errors[] = "Salary must be a number.";
        else $baseSalary = (float)$salaryInput;
    }

    if (empty($errors)) {
        $tax = $baseSalary * 0.15;
        $net = $baseSalary - $tax;
        $netResult = [
            'gross' => $baseSalary,
            'tax' => $tax,
            'net' => $net
        ];
        // Optionally store payroll results in a payroll.json if you want
        // For now we just display
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Payroll</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
<a href="dashboard.php">Home</a>
<a href="payroll.php">Payroll</a>
</nav>

<div class="container">
<h2>Payroll Calculator</h2>

<?php if ($errors): ?><div style="color:#b00020;"><?php foreach ($errors as $err) echo "<div>".h($err)."</div>"; ?></div><?php endif; ?>

<form method="post" action="payroll.php">
  <select name="employeeId" id="payName">
    <option value="0">-- Select employee (optional) --</option>
    <?php foreach ($employees as $e): ?>
      <option value="<?php echo (int)$e['id']; ?>"><?php echo h($e['name']); ?></option>
    <?php endforeach; ?>
  </select>
  <input id="paySalary" name="salary" placeholder="Monthly Salary (optional)">
  <button type="submit">Calculate</button>
</form>

<?php if ($netResult): ?>
  <p id="payResult">Gross: R<?php echo number_format($netResult['gross'],2); ?> — Tax: R<?php echo number_format($netResult['tax'],2); ?> — Net Salary: R<?php echo number_format($netResult['net'],2); ?></p>
<?php endif; ?>

</div>

<script src="js/app.js"></script>
</body>
</html>

