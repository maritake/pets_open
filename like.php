<?php
//共通phpを取得
include(dirname(__FILE__) . '/common/php_header.php');
//お気に入り登録したペットIDを新規投稿順に取得
$get_favorite_pets = $db->prepare(
    'SELECT DISTINCT p.pet_id 
    FROM posts p INNER JOIN favoritePets f 
    ON p.pet_id=f.favorite_pet_id 
    WHERE f.email=? 
    ORDER BY p.id DESC'
    );
    $get_favorite_pets->execute(array($email));
    $favorite_pets = $get_favorite_pets->fetchAll(PDO::FETCH_COLUMN);
    //いいねした投稿を取得
    $get_good_posts = $db->prepare(
        'SELECT p.id, p.photo, p.comment, p.pet_id 
    FROM posts p, goods g 
    WHERE p.id=g.post_id AND g.email=? 
    ORDER BY p.id DESC'
    );
    $get_good_posts->execute(array(
        $email
    ));
    $good_posts = $get_good_posts->fetchAll(PDO::FETCH_ASSOC); 
    include(dirname(__FILE__) . '/common/html_header.php');
    ?>
    <section class="contents">
        <t2 class="post_t2">お気に入り</t2>
        
        <div class="post_container">
            <?php foreach($good_posts as $post):?>
                <div class="post_post">
                    <?php include(dirname(__FILE__) . '/common/parts_post.php'); ?> 
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        foreach ($favorite_pets as $pet):   
            //お気に入りのペットの投稿を各4つずつ取得
            $likes_sql = 
            'SELECT id, photo, comment, pet_id, date_created
            FROM posts 
            WHERE pet_id=? 
            ORDER BY id DESC 
            LIMIT 4';
            $get_favorite_posts = $db->prepare($likes_sql);
            $get_favorite_posts->execute(array($pet));
            $favorite_posts = $get_favorite_posts->fetchAll(PDO::FETCH_ASSOC);

            //ペットの名前を取得
            $get_petinfo = $db->prepare(
                'SELECT pet_name, pet_image 
                FROM pets 
                WHERE id=?');
            $get_petinfo->execute(array($pet));
            $petinfo = $get_petinfo->fetch(PDO::FETCH_ASSOC);

            //取得した投稿を１つずつ取り出す
            foreach($favorite_posts as $index=>$post):
                switch ($index):
                    case 0:
                ?>
                <div class="post_multicontainer">
                    <t2>
                        <img class="icon" <?php print('src="/pets/images/' . $petinfo['pet_image'] . '"'); ?>>
                        <?php print(htmlspecialchars($petinfo['pet_name'], ENT_QUOTES)); ?>
                    </t2>
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
                default:
                break;
            endswitch;
        endforeach;
        //投稿数が4以下の場合は空のboxで埋める
        if (count($favorite_posts) < 4):
            for ($i = 1; $i <= (4 - count($favorite_posts)); $i++):
    ?>
                <div class="post_small_post"></div>
    <?php endfor; ?>
        <!-- 投稿数が4以下の場合の.post_imagesの閉じタグ -->
            </div>
        <!-- 投稿数が4以下の場合の.post_images_commentの閉じタグ -->
            </div>
        <!-- 投稿数が4以下の場合の.post_multicontentsの閉じタグ -->
            </div>
            <?php endif; ?>            
        </div>
            <?php endforeach; ?>            

    </section>
            </div>    

<?php
include(dirname(__FILE__) . '/common/footer.html');