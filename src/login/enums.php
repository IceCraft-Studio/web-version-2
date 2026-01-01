<?php
enum LoginFormError {
    case CsrfInvalid;
    case WrongCredentials;
    case None;
}