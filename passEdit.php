<?php
//共通変数・関数の読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('パスワード変更ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
//DBからユーザー情報を取得
$userData = getUser($_SESSION['login_user']);
debug('ユーザー情報：'.print_r($userData,true));

if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：'.print_r($_POST,true));
  
  //変数にユーザー情報を格納
  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];
  
  //未入力チェック
  validRequired($pass_old,'pass_old');
  validRequired($pass_new,'pass_new');
  validRequired($pass_new_re,'pass_new_re');
  
  if(empty($err_msg)){
    //古いパスワードのチェック
    validpass($pass_old,'pass_old');
    //新しいパスワードのチェック
    validpass($pass_new,'pass_new');
    
    //古いパスワードがデータベースのパスワード一致するかチェック
    if(!password_verify($pass_old,$userData['password'])){
      $err_msg['pass_old'] = MSG11;
    }
    if($pass_old ===$pass_new){
      $err_msg['pass_new'] = MSG12;
    }
    validMatch($pass_new,$pass_new_re,'pass_new_re');
    
    if(empty($err_msg)){
      try{
        $dbh = dbConnect();
        $sql = 'UPDATE users SET password = :password WHERE id = :u_id';
        $data = array(':u_id' => $userData[id],':password' => password_hash($pass_new,PASSWORD_DEFAULT));
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
          debug('クエリ成功');
          $_SESSION['msg_success'] = SUC01;
          
          $username = $userData['username'];
          $from = $userData['email'];
          $subject = 'パスワード変更のお知らせ';
          $comment = <<<EOT
{$username}さん
パスワードが変更されましたのでお知らせします。

///////////////////////////////////////
EOT;
   $result = mb_send_mail($from,$username,$subject,$comment);
      if($result){
      debug('メールが送信されました');
        }else{
      debug('メール送信失敗');
      
    }
      header("Location:mypage.php");
        }else{
          $err_msg['common'] = MSG07;
        }
  
    
      }catch(Exception $e){
        $error_log('エラー発生'. $e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}
debug('画面処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'パスワード変更';
require('head.php'); 
?>
<body class="page-passEdit page-2colum page-logined">
    <style>
      .form{
        margin-top: 50px;
      }
    </style>
    
    <!-- メニュー -->
    <?php
      require('header.php'); 
    ?>
    
    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
      <h1 class="page-title">パスワード変更</h1>
      <!-- Main -->
    <section id="main">
      <div class="form-container">
        <form action="" method="post" class="form">
          <div class="area-msg">
            <?php
            echo getErrMsg('common');
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">
            古いパスワード
            <input type="password" name="pass_old" value="<?php echo getFormData('pass_old'); ?>">
          </label>
          <div class="area-msg">
            <?php
            echo getErrMsg('pass_old');
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">
            新しいパスワード
            <input type="password" name="pass_new" value="<?php echo getFormData('pass_new'); ?>">
          </label>
          <div class="area-msg">
           <?php
            echo getErrMsg('pass_new');
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
            新しいパスワード（再入力）
            <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re'); ?>">
          </label>
          <div class="area-msg">
            <?php
            echo getErrMsg('pass_new_re');
            ?>
          </div>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="変更する">
          </div>
        </form>
      </div>
      </section>
      
       <!-- サイドバー -->
      <?php
      require('sidebar.php');
      ?>
    </div>
        <!-- footer -->
    <?php
    require('footer.php'); 
    ?>