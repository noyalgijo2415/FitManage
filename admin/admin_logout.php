<?php
session_start();
session_destroy();
header("Location: http://localhost/gym/fitmanage.html");
exit();
?>
