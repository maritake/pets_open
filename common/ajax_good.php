<?php

session_start();
require(dirname(__FILE__) . '/dbconnection.php');

//いいねが押された時の処理
if (!empty($_GET['id'])) {
    if ($_GET['status'] == 'unselected') {
        $sql_good = $db->prepare('INSERT INTO goods SET post_id=?, email=?');
    } else {
        $sql_good = $db->prepare('DELETE FROM goods WHERE post_id=? AND email=?');
    }
    $sql_good->execute(array($_GET['id'], $_SESSION['login']['email'])); 
}