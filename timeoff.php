<?php
// Include helper functions for reading/writing JSON data and escaping output
require_once __DIR__ . '/includes/functions.php';

// Initialize array to store validation errors
$errors = [];
// Load employees list from the employees.json file
$employees = readData('employees');

// Check if form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get employee ID from form and convert to integer (default 0 if not set)
    $employeeId = isset($_POST['employeeId']) ? (int)$_POST['employeeId'] : 0;
    // Get leave date from form and trim whitespace
    $date = trim($_POST['date'] ?? '');
    // Get leave reason from form and trim whitespace (can be empty)
    $reason = trim($_POST['reason'] ?? '');

    // Validate that an employee was selected
    if ($employeeId <= 0) $errors[] = "Select an employee.";
    // Validate that a date was provided
    if ($date === '') $errors[] = "Date is required.";

    // If validation passed, save the leave request
    if (empty($errors)) {
        // Load existing leave requests from file
        $leaves = readData('leaves');
        // Add new leave request with generated ID and current timestamp
        $leaves[] = [
            'id' => nextId($leaves),
            'employee_id' => $employeeId,
            'date' => $date,
            'reason' => $reason,
            'status' => 'Pending',
            'created_at' => date('c')
        ];
        // Write updated leave requests back to file
        writeData('leaves', $leaves);
        // Redirect to refresh the page and show updated list
        header('Location: timeoff.php');
        exit;
    }
}

// Load all leave requests from the leaves.json file
$leaves = readData('leaves');
?>
<!DOCTYPE html>
<html>
<head>
<title>Time Off</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navigation menu with links to home and time off pages -->
<nav>
<a href="dashboard.php">Home</a>
<a href="timeoff.php">Time Off</a>
</nav>

<div class="container">
<h2>Request Leave</h2>

<!-- Display validation errors if any exist -->
<?php if ($errors): ?>
  <div style="color:#b00020;"><?php foreach ($errors as $err) echo "<div>".h($err)."</div>"; ?></div>
<?php endif; ?>

<!-- Form to submit a new leave request -->
<form method="post" action="timeoff.php">
  <!-- Dropdown to select an employee -->
  <select name="employeeId" id="leaveName" required>
    <option value="">-- Select employee --</option>
    <?php foreach ($employees as $e): ?>
      <option value="<?php echo (int)$e['id']; ?>"><?php echo h($e['name']); ?></option>
    <?php endforeach; ?>
  </select>
  <!-- Date input for the leave date -->
  <input type="date" name="date" id="leaveDate" required>
  <!-- Text input for optional leave reason -->
  <input type="text" name="reason" placeholder="Reason (optional)">
  <!-- Submit button to save the leave request -->
  <button type="submit">Submit</button>
</form>

<h3>Leave Requests</h3>
<!-- Display list of all leave requests in reverse chronological order -->
<ul id="leaveList">
<!-- Show message if no leave requests have been submitted -->
<?php if (empty($leaves)): ?>
  <li>No leave requests yet.</li>
<?php else: ?>
  <!-- Loop through all leave requests in reverse order (newest first) -->
  <?php foreach (array_reverse($leaves) as $l): 
    // Find and display the employee name, default to "Unknown" if not found
    $empName = "Unknown";
    foreach ($employees as $e) { if ((int)$e['id'] === (int)$l['employee_id']) { $empName = $e['name']; break; } }
  ?>
    <!-- Display leave request details: employee name, date, status, and optional reason -->
    <li><?php echo h($empName); ?> - <?php echo h($l['date']); ?> - <?php echo h($l['status']); ?> <?php if (!empty($l['reason'])) echo '- ' . h($l['reason']); ?></li>
  <?php endforeach; ?>
<?php endif; ?>
</ul>
</div>

<script src="js/app.js"></script>
</body>
</html>

