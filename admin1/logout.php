<?php
session_start();
session_destroy();
header("Location: ../index.php"); // arahkan ke halaman login
exit();
