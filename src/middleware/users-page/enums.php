<?php
/**
 * Enum for keeping track of the state of password update by the admin.
 */
enum ManagePasswordState {
    /**
     * No password update.
     */
    case NoUpdate;
    /**
     * The password didn't meat validation criterea.
     */
    case PasswordInvalid;
    /**
     * Password wasn't confirmed.
     */
    case PasswordMismatch;
    /**
     * The password change has failed.
     */
    case Failure;
    /**
     * The password change was successfull.
     */
    case Success;
}

/**
 * Enum for keeping track of the state admin actions on the user.
 */
enum UserActionState {
    /**
     * No action.
     */
    case NoUpdate;
    /**
     * CSRF token failed validation.
     */
    case CsrfInvalid;
    /**
     * The action failed.
     */
    case Failure;
    /**
     * The action was succesfull.
     */
    case Success;
}