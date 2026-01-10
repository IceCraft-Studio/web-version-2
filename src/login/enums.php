<?php

/**
 * Enum for error states when logging in.
 */
enum LoginFormError {
    /**
     * CSRF token failed validation.
     */
    case CsrfInvalid;
    /**
     * Wrong credentials were input.
     */
    case WrongCredentials;
    /**
     * The user trying to log in is banned.
     */
    case UserBanned;
    /**
     * No error has occured.
     */
    case None;
}