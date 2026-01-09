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
            'Accept-Language: en,en-US;q=0.7,cs;q=0.3',
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:146.0) Gecko/20100101 Firefox/146.0',
            'X-GitHub-Api-Version: 2022-11-28',
        ],
        CURLOPT_POSTFIELDS => $payload,
    ]);

    $response = curl_exec($curlHandle);
    curl_close($curlHandle);

    return $response;
}