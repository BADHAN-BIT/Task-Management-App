<?php
// Database Connection
$conn = mysqli_connect("localhost", "root", "", "task_db");

// Error Message Variable
$error = "";

// Step 8: Add Task (with validation)
if (isset($_POST['add_task'])) {
    $title = trim(mysqli_real_escape_string($conn, $_POST['new_task']));
    $status = mysqli_real_escape_string($conn, $_POST['new_status']);

    if (!empty($title)) {
        $insertQuery = "INSERT INTO tasks (title, status) VALUES ('$title', '$status')";
        mysqli_query($conn, $insertQuery);
        header("Location: index.php");
        exit();
    } else {
        $error = "⚠ Please enter a valid task title.";
    }
}

// Step 9: Delete Task
if (isset($_POST['delete_task'])) {
    $deleteId = mysqli_real_escape_string($conn, $_POST['delete_id']);
    $deleteQuery = "DELETE FROM tasks WHERE id = $deleteId";
    mysqli_query($conn, $deleteQuery);
    header("Location: index.php");
    exit();
}

// Step 10: Update Task
if (isset($_POST['update_task'])) {
    $updateId = mysqli_real_escape_string($conn, $_POST['update_id']);
    $updatedTitle = trim(mysqli_real_escape_string($conn, $_POST['updated_title']));
    $updatedStatus = mysqli_real_escape_string($conn, $_POST['updated_status']);

    if (!empty($updatedTitle)) {
        $updateQuery = "UPDATE tasks SET title='$updatedTitle', status='$updatedStatus' WHERE id=$updateId";
        mysqli_query($conn, $updateQuery);
        header("Location: index.php");
        exit();
    } else {
        $error = "⚠ Task title cannot be empty!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Task Management App</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="container">
    <h1>📝 Task Management Dashboard</h1>

    <!-- Filter Form -->
    <form method="GET" action="">
      <input type="text" name="search" placeholder="Search Task" />
      <select name="status">
        <option value="">All</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
      </select>
      <button type="submit">Filter</button>
    </form>

    <!-- Show Tasks -->
    <?php
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';

    $query = "SELECT * FROM tasks WHERE 1=1";

    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $query .= " AND title LIKE '%$search%'";
    }

    if (!empty($status)) {
        $status = mysqli_real_escape_string($conn, $status);
        $query .= " AND status='$status'";
    }

    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $statusClass = $row['status'] === 'completed' ? 'completed' : 'pending';

        echo "<div class='task-item $statusClass'>";
        echo "<strong>" . $row['title'] . "</strong> - " . $row['status'];

        // Edit button
        echo "<form method='GET' action='' style='display:inline'>";
        echo "<input type='hidden' name='edit_id' value='" . $row['id'] . "' />";
        echo "<button type='submit'>✏ Edit</button>";
        echo "</form>";

        // Delete button
        echo "<form method='POST' action='' style='display:inline'>";
        echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "' />";
        echo "<button type='submit' name='delete_task' onclick=\"return confirm('Are you sure?')\">❌ Delete</button>";
        echo "</form>";

        echo "</div>";
    }
    ?>

    <!-- Add New Task Form -->
    <h3>Add New Task</h3>

    <?php if (!empty($error)): ?>
      <div style="color: red; background: #ffe6e6; padding: 10px; margin-bottom: 10px; border-left: 4px solid red;">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="text" name="new_task" placeholder="Enter new task" required />
      <select name="new_status" required>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
      </select>
      <button type="submit" name="add_task">Add Task</button>
    </form>

    <!-- Edit Task Form -->
    <?php
    if (isset($_GET['edit_id'])) {
        $editId = $_GET['edit_id'];
        $editQuery = "SELECT * FROM tasks WHERE id = $editId";
        $editResult = mysqli_query($conn, $editQuery);
        $editData = mysqli_fetch_assoc($editResult);
    ?>
    <h3>✏ Edit Task</h3>
    <form method="POST" action="">
      <input type="hidden" name="update_id" value="<?php echo $editData['id']; ?>" />
      <input type="text" name="updated_title" value="<?php echo $editData['title']; ?>" required />
      <select name="updated_status">
        <option value="pending" <?php if ($editData['status'] == 'pending') echo 'selected'; ?>>Pending</option>
        <option value="completed" <?php if ($editData['status'] == 'completed') echo 'selected'; ?>>Completed</option>
      </select>
      <button type="submit" name="update_task">✅ Save</button>
    </form>
    <?php } ?>

  </div>
</body>
</html>