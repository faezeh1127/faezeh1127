<?php
function columnHas($db_connection, $table_name, $column_name, $value)
{
    if (empty($column_name))
        throw new InvalidArgumentException("Column name can't be empty");

    $select_query_builder = $db_connection->prepare(
        "SELECT ? FROM ? WHERE ? = ?"
    );

    foreach (array($column_name, $table_name, $column_name, $value) as $item)
        $select_query_builder->bind_param("s", $item);

    $select_query_builder->execute();

    $select_query_builder->store_result();

    if ($select_query_builder->num_rows == 0)
        return false;
    return true;
}
?>