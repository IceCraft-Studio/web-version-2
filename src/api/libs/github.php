<?php
/**
 * The URL to the Github Markdown API. See https://docs.github.com/en/rest/markdown/markdown?apiVersion=2022-11-28.
 * @var string
 */
const MARKDOWN_URL = 'https://api.github.com/markdown';

/**
 * Calls GitHub Markdown API to retrieve the HTML representation of a given markdown string. The API ensures the HTML doesn't contain XSS.
 * @param string $data The input markdown string.
 * @return bool|string HTML result or `false` on cURL failure.
 */
function markdownGithub(string $data)
{
    $payload = json_encode([
        'text' => $data,
        'mode' => 'gfm',
    ]);

    $curlHandle = curl_init(MARKDOWN_URL);
    curl_setopt_array($curlHandle, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/vnd.github+json',
            'Content-Type: application/json',
            'X-GitHub-Api-Version: 2022-11-28',
        ],
        CURLOPT_POSTFIELDS => $payload,
    ]);

    $response = curl_exec($curlHandle);
    curl_close($curlHandle);

    return $response;
}