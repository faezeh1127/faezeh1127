<?php
class ManagerUsersTable
{
    const NAME = "manager_users";
    const ID_COLUMN_NAME = "id";
    const USER_COLUMN_NAME = "username";
    const PASSWORD_COLUMN_NAME = "password";
}
class ManagerUserLoginTable
{
    const NAME = "manager_user_login";
    const ID_COLUMN_NAME = "id";
    const TOKEN_COLUMN_NAME = "token";
    const EXPIRE_COLUMN_NAME = "expire";
}
?>