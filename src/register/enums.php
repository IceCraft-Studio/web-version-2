<?php
enum RegisterFormError {
    case CsrfInvalid;
    case UsernameInvalid;
    case UsernameTaken;
    case PasswordInvalid;
    case PasswordMismatch;
    case ServerDatabase;
    case None;
}