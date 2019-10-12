<?php
//共通関数・変数の読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('認証キー入力画面');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(empty($_SESSION['auth_key'])){
    header("Location:passRemindSend.php");
}
   
  if (!empty($_POST)) {
      $auth_key = $_POST['token'];
    
      validRequired($auth_key, 'token');
    
      if (empty($err_msg)) {
          strLength($auth_key, 'token');
          validHalf($auth_key, 'token');
    
          if (empty($err_msg)) {
              if ($auth_key !== $_SESSION['auth_key']) {
                  $err_msg['common'] = MSG14;
              }
              if (time() > $_SESSION['auth_login_limit']) {
                  $err_msg['common'] = MSG15;
              }
              if (empty($err_msg)) {
                  $pass = makerandkey();
        
                  try {
                      $dbh = dbConnect();
                      $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
                      $data = array(':pass' => password_hash($pass, PASSWORD_DEFAULT), ':email' => $_SESSION['email']);
                      $stmt = queryPost($dbh, $sql, $data);
          
                      if ($stmt) {
                          $from = 'kalastus33@yahoo.co.jp';
                          $to = $_SESSION['email'];
                          $subject = '仮パスワード送信のお知らせ';
                          $comment = <<<EOT
仮パスワードを下記の通りお知らせいたします。
仮パスワード：{$pass}
EOT;
                        debug('メールの内容：'.print_r($comment,true));
                          sendMail($from, $to, $subject, $comment);
                          session_unset();
            
                          $_SESSION['success_msg'] = SUC03;
                          debug('セッション変数の中身：'.print_r($_SESSION, true));
            
                          header("Location:login.php");
                      } else {
                          $err_msg['common'] = MSG07;
                      }
                  } catch (Exception $e) {
                      error_log('エラー発生：'.$e->getMessage());
                      $err_msg['common'] = MSG07;
                  }
              }
          }
      }
  }
?>
<?php
$siteTitle = 'パスワード再発行認証';
require('head.php');
?>
<body class="page-signup page-1column">
  
  <!-- メニュー -->
  <?php
  require('header.php');
  ?>
  <p id="js-show-msg" style="display:none;" class="msg-slide">
    <?php echo getSessionFlash('success_msg'); ?>
  </p>
  
  <!-- メインコンテンツ -->
  <div id="contents" class="site-width">
    
    <!-- Main -->
    <section id="main">
      <div class="form-container">
        
        <form action="" method="post" class="form">
          <p>ご指定のメールアドレスにお送りしメール内にある「認証キー」をご入力ください。</p>
          <div class="area-msg">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
          </div>
          <label class="<?php if(!empty($err_msg['token'])) echo 'err'; ?>">
            認証キー
            <input type="text" name="token" value="<?php echo getFormData('token'); ?>">
          </label>
          <div class="area-msg">
            <?php if(!empty($err_msg['token'])) echo $err_msg['token']; ?>
          </div>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="再発行する">
          </div>
        </form>
      </div>
      <a href="passRemindSend.php">&lt; パスワード再発行メールを再度送信する</a>
    </section>
  </div>
    <!-- footer -->
    <?php
    require('footer.php'); 
    ?>