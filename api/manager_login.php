<?php
include("../utils/api_post_headers.php");
include("../database.php");
include("../database_model.php");
include("../utils/request_utils.php");
include("../utils/api_response_utils.php");
include("../utils/database_utils.php");


const LOGIN_TOKEN_LENGTH = 16;
const TOKEN_VALID_TIME_INTERVAL = "+1 day";
$response_status_code = 200;
$json_response = "";

try {
    $body = readJson();
}
catch (InvalidArgumentException $e) {
    $response_status_code = 422;
    http_response_code($response_status_code);
    $json_response = parameterError(
        "Invalid JSON",
        $response_status_code
    );

    exit($json_response);
}


if (!isset($body->username) || empty($body->username)) {

    $response_status_code = 422;
    http_response_code($response_status_code);
    $json_response = parameterError(
        "Invalid parameter: username",
        $response_status_code
    );

    exit($json_response);
} elseif (!isset($body->password) || empty($body->password)) {

    $response_status_code = 422;
    http_response_code($response_status_code);
    $json_response = parameterError(
        "Invalid parameter: password",
        $response_status_code
    );

    exit($json_response);
}
$manager_username = trim($body->username);
$manager_password = trim($body->password);


if (
    !columnHas(
        $database_connection,
        ManagerUsersTable::NAME,
        ManagerUsersTable::USER_COLUMN_NAME,
        $manager_username
    )
) {
    $response_status_code = 404;
    $json_response = makeResponse(
        array(
            "error" => "User not found"
        )
    );
}

$manager_user_password_inquiry_sql = "SELECT %s, %s FROM %s WHERE %s = ?";

$select_query_builder = $database_connection->prepare(
    sprintf(
        $manager_user_password_inquiry_sql,
        ManagerUsersTable::ID_COLUMN_NAME,
        ManagerUsersTable::PASSWORD_COLUMN_NAME,
        ManagerUsersTable::NAME,
        ManagerUsersTable::USER_COLUMN_NAME
    )
);

$select_query_builder->bind_param("s", $manager_username);

$select_query_builder->execute();
$select_query_results = $select_query_builder->get_result();

$row = $select_query_results->fetch_assoc();
$select_query_builder->close();

if (
    $row && password_verify(
        $manager_password, $row[ManagerUsersTable::PASSWORD_COLUMN_NAME]
    )
) {
    $user_id = $row[ManagerUsersTable::ID_COLUMN_NAME];

    // Send existing if exists and is not expired
    if (columnHas(
        $database_connection,
        ManagerUserLoginTable::NAME,
        ManagerUserLoginTable::ID_COLUMN_NAME,
        $user_id
    )) {
        // Check if token is not expired
        $manager_user_token_inquiry_sql = "SELECT %s, %s FROM %s WHERE %s = ?";
        $stmt = $database_connection->prepare(
            sprintf(
                $manager_user_token_inquiry_sql,
                ManagerUserLoginTable::TOKEN_COLUMN_NAME,
                ManagerUserLoginTable::EXPIRE_COLUMN_NAME,
                ManagerUserLoginTable::NAME,
                ManagerUserLoginTable::ID_COLUMN_NAME
            )
        );

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $token_expiration = $row[ManagerUserLoginTable::EXPIRE_COLUMN_NAME];
            $token = $row[ManagerUserLoginTable::TOKEN_COLUMN_NAME];
        } else {
            $response_status_code = 500;
            http_response_code($response_status_code);
            $json_response = makeResponse(
                array("error" => "Unable to get current token expiration"),
                $response_status_code
            );

            exit($json_response);
        }

        $current_datetime = date("Y-m-d H:i:s");

        if ($token_expiration > $current_datetime) {
            http_response_code($response_status_code);
            $json_response = makeResponse(
                array("token" => $token),
                $response_status_code
            );
            exit($json_response);
        }
        else {
            // Remove expired token
            $token_removal_query_sql = "DELETE FROM %s WHERE %s = ?";

            $token_removal_query = $database_connection->prepare(
                sprintf(
                    $token_removal_query,
                    ManagerUserLoginTable::NAME,
                    ManagerUserLoginTable::ID_COLUMN_NAME
                )
            );

            $token_removal_query->bind_param("i", $user_id);
            $token_removal_query->execute();
        }
    }

    $access_token = bin2hex(openssl_random_pseudo_bytes(LOGIN_TOKEN_LENGTH));

    // Adding access token to table
    $manger_user_login_token_establish_sql = "INSERT INTO %s (%s, %s, %s) VALUES (?, ?, ?)";

    $insert_token_query_builder = $database_connection->prepare(
        sprintf(
            $manger_user_login_token_establish_sql,
            ManagerUserLoginTable::NAME,
            ManagerUserLoginTable::ID_COLUMN_NAME,
            ManagerUserLoginTable::TOKEN_COLUMN_NAME,
            ManagerUserLoginTable::EXPIRE_COLUMN_NAME
        )
    );

    $token_expiration = strtotime(TOKEN_VALID_TIME_INTERVAL);

    $insert_token_query_builder->bind_param("sss",
        $user_id,
        $access_token,
        date('Y-m-d H:i:s', $token_expiration)
    );

    $insert_token_query_builder->execute();
    $insert_token_query_builder->close();

    $json_response = makeResponse(array("token" => $access_token));
} else {
    $response_status_code = 401;
    $json_response = makeResponse(
        array("error" => "Password is incorrect.")
    );
}


http_response_code($response_status_code);
echo ($json_response);
?>