<?php
//共通変数・関数の読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行メール送信ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


if(!empty($_POST)){
  $email = $_POST['email'];
  //未入力チェック
  validRequired($email,'email');

  if(empty($err_msg)){
    //Eメールの形式チェック
    validEmail($email,'email');
    //メールの最大文字数チェック
    validMaxLen($email,'email');
    
    if(empty($err_msg)){
      //DB接続
      try{
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE  email = :email AND delete_flg = 0 ';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt ->fetch(PDO::FETCH_ASSOC);
        
        if($stmt && array_shift($result)){
          $_SESSION['success_msg'] = SUC03;
          
          //認証キー作成
          $key_auth = makerandkey();
          
          $from = 'kalastus33@yahoo.co.jp';
          $to = $email;
          $subject = '認証キーのお知らせ';
          $comment = <<<EOT
認証キー送信のお知らせ。
お客様の認証キーを下記の通りお知らせします。
認証機キー：{$key_auth}
有効期限は３０分です。
EOT;
          
          sendMail($from,$to,$subject,$comment);
          
          $_SESSION['auth_key'] = $key_auth;
          $_SESSION['email'] = $email;
          $_SESSION['auth_login_limit'] = time() + 60*30;
          
          debug('セッションの中身：'.print_r($_SESSION,true));
          
          header("Location:passRemindReceive.php");
        }else{
          debug('クエリに失敗したかDBに登録のないEmailが入力されました。');
          $err_msg['common'] = MSG07;
        }
      }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}
?>
<?php
$siteTitle = 'パスワード再発行メール送信';
require('head.php');
?>


<body class="page-signup page-1column">
  
      <!-- メニュー -->
    <?php
    require('header.php'); 
    ?>

<!-- メインコンテンツ -->
<div id="contents" class="site-width">
  
  <!-- Main -->
  <section id="mail">
    
    <div class="form-container">
      
      <form action="" method="post" class="form">
        <p>ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送りいたします。</p>
        <div class="area-msg">
          <?php
          if(!empty($err_msg['common'])) echo $err_msg['common'];
          ?>
        </div>
        <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
          Email
          <input type="text" name="email" value ="<?php echo getFormData('email'); ?>">
        </label>
        <div class="area-msg">
          <?php
          if(!empty($err_msg['email'])) echo $err_msg['email'];
          ?>
        </div>
        <div class="btn-container">
          <input type="submit" class="btn btn-mid" value="送信する">
        </div>
      </form>
    </div>
  </section>
</div>
</body>
    <!-- footer -->
    <?php
    require('footer.php'); 
    ?>