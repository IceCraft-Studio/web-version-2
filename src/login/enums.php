<?php
enum LoginFormError {
    case CsrfInvalid;
    case WrongCredentials;
    case UserBanned;
    case None;
}