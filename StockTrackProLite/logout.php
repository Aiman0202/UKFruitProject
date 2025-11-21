<?php
/* logout.php – destroy session and return to login */
session_start();

/* Remove all session variables & cookies */
session_unset();
session_destroy();

/* Redirect back to the login page */
header('Location: http://localhost/access.html');
exit();
