<?php
require_once __DIR__ . '/includes/functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $salary = trim($_POST['salary'] ?? '');

    if ($name === '') $errors[] = "Name is required.";
    if ($salary !== '' && !is_numeric($salary)) $errors[] = "Salary must be a number.";

    if (empty($errors)) {
        $employees = readData('employees');
        $employee = [
            'id' => nextId($employees),
            'name' => $name,
            'role' => $role,
            'salary' => $salary === '' ? 0 : (float)$salary,
            'created_at' => date('c')
        ];
        $employees[] = $employee;
        writeData('employees', $employees);
        // redirect to avoid duplicate form submit
        header('Location: employees.php');
        exit;
    }
}

$employees = readData('employees');
?>
<!DOCTYPE html>
<html>
<head>
<title>Employees</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
<a href="dashboard.php">Home</a>
<a href="employees.php">Employees</a>
</nav>

<div class="container">
<h2>Add Employee</h2>

<?php if ($errors): ?>
  <div style="color: #b00020;">
    <?php foreach ($errors as $err): ?>
      <div><?php echo h($err); ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="post" action="employees.php">
  <input name="name" id="empName" placeholder="Name" required>
  <input name="role" id="empRole" placeholder="Role">
  <input name="salary" id="empSalary" placeholder="Salary">
  <button type="submit">Add</button>
</form>

<h2>Employee List</h2>
<ul id="employeeList">
<?php if (empty($employees)): ?>
  <li>No employees yet.</li>
<?php else: ?>
  <?php foreach ($employees as $e): ?>
    <li><?php echo h($e['name']); ?> - <?php echo h($e['role']); ?> - R<?php echo number_format($e['salary'],2); ?></li>
  <?php endforeach; ?>
<?php endif; ?>
</ul>
</div>

<script src="js/app.js"></script>
</body>
</html>

