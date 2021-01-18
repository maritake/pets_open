<?php
session_start();
require(dirname(__FILE__) . '/common/php_header.php');
$error = NULL;
date_default_timezone_set('Asia/Tokyo');
// POSTされた時にエラーチェックをする
if (!empty($_POST)) {
    if ($_POST['email'] !== '' && $_POST['password'] !== '') {
        //データベースを検索して会員情報を取得
        $login = $db->prepare(
            'SELECT * FROM members WHERE email=?'
        );
        $login->execute(array(
            $_POST['email'],
        ));
        $member = $login->fetch(PDO::FETCH_ASSOC);
        $latest_login = strtotime($member['latest_login']);
        $now = strtotime(date('Y-m-d H:i:s'));
        $try_login = $member['try_login'];
    }
    //10分以内にログイン試行していなければ$try_loginを0にする
    if (($now - $latest_login) >= 600) {
        $try_login = 0;
    }
    if ($try_login !== 'locked') {
        //登録したパスワードと一致していればログイン完了
        if (password_verify($_POST['password'], $member['password'])) {
            $_SESSION['login']['email'] = $member['email'];
            $_SESSION['login']['member_id'] = $member['member_id'];
            //最終ログインとログイン試行回数をアップデート
            $update_login = $db->prepare(
                'UPDATE members SET latest_login=NOW(), try_login="0" WHERE member_id=?'
            );
            $update_login->execute(array($member['member_id']));
            header('Location: new_post.php');
            exit();
        } else if ($try_login <= 2) {
            $error = 'wrong';
            //最終ログインとログイン試行回数をアップデート
            $try_login += 1;
            $update_login = $db->prepare(
                'UPDATE members SET latest_login=NOW(), try_login=? WHERE member_id=?'
            );
            $update_login->execute(array(
                $try_login,
                $member['member_id'],
            ));
        } else {
            $error = 'fail';
            //ロック状態にする
            $update_login = $db->prepare(
                'UPDATE members SET latest_login=NOW(), try_login="locked" WHERE member_id=?'
            );
            $update_login->execute(array($member['member_id']));
        }
    } else {
        //ロック状態の時のエラーメッセージ
        $error = 'fail';
    }
}
require(dirname(__FILE__) . '/common/html_header.php');
?>

    <section class="contents">
        <div class="form_container">
            <t1 class="form_t1">ログイン</t1>
            <p class="form_p">以下の項目を記入し、<br>
            ログインボタンを<br>
            クリックしてください</p>
                <form class="form_form" action="" method="POST">
                    <div>
                        <!-- エラーメッセージの表示 -->
                        <?php if ($error === 'wrong'): ?>
                        <p class="error"> ※e-mailアドレスとパスワードを正しく入力してください</p> 
                        <?php endif; ?>
                        <?php if ($error === 'fail'): ?>
                        <p class="error"> ※時間を置いてから再度ログインしてください</p> 
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="form_label" for="email">e-mailアドレス</label><br>
                        <input class="form_input" type="text" name="email" id="email" value=<?php print(htmlspecialchars($_POST['email'], ENT_QUOTES)); ?>><br>  
                    </div>
                    <div>
                        <label class="form_label" for="password">パスワード</label><br>
                        <input class="form_input" type="password" name="password" id="password"><br>
                    </div>
                    <div>
                            <input class="form_input" type="submit" value="ログイン">
                    </div> 
                </form>
        </div>
    </section>
    
<?php 
include(dirname(__FILE__) . '/common/footer.html');