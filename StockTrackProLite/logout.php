<?php
session_start();

session_unset();
session_destroy();

header('Location: access.html');
header('Location: http://localhost/access.html');
exit();
