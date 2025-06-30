<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Upload Notes</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>

<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">⬆️ Upload a New Note</h1>
            <a href="index.php" class="btn btn-secondary">Back to Notes</a>
        </div>

        <?php
        if (isset($_GET['message'])) {
            $message = htmlspecialchars($_GET['message']);
            echo '<div class="alert alert-info text-center mt-3">' . $message . '</div>';
        }
        if (isset($_GET['error'])) {
            $error = htmlspecialchars($_GET['error']);
            echo '<div class="alert alert-danger text-center mt-3">' . $error . '</div>';
        }
        ?>

        <form
            action="process_upload.php"
            method="POST"
            enctype="multipart/form-data"
            class="bg-secondary p-4 rounded">
            <div class="mb-3">
                <label for="file" class="form-label">Upload Notes (PDF, Word, TXT, PPT):</label>
                <input
                    type="file"
                    name="note"
                    id="file"
                    class="form-control"
                    required />
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category (Subject/Semester):</label>
                <input
                    type="text"
                    name="category"
                    id="category"
                    class="form-control"
                    required />
            </div>
            <button type="submit" class="btn btn-light">Upload</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>

</html>