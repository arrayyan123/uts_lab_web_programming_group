<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once(__DIR__ . '/../models/db.php');

function createTask($userId, $groupId, $title, $description, $deadline, $assigned_by) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "INSERT INTO tasks (user_id, group_task_id, title, description, deadline, assigned_by) 
              VALUES (:user_id, :group_task_id, :title, :description, :deadline, :assigned_by)";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':group_task_id', $groupId, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':deadline', $deadline);
    $stmt->bindParam(':assigned_by', $assigned_by, PDO::PARAM_INT);

    return $stmt->execute();
}

function getTasks($userId, $groupId) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT * FROM tasks WHERE group_task_id = :group_task_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':group_task_id', $groupId, PDO::PARAM_INT);
    
    if ($userId !== null) {
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    }
    
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// function updateTask($taskId, $is_completed, $groupId = null, $title = null, $description = null, $deadline = null) {
//     if (empty($taskId)) {
//         echo "Error: taskId cannot be null.";
//         return false;
//     }
//     $db = new Database();
//     $conn = $db->getConnection();
//     $query = "UPDATE tasks SET is_completed = :is_completed";
//     if ($groupId !== null) {
//         $query .= ", group_task_id = :group_task_id";
//     }
//     if ($title !== null) {
//         $query .= ", title = :title";
//     }
//     if ($description !== null) {
//         $query .= ", description = :description";
//     }
//     if ($deadline !== null) {
//         $query .= ", deadline = :deadline";
//     }
//     $query .= " WHERE id = :id";
//     $stmt = $conn->prepare($query);
//     $stmt->bindParam(':is_completed', $is_completed, PDO::PARAM_BOOL);
//     $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
//     if ($groupId !== null) {
//         $stmt->bindParam(':group_task_id', $groupId, PDO::PARAM_INT);
//     }
//     if ($title !== null) {
//         $stmt->bindParam(':title', $title, PDO::PARAM_STR);
//     }
//     if ($description !== null) {
//         $stmt->bindParam(':description', $description, PDO::PARAM_STR);
//     }
//     if ($deadline !== null) {
//         $stmt->bindParam(':deadline', $deadline);
//     }

//     return $stmt->execute();
// }

function updateTask($taskId, $is_completed, $groupId = null, $title = null, $description = null, $deadline = null) {
    if (empty($taskId)) {
        echo "Error: taskId cannot be null.";
        return false;
    }
    $db = new Database();
    $conn = $db->getConnection();
    $query = "UPDATE tasks SET is_completed = :is_completed";
    if (!is_null($groupId)) {
        $query .= ", group_task_id = :group_task_id";
    }
    if (!is_null($title)) {
        $query .= ", title = :title";
    }
    if (!is_null($description)) {
        $query .= ", description = :description";
    }
    if (!is_null($deadline)) {
        $query .= ", deadline = :deadline";
    }
    $query .= " WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':is_completed', $is_completed, PDO::PARAM_BOOL);
    if (!is_null($groupId)) {
        $stmt->bindParam(':group_task_id', $groupId, PDO::PARAM_INT);
    }
    if (!is_null($title)) {
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    }
    if (!is_null($description)) {
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    }
    if (!is_null($deadline)) {
        $stmt->bindParam(':deadline', $deadline);
    }
    $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
    return $stmt->execute();
}

function deleteTask($taskId) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "DELETE FROM tasks WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);

    return $stmt->execute();
}

function markTaskComplete($taskId) {
    return updateTask($taskId, null, null, null, null, true);
}

function getAllGroups($userId) {
    $db = new Database();
    $conn = $db->getConnection();
    $query = "SELECT * FROM group_tasks WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}


function createGroup($title) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $query = "INSERT INTO group_tasks (title) VALUES (:title)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $title); 

    return $stmt->execute();
}

function deleteTaskAndRearrange($taskId, $userId) {
    $db = new Database();
    $conn = $db->getConnection();
    try {
        $conn->beginTransaction();
        $deleteStmt = $conn->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
        $deleteStmt->bindParam(':id', $taskId, PDO::PARAM_INT);
        $deleteStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if (!$deleteStmt->execute()) {
            throw new Exception("Failed to delete task.");
        }

        $selectStmt = $conn->prepare("SELECT id FROM tasks WHERE user_id = :user_id ORDER BY id ASC");
        $selectStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $selectStmt->execute();

        $tasks = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
        $newId = 1;

        foreach ($tasks as $task) {
            $updateStmt = $conn->prepare("UPDATE tasks SET id = :new_id WHERE id = :old_id");
            $updateStmt->bindParam(':new_id', $newId, PDO::PARAM_INT);
            $updateStmt->bindParam(':old_id', $task['id'], PDO::PARAM_INT);

            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update task ID.");
            }
            $newId++;
        }
        $conn->exec("ALTER TABLE tasks AUTO_INCREMENT = $newId");
        $conn->commit();
        return true;
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log("Failed to delete task: " . $e->getMessage());
        return false;
    }
}

?>
