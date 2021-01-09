<?php
include(dirname(__FILE__) . '/common/php_header.php');
include(dirname(__FILE__) . '/common/html_header.php');
?>
<section class="contents">
    <div class="form_container">

    <?php switch ($_REQUEST['from']):
    case 'post': ?>
        <p class="form_p" class="label">投稿が完了しました！<br></p>
    <?php exit();
    case 'join': ?>
        <p class="form_p" class="label">登録が完了しました！<br></p>
    <?php exit();
    case 'logout': ?>
        <p class="form_p" class="label">ログアウトしました！<br></p>
    <?php exit();
    case 'quit': ?>
        <p class="form_p" class="label">会員登録を削除しました。<br>
        ご利用ありがとうございました。<br>
        またのご利用をお待ちしております。<br>
        ※登録したペットおよびこれまでの投稿は削除されません。削除したい場合は管理人までお問い合わせください。<br>
        お問い合わせ先；管理人Twitterアカウント<br>
        <a id="twitter" href="https://twitter.com/@mrtk1025">こちらにDMをお送りください</a><br>
        </p>
    <?php exit();
    case 'change': ?>
        <p class="form_p" class="label">変更しました！<br></p>
    <?php exit();
    default:
    exit();
    endswitch;
    ?>

    </div>
</section>

<?php 
include(dirname(__FILE__) . '/common/footer.html');