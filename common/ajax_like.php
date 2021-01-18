<?php

session_start();
require(dirname(__FILE__) . '/dbconnection.php');

//ボタンが押された時の処理
if (!empty($_GET['id'])) {
    if ($_GET['status'] == 'unselected') {
        $sql_like = $db->prepare('INSERT INTO favoritePets SET member_id=?, favorite_pet_id=?');
    } else {
        $sql_like = $db->prepare('DELETE FROM favoritePets WHERE  member_id=? AND favorite_pet_id=?');
    }
    $sql_like->execute(array(
        $_SESSION['login']['member_id'],
        $_GET['id'],
    )); 
}