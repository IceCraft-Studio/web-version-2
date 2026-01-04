<?php
enum ProjectUploadState {
    case CsrfInvalid;
    case TitleInvalid;
    case DescriptionInvalid;
    case ThumbnailInvalid;
    case SlugInvalid;
    case SlugTaken;
    case CategoryInvalid;
    case ArticleInvalid;
    case GalleryInvalid;
    case LinkInvalid;
    case FileInvalid;
    case Success;

}