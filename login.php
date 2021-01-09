<?php
session_start();
require(dirname(__FILE__) . '/common/php_header.php');
$error = NULL;
// POSTされた時にエラーチェックをする
if (!empty($_POST)) {
    if ($_POST['email'] !== '' && $_POST['password'] !== '') {
        //データベースを検索して会員情報を取得
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
        $login->execute(array(
            $_POST['email'],
            sha1($_POST['password']),
        ));
        $member = $login->fetch();
    }
    //登録情報と一致していればログイン完了
    if ($member) {
        $_SESSION['login'] = $_POST;
        header('Location: new_post.php');
        exit();
    } else {
        $error = 'wrong';
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