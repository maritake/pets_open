<?php
session_start();
require(dirname(__FILE__) . '/dbconnection.php');
//ログイン時の処理
if (!empty($_SESSION['login'])) {
    //ニックネームを取得
    $email = $_SESSION['login']['email'];
    $get_name = $db->prepare('SELECT name FROM members WHERE email=?');
    $get_name->execute(array($email));
    $name = $get_name->fetch(PDO::FETCH_COLUMN);
    //自分のペット情報を取得
    $get_pet = $db->prepare('SELECT id, pet_name, pet_image, pet_type FROM pets WHERE email=?');
    $get_pet->execute(array($email));
    $pets = $get_pet->fetchAll(PDO::FETCH_ASSOC);
}
//ログアウト時の処理
if ($_POST['logout'] === 'ok') {
    $_SESSION = array();
    session_destroy();
    header('Location: /pets/complete.php?from=logout');
    exit();
}
//ログアウト時の処理
if ($_POST['quit'] === 'ok') {
    //お気に入りの動物の削除
    $delete_favoriteAnimals = $db->prepare('DELETE FROM favoriteAnimals WHERE email=?');
    $delete_favoriteAnimals->execute(array($email));
    //お気に入りのペットの削除
    $delete_favoritePets = $db->prepare('DELETE FROM favoritePets WHERE email=?');
    $delete_favoritePets->execute(array($email));
    //いいねの削除
    $delete_goods = $db->prepare('DELETE FROM goods WHERE email=?');
    $delete_goods->execute(array($email));
    //メンバーの削除
    $delete_member = $db->prepare('DELETE FROM members WHERE email=?');
    $delete_member->execute(array($email));
    $_SESSION = array();
    session_destroy();
    header('Location: /pets/complete.php?from=quit');
    exit();
}
?>