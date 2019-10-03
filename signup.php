<?php

//共通変数・関数ファイルを読込み
require('function.php');

//POST送信されているか
if(!empty($_POST)){
   
  //変数にユーザー情報を代入
  $username = $_POST['username'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];
  
  //未入力チェック
  validRequired($username,'username');
  validRequired($email,'email');
  validRequired($pass,'pass');
  validRequired($pass_re,'pass_re');
  
  if(empty($err_msg)){
    //usernameの文字数チェック
    validMinLenUsername($username,'username');
    
    //Emailの形式チェック
    validEmail($email,'email');
    //emailの最大文字数チェック
    validMaxLen($email,'email');
    //emailの最小文字数チェック
    validMinLen($email,'email');
    
    //パスワード半角英数字チェック
    validHalf($pass, 'pass');
    //パスワード最大文字数チェック
    validMaxLen($pass,'pass');
    //email重複チェック
    validEmailDup($email);
    
    if(empty($err_msg)){
      
      //パスワードとパスワードの再入力があっているかチェック
      validMatch($pass, $pass_re, 'pass_re');
 
  if(empty($err_msg)){
    
    //例外処理
    try{
      //DB接続
      $dbh = dbConnect();
      //SQL文作成
      $sql = 'INSERT INTO users(username,email,password,login_time,create_date) VALUES(:username,:email,:pass,:login_time,:create_date)';
      $data = array(':username' => $username, ':email' => $email, ':pass' => password_hash($pass,PASSWORD_DEFAULT),':login_time' => date('Y-m-d H:i:s'), 'create_date' => date('Y-m-d H:i:s'));
      //クエリ実行
      queryPost($dbh,$sql,$data);
      
      header("Location:mypage.php");
      
    } catch (Exception $e){
      error_log('エラー発生：' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
   }
  }
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>ユーザー登録 | travel record</title>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" type="text/css" href="css/reset.css">
  <link href="https://fonts.googleapis.com/css?family=Big+Shoulders+Text:400,700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,700&display=swap" rel="stylesheet">
</head>

<body class="page-signup page-1colum" background="img/luca-bravo-UKJev6G45f0-unsplash.jpg">
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
  <div id="contents" class="site-width page-1column">

    <!-- Main -->
    <section id="main">

      <div class="form-container">
        <form action="" method="post" class="form">
          <h2 class="title">ユーザー登録</h2>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
            ユーザー名<br>
            <input type="text" name="username" value="<?php if(!empty($_POST['username'])) echo $_POST['username']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['username'])) echo $err_msg['username'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            Email<br>
            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="area-msg">
            <?php 
            if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
            パスワード<span>※英数字6文字以上</span><br>
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'] ;?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['pass'])) echo $err_msg['pass'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
            パスワード（再入力）<br>
            <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
            ?>
          </div>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="登録する">
          </div>
        </form>
      </div>
    </section>
  </div>

  <!-- footer -->
  <footer id="footer" class="site-width">
    Copyright <a href="">Travel Record</a>. All RIghts Reserved.
  </footer>

  <script src="js/vendor/jquery-2.2.2.min.js"></script>
</body>
</head>
