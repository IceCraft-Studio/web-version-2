<?php
enum PasswordUpdateState {
    case NoUpdate;
    case PasswordWrong;
    case PasswordInvalid;
    case PasswordMismatch;
    case Success;
}

enum ProfileUpdateState {
    case NoUpdate;
    case CsrfInvalid;
    case Failure;
    case Success;
}