<?php
require_once 'db_connect.php'; // Include the database connection file
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Course Notes Sharing</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>

<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">📘 Available Course Notes</h1>
            <a href="upload_form.php" class="btn btn-primary">Upload New Note</a>
        </div>

        <?php
        // Display message if present in the URL
        if (isset($_GET['message'])) {
            $message = htmlspecialchars($_GET['message']);
            echo '<div class="alert alert-info text-center mt-3">' . $message . '</div>';
        }
        if (isset($_GET['error'])) {
            $error = htmlspecialchars($_GET['error']);
            echo '<div class="alert alert-danger text-center mt-3">' . $error . '</div>';
        }
        ?>

        <hr class="my-4" />

        <h3>📄 Notes List</h3>
        <div id="notes-list" class="mt-3">
            <?php
            $uploadFolder = "uploads/";

            echo '<ul class="list-group">';

            // Modified SQL: Select the 'id' column as well
            $sql = "SELECT id, filename, category, upload_time FROM notes ORDER BY upload_time DESC";
            $result = $conn->query($sql);

            if ($result) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $noteId = $row['id']; // Get the ID
                        $storedFileName = $row["filename"];
                        $category = htmlspecialchars($row["category"]);
                        $uploadTime = date("F j, Y, g:i a", strtotime($row["upload_time"]));
                        $filePath = $uploadFolder . htmlspecialchars($storedFileName);

                        $displayName = $storedFileName;
                        $underscorePos = strpos($storedFileName, '_');
                        if ($underscorePos !== false) {
                            $displayName = substr($storedFileName, $underscorePos + 1);
                        }
                        $displayName = htmlspecialchars($displayName);

                        echo '<li class="list-group-item bg-light text-dark d-flex justify-content-between align-items-center mb-2 rounded">';
                        echo '  <div>';
                        echo '      <strong>' . $displayName . '</strong><br>';
                        echo '      <small class="text-muted">Category: ' . $category . '</small><br>';
                        echo '      <small class="text-muted">Uploaded: ' . $uploadTime . '</small>';
                        echo '  </div>';
                        echo '  <div class="d-flex align-items-center">'; // Container for buttons
                        echo '      <a href="' . $filePath . '" class="btn btn-sm btn-outline-primary me-2" download="' . $displayName . '">Download</a>';
                        // Add the delete button. We'll use a form for proper POST request.
                        echo '      <form action="delete_note.php" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this note?\');">';
                        echo '          <input type="hidden" name="note_id" value="' . $noteId . '">';
                        echo '          <button type="submit" class="btn btn-sm btn-danger">Delete</button>';
                        echo '      </form>';
                        echo '  </div>';
                        echo '</li>';
                    }
                } else {
                    echo '<p class="text-muted">No notes have been uploaded yet. Be the first!</p>';
                }
                $result->free();
            } else {
                echo '<p class="text-danger">Error fetching notes: ' . $conn->error . '</p>';
            }

            echo '</ul>';
            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>

</html>