<?php
require_once 'db_connect.php'; // Include the database connection file

$uploadFolder = "uploads/"; // Make sure this matches your upload folder

$message = "";
$error = "";

// Check if the request method is POST and if note_id is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['note_id'])) {
    $noteId = filter_var($_POST['note_id'], FILTER_VALIDATE_INT); // Sanitize and validate as integer

    if ($noteId === false || $noteId <= 0) {
        $error = "Invalid note ID.";
    } else {
        // Start a transaction for safety (optional but good practice)
        $conn->begin_transaction();

        try {
            // 1. Get the filename from the database before deleting the record
            $stmt = $conn->prepare("SELECT filename FROM notes WHERE id = ?");
            if ($stmt === false) {
                throw new Exception("Database error (select prepare): " . $conn->error);
            }
            $stmt->bind_param("i", $noteId);
            $stmt->execute();
            $result = $stmt->get_result();
            $note = $result->fetch_assoc();
            $stmt->close();

            if (!$note) {
                throw new Exception("Note not found with ID: " . $noteId);
            }

            $fileNameToDelete = $note['filename'];
            $filePath = $uploadFolder . $fileNameToDelete;

            // 2. Delete the record from the database
            $stmt = $conn->prepare("DELETE FROM notes WHERE id = ?");
            if ($stmt === false) {
                throw new Exception("Database error (delete prepare): " . $conn->error);
            }
            $stmt->bind_param("i", $noteId);

            if ($stmt->execute()) {
                // 3. Delete the physical file from the server
                if (file_exists($filePath)) {
                    if (unlink($filePath)) {
                        $message = "Note '" . htmlspecialchars($fileNameToDelete) . "' and file deleted successfully!";
                        $conn->commit(); // Commit transaction if everything is successful
                    } else {
                        // File exists but couldn't be deleted (permissions issue?)
                        throw new Exception("Note deleted from DB, but could not delete physical file: " . $filePath);
                    }
                } else {
                    // File not found on server, but entry was in DB. Proceed as success for DB deletion.
                    $message = "Note '" . htmlspecialchars($fileNameToDelete) . "' deleted from DB. Physical file not found.";
                    $conn->commit();
                }
            } else {
                throw new Exception("Error deleting note from database: " . $stmt->error);
            }
        } catch (Exception $e) {
            $conn->rollback(); // Rollback transaction on any error
            $error = $e->getMessage();
        } finally {
            if (isset($stmt) && $stmt->num_rows === null) { // Check if stmt was executed and closed
                $stmt->close();
            }
        }
    }
} else {
    $error = "Invalid request to delete note.";
}

// Close the database connection
$conn->close();

// Redirect back to index.php with a message
if (!empty($error)) {
    header("Location: index.php?error=" . urlencode($error));
} else {
    header("Location: index.php?message=" . urlencode($message));
}
exit();
