<?php
session_start();
session_unset();
session_destroy();
header("Location: nindex.html#login");
exit();
?>