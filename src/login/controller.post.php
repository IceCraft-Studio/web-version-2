<?php
if ($_POST['login_username'] == "user" && $_POST['login_password'] == "password") {
    header("Location: ../home");
    exit();
} else {
    
}