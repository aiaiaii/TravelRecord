<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('退会');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

if(!empty($_POST)){
  try{
    $dbh = dbConnect();
    
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id';
    $sql2 = 'UPDATE content SET delete_flg = 1 WHERE user_id = :u_id';
    $sql3 = 'UPDATE `like` SET delete_flg = 1 WHERE user_id = :u_id';
    
    $data = array(':u_id'=> $_SESSION['login_user']);
    
    $stmt1 =queryPost($dbh,$sql1,$data);
    $stmt2 =queryPost($dbh,$sql2,$data);
    $stmt3 =queryPost($dbh,$sql3,$data);
    
    if($stmt1){
      debug('クエリ成功');
      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION,true));
      header("Location:login.php");
    }else{
  debug('ポスト送信がありません');
  $err_msg['common'] = MSG07;
}
    
  }catch(Exception $e){
    $err_log('エラー発生'. $e->getMessage());
    $err_msg['common'] = MSG07;
  }
  
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = '退会';
require('head.php');
?>

<body class="page-withdraw page-1column">

<!-- メニュー -->
<?php
require('header.php');
?>

 <style>
   .form .btn{
     float: none;
   }
   .form{
     text-align: center;
   }
  </style>

<!-- メインコンテンツ -->
<div id="contents" class="site-width">
  <!-- Main -->
  <section id="main">
    <div class="form-container">
      <form action="" method="post" class="form">
        <h2 class="title">退会</h2>
        <div class="area-msg">
          <?php
          if(!empty($err_msg['common'])) echo $err_msg['common'];
          ?>
        </div>
        <div class="btn-container">
        <input type="submit" class="btn btn-mid" value="退会する" name="submit">
        </div>
      </form>
    </div>
  </section>
</div>
<!-- footer -->
   <footer id="footer">
     Copyright <a href="http://webukatu.com/">Travel Record</a>. All Rights Reserved.
   </footer>
   <script src="js/vendor/jquery-2.2.2.min.js"></script>
   <script>
  $(function(){
      var $ftr = $('#footer');
      if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
        $ftr.attr({'style':'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'});
      }
    });
  </script>
</body>