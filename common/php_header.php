<?php
session_start();
require(dirname(__FILE__) . '/dbconnection.php');
//ログイン時の処理
if (!empty($_SESSION['login'])) {
    //ニックネームを取得
    $email = $_SESSION['login']['email'];
    $member_id = $_SESSION['login']['member_id'];
    $get_name = $db->prepare('SELECT name FROM members WHERE member_id=?');
    $get_name->execute(array($member_id));
    $name = $get_name->fetch(PDO::FETCH_COLUMN);
    //ニックネームが6文字以上の場合は最初の5文字だけを切り出す
    if (mb_strlen($name, 'UTF-8') > 5) {
        $logined_name = mb_substr($name, 0, 5) . '...';
    } else {
        $logined_name = $name;
    }
    //自分のペット情報を取得
    $get_pet = $db->prepare('SELECT id, pet_name, pet_image, pet_type FROM pets WHERE member_id=?');
    $get_pet->execute(array($member_id));
    $pets = $get_pet->fetchAll(PDO::FETCH_ASSOC);
}
//ログアウト時の処理
if ($_POST['logout'] === 'ok') {
    $_SESSION = array();
    session_destroy();
    header('Location: /pets/complete.php?from=logout');
    exit();
}
//会員登録削除の処理
if ($_POST['quit'] === 'ok') {
    //お気に入りの動物の削除
    $delete_favoriteAnimals = $db->prepare('DELETE FROM favoriteAnimals WHERE member_id=?');
    $delete_favoriteAnimals->execute(array($member_id));
    //お気に入りのペットの削除
    $delete_favoritePets = $db->prepare('DELETE FROM favoritePets WHERE member_id=?');
    $delete_favoritePets->execute(array($member_id));
    //いいねの削除
    $delete_goods = $db->prepare('DELETE FROM goods WHERE member_id=?');
    $delete_goods->execute(array($member_id));
    //メンバーの削除
    $delete_member = $db->prepare('DELETE FROM members WHERE member_id=?');
    $delete_member->execute(array($member_id));
    //登録情報の削除
    $delete_registration = $db->prepare('DELETE FROM registration WHERE member_id=?');
    $delete_registration->execute(array($member_id));
    $_SESSION = array();
    session_destroy();
    header('Location: /pets/complete.php?from=quit');
    exit();
}
?>