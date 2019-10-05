<?php
require('function.php');

require('auth.php');

if(!empty($_POST)){
  $username = $_POST['username'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true: false ;
  
  //バリデーションチェック
  validMinLenUsername($username,'username');
  validEmail($email,'email');
  validMaxLen($email,'email');
  validHalf($pass,'pass');
  validMaxLen($pass,'pass');
  validMinLen($pass,'pass');
  validRequired($username,'username');
  validRequired($email,'email');
  validRequired($pass,'pass');
  
  if(empty($err_msg)){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT password,id FROM users WHERE email = :email';
      $data = array(':email' => $email);
      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if(!empty($result) && password_verify($pass,array_shift($result))){
       $sesLimit = 60*60;
       $_SESSION['login_date'] = time();
        if($pass_save){
          $_SESSION['login_limit'] = 60*60*30;
        }else{
          $_SESSION['login_limit'] = $sesLimit;
        }
        $_SESSION['login_user'] = $result['id'];
        header("Location:mypage.php");
      }else{
        $err_msg['common'] = MSG010;
      }
    }catch(Exception $e){
      error_log('エラー発生：' . $e-> getMessage);
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<!DOCTYPE html>
<html lang="ja">

  <head>
    <meta charset="utf-8">
    <title>ログイン | Ttavel Record</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" type="text/css" href="css/reset.css">
  <link href="https://fonts.googleapis.com/css?family=Big+Shoulders+Text:400,700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,700&display=swap" rel="stylesheet">
  </head>

  <body class="page-login page-1colum">

    <!-- メニュー -->
    <header class="header">
    <div class="site-width clearfix">
      <h1><a href="index.php">Travel Record</a></h1>
      <nav id="top-nav">
        <ul>
         <li><a href="login.php" class="btn">ログイン</a></li>
          <li><a href="signup.php" class="btn btn-primary">ユーザー登録</a></li>
        </ul>
      </nav>
    </div>
  </header>
  
     <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
    <!-- Main -->
    <senction id="main">
      <div class="form-container">
        <form action="" method="post" class="form">
          <h2 class="title">ログイン</h2>
          <div class="area-msg">
          <?php
            if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['user_name'])) echo 'err'; ?>">
            ユーザー名
            <input type="text" name="username" value="<?php if(!empty($_POST['username'])) echo $_POST['username']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['username'])) echo $err_msg['username'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err';?>">
            メールアドレス
            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['email'])) echo $err_msg['email'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass'])) echo 'err'?>">
            パスワード
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['pass'])) echo $err_msg['pass'];
            ?>
          </div>
          <label>
            <input type="checkbox" name="pass_save">次回ログインを省略する
          </label>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="ログイン">
          </div>
          パスワードを忘れた方は<a href="passRemid.php">こちら</a>
        </form>
      </div>
    </senction>
    
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