<?php
const USE_INCLUDE_PATH = false;
const READ_DEFAULT_LENGTH = 200;
function readPayload($start_index = 0, $length = READ_DEFAULT_LENGTH)
{
    return file_get_contents(
        'php://input',
        USE_INCLUDE_PATH,
        NULL,
        $start_index,
        $length
    );
}

function validateJson($json_string)
{
    json_decode($json_string);

    return json_last_error() === JSON_ERROR_NONE;
}

function readJson($start_index = 0, $length = READ_DEFAULT_LENGTH)
{
    $payload = file_get_contents(
        'php://input',
        USE_INCLUDE_PATH,
        NULL,
        $start_index,
        $length
    );

    if (!$payload)
        return "";
    
    if (!validateJson($payload))
        throw new InvalidArgumentException("Request payload is not valid JSON");

    return json_decode($payload);
}
?>