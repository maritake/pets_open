<?php
include(dirname(__FILE__) . '/common/php_header.php');
$error = NULL;
//ペットの名前と種類を取得
$get_petsname = $db->prepare('SELECT id, pet_name FROM pets WHERE email=?');
$get_petsname->execute(array($_SESSION['login']['email']));
$petsname = $get_petsname->fetchAll(PDO::FETCH_ASSOC);
// POSTされた時にエラーチェックする
if (!empty($_POST)) {
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] == $_SESSION['csrf_token']) {
        //コメントの長さを確認
        if (!($_POST['comment'] === '')) {
            if (mb_strlen($_POST['comment']) > 100) {
                $error['comment'] = 'length';
            }
        }
        //写真添付の有無の確認
        if (!is_uploaded_file($_FILES['picture']['tmp_name'])){
            $error['picture'] = 'blunk';
        } else {
            //ファイルの種類を確認
            $ext = substr($_FILES['picture']['name'], -3);
            if ($ext != 'png' && $ext != 'jpeg' && $ext != 'jpg' && $ext !='gif' && $ext != 'JPG') {
                $error['picture'] = 'type';
            } else {
                //一時ファイルを保存ファイルにコピーできたか確認
                //ファイル名のみを取得（ディレクトリトラバーサル対策）
                $image = date('YmdHis') . basename($_FILES['picture']['name']);
                if (!move_uploaded_file($_FILES['picture']['tmp_name'], "images/" . $image)) {
                    $error['picture'] = 'save';
                }
            }
        }
    }
    //エラーがなければデータベースに登録して投稿を完了
    if(!isset($error)) {
        $statement = $db->prepare('INSERT INTO posts SET email=?, photo=?, comment=?, pet_id=?, good=0, date_created=NOW()');
        $statement->execute(array(
            $_SESSION['login']['email'],
            $image,
            $_POST['comment'],
            $_POST['petid'],
        ));
        header('Location: /pets/complete.php?from=post');
        exit();
    }
}
//CSRF対策
$random = openssl_random_pseudo_bytes(16);
$csrf_token = bin2hex($random);
$_SESSION['csrf_token'] = $csrf_token;
include(dirname(__FILE__) . '/common/html_header.php');
?>

<section class="contents">
    <div class="form_container">
        <t1 class="form_t1">新規投稿</t1>
            <form class="form_form" action="" method="POST" enctype="multipart/form-data">
                <div>
                    <!-- 未選択の場合 -->
                    <input type="hidden" name="petid" value="0">
                    <?php if (count($petsname) >= 1):
                         foreach ($petsname as $petname): ?>
                    <input class="form_input select" type="radio" id="<?php print(htmlspecialchars($petname['pet_name'], ENT_QUOTES)); ?>" name="petid" value=<?php print(htmlspecialchars($petname['id'], ENT_QUOTES)); ?>>
                    <label class="form_label selectlabel" for="<?php print(htmlspecialchars($petname['pet_name'], ENT_QUOTES)); ?>"><?php print(htmlspecialchars($petname['pet_name'], ENT_QUOTES));?></label><br>
                    <?php endforeach; 
                        endif; ?>
                    <!-- ペット以外の投稿の場合 -->
                    <input class="form_input select" type="radio" id="other" name="petid" value="0">
                    <label class="form_label selectlabel" for="other">登録したペット以外の動物</label><br> 
                </div>
                <div>
                    <label class="form_label">ペットの写真</label><br>
                    <span class="form_span"> ※画像ファイルをアップしてください</span><br>
                    <input type="file" name="picture"><br>
                    <?php if ($error['picture'] === 'save'): ?>
                    <p class="error">
                        ※画像が上手くアップロードされませんでした<br>
                        ※再度画像をアップロードしてください
                    </p>
                    <?php endif; ?>
                    <?php if ($error['picture'] === 'type'): ?>
                    <p class="error">
                        ※以下のいずれかの形式の画像をアップしてください<br>
                        　.png，.jpeg，.jpg，.gif
                    </p>
                    <?php endif; ?>
                    <?php if ($error['picture'] === 'blunk'): ?>
                    <p class="error">
                        ※投稿する画像を選択してください
                    </p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="form_label" for="comment">コメント</label><br>
                    <span class="form_span">※任意，200文字以内</span><br>
                    <input class="form_input" type="textfield" name="comment" id="comment"> 
                    <?php if ($error['comment'] === 'length'): ?>
                    <p class="error"> ※コメントは200字以内で入力してください</p> 
                    <?php endif; ?>
                </div>
                <div>
                        <input type="hidden" name="csrf_token" value="<?php print($csrf_token); ?>">
                        <input class="form_input" type="submit" value="投稿する">
                </div> 
            </form>
    </div>
</section>
    
<?php 
include(dirname(__FILE__) . '/common/footer.html');