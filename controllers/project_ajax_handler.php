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

// Check if group_projects table exists, if not create it
try {
    $checkTable = $conn->query("SHOW TABLES LIKE 'group_projects'");
    if ($checkTable->num_rows == 0) {
        $createTable = "CREATE TABLE IF NOT EXISTS group_projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_name VARCHAR(255) NOT NULL,
            leader_name VARCHAR(255) NOT NULL,
            num_members INT NOT NULL,
            description TEXT,
            created_by INT NOT NULL,
            status ENUM('planning','in-progress','completed') DEFAULT 'planning',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
        )";
        $conn->query($createTable);
    }
} catch (Exception $e) {
    // Table might already exist or error, continue
}

try {
    switch ($action) {
        case 'create':
            $project_name = trim($_POST['project_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $num_members = intval($_POST['num_members'] ?? 0);
            $leader_name = trim($_POST['leader_name'] ?? '');
            
            if (empty($project_name)) {
                echo json_encode(['success' => false, 'message' => 'Project name is required']);
                exit;
            }
            
            if ($num_members < 2 || $num_members > 20) {
                echo json_encode(['success' => false, 'message' => 'Number of members must be between 2 and 20']);
                exit;
            }
            
            if (empty($leader_name)) {
                echo json_encode(['success' => false, 'message' => 'Team leader name is required']);
                exit;
            }
            
            $stmt = $conn->prepare('INSERT INTO group_projects (project_name, description, num_members, leader_name, created_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->bind_param("ssisi", $project_name, $description, $num_members, $leader_name, $user_id);
            
            if ($stmt->execute()) {
                $project_id = $conn->insert_id;
                $stmt->close();
                
                // Fetch the created project
                $stmt = $conn->prepare('SELECT * FROM group_projects WHERE id = ?');
                $stmt->bind_param("i", $project_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $project = $result->fetch_assoc();
                $stmt->close();
                
                echo json_encode(['success' => true, 'message' => 'Project created successfully', 'project' => $project]);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Failed to create project']);
            }
            break;
            
        case 'update':
            $project_id = intval($_POST['project_id'] ?? 0);
            $project_name = trim($_POST['project_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $num_members = intval($_POST['num_members'] ?? 0);
            $leader_name = trim($_POST['leader_name'] ?? '');
            
            if (empty($project_name) || $project_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
                exit;
            }
            
            if ($num_members < 2 || $num_members > 20) {
                echo json_encode(['success' => false, 'message' => 'Number of members must be between 2 and 20']);
                exit;
            }
            
            // Verify project belongs to user
            $stmt = $conn->prepare('SELECT id FROM group_projects WHERE id = ? AND created_by = ?');
            $stmt->bind_param("ii", $project_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Project not found']);
                exit;
            }
            $stmt->close();
            
            $stmt = $conn->prepare('UPDATE group_projects SET project_name = ?, description = ?, num_members = ?, leader_name = ? WHERE id = ? AND created_by = ?');
            $stmt->bind_param("ssisii", $project_name, $description, $num_members, $leader_name, $project_id, $user_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                
                // Fetch updated project
                $stmt = $conn->prepare('SELECT * FROM group_projects WHERE id = ?');
                $stmt->bind_param("i", $project_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $project = $result->fetch_assoc();
                $stmt->close();
                
                echo json_encode(['success' => true, 'message' => 'Project updated successfully', 'project' => $project]);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Failed to update project']);
            }
            break;
            
        case 'delete':
            $project_id = intval($_POST['project_id'] ?? 0);
            
            if ($project_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid project ID']);
                exit;
            }
            
            // Verify project belongs to user
            $stmt = $conn->prepare('SELECT id FROM group_projects WHERE id = ? AND created_by = ?');
            $stmt->bind_param("ii", $project_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Project not found']);
                exit;
            }
            $stmt->close();
            
            $stmt = $conn->prepare('DELETE FROM group_projects WHERE id = ? AND created_by = ?');
            $stmt->bind_param("ii", $project_id, $user_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                echo json_encode(['success' => true, 'message' => 'Project deleted successfully']);
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Failed to delete project']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

