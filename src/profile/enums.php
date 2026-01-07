<?php
enum PasswordUpdateState {
    case NoUpdate;
    case PasswordWrong;
    case PasswordInvalid;
    case PasswordMismatch;
    case Failure;
    case Success;
}
enum PictureUpdateState {
    case NoUpdate;
    case WrongSize;
    case WrongType;
    case WrongAspectRatio;
    case Failure;
    case Success;
}

enum ProfileUpdateState {
    case NoUpdate;
    case CsrfInvalid;
    case Failure;
    case Success;
}