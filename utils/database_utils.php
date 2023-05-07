<?php
function columnHas($db_connection, $table_name, $column_name, $value)
{
    if (empty($column_name))
        throw new InvalidArgumentException("Column name can't be empty");

    $select_query_builder = $db_connection->prepare(
        "SELECT {$column_name} FROM {$table_name} WHERE {$column_name} = ?"
    );

    $select_query_builder->bind_param("s", $value);

    $select_query_builder->execute();

    $select_query_builder->store_result();
    
    if ($select_query_builder->num_rows == 0)
        return false;
    
    $select_query_builder->close();
    return true;
}

function isEmpty($db_connection, $table_name, $column_name, $index_column_name, $index_value) {
    $select_query_sql = "SELECT * FROM {$table_name} WHERE {$index_column_name}=? AND {$column_name}=''";
    $stmt = $db_connection->prepare($select_query_sql);

    $stmt->bind_param("s", $index_value);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // if ($result->num_rows < 0) {
    //     throw new // NotExists
    // }
    if ($result->num_rows == 0)
        return false;

    return true;
}
?>