<?php
function Sql_connect()
{
    $connection = mysqli_connect('localhost', 'root', 'HewpphP9vQ', 'gallery');
    if (!$connection) die("Помилка підключення: " . mysqli_connect_error());
    return $connection;
}

function Sql_exec($sql)
{
    $connection = Sql_connect();
    $result = mysqli_query($connection, $sql);
    mysqli_close($connection);
    return $result;
}

function Sql_query($sql)
{
    $connection = Sql_connect();
    $result = mysqli_query($connection, $sql);
    $ret = [];
    while ($row = mysqli_fetch_assoc($result)) $ret[] = $row;
    mysqli_close($connection);
    return $ret;
}
