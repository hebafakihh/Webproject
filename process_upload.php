<?php
require_once 'db_connect.php';

// Specify where to save uploaded files
$uploadFolder = "uploads/";

$message = ""; // To store success message
$error = ""; // To store error message

// Check if the form was submitted and a file was chosen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["note"]["name"])) {

    $fileName = $_FILES["note"]["name"];
    $fileTmpName = $_FILES["note"]["tmp_name"];
    $category = trim($_POST["category"]); // Trim category input

    // Basic server-side validation for inputs
    if (empty($category)) {
        $error = "Category is required.";
    } elseif (empty($fileName)) {
        $error = "Please choose a file to upload.";
    } else {
        // Make sure the file name is safe and unique (important for uploads)
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        // Use uniqid() + original name for better uniqueness and readability
        $safeFileName = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9_\-.]/", "", basename($fileName));
        $targetPath = $uploadFolder . $safeFileName;

        $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx'];
        $maxFileSize = 5 * 1024 * 1024;

        if (!in_array($fileExtension, $allowedTypes)) {
            $error = "Invalid file type. Only PDF, Word, TXT, PowerPoint files are allowed.";
        } elseif ($_FILES["note"]["size"] > $maxFileSize) {
            $error = "File is too large. Maximum size is 5MB.";
        } else {
            // Try to move the uploaded file
            if (move_uploaded_file($fileTmpName, $targetPath)) {
                // File moved successfully!

                // Prepare SQL INSERT statement
                $stmt = $conn->prepare("INSERT INTO notes (filename, category, upload_time) VALUES (?, ?, NOW())");
                if ($stmt === false) {
                    $error = "Database error (prepare): " . $conn->error;
                    unlink($targetPath); // Clean up uploaded file if DB prep fails
                } else {
                    $stmt->bind_param("ss", $safeFileName, $category);

                    if ($stmt->execute()) {
                        $message = "File '" . htmlspecialchars($fileName) . "' uploaded successfully!";
                    } else {
                        $error = "File uploaded, but there was an error saving its information to the database: " . $stmt->error;
                        unlink($targetPath); // Clean up uploaded file if DB insert fails
                    }
                    $stmt->close();
                }
            } else {
                // Error moving the file
                $error = "Sorry, there was an error uploading your file. Error code: " . $_FILES["note"]["error"];
            }
        }
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = "No file chosen or invalid request.";
} else {
    // If accessed directly without POST, redirect back to the form
    header("Location: upload_form.php");
    exit();
}

// Close the database connection
$conn->close();

// Redirect based on whether there was an error or a success
if (!empty($error)) {
    header("Location: upload_form.php?error=" . urlencode($error));
} else {
    header("Location: index.php?message=" . urlencode($message));
}
exit();
