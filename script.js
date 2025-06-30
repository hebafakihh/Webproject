document.addEventListener("DOMContentLoaded", function () {
  const uploadForm = document.querySelector(
    'form[action="process_upload.php"]'
  );
  const fileInput = document.querySelector('input[type="file"][name="note"]');

  if (uploadForm && fileInput) {
    uploadForm.addEventListener("submit", function (event) {
      const file = fileInput.files[0];

      if (file) {
        const allowedTypes = [
          "application/pdf",
          "application/msword",
          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
          "text/plain",
          "application/vnd.ms-powerpoint",
          "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        ];
        const maxFileSize = 5 * 1024 * 1024;

        if (!allowedTypes.includes(file.type)) {
          alert(
            "Invalid file type.\nPlease upload PDF, Word (doc, docx), Text (txt), or PowerPoint (ppt, pptx) files."
          );
          event.preventDefault();
          fileInput.value = "";
          return;
        }

        if (file.size > maxFileSize) {
          alert("File is too large. Maximum size is 5MB.");
          event.preventDefault();
          fileInput.value = "";
          return;
        }
      }
    });
  }

  // --- Clean up URL message after display (on index.php and upload_form.php) ---
  // This removes the ?message= or ?error= from the URL after the page loads
  // without causing a page refresh.
  if (
    window.location.search.includes("message=") ||
    window.location.search.includes("error=")
  ) {
    const cleanUrl =
      window.location.protocol +
      "//" +
      window.location.host +
      window.location.pathname;
    window.history.replaceState({ path: cleanUrl }, "", cleanUrl);
  }
});
