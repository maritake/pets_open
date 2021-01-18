<?php
//loginせずに見る場合（全ての投稿を表示）
include(dirname(__FILE__) . '/common/php_header.php');
//全投稿を取得
$sql_posts = 'SELECT id, member_id, photo, comment, pet_id, good, date_created FROM posts ORDER BY id DESC';
$get_posts = $db->query($sql_posts);
$posts = $get_posts->fetchAll(PDO::FETCH_ASSOC);
include(dirname(__FILE__) . '/common/html_header.php');
?>
    <section class="contents">
        <t1 class="explain_t1">Petsとは？？</t1>
        <div class="explain_container">
        <div class="explain">
            <div id="explain_div1">
                <t2>ペットの画像を投稿<br>
                －うちの子を自慢しちゃおう！－</t2>
                <p>ペットを飼っていない場合も大丈夫<br>
                かわいい動物の写真は大歓迎！</p>
            </div>
            <div id="explain_div2">
                <t2>好きな動物の投稿をチェック<br>
                －見たい動物の投稿だけを閲覧できる！－</t2>
                <p>投稿を［犬／猫／小動物／爬虫類／その他］に分類<br>
                興味のある種類の動物の投稿だけを閲覧可能！
                </p>
            </div>
            <div id="explain_div3">
                <t2>お気に入りのペットを登録<br>
                －お気に入りの投稿はかかさずチェック！－</t2>
                <p>あなたの推しペットはどの子？？！</p>
            </div>
        </div>
            <t1 class="explain_t1">さあ、今すぐ<a href="/pets/join/index.php?action=join">会員登録！</a></t1>
            <p>登録／利用は完全無料です</p>
        </div>

        <t2 class="post_t2">新規投稿</t2>
        <div class="post_container">
            <?php foreach($posts as $post): ?>
                <div class="post_post">
                    <?php include(dirname(__FILE__) . '/common/parts_post.php'); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<?php 
include(dirname(__FILE__) . '/common/footer.html');