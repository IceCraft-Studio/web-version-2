<?php
enum ManagePasswordState {
    case NoUpdate;
    case PasswordInvalid;
    case PasswordMismatch;
    case Failure;
    case Success;
}

enum UserActionState {
    case NoUpdate;
    case CsrfInvalid;
    case Failure;
    case Success;
}