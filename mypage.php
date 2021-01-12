<?php
include(dirname(__FILE__) . '/common/php_header.php');
//登録した年月日を取得
$get_datejoin = $db->prepare('
SELECT date_join 
FROM members
WHERE email=?
');
$get_datejoin->execute(array(
    $email,
));
$datejoin = $get_datejoin->fetch(PDO::FETCH_COLUMN);
include(dirname(__FILE__) . '/common/html_header.php');
?>

    <section class="contents">
        <t2 class="post_t2">
            <?php print(htmlspecialchars($name, ENT_QUOTES) . 'のページ'); ?>
        </t2>
        <p>
            <?php print($datejoin); ?>からpetsを利用しています
        </p>
        
        <?php
        foreach ($pets as $pet):   
            //自分のペットの投稿を取得
            $sql = '
            SELECT id, photo, comment, pet_id, date_created
            FROM posts 
            WHERE pet_id=? ORDER BY id DESC
            ';
            $get_posts = $db->prepare($sql);
            $get_posts->execute(array($pet['id']));
            $posts = $get_posts->fetchAll(PDO::FETCH_ASSOC);

            //ペットのお気に入り登録数を取得
            $get_like_number = $db->prepare('
            SELECT f.email, m.name
            FROM favoritePets f, members m
            WHERE f.email=m.email AND f.favorite_pet_id=?
            ');
            $get_like_number->execute(array(
                $pet['id'],
            ));
            $like_number = $get_like_number->fetchAll(PDO::FETCH_ASSOC);

            //取得した投稿を１つずつ取り出す
            $count = NULL;
            foreach($posts as $index=>$post):
                switch ($index):
                    case 0:
                ?>
                <div class="post_multicontainer">
                    <t2>
                        <img class="icon" <?php print('src="/pets/images/' . $pet['pet_image'] . '"'); ?>>
                        <?php print(htmlspecialchars($pet['pet_name'], ENT_QUOTES)); ?>
                    </t2>
                    <div>
                        投稿数
                        <?php print(count($posts)); ?>
                    </div>
                    <div>
                        お気に入り登録数
                        <?php print(count($like_number)); ?>
                    </div>
                    <div class="post_multicontents">
                        <div class="post_post">
                          <?php include(dirname(__FILE__) . '/common/parts_post.php'); ?>      
                        </div>
                        <div class="post_images_comment">
                            <div class="post_comment">
                                <p>
                                    <?php print(htmlspecialchars($post['comment'] . '(' . $post['date_created'] . ')', ENT_QUOTES)); ?>
                                </p>                            
                            </div>        
                            <div class="post_images">        
            <?php 
                break;
                case 1:
                case 2:
            ?>
                            <?php include(dirname(__FILE__) . '/common/parts_smallpost.php'); ?> 
            <?php 
                break;
                case 3:
            ?>
                            <?php include(dirname(__FILE__) . '/common/parts_smallpost.php'); ?> 
                            </div>
                        </div>
                    </div>
            <?php
                break;
                case 4:  
            ?>
                        <div class="post_additionalcontents">
                            <?php include(dirname(__FILE__) . '/common/parts_smallpost.php'); ?> 
            <?php 
                break;
                default:
            ?>                            
                            <?php include(dirname(__FILE__) . '/common/parts_smallpost.php'); ?> 
            <?php 
                break;
                endswitch;
                endforeach;
                //投稿数が4以下の場合は空のboxで埋める
                if (count($posts) < 4):
                    for ($i = 1; $i <= (4 - count($posts)); $i++):
            ?>
                        <div class="post_small_post"></div>
            <?php endfor; ?>
                <!-- 投稿数が4以下の場合の.post_imagesの閉じタグ -->
                    </div>
                <!-- 投稿数が4以下の場合の.post_images_commentの閉じタグ -->
                    </div>
                <!-- 投稿数が4以下の場合の.post_multicontentsの閉じタグ -->
                    </div>
            <?php elseif(count($posts) >= 5): ?>
                <!-- 投稿数が5以上だった場合の.post_additionalcontentsの閉じタグ -->
                </div>
                <?php endif; ?>
                <!-- .multicontainerの閉じタグ -->
            </div>
            <!-- ペット毎の処理の終了-->
            <?php 
            endforeach; 
            //ペット以外の動物の投稿を取得
            $sql =
            'SELECT id, photo, comment, pet_id, date_created
            FROM posts 
            WHERE pet_id=0 AND email=?
            ORDER BY id DESC';
            $get_posts = $db->prepare($sql);
            $get_posts->execute(array($email));
            $posts = $get_posts->fetchAll(PDO::FETCH_ASSOC);
            if (count($posts) !==0) :

                $count = NULL;
                foreach($posts as $index=>$post):
                    switch ($index):
                        case 0:
                    ?>
                    <div class="post_multicontainer">
                        <t2>
                            登録したペット以外の動物
                        </t2>
                        <div>
                            投稿数
                            <?php print(count($posts)); ?>
                        </div>
                        <div class="post_multicontents">
                            <div class="post_post">
                              <?php include(dirname(__FILE__) . '/common/parts_post.php'); ?>      
                            </div>
                            <div class="post_images_comment">
                                <div class="post_comment">
                                    <p>
                                        <?php print(htmlspecialchars($post['comment'] . '(' . $post['date_created'] . ')', ENT_QUOTES)); ?>
                                    </p>                            
                                </div>        
                                <div class="post_images">        
                <?php 
                    break;
                    case 1:
                    case 2:
                ?>
                                <?php include(dirname(__FILE__) . '/common/parts_smallpost.php'); ?> 
                <?php 
                    break;
                    case 3:
                ?>
                                <?php include(dirname(__FILE__) . '/common/parts_smallpost.php'); ?> 
                                </div>
                            </div>
                        </div>
                <?php
                    break;
                    case 4:  
                ?>
                            <div class="post_additionalcontents">
                                <?php include(dirname(__FILE__) . '/common/parts_smallpost.php'); ?> 
                <?php 
                    break;
                    default:
                ?>                            
                                <?php include(dirname(__FILE__) . '/common/parts_smallpost.php'); ?> 
                <?php 
                    break;
                    endswitch;
                    endforeach;
                    //投稿数が4以下の場合は空のboxで埋める
                    if (count($posts) < 4):
                        for ($i = 1; $i <= (4 - count($posts)); $i++):
                ?>
                            <div class="post_small_post"></div>
                <?php endfor; ?>
                    <!-- 投稿数が4以下の場合の.post_imagesの閉じタグ -->
                        </div>
                    <!-- 投稿数が4以下の場合の.post_images_commentの閉じタグ -->
                        </div>
                    <!-- 投稿数が4以下の場合の.post_multicontentsの閉じタグ -->
                        </div>
                <?php elseif(count($posts) >= 5): ?>
                    <!-- 投稿数が5以上だった場合の.post_additionalcontentsの閉じタグ -->
                    </div>
                    <?php endif; ?>
                    <!-- .multicontainerの閉じタグ -->
                </div>
                <?php endif; ?>


    </section>
<!-- .side_and_contentsの閉じタグ -->
</div>
    
<?php
include(dirname(__FILE__) . '/common/footer.html');