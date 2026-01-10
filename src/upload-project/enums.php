<?php
/**
 * Enum for keeping track of the state of uploading a project.
 */
enum ProjectUploadState {
    /**
     * No upload yet, no state.
     */
    case NoState;
    /**
     * CSRF token has failed the validation.
     */
    case CsrfInvalid;
    /**
     * The title of the project failed validation.
     */
    case TitleInvalid;
    /**
     * The description of the project failed validation.
     */
    case DescriptionInvalid;
    /**
     * The thumbnail of the project failed validation.
     */
    case ThumbnailInvalid;
    /**
     * The slug of the project failed validation.
     */
    case SlugInvalid;
    /**
     * The slug of the project isn't available.
     */
    case SlugTaken;
    /**
     * The category of the project doesn't exist.
     */
    case CategoryInvalid;
    /**
     * The article of the project failed validation.
     */
    case ArticleInvalid;
    /**
     * The some gallery upload of the project failed validation.
     */
    case GalleryInvalid;
    /**
     * The some link upload of the project failed validation.
     */
    case LinkInvalid;
    /**
     * The some file upload of the project failed validation.
     */
    case FileInvalid;
    /**
     * The project doesn't have at least a single link or a file.
     */
    case NoUploads;
    /**
     * An error has occured on the server or database.
     */
    case ServerError;
    /**
     * The project was successfully uploaded.
     */
    case Success;
}