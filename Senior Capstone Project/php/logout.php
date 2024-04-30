<?php
// Start the session
session_start();

// Destroy all session data
session_destroy();

// Redirect to index.html
header("Location: ../index.html");
exit();
?>
