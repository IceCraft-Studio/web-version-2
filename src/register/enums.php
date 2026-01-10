<?php
/**
 * Enum for error states when registering.
 */
enum RegisterFormError {
    /**
     * CSRF validation has failed.
     */
    case CsrfInvalid;
    /**
     * The username didn't meat the validation criterea.
     */
    case UsernameInvalid;
    /**
     * The username isn't available.
     */
    case UsernameTaken;
    /**
     * The password didn't meat the validation criterea.
     */
    case PasswordInvalid;
    /**
     * The password wasn't confirmed.
     */
    case PasswordMismatch;
    /**
     * Server database has failed.
     */
    case ServerDatabase;
    /**
     * No error has occured.
     */
    case None;
}