<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>pets|ペットの写真投稿アプリ</title>
    <meta name="description" content="【駆け出しエンジニアまりたけのポートフォリオ】ペットの写真を投稿・閲覧するWebアプリです。好きな動物やお気に入りのペットを登録すれば、好きな写真を効率良く閲覧可能。登録・利用は完全無料！">
    <link rel="stylesheet" href="/pets/css/html5reset-1.6.1.css">
    <link rel="stylesheet" href="/pets/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

</head>
<body>
    <div id="side_and_contents">
    <section id="topbar">
        <div id="top_container">
            
            <?php if (!empty($_SESSION['login'])): ?>
            <div id="logo">
                <a href="/pets/new_post.php"><t1>pets</t1></a>
            </div>
            <div id="logo_side">
                <p>
                    <?php
                    print(htmlspecialchars($name, ENT_QUOTES));
                    ?>
                </p>
                <?php 
                if (!empty($pets)):
                    foreach ($pets as $pet):
                        if (!is_null($pet['pet_image'])): ?>
                    <img class="icon" <?php print('src="/pets/images/' . htmlspecialchars($pet['pet_image'], ENT_QUOTES) . '"'); ?>>
                    <?php
                    endif;
                    endforeach;
                    endif; 
                    ?>
            </div>
                <div id="hamburger">
                    <span class="hamburger_line" id="hamburger_line1"></span>
                    <span class="hamburger_line" id="hamburger_line2"></span>
                    <span class="hamburger_line" id="hamburger_line3"></span>
                </div>
                <div id="top_menu">
                    <ul>
                        <li><a href="/pets/join/check.php?action=check">登録情報照会・変更</a></li>
                        <li><a href="/pets/join/index.php?action=change">パスワード変更</a></li>
                        <li><p id="logout" >ログアウト</p></li>
                        <li><p id="quit" >会員登録削除</p></li>
                    </ul>
                </div>
                <div id="logout_check">
                    <form method="POST" action="">
                        <p>ログアウトしますか？</p>
                        <input type="hidden" value="ok" name="logout">
                        <input type=submit value="はい"><br>
                    </form>
                    <a id="logout_no">いいえ</a>
                </div>
                <div id="quit_check">
                    <form method="POST" action="">
                        <p>会員登録を削除しますか？</p>
                        <input type="hidden" value="ok" name="quit">
                        <input type=submit value="はい"><br>
                    </form>
                    <a id="quit_no">いいえ</a>
                </div>
        </div>

        <div id="top_link">
            <div 
            <?php if(strpos($_SERVER['REQUEST_URI'], 'new_post')) {
                print('class="top_link_selected"');
            }
            ?>
            ><a href="/pets/new_post.php"><t2>NEW</t2></a></div>
            <div
            <?php if(strpos($_SERVER['REQUEST_URI'], 'like')) {
                print('class="top_link_selected"');
            }
            ?>
            ><a href="/pets/like.php"><t2>LIKE</t2></a></div>
            <div
            <?php if(strpos($_SERVER['REQUEST_URI'], 'mypage')) {
                print('class="top_link_selected"');
            }
            ?>
            ><a href="/pets/mypage.php"><t2>MYPAGE</t2></a></div>
        </div>
    </section>

    <?php if (strpos($_SERVER['REQUEST_URI'], 'new_post') || strpos($_SERVER['REQUEST_URI'], 'like') || strpos($_SERVER['REQUEST_URI'], 'mypage')): ?>
    <div id="newpost_btn">
        <a href="/pets/post.php">+<span>新規投稿</span></a>
    </div>
    <?php endif; ?>
    <script type="text/javascript" src="/pets/js/logined.js"></script>
        
                <?php else:?>
                    <div id="logo">
                        <a href="/pets/index.php"><t1>pets</t1></a>
                    </div>
                    <div id="logo_side_unlogined">
                    <div>
                        <a href="/pets/join/index.php?action=join"><p>会員登録</p></a>
                    </div>
                    <div>
                        <a href="/pets/login.php"><p>ログイン</p></a>
                    </div>
                </div>
            </div>
        </section>
        <script type="text/javascript" src="/pets/js/unlogined.js"></script>
        <?php endif; ?>            
