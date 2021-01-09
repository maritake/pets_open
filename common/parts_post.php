<?php
if (!empty($_SESSION['login'])) {
    $get_good = $db->prepare('SELECT email FROM goods WHERE post_id=?');
    $get_good->execute(array($post['id']));
    $goods = $get_good->fetchAll(PDO::FETCH_COLUMN);
}
?>

<a <?php print('href="/pets/post_detail.php?id=' . htmlspecialchars($post['id'], ENT_QUOTES) . '"'); ?>>
    <img class="post_postimage" <?php print('src="/pets/images/' . htmlspecialchars($post['photo'], ENT_QUOTES) . '"'); ?>>
</a>

<?php if (!empty($_SESSION['login'])): ?>
<div class="post_good
<?php if(in_array($email, $goods)) {
    print(' post_good_selected');
}
?>
 <?php print('" data-postid="' . htmlspecialchars($post['id'], ENT_QUOTES) . '" data-good="' . count($goods) . '"'); ?>>
    <?php print(count($goods)); ?>
</div>
<?php endif; ?>