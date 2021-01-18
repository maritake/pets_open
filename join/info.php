<?php
if ($_REQUEST['action'] !== 'join' && $_REQUEST['action'] !== 'rewrite' && $_REQUEST['action'] !== 'change') {
    header('Location: /pets/index.php');
    exit();
}
session_start();
include(dirname(__FILE__) . '/../common/php_header.php'); 
$error = NULL;
//ペットの数
if ($_REQUEST['action'] === 'change') {
    //登録変更時
    $number = count($pets);
    foreach ($pets as $index => $pet) {
        $pname[$index+1] = $pet['pet_name'];
        $checked[$pet['pet_type']][$index+1] = 'checked';
        $image[$index+1] = $pet['pet_image'];
    }
} else if ($_REQUEST['action'] === 'rewrite' && isset($_SESSION['POSTinfo'])) {
    //書き直し時
    $number = $_SESSION['POSTinfo']['number'];
    //ペットの種類のチェックを反映するために変数にチェック状態を格納
    for ($i = 1; $i <= $number; $i++) {
        $pname[$i] = $_SESSION['POSTinfo']['pname'][$i];
        $checked[$_SESSION['POSTinfo']['type'][$i]][$i] = 'checked';
        $image[$i] = $_SESSION['POSTinfo']['image'][$i];
    }
} else {
    //新規登録時
    $number = $_SESSION['POSTmember']['number'];
}
// POSTされた時にエラーチェックをする
if (!empty($_POST)) {
    for ($i = 1; $i <= $number; $i++) {
        // ペットの名前のエラーチェック
        $pname[$i] = $_POST['pname'][$i];
        //ペットの名前が未入力の場合はエラー
        if ($pname[$i] == '') {
            $error['pname'][$i] = 'blunk';        
        } else if (mb_strlen($pname[$i]) > 15) {
            $error['pname'][$i] = 'long';
        } else {
            //ペットの名前が15文字以内で入力されていれば
            //ペットの種類のチェックを反映するために変数にチェック状態を格納
            if (isset($_POST['type'][$i])) {
                $type[$i] = $_POST['type'][$i];
                $checked[$type[$i]][$i] = 'checked';
            } else {
                //ペットの種類が未登録の場合にはその他にする
                $type[$i] = 'others';
                $checked['others'][$i] = 'checked';
            }        
            //画像のアップロードと確認
            if (is_uploaded_file($_FILES["picture${i}"]['tmp_name'])) {
                //ファイルの種類を確認
                $ext = substr($_FILES["picture${i}"]['name'], -3);
                if ($ext != 'png' && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'JPG') {
                    $error['picture'][$i] = 'type';
                } else {
                    //一時ファイルを保存ファイルにコピーできたか確認
                    //ファイル名のみを取得（ディレクトリトラバーサル対策）
                    $image[$i] = date('YmdHis') . basename($_FILES["picture${i}"]['name']);
                    if (!move_uploaded_file($_FILES["picture${i}"]['tmp_name'], dirname(__FILE__) . '/../images/' . $image[$i])) {
                        $error['picture'][$i] = 'save';
                    } 
                }
            } else {
                //画像をアップロードしていない場合
                $image[$i] = $image[$i] ? $image[$i] : NULL;
            }
        }
    }
    // エラーがなければセッションに値を保存して次のページにジャンプ
    if (!isset($error)) {
        $_SESSION['POSTinfo']['pname'] = $pname;
        $_SESSION['POSTinfo']['type'] = $type;
        $_SESSION['POSTinfo']['image'] = $image;
        $_SESSION['POSTinfo']['number'] = $number;    
        if ($_REQUEST['action'] === 'change') {
            header('Location: /pets/join/check.php?action=change_info');
            exit(); 
        } else {
            header('Location: /pets/join/check.php?action=join');
            exit(); 
        }
    }
}

include(dirname(__FILE__) . '/../common/html_header.php'); 
?>

    <section class="contents">
        <div class="form_container">
            <t1 class="form_t1">ペット情報登録</t1>
            <p class="form_p">ペット情報を入力し、<br>
            確認ボタンを<br>
            クリックしてください<br>
            <span class="form_span">※ペット情報の入力は任意です</span></p>
                <form class="form_form" action="" method="POST" enctype="multipart/form-data" >

                <?php for ($i = 1; $i <= $number; $i++): ?>
                    <div>
                        <t2 class="form_t2"><?php print($i); ?>匹目のペット情報</t2><br>
                        <label class="form_label" for="pname">ペットの名前</label><br>
                        <span class="form_span">※ペット情報を登録する場合は必須，15文字以内</span><br>
                        <input class="form_input" type="text" name="pname[<?php print($i); ?>]" id="pname" placeholder="例：たま" value="<?php print(htmlspecialchars($pname[$i], ENT_QUOTES)); ?>"><br>  
                        <!-- 未入力の場合のエラーメッセージ -->
                        <?php if ($error['pname'][$i] === 'blunk'): ?>
                        <p class="error"> ※ペットの名前を入力してください</p> 
                        <?php endif; ?>                        
                        <!-- 長すぎる場合はエラーメッセージ -->
                        <?php if ($error['pname'][$i] === 'long'): ?>
                        <p class="error"> ※ペットの名前は15字以内で入力してください</p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="form_label">ペットの種類</label><br>
                        <span class="form_span">※任意，未入力の場合はその他になります</span><br>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="radio" name="type[<?php print($i); ?>]" value="dog" <?php print($checked['dog'][$i]); ?>>犬
                        </label><br>  
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="radio" name="type[<?php print($i); ?>]" value="cat" <?php print($checked['cat'][$i]); ?>>猫
                        </label><br>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="radio" name="type[<?php print($i); ?>]" value="small" <?php print($checked['small'][$i]); ?>>小動物<span class="form_span">（うざぎ，ハムスター，鳥など）</span>
                        </label><br>
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="radio" name="type[<?php print($i); ?>]" value="reptiles" <?php print($checked['reptiles'][$i]); ?>>爬虫類
                        </label><br>  
                        <label class="form_label selectlabel">
                            <input class="form_input select" type="radio" name="type[<?php print($i); ?>]" value="others" <?php print($checked['others'][$i]); ?>>その他
                        </label><br>  
                    </div>
                    <div>
                        <label class="form_label">ペットの写真</label><br>
                        <span class="form_span"> ※任意，画像ファイルをアップしてください</span><br>
                        <?php 
                        if (isset($image[$i])):
                            print('<img class="form_image" src="/pets/images/' . $image[$i] . '">');
                        else:
                        ?>
                        <input class="form_input" type="file" name="picture<?php print($i); ?>"><br>
                        <?php 
                        endif;
                        if ($error['picture'][$i] === 'save'): ?>
                        <p class="error">
                            ※画像が上手くアップロードされませんでした<br>
                            ※再度画像をアップロードしてください
                        </p>
                        <?php endif; 
                        if ($error['picture'][$i] === 'type'): ?>
                        <p class="error">
                            ※以下のいずれかの形式の画像をアップしてください<br>
                            　.png，.jpeg，.jpg，.gif
                        </p>
                        <?php endif; ?>
                    </div>
                        <?php endfor; ?>
                    <div>
                        <?php if ($_REQUEST['action'] !== 'change'): ?>
                        <a class="form_label form_label_red" href="/pets/join/member.php?action=rewrite">←戻る</a>
                        <?php endif; ?>
                        <input class="form_input" type="submit" value="確認">
                    </div>    
                </form>
        </div>
    </section>

<?php 
include(dirname(__FILE__) . '/../common/footer.html');