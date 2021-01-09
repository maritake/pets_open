
{
'use strict';

document.addEventListener('DOMContentLoaded', function() {

    // goodボタンを取得
    let good_btn = document.getElementsByClassName('post_good');
    let small_good_btn = document.getElementsByClassName('post_good_small');
    
    [].forEach.call(good_btn, function(good_btn) {
        good_btn.addEventListener('click', function() {
            //クリックされたpostのidを取得
            var postID = good_btn.dataset.postid;
            //クリックされたpostのgoodの数を取得
            var postGOOD = good_btn.dataset.good;
            if (good_btn.classList.contains("post_good_selected")) {
                postGOOD--;
                var btn_status = 'selected'
            } else {
                postGOOD++;
                var btn_status = 'unselected'
            }
            $.ajax({
                type: 'GET',
                url: '/pets/common/ajax_good.php',
                datatype: 'text',
                data: {id: postID, status: btn_status} 
            }).done(function(result) {
                good_btn.innerHTML = postGOOD;
                good_btn.classList.toggle('post_good_selected');
                good_btn.dataset.good = postGOOD;
            });        
        })
    });
    [].forEach.call(small_good_btn, function(good_btn) {
        good_btn.addEventListener('click', function() {
            //クリックされたpostのidを取得
            var postID = good_btn.dataset.postid;
            //クリックされたpostのgoodの数を取得
            var postGOOD = good_btn.dataset.good;
            if (good_btn.classList.contains("post_good_selected")) {
                postGOOD--;
                var btn_status = 'selected'
            } else {
                postGOOD++;
                var btn_status = 'unselected'
            }
            $.ajax({
                type: 'GET',
                url: '/pets/common/ajax_good.php',
                datatype: 'text',
                data: {id: postID, status: btn_status} 
            }).done(function(result) {
                good_btn.innerHTML = postGOOD;
                good_btn.classList.toggle('post_good_selected');
                good_btn.dataset.good = postGOOD;
            });        
        })
    });
    

    //ペットのお気に入り登録
    var like_btn = document.getElementsByClassName('pet_favorite');
    [].forEach.call(like_btn, function(like_btn) {
        like_btn.addEventListener('click', function() {
            var pet_id = like_btn.dataset.petid;
            if (like_btn.classList.contains('pet_favorite_selected')) {
                var btn_status = 'selected';
                like_btn.firstElementChild.innerHTML = ' をお気に入り登録する'
            } else {
                var btn_status = 'unselected';
                like_btn.firstElementChild.innerHTML = ' のお気に入りを解除する'
            }
            $.ajax({
                type: 'GET',
                url: '/pets/common/ajax_like.php',
                datatype: 'text',
                data: {id: pet_id, status: btn_status} 
            }).done(function(result) {
                like_btn.classList.toggle('pet_favorite_selected');
            })
        });
    });

    //ハンバーガーメニュー
    document.getElementById('hamburger').addEventListener('click', function() {
        document.getElementById('hamburger_line1').classList.toggle('hamburger_line1_clicked');
        document.getElementById('hamburger_line2').classList.toggle('hamburger_line2_clicked');
        document.getElementById('hamburger_line3').classList.toggle('hamburger_line3_clicked');
        document.getElementById('top_menu').classList.toggle('top_menu_clicked');    
    });

    //ログアウト
    document.getElementById('logout').addEventListener('click', function() {
        document.getElementById('hamburger_line1').classList.remove('hamburger_line1_clicked');
        document.getElementById('hamburger_line2').classList.remove('hamburger_line2_clicked');
        document.getElementById('hamburger_line3').classList.remove('hamburger_line3_clicked');
        document.getElementById('top_menu').classList.remove('top_menu_clicked');
            
        document.getElementById('logout_check').classList.add('logout_modal');

        document.getElementById('logout_no').addEventListener('click', function() {
            document.getElementById('logout_check').classList.remove('logout_modal');
        });
    });

    //会員登録削除
    document.getElementById('quit').addEventListener('click', function() {
        document.getElementById('hamburger_line1').classList.remove('hamburger_line1_clicked');
        document.getElementById('hamburger_line2').classList.remove('hamburger_line2_clicked');
        document.getElementById('hamburger_line3').classList.remove('hamburger_line3_clicked');
        document.getElementById('top_menu').classList.remove('top_menu_clicked');
            
        document.getElementById('quit_check').classList.add('quit_modal');

        document.getElementById('quit_no').addEventListener('click', function() {
            document.getElementById('quit_check').classList.remove('quit_modal');
        });
    });

    //投稿削除
    document.getElementById('post_delete').addEventListener('click', function() {
        document.getElementById('post_delete_hidden').classList.remove('post_hidden');
    });
    [].forEach.call(document.getElementsByClassName('post_delete_confirm'), function(confirm_btn) {
        confirm_btn.addEventListener('click', function() {
            document.getElementById('post_delete_hidden').classList.add('post_hidden');
        });
    });



});
}