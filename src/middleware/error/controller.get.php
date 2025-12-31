<?php
function getStatusMessage(int $statusCode): string {
    $map = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => "I'm a teapot",
        422 => 'Unprocessable Entity',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        511 => 'Network Authentication Required',
    ];

    return $map[$statusCode] ?? 'Error';
}

if (http_response_code() === 419) {
    $errorImage = "/~dobiapa2/assets/errors/teapot.webp"; // https://www.reddit.com/r/Minecraftbuilds/comments/t0kz1j/simple_cottagecore_teapot_house_tutorial/
} else {
    $errorImage = "/~dobiapa2/assets/errors/cracked-ice.webp";
}

$viewState = ViewData::getInstance();
$viewState->set('response-message', getStatusMessage(http_response_code()));
$viewState->set('error-image', $errorImage);