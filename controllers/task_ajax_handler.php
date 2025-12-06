<?php
session_start();
require_once __DIR__ . '/../config/DBconnection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
            
            if (empty($title)) {
                echo json_encode(['success' => false, 'message' => 'Title is required']);
                exit;
            }
            
            $stmt = $conn->prepare('INSERT INTO tasks (user_id, title, description, due_date, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->bind_param("isss", $user_id, $title, $description, $due_date);
            
            if ($stmt->execute()) {
                $task_id = $conn->insert_id;
                $stmt->close();
                
                // Fetch the created task
                $stmt = $conn->prepare('SELECT * FROM tasks WHERE id = ?');
                $stmt->bind_param("i", $task_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $task = $result->fetch_assoc();
                $stmt->close();
                
                echo json_encode(['success' => true, 'message' => 'Task created successfully', 'task' => $task]);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Failed to create task']);
            }
            break;
            
        case 'update':
            $task_id = intval($_POST['task_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
            
            if (empty($title) || $task_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
                exit;
            }
            
            // Verify task belongs to user
            $stmt = $conn->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ?');
            $stmt->bind_param("ii", $task_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Task not found']);
                exit;
            }
            $stmt->close();
            
            $stmt = $conn->prepare('UPDATE tasks SET title = ?, description = ?, due_date = ? WHERE id = ? AND user_id = ?');
            $stmt->bind_param("sssii", $title, $description, $due_date, $task_id, $user_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                
                // Fetch updated task
                $stmt = $conn->prepare('SELECT * FROM tasks WHERE id = ?');
                $stmt->bind_param("i", $task_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $task = $result->fetch_assoc();
                $stmt->close();
                
                echo json_encode(['success' => true, 'message' => 'Task updated successfully', 'task' => $task]);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Failed to update task']);
            }
            break;
            
        case 'delete':
            $task_id = intval($_POST['task_id'] ?? 0);
            
            if ($task_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
                exit;
            }
            
            // Verify task belongs to user
            $stmt = $conn->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ?');
            $stmt->bind_param("ii", $task_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Task not found']);
                exit;
            }
            $stmt->close();
            
            $stmt = $conn->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
            $stmt->bind_param("ii", $task_id, $user_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Failed to delete task']);
            }
            break;
            
        case 'toggle_complete':
            $task_id = intval($_POST['task_id'] ?? 0);
            
            if ($task_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
                exit;
            }
            
            // Check if status column exists, if not, add it
            $checkColumn = $conn->query("SHOW COLUMNS FROM tasks LIKE 'status'");
            if ($checkColumn->num_rows == 0) {
                // Add status column if it doesn't exist
                $conn->query("ALTER TABLE tasks ADD COLUMN status ENUM('pending', 'completed') DEFAULT 'pending'");
            }
            
            // Verify task belongs to user and get current status
            $stmt = $conn->prepare('SELECT id, status FROM tasks WHERE id = ? AND user_id = ?');
            $stmt->bind_param("ii", $task_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Task not found']);
                exit;
            }
            $task = $result->fetch_assoc();
            $stmt->close();
            
            $new_status = ($task['status'] === 'completed') ? 'pending' : 'completed';
            
            $stmt = $conn->prepare('UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?');
            $stmt->bind_param("sii", $new_status, $task_id, $user_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                echo json_encode(['success' => true, 'message' => 'Task status updated', 'status' => $new_status]);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Failed to update task status']);
            }
            break;
            
        case 'get':
            $task_id = intval($_GET['task_id'] ?? 0);
            
            if ($task_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
                exit;
            }
            
            $stmt = $conn->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?');
            $stmt->bind_param("ii", $task_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $task = $result->fetch_assoc();
                $stmt->close();
                echo json_encode(['success' => true, 'task' => $task]);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Task not found']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

