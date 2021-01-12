<?php
include(dirname(__FILE__) . '/common/php_header.php');
//クリックされた投稿を取得
$sql_post = 
'SELECT m.name, po.email, po.pet_id, pe.pet_name, pe.pet_image, po.photo, po.comment, po.good, po.date_created, COUNT(*) AS good 
FROM posts po LEFT OUTER JOIN pets pe
ON po.pet_id=pe.id
LEFT OUTER JOIN members m 
ON po.email=m.email
INNER JOIN goods g
ON po.id=g.post_id
WHERE po.id=?';
$get_clicked_post = $db->prepare($sql_post);
$get_clicked_post->execute(array($_REQUEST['id']));
$clicked_post = $get_clicked_post->fetch(PDO::FETCH_ASSOC);
// 同じペットの投稿を取得
//ペット以外の投稿だった場合(pet_id=0)は取得しない
if ($clicked_post['pet_id'] !== 0) {
    $sql_post = 
    'SELECT id, photo, comment, good, date_created 
    FROM posts 
    WHERE pet_id=? AND id NOT IN (?) 
    ORDER BY id DESC';
    $get_same_posts = $db->prepare($sql_post);
    $get_same_posts->execute(array($clicked_post['pet_id'], $_REQUEST['id']));
    $same_posts = $get_same_posts->fetchAll(PDO::FETCH_ASSOC);
}
// 同じ会員の他のペット情報を取得
$sql_post = 'SELECT id, pet_name, pet_image FROM pets WHERE email=? AND id NOT IN (?) ORDER BY id DESC';
$get_same_pets = $db->prepare($sql_post);
$get_same_pets->execute(array($clicked_post['email'], $clicked_post['pet_id']));
$same_pets = $get_same_pets->fetchAll(PDO::FETCH_ASSOC);
//ペット以外の動物の投稿を取得
$sql =
'SELECT id, photo, comment, pet_id, date_created
FROM posts 
WHERE pet_id=0 AND email=? NOT id=?
ORDER BY id DESC';
$get_other_posts = $db->prepare($sql);
$get_other_posts->execute(array(
    $clicked_post['email'],
    $_REQUEST['id']
));
$other_posts = $get_other_posts->fetchAll(PDO::FETCH_ASSOC);
//お気に入りペットの取得
$get_like_pets = $db->prepare('
SELECT favorite_pet_id 
FROM favoritePets 
WHERE email=?
');
$get_like_pets->execute(array(
    $_SESSION['login']['email'],
));
$like_pets = $get_like_pets->fetchAll(PDO::FETCH_COLUMN);
//投稿削除
if ($clicked_post['email'] === $_SESSION['login']['email'] && $_POST['delete'] === 'ok') {
    $delete_post = $db->prepare('
    DELETE FROM posts
    WHERE id=?
    ');
    $delete_post->execute(array($_REQUEST['id']));
    //削除後はmypageに遷移
    header('Location: /pets/mypage.php');
    exit();
}
include(dirname(__FILE__) . '/common/html_header.php');
?>
    <section class="contents">
        <?php if ($clicked_post['pet_id'] != 0): ?>
        <t2 class="post_t2">
                <img class="icon" <?php print('src="/pets/images/' . $clicked_post['pet_image'] . '"'); ?>>
            <?php print(htmlspecialchars($clicked_post['pet_name'])); ?>
        </t2>
        <?php endif; ?>
        <p>
            投稿日：<?php print(htmlspecialchars($clicked_post['date_created'])); ?>
        </p>
        <p>
            いいね：<?php print(htmlspecialchars($clicked_post['good'])); ?>
        </p>
        <img class="post_bigimage" <?php print('src="/pets/images/' . htmlspecialchars($clicked_post['photo'], ENT_QUOTES) . '"'); ?>>
        <div>
            <?php print(htmlspecialchars($clicked_post['comment'], ENT_QUOTES)); ?>
        </div>
        <?php if ($clicked_post['email'] === $_SESSION['login']['email']): ?>
        <div id="post_delete">
            この投稿を削除する
        </div>
        <div id="post_delete_hidden" class="post_hidden">
            <form method="POST" action="">
                <p>本当に削除してよろしいですか？</p>
                <input type="hidden" name="delete" value="ok">
                <input class="post_delete_confirm" type="submit" value="削除する"><br>
                <p class="post_delete_confirm">戻る</p>
            </form>
        </div>
        <?php endif; ?>

        <!-- 同じペットの他の投稿を表示 -->
        <!-- ページを開いた時にお気に入り状態を反映させる  -->
        <?php if (!empty($_SESSION['login']) && $clicked_post['email'] !== $_SESSION['login']['email'] && $clicked_post['pet_id'] != 0):
        if (in_array($clicked_post['pet_id'], $like_pets)): ?>
            <div class="pet_favorite pet_favorite_selected" data-petid="<?php print($clicked_post['pet_id']); ?>">
                <?php print(htmlspecialchars($clicked_post['pet_name'], ENT_QUOTES)); ?><span class="post_span"> のお気に入りを解除する</span>
            </div>
        <?php else: ?>
            <div class="pet_favorite" data-petid="<?php print($clicked_post['pet_id']); ?>">
                <?php print(htmlspecialchars($clicked_post['pet_name'], ENT_QUOTES)); ?><span class="post_span"> をお気に入り登録する</span>
            </div>
        <?php endif;
        endif;
        if ($clicked_post['pet_id'] !=0 && count($same_posts) !== 0): ?>

        <t2 class="post_t2">
            <img class="icon" <?php print('src="/pets/images/' . $clicked_post['pet_image'] . '"'); ?>>
            <?php print(htmlspecialchars($clicked_post['pet_name'])); ?>の他の投稿
        </t2>
        

        <div class="post_container">
            <?php foreach($same_posts as $post): ?>
                <div class="post_post">
                    <?php include(dirname(__FILE__) . '/common/parts_post.php'); ?> 
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

<!-- 同じ会員の他のペットの投稿を表示 -->
        <?php
            if (!empty($same_pets) || count($other_posts) !==0):
        ?>
        <t2 class="post_t2">
            <?php print(htmlspecialchars($clicked_post['name'])); ?>さんの他のペットの投稿を見る
        </t2><br>
        <?php foreach($same_pets as $pet): ?>
            <?php //if ($pet['id'] !== 0): ?>
            <t3 class="post_t3">
                <img class="icon" <?php print('src="/pets/images/' . $pet['pet_image'] . '"'); ?>>
                <?php print(htmlspecialchars($pet['pet_name'], ENT_QUOTES)); ?>
            </t3>

            <?php if (!empty($_SESSION['login']) && $clicked_post['email'] !== $_SESSION['login']['email']):
            if (in_array($pet['id'], $like_pets)): ?>
            <div class="pet_favorite pet_favorite_selected" data-petid="<?php print($pet['id']); ?>">
                <?php print(htmlspecialchars($pet['pet_name'], ENT_QUOTES)); ?><span class="post_span"> のお気に入りを解除する</span>
            </div>
            <?php else: ?>
            <div class="pet_favorite" data-petid="<?php print($pet['id']); ?>">
                <?php print(htmlspecialchars($pet['pet_name'], ENT_QUOTES)); ?><span class="post_span"> をお気に入り登録する</span>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <div class="post_container">
                <?php 
                    //ペット毎の投稿を取得
                    $sql_post = 'SELECT id, photo, comment, good, date_created FROM posts WHERE pet_id=? ORDER BY id DESC';
                    $get_related_posts = $db->prepare($sql_post);
                    $get_related_posts->execute(array($pet['id']));
                    $related_posts = $get_related_posts->fetchAll(PDO::FETCH_ASSOC);
                                        
                    foreach($related_posts as $post): ?>
                    <div class="post_post">
                        <?php include(dirname(__FILE__) . '/common/parts_post.php'); ?>
                    </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>


            <?php if (count($other_posts) !== 0): ?>
            <t3 class="post_t3">登録したペット以外の投稿</t3>
            <div class="post_container">
                <?php 
                    foreach($other_posts as $post): ?>
                    <div class="post_post">
                        <?php include(dirname(__FILE__) . '/common/parts_post.php'); ?>
                    </div>
                    <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php endif; ?>
        <div class="last"></div>
    </section>
                    </div>
    
<?php 
include(dirname(__FILE__) . '/common/footer.html');