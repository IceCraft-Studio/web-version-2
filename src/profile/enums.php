<?php
/**
 * Enum for keeping track of profile password update state.
 */
enum PasswordUpdateState {
    /**
     * No password update.
     */
    case NoUpdate;
    /**
     * The old password is wrong.
     */
    case PasswordWrong;
    /**
     * The password has failed validation.
     */
    case PasswordInvalid;
    /**
     * The password wasn't confirmed.
     */
    case PasswordMismatch;
    /**
     * The password update has failed.
     */
    case Failure;
    /**
     * The password update was successful.
     */
    case Success;
}

/**
 * Enum for keeping track of profile picture update state.
 */
enum PictureUpdateState {
    /**
     * No profile picture update.
     */
    case NoUpdate;
    /**
     * The profile picture is too big in file size.
     */
    case WrongSize;
    /**
     * The profile picture is a wrong type.
     */
    case WrongType;
    /**
     * The profile picture has a wrong aspect ratio.
     */
    case WrongAspectRatio;
    /**
     * The profile picture update has failed.
     */
    case Failure;
    /**
     * The profile picture update was successful.
     */
    case Success;
}

/**
 * Enum for keeping track of generic profile update state.
 */
enum ProfileUpdateState {
    /**
     * No profile update.
     */
    case NoUpdate;
    /**
     * CSRF token validation has failed.
     */
    case CsrfInvalid;
    /**
     * The profile update has failed.
     */
    case Failure;
    /**
     * The profile update was successful.
     */
    case Success;
}