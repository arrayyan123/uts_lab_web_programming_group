<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once (__DIR__ .'/../models/db.php');

function createTask($userId, $title, $description, $assigned_by) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "INSERT INTO tasks (user_id, title, description, assigned_by) VALUES (:user_id, :title, :description, :assigned_by)";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':assigned_by', $assigned_by);

    return $stmt->execute();
}

function getTasks($userId, $filter = null) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT * FROM tasks WHERE user_id = :user_id";
    if ($filter !== null) {
        $query .= " AND is_completed = :is_completed";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    if ($filter !== null) {
        $stmt->bindParam(':is_completed', $filter, PDO::PARAM_BOOL);
    }
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateTask($taskId, $title, $description, $is_completed) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "UPDATE tasks SET title = :title, description = :description, is_completed = :is_completed WHERE id = :id";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':is_completed', $is_completed, PDO::PARAM_BOOL);
    $stmt->bindParam(':id', $taskId);

    return $stmt->execute();
}

function deleteTask($taskId) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "DELETE FROM tasks WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $taskId);

    return $stmt->execute();
}

function markTaskComplete($taskId) {
    return updateTask($taskId, null, null, true);
}

function deleteTaskAndRearrange($taskId, $userId) {
    $database = new Database();
    $conn = $database->getConnection();
    if (!$conn) {
        echo "Database connection error.";
        return false;
    }

    try {
        $conn->beginTransaction();
        $deleteStmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        if (!$deleteStmt->execute([$taskId, $userId])) {
            throw new Exception("Failed to delete task.");
        }
        $selectStmt = $conn->prepare("SELECT id FROM tasks WHERE user_id = ? ORDER BY id ASC");
        if (!$selectStmt->execute([$userId])) {
            throw new Exception("Failed to retrieve tasks.");
        }
        $tasks = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        $newId = 1;
        foreach ($tasks as $task) {
            $updateStmt = $conn->prepare("UPDATE tasks SET id = ? WHERE id = ?");
            if (!$updateStmt->execute([$newId, $task['id']])) {
                throw new Exception("Failed to update task ID.");
            }
            $newId++;
        }
        $conn->exec("ALTER TABLE tasks AUTO_INCREMENT = $newId");
        $conn->commit();
        echo "Task deleted and IDs rearranged successfully.";
        return true;
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo "Failed to delete task: " . $e->getMessage();
        return false;
    }
}


?>
