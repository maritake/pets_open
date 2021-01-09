<?php

session_start();
require(dirname(__FILE__) . '/dbconnection.php');

//ボタンが押された時の処理
if (!empty($_GET['id'])) {
    if ($_GET['status'] == 'unselected') {
        $sql_like = $db->prepare('INSERT INTO favoritePets SET email=?, favorite_pet_id=?');
    } else {
        $sql_like = $db->prepare('DELETE FROM favoritePets WHERE  email=? AND favorite_pet_id=?');
    }
    $sql_like->execute(array(
        $_SESSION['login']['email'],
        $_GET['id'],
    )); 
}