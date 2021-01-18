<?php
if ($_REQUEST['action'] !== 'join' && $_REQUEST['action'] !== 'rewrite' && $_REQUEST['action'] !== 'change') {
    header('Location: /pets/index.php');
    exit();
}
session_start();
include(dirname(__FILE__) . '/../common/php_header.php'); 
$error = NULL;
// POSTされた時にエラーチェックをする
if (!empty($_POST)) {    
    $name = $_POST['name'];
    // ニックネームのエラーチェック
    if ($name === '') {
        $error['name'] = 'blunk';
    } else if (mb_strlen($name) > 20) {
        $error['name'] = 'long';
    }
    //好きな動物のチェックを反映するために変数にチェック状態を格納
    if (isset($_POST['favoritetype']) && is_array($_POST['favoritetype'])) {
        foreach ($_POST['favoritetype'] as $type) {
            $checked[$type] = 'checked';
        }        
    } else {
        //好きな動物が未選択の場合は全て選択したことにする
        $_POST['favoritetype'] = ['dog', 'cat', 'small', 'reptiles', 'others'];
        foreach ($_POST['favoritetype'] as $type) {
            $checked[$type] = 'checked';
        }
    }
    // エラーがなければセッションに値を保存して次のページにジャンプ
    if (!isset($error)) {
        $_SESSION['POSTmember'] = $_POST;    
        if ($_POST['go_petinfo'] === 'yes') {
            //ペット情報の登録をする場合
            header('Location: /pets/join/info.php?action=join');
            exit(); 
        } else if ($_REQUEST['action'] === 'change') {
            //登録変更の場合
            header('Location: /pets/join/check.php?action=change_member');
            exit();
        } else if ($_POST['go_petinfo'] === 'no') {
            //ペット情報の登録をしない場合
            unset($_SESSION['POSTinfo']);
            header('Location: /pets/join/check.php?action=join');
            exit();
        } else {
            //ペット情報の登録を未選択の場合（書き直しでペット情報はそのまま）
            header('Location: /pets/join/check.php?action=join');
            exit();
        }   
    }
}
if ($_REQUEST['action'] === 'join') {
    $email = $_SESSION['POSTindex']['email'];
} else if ($_REQUEST['action'] === 'change') {
    //登録変更時
    //好きな動物を取得
    $get_favoritetype = $db->prepare('SELECT type FROM favoriteAnimals WHERE member_id=?');
    $get_favoritetype->execute(array($member_id));
    $favoritetype = $get_favoritetype->fetchAll(PDO::FETCH_COLUMN);
} else if ($_REQUEST['action'] === 'rewrite' && isset($_SESSION['POSTmember'])) {
    //書き直しの処理
    //変数に格納
    $email = $_SESSION['POSTindex']['email'];
    $name = $_SESSION['POSTmember']['name'];
    $favoritetype = $_SESSION['POSTmember']['favoritetype'];
}
//好きな動物のチェックを反映するために変数にチェック状態を格納
if (isset($favoritetype) && is_array($favoritetype)) {
    foreach ($favoritetype as $type) {
        $checked[$type] = 'checked';
    }
}
include(dirname(__FILE__) . '/../common/html_header.php'); 
?>

    <section class="contents">
        <div class="form_container">
            <t1 class="form_t1">会員情報</t1>
            <p class="form_p">以下の項目を記入し、<br>
            次に進むボタンを<br>
            クリックしてください</p>
                <form class="form_form" action="" method="POST">
                    <div>
                        <label class="form_label" for="name">ニックネーム</label><br>
                        <span class="form_span">※必須，20文字以内</span><br>
                        <input class="form_input" type="text" name="name" id="name" placeholder="例：動物好きの太郎" value=<?php print(htmlspecialchars($name, ENT_QUOTES)); ?>><br>  
                        <!-- 未入力の場合のエラーメッセージ -->
                        <?php if ($error['name'] === 'blunk'): ?>
                        <p class="error"> ※ニックネームを入力してください</p>
                        <?php endif; ?>
                        
                        <!-- 長すぎる場合のエラーメッセージ -->
                        <?php if ($error['name'] === 'long'): ?>
                        <p class="error"> ※20字以内で入力してください</p> 
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="form_label" for="email">e-mailアドレス</label><br>
                        <p><?php print(htmlspecialchars($email, ENT_QUOTES)); ?></p>
                    </div>
                    <div>
                        <label class="form_label">好きな動物</label><br>
                        <span class="form_span">※任意，未登録の場合は全選択になります</span><br>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="checkbox" name="favoritetype[]" value="dog" <?php print($checked['dog']); ?>>犬
                        </label><br>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="checkbox" name="favoritetype[]" value="cat" <?php print($checked['cat']); ?>>猫
                        </label><br>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="checkbox" name="favoritetype[]" value="small" <?php print($checked['small']); ?>>小動物
                        </label><br>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="checkbox" name="favoritetype[]" value="reptiles" <?php print($checked['reptiles']); ?>>爬虫類
                        </label><br>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="checkbox" name="favoritetype[]" value="others" <?php print($checked['others']); ?>>その他
                        </label><br>
                    </div>

<!-- 登録変更時                     -->
                    <?php if ($_REQUEST['action'] === 'change'): ?>
                    <div>
                            <input class="form_input" type="submit" value="変更する">
                    </div> 

<!-- 新規登録時 -->
                    <?php else: ?>
                    <div>
                    <label class="form_label">ペット情報を登録しますか？</label><br>
                    <label class="form_label selectlabel">
                        <input class="form_input select" id="form_pet_yes" type="radio" name="go_petinfo" value="yes">登録する
                    </label><br>
                    <div id="form_register_number" class="hidden">
                        <label class="form_label">何匹登録しますか？</label><br>
                        <span class="form_span">※最大5匹登録できます</span><br>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="radio" name="number" value="1" checked>1</label>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="radio" name="number" value="2">2</label>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="radio" name="number" value="3">3</label>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="radio" name="number" value="4">4</label>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="radio" name="number" value="5">5</label><br>
                    </div>
                    <label class="form_label selectlabel">
                        <input class="form_input select" id="form_pet_no" type="radio" name="go_petinfo" value="no">登録しない
                    </label><br>
                    </div>
                        
                    <div>
                            <input class="form_input" type="submit" value="次に進む">
                    </div>
                    <?php endif; ?> 
                </form>
        </div>
    </section>
    
    
<?php 
include(dirname(__FILE__) . '/../common/footer.html');