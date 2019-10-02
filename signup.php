<?php
//ログを取るかどうか
ini_set('log_errors','on');
//ログを出力ファイルを指定
ini_set('error_log','php.log');

//エラーメッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02','Emailの形式で入力してください');
define('MSG03','パスワード（再入力）があっていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','255文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください');
define('MSG08','そのEmailは既に登録されています');
define('MSG09','3文字以上で入力してください')

//エラーメッセージ格納用の配列
$err_msg = array();

//バリデーション関数（未入力チェック）
function validRequired($str,$key){
  if(empty($str)){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
//バリデーション関数（username文字数チェック）
function validMinLenUsername($str,$key,$min = 3){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG09;
  }
}
//バリデーション関数（Email形式チェック）
function validEmail($str,$key){
  if(!pregmatch("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
//バリデーション関数(Email重複チェック)
function validEmailDup($email){
  global $err_msg;
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文を作成
    $sql = 'SELECT count(*) FROM users WHERE email = :email';
    $data = array(':email' => $email);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    // クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC)
    return $stmt; // なぜ$resultではなく、$stmtを返すのか？
  }
}
//バリデーション関数（同値チェック）
function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
//バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key, $max = 255){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}
//バリデーション関数（半角チェック）
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/",$key)){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
//DB接続関数
function dbConnect(){
  $dsn = 'mysql:dbname=travelrecord;host=localhost;charset=utf8';
  $user = 'root'
  $password = 'root'
  $options = array(
  // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  //PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}
//SQL実行関数
function queryPost($dbh,$sql,$data){
  //クエリー作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダに値をセットし、SQL文を実行
  $stmt->execute($data);
  return $stmt;
}

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
    validMaxLen($emial,'email');
    //emailの最小文字数チェック
    validMinLen($email,'email')
    
    //パスワード半角英数字チェック
    validHalf($pass,'pass');
    //パスワード最大文字数チェック
    validMaxLen($pass,'pass');
    //パスワード最小文字数チェック
    validMinLen($pass,'pass');
    
    if(empty($err_msg)){
      
      //パスワードとパスワードの再入力があっているかチェック
      validMatch($pass, $pass_re, 'pass_re');
 
  if(empty($err_msg)){
    
    //例外処理
    try{
      //DB接続
      $dbh = dbConnect();
      //SQL文作成
      $sql = 'INSERT INTO users (username,email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)';
      $data = array(':username' => $username, ':email' => $email, ':pass' => password_hash($pass,PASSWORD_DEFAULT), ':logintime' =>datedate('Y-m-d H:i:s'),':create_date' => date('Y-m-d H:i:s'));
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
  <link rel="stylesheet" type="text/css" href="style.css">
  <link href="https://fonts.googleapis.com/css?family=Big+Shoulders+Text:400,700&display=swap" rel="stylesheet">
  <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>

<body class="page-signup page-1colum">
  <!-- メニュー -->
  <header>
    <div class="site-width">
      <h1><a href="index.php">Travel Record</a></h1>
      <nav id="top-nav">
        <ul>
          <li><a href="signup.php" class="btn btn-primary">ユーザー登録</a></li>
          <li><a href="login.php">ログイン</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- メインコンテンツ -->
  <div id="contents" class="site-width">

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
          <label class="">
            ユーザー名
            <input type="text" name="userneame" value="<?php if(!empty($_POST['username'])) echo $_POST['username']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['username'])) echo $err_msg['username'];
            ?>
          </div>
          <label class="">
            Email
            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="area-msg">
            <?php 
            if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
          </div>
          <label class="<?php (!empty($err_msg['email'])) echo 'err'; ?>">
            パスワード<span>※英数字6文字以上</span>
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'] ;?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['pass'])) echo $err_msg['pass'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
            パスワード（再入力）
            <input type="password" name="pass_re" value="<?php (!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
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
  <footer id="footer">
    Copyright <a href="">Record Travel</a>. All RIghts Reserved.
  </footer>
  
  <script src="js/vendor/jquery-2.2.2.min.js"></script>
</body>
</head>
