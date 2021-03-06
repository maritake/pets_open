<?php
if ($_REQUEST['action'] !== 'join' && $_REQUEST['action'] !== 'change') {
    header('Location: /pets/index.php');
    exit();
}
session_start();
include(dirname(__FILE__) . '/../common/php_header.php');
$error = NULL;
// POSTされた時にエラーチェックとcsrf_tokenの照合
if (!empty($_POST)) {
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] == $_SESSION['csrf_token']) {
        // 各項目のエラーチェック
        if ($_POST['password'] === '') {
            $error['password'] = 'blunk';
        } else if (mb_strlen($_POST['password']) < 8) {
            $error['password'] = 'short';
        } else if ($_POST['password'] !== $_POST['password_check']) {
            $error['password'] = 'false';
        }
        if ($_POST['action'] === 'join') {
            //登録済みのメールアドレスとの重複チェック
            $check_email = $db->prepare(
                'SELECT * FROM members WHERE email=?'
            );
            $check_email->execute(array(
                $_POST['email'],
            ));
            $is_exist_email = !empty($check_email->fetch(PDO::FETCH_ASSOC));  //すでに登録済みであればtrueを格納
            if ($_POST['email'] === '') {
                $error['email'] = 'blunk';
            } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $error['email'] = 'notemail';
            } else if ($is_exist_email) {
                $error['email'] = 'exist';
            }
        }    
        // エラーがなければデータベース登録or更新して次のページにジャンプ
        if (!isset($error)) {   
            //パスワードをハッシュ処理
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            if ($_POST['action'] === 'join') {
                //新規登録時 
                //registrationテーブルに一時登録
                $register = $db->prepare(
                    'INSERT INTO registration SET email=?, password=?'
                );
                $register->execute(array(
                    $_POST['email'],
                    $hash,
                ));
                $_SESSION['POSTindex']['email'] = $_POST['email'];
                header('Location: /pets/join/member.php?action=join');
                exit(); 
            } else {
                //登録変更時
                //パスワードをハッシュ処理
                $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $update_password = $db->prepare(
                    'UPDATE members SET password=? WHERE member_id=?'
                );
                $update_password->execute(array(
                    $hash,
                    $member_id,
                ));
                header('Location: /pets/complete.php?from=change');
                exit();
            }
        }
    } else {
        header('Location: /pets/index.php');
    }
}
//CSRF対策
$random = openssl_random_pseudo_bytes(16);
$csrf_token = bin2hex($random);
$_SESSION['csrf_token'] = $csrf_token;
include(dirname(__FILE__) . '/../common/html_header.php'); 
?>

    <section class="contents">
        <div class="form_container">
            <t1 class="form_t1">会員登録</t1>
            <p class="form_p">以下の項目を記入し、<br>
            「確定」ボタンを<br>
            クリックしてください</p>
                <form class="form_form" action="" method="POST">
                    <div>
                        <label class="form_label" for="email">e-mailアドレス</label><br>
                        <span class="form_span">※必須</span><br>


<!-- 登録変更時 -->
                        <?php if ($_REQUEST['action'] === 'change'): ?>
                        <p><?php print(htmlspecialchars($email, ENT_QUOTES)); ?></p>
                        <span class="form_span">※変更できません</span><br>
<!-- 新規登録時 -->
                        <?php else: ?>
                        <input class="form_input" type="text" name="email" id="email" placeholder="例：pets@gmail.com" value=<?php print(htmlspecialchars($_POST['email'], ENT_QUOTES)); ?>><br>
                        <!-- 未入力の場合のエラーメッセージ -->
                        <?php if ($error['email'] === 'blunk'): ?>
                        <p class="error"> ※e-mailアドレスを入力してください</p> 
                        <?php endif; ?>
                        <!-- 入力情報がemailアドレス以外の場合のエラーメッセージ -->
                        <?php if ($error['email'] === 'notemail'): ?>
                        <p class="error"> ※有効なe-mailアドレスを入力してください</p> 
                        <?php endif; ?>
                        <!-- emailアドレスが登録済みの場合のエラーメッセージ -->
                        <?php if ($error['email'] === 'exist'): ?>
                        <p class="error"> ※このe-mailアドレスは既に登録されています</p> 
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="form_label" for="password">
                            <?php if ($_REQUEST['action'] === 'chenge') {
                                print('新しい'); }?>
                        パスワード
                        </label><br>
                        <span class="form_span"> ※必須，半角英数字8文字以上</span><br>
                        <input class="form_input" type="password" name="password" id="password"><br>
                        <!-- 未入力の場合のエラーメッセージ -->
                        <?php if ($error['password'] === 'blunk'): ?>
                        <p class="error"> ※パスワードを入力してください</p> 
                        <?php endif; ?>
                        <!-- パスワードが短い場合のエラーメッセージ -->
                        <?php if ($error['password'] === 'short'): ?>
                        <p class="error"> ※パスワードは8文字以上で入力してください</p> 
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="form_label" for="password_check">パスワード(確認)</label><br>
                        <span class="form_span"> ※同じパスワードを再度入力してください</span><br>
                        <input class="form_input" type="password" name="password_check" id="password_check"><br>
                        <!-- パスワードが異なる場合のエラーメッセージ -->
                        <?php if ($error['password'] === 'false'): ?>
                        <p class="error"> ※上と同じパスワードを再度入力してください</p> 
                        <?php endif; ?>
                    </div>

<!-- 登録変更時 -->
                    <?php if ($_REQUEST['action'] === 'change'): ?>
                    <div>
                            <input type="hidden" name="action" value="change">
                            <input type="hidden" name="csrf_token" value="<?php print($csrf_token); ?>">
                            <input class="form_input" type="submit" value="確定">
                        </div>
                        <!-- 新規登録時 -->             
                        <?php else: ?>
                    <div>
                        <input type="hidden" name="csrf_token" value="<?php print($csrf_token); ?>">
                        <input type="hidden" name="action" value="join">
                        <input class="form_input" type="submit" value="確定">
                    </div>
                    <?php endif; ?> 
                </form>
        </div>
    </section>
    
    
<?php 
include(dirname(__FILE__) . '/../common/footer.html');