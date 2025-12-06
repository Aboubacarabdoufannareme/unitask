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
            $project_id = intval($_POST['project_id'] ?? 0);
            $task_name = trim($_POST['task_name'] ?? '');
            $assigned_to = trim($_POST['assigned_to'] ?? '');
            $priority = $_POST['priority'] ?? 'medium';
            $status = $_POST['status'] ?? 'pending';
            $due_date = $_POST['due_date'] ?? null;
            
            if ($project_id <= 0 || empty($task_name)) {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
                exit;
            }
            
            // Verify project belongs to user
            $stmt = $conn->prepare('SELECT id FROM group_projects WHERE id = ? AND created_by = ?');
            $stmt->bind_param("ii", $project_id, $user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Project not found']);
                exit;
            }
            $stmt->close();
            
            // Insert task
            $stmt = $conn->prepare('INSERT INTO group_tasks (project_id, task_name, assigned_to, priority, status, due_date, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
            $stmt->bind_param("isssss", $project_id, $task_name, $assigned_to, $priority, $status, $due_date);
            
            if ($stmt->execute()) {
                $stmt->close();
                echo json_encode(['success' => true, 'message' => 'Task created successfully']);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Failed to create task']);
            }
            break;
            
        case 'update':
            $task_id = intval($_POST['task_id'] ?? 0);
            $project_id = intval($_POST['project_id'] ?? 0);
            $task_name = trim($_POST['task_name'] ?? '');
            $assigned_to = trim($_POST['assigned_to'] ?? '');
            $priority = $_POST['priority'] ?? 'medium';
            $status = $_POST['status'] ?? 'pending';
            $due_date = $_POST['due_date'] ?? null;
            
            if ($task_id <= 0 || $project_id <= 0 || empty($task_name)) {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
                exit;
            }
            
            // Verify project belongs to user
            $stmt = $conn->prepare('SELECT id FROM group_projects WHERE id = ? AND created_by = ?');
            $stmt->bind_param("ii", $project_id, $user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Project not found']);
                exit;
            }
            $stmt->close();
            
            // Verify task belongs to project
            $stmt = $conn->prepare('SELECT id FROM group_tasks WHERE id = ? AND project_id = ?');
            $stmt->bind_param("ii", $task_id, $project_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Task not found']);
                exit;
            }
            $stmt->close();
            
            // Update task
            $stmt = $conn->prepare('UPDATE group_tasks SET task_name = ?, assigned_to = ?, priority = ?, status = ?, due_date = ?, updated_at = NOW() WHERE id = ?');
            $stmt->bind_param("sssssi", $task_name, $assigned_to, $priority, $status, $due_date, $task_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Failed to update task']);
            }
            break;
            
        case 'delete':
            $task_id = intval($_POST['task_id'] ?? 0);
            $project_id = intval($_POST['project_id'] ?? 0);
            
            if ($task_id <= 0 || $project_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
                exit;
            }
            
            // Verify project belongs to user
            $stmt = $conn->prepare('SELECT id FROM group_projects WHERE id = ? AND created_by = ?');
            $stmt->bind_param("ii", $project_id, $user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Project not found']);
                exit;
            }
            $stmt->close();
            
            // Verify task belongs to project
            $stmt = $conn->prepare('SELECT id FROM group_tasks WHERE id = ? AND project_id = ?');
            $stmt->bind_param("ii", $task_id, $project_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Task not found']);
                exit;
            }
            $stmt->close();
            
            // Delete task
            $stmt = $conn->prepare('DELETE FROM group_tasks WHERE id = ? AND project_id = ?');
            $stmt->bind_param("ii", $task_id, $project_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Failed to delete task']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
