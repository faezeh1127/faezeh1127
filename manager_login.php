<?php
include("utils/api_post_headers.php");
include("database.php");
include("database_model.php");
include("utils/request_utils.php");
include("utils/api_response_utils.php");
include("utils/database_utils.php");


const LOGIN_TOKEN_LENGTH = 16;
const TOKEN_VALID_TIME_INTERVAL = new DateInterval('P1D'); // One day
$response_status_code = 200;
$json_response = "";

$body = readJson();


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

$select_query_builder = $db_connection->prepare(
    "SELECT ?, ? FROM ? WHERE ? = ?"
);

foreach (array(
    ManagerUsersTable::ID_COLUMN_NAME,
    ManagerUsersTable::PASSWORD_COLUMN_NAME,
    ManagerUsersTable::NAME,
    ManagerUsersTable::USER_COLUMN_NAME,
    $manager_username) as $item) {
    $select_query_builder->bind_param("s", $item);
}

$select_query_builder->execute();
$select_query_results = $select_query_builder->get_result();

$row = $select_query_results->fetch_assoc();
if ($row && password_verify(
    $manager_password, $row[ManagerUsersTable::PASSWORD_COLUMN_NAME])) {
    $user_id = $row[ManagerUsersTable::ID_COLUMN_NAME];
    
    $access_token = bin2hex(random_bytes(LOGIN_TOKEN_LENGTH));

    // Adding access token to table
    $stmt = $db_connection->prepare("INSERT INTO ? (?, ?, ?) VALUES (?, ?, ?)");

    $now_datetime = new DateTime();
    $tomorrow = $now->add($timeDelta);

    foreach (array(
        ManagerUserLoginTable::NAME,
        ManagerUserLoginTable::ID_COLUMN_NAME,
        ManagerUserLoginTable::TOKEN_COLUMN_NAME,
        ManagerUserLoginTable::EXPIRE_COLUMN_NAME,
        $user_id,
        $access_token,
        date('Y-m-d H:i:s', $tomorrow)) as $item) {
        $select_query_builder->bind_param("s", $item);
    }

    $json_response = makeResponse(array("token" => $access_token));
} else {
    $response_status_code = 401;
    $json_response = makeResponse(
        array("error" => "Password is incorrect."));
}

$select_query_builder->close();


http_response_code($response_status_code);
echo ($json_response);
?>