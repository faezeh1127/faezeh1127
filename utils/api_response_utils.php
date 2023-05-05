<?php
function parameterError($error_message, $status_code = 422)
{
    $dict_response = array(
        "status" => $status_code,
        "error" => $error_message
    );

    return json_encode($dict_response);
}

function makeResponse($dict, $status_code = 200)
{
    $dict_response = array_merge(array(
        "status" => $status_code
    ), $dict);

    return json_encode($dict_response);
}
?>