<?php
if ($_REQUEST['action'] !== 'join' && $_REQUEST['action'] !== 'change_member' && $_REQUEST['action'] !== 'change_info' && $_REQUEST['action'] !== 'check') {
    header('Location: /pets/index.php');
    exit();
}
session_start();
include(dirname(__FILE__) . '/../common/php_header.php');
//メンバー情報
//各入力項目を変数に代入
if ($_REQUEST['action'] === 'join') {
    //新規登録の場合
    $email = $_SESSION['POSTindex']['email'];
    $name = $_SESSION['POSTmember']['name'];
    $favoritetype = $_SESSION['POSTmember']['favoritetype'];
    //registrationテーブルのmember_idとパスワードを取得
    $get_registration = $db->prepare('SELECT member_id, password FROM registration WHERE email=?');
    $get_registration->execute(array(
        $email,
    ));
    $registration = $get_registration->fetch(PDO::FETCH_ASSOC);
    $member_id = $registration['member_id'];
    $password = $registration['password'];
} else if ($_REQUEST['action'] === 'change_member') {
    //会員情報変更の場合
    $name = $_SESSION['POSTmember']['name'];
    $favoritetype = $_SESSION['POSTmember']['favoritetype'];
} else if ($_REQUEST['action'] === 'check'){
    //会員情報照会の場合
    //好きな動物を取得
    $get_favoritetype = $db->prepare('SELECT type FROM favoriteAnimals WHERE email=?');
    $get_favoritetype->execute(array($email));
    $favoritetype = $get_favoritetype->fetchAll(PDO::FETCH_COLUMN);
}
$search = ['dog', 'cat', 'small', 'reptiles', 'others'];
$replace = ['犬', '猫', '小動物', '爬虫類', 'その他'];
if ($_REQUEST['action'] !== 'change_info') {
    //好きな動物をカンマで繋いで文字列にする
    $favorite_string = implode(', ', $favoritetype);
    //画面表示用に日本語に置換
    $JP_favorite_string = str_replace($search, $replace, $favorite_string);
}
//ペット情報が登録されていればペット登録数を取得
if (!empty($_SESSION['POSTinfo'])) {
    //新規登録andペット情報変更の場合
    $number = $_SESSION['POSTinfo']['number'];
    for ($i = 1; $i <= $number; $i++) {
        $pname = $_SESSION['POSTinfo']['pname'][$i];
        $type = $_SESSION['POSTinfo']['type'][$i];
        $image = $_SESSION['POSTinfo']['image'][$i];
        $pets[$i-1] = array(
            'pet_name'=>$pname,
            'pet_type'=>$type,
            'pet_image'=>$image,
        );
    }
} else if ($_REQUEST['action'] === 'check') {
    //情報照会時
    $number = count($pets);
}
//登録ボタンが押されたらtokenを照合してデータベースに保存
if (!empty($_POST)) {
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] == $_SESSION['csrf_token']) {
        //新規登録
        if ($_REQUEST['action'] === 'join') {
            //会員情報の登録
            $statement = $db->prepare('INSERT INTO members SET member_id=?, email=?, password=?, name=?, date_join=NOW(), date_modified=NOW(), latest_login=NOW(), try_login=0');
            $result = $statement->execute(array(
                $member_id,
                $email,
                $password,
                $name,
            ));
        }        
        //ペット情報の登録
        if (isset($number)) {
            $statement = $db->prepare('INSERT INTO pets SET email=?, pet_name=?, pet_type=?, pet_image=?');
            foreach ($pets as $pet) {
                $statement->execute(array(
                    $email,
            // セッション変数にログイン情報を保持
            $_SESSION['login']['email'] = $email;
            $_SESSION['login']['member_id'] = $member_id;
                    $pet['pet_name'],
                    $pet['pet_type'],
                    $pet['pet_image'],
                ));
            }
        }
    } else if ($_REQUEST['action'] === 'change_member') {
        $update_member = $db->prepare('
        UPDATE members
        SET name=?,
        date_modified=NOW()
        WHERE email=?
        ');
        $update_member->execute(array(
            $name,
            $email,
        ));
        $delete_favorite = $db->prepare('
        DELETE FROM favoriteAnimals
        WHERE email=?
        ');
        $delete_favorite->execute(array(
            $email,
        ));
        $insert_favorite = $db->prepare('
        INSERT INTO favoriteAnimals
        SET email=?, type=?         
        ');
        foreach ($favoritetype as $type) {
            $insert_favorite->execute(array(
                $email,
                $type,
            ));
        }
    } else if ($_REQUEST['action'] === 'change_info') {
        $delete_pet = $db->prepare('
        DELETE FROM pets
        WHERE email=?
        ');
        $delete_pet->execute(array(
            $email
        ));
        $insert_pet = $db->prepare('
        INSERT INTO pets
        SET email=?, pet_name=?, pet_type=?, pet_image=?
        ');
        foreach ($pets as $pet) {
            $insert_pet->execute(array(
                $email,
                $pet['pet_name'],
                $pet['pet_type'],
                $pet['pet_image'],
            ));
        }
    }
    //セッション変数のリセット
    unset($_SESSION['POSTindex']);
    unset($_SESSION['POSTmember']);
    unset($_SESSION['POSTinfo']);
    //登録完了画面に遷移
    header('Location: /pets/complete.php?from=join');
    exit();
}
//CSRF対策
$random = openssl_random_pseudo_bytes(16);
$csrf_token = bin2hex($random);
$_SESSION['csrf_token'] = $csrf_token;
include(dirname(__FILE__) . '/../common/html_header.php');
?>

<section class="contents">
    <div class="form_container">

        <?php if ($_REQUEST['action'] === 'check'): ?>
        <t1 class="form_t1">登録情報照会</t1>
        <p class="form_p">変更する場合は<br>
        該当箇所の変更ボタンをクリックしてください</p>

        <?php else: ?>
        <t1 class="form_t1">確認</t1>
        <p class="form_p">以下でお間違えなければ<br>
        登録ボタンをクリックしてください</p>
        <?php endif; ?>
        
            <form class="form_form" action="" method="POST">
            <?php if ($_REQUEST['action'] !== 'change_info'): ?>
                <div>
                    <p class="form_label">ニックネーム</p>
                    <p class="form_check"><?php print(htmlspecialchars($name, ENT_QUOTES)); ?></p>  
                </div>
                <div>
                    <p class="form_label">e-mailアドレス</p>
                    <p class="form_check"><?php print(htmlspecialchars($email, ENT_QUOTES)); ?></p>  
                </div>
                <div>
                    <p class="form_label">パスワード</p>
                    <p class="form_check">【非表示】</p>  
                </div>
                <div>
                    <p class="form_label">好きな動物</p>
                    <p class="form_check"><?php 
                    print(htmlspecialchars($JP_favorite_string, ENT_QUOTES)); ?></p>
                </div>

            <?php endif;
                if (isset($number)):                
                foreach($pets as $index => $pet):?>

                <div>
                    <t2 class="form_t2"><?php print($index+1); ?>匹目のペット</t2>
                </div>
                <div>
                    <p class="form_label">ペットの名前</p>
                    <p class="form_check"><?php 
                    print(htmlspecialchars($pet['pet_name'], ENT_QUOTES)); ?></p>  
                </div>


                <div>
                    <p class="form_label">ペットの種類</p>
                    <p class="form_check"><?php 
                    print(str_replace($search, $replace, $pet['pet_type'])); ?></p>
                </div>
                <div>
                    <p class="form_label">ペットの写真</p>
                    <div>
                        <?php if (!is_null($pet['pet_image'])): ?>
                        <img class="form_image" <?php print('src="/pets/images/' . $pet['pet_image'] . '"') ?>>
                        <?php endif; ?>
                    </div>
                </div>

                <?php 
                endforeach; 
                endif; ?>


            <?php if ($_REQUEST['action'] === 'check'): ?>
                <div>
                    <a class="form_label form_label_red" href="/pets/join/member.php?action=change">←会員情報(ニックネーム/好きな動物)を変更する</a><br>
                    <a class="form_label form_label_red" href="/pets/join/info.php?action=change">←ペット情報を変更する</a><br>
                    <a class="form_label form_label_red" href="/pets/new_post.php">変更しない</a>
                </div>
            
            <?php else: ?>

            <?php if ($_REQUEST['action'] === 'join'): ?>
                <div>
                    <a class="form_label form_label_red" href="/pets/join/member.php?action=rewrite">←会員情報の入力に戻る</a><br>
                <?php if (isset($number)): ?>    
                        <a class="form_label form_label_red" href="/pets/join/info.php?action=rewrite">←ペット情報の入力に戻る</a><br>
                <?php endif; ?>
                </div>
            
            <?php elseif ($_REQUEST['action'] === 'change_member'): ?>
                <div>
                    <a class="form_label form_label_red" href="/pets/join/member.php?action=change">←会員情報の入力に戻る</a>
                </div>

            <?php elseif ($_REQUEST['action'] === 'change_info'): ?>
                <div>
                    <a class="form_label form_label_red" href="/pets/join/info.php?action=change">←ペット情報の入力に戻る</a>
                </div>
            <?php endif; ?>
            <?php endif; ?>
                
            <div>
                    <input type="hidden" name="csrf_token" value="<?php print($csrf_token); ?>">
                    <input type="hidden" name="submit" value="submit">
                    <input class="form_input" type="submit" value="登録">
                </div>
            </form>
    </div>
</section>
<?php 
include(dirname(__FILE__) . '/../common/footer.html');