<?php
//共通phpを取得
include(dirname(__FILE__) . '/common/php_header.php');

//ページ毎のphpを記述
//好きな動物を取得
$get_favoritetypes = $db->prepare('SELECT type FROM favoriteAnimals WHERE member_id=?');
$get_favoritetypes->execute(array($member_id));
$favoritetypes = $get_favoritetypes->fetchAll(PDO::FETCH_COLUMN);

//好きな動物を日本語文字列に変換
$str_favoritetypes = implode(', ', $favoritetypes);
$search = ['dog', 'cat', 'small', 'reptiles', 'others'];
$replace = ['犬', '猫', '小動物', '爬虫類', 'その他'];
$JP_favoritetypes = str_replace($search, $replace, $str_favoritetypes);

// 好きな動物の投稿を取得
$sql_posts = 
'SELECT po.id, po.member_id, po.photo, po.comment, po.pet_id, po.date_created 
FROM posts po LEFT OUTER JOIN pets pe 
ON po.pet_id=pe.id 
WHERE pe.pet_type=?';
for ($i=0; $i < count($favoritetypes)-1; $i++) {
    $sql_posts .= ' OR pe.pet_type=?';
}
//その他を選択した場合はペット以外の投稿も表示
if (in_array('others', $favoritetypes)) {
    $sql_posts .= ' OR po.pet_id=0';
}

$sql_posts .= ' ORDER BY po.id DESC';
$get_posts = $db->prepare($sql_posts);
$get_posts->execute($favoritetypes);

$posts = $get_posts->fetchAll(PDO::FETCH_ASSOC);

include(dirname(__FILE__) . '/common/html_header.php');
?>
    <section class="contents">
        <t2 class="post_t2">新規投稿 #<?php print($JP_favoritetypes); ?></t2>
        <div class="post_container">
            <?php foreach($posts as $post):?>
                <div class="post_post">
                    <?php include(dirname(__FILE__) . '/common/parts_post.php'); ?> 
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
<?php 
include(dirname(__FILE__) . '/common/footer.html');