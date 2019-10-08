<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('プロフィール編集画面');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

$dbFormData = getUser($_SESSION['login_user']);
debug('取得したユーザー情報：' .print_r($dbFormData,true));
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報'.print_r($_POST,true));
  
  $username = $_POST['username'];
  $email = $_POST['email'];
  
  if($dbFormData['username'] !== $username){
    validMinLenUsername($username,'username');
      }
  if($dbFormData['email'] !== $email){
     validEmail($email,'email');
    validMaxLen($email,'email');
    validHalf($email,'email');
  }
   
    if(empty($err_msg)){
      try{
        $dbh = dbConnect();
        $sql = 'UPDATE users SET username = :u_name,email = :email WHERE id = :u_id';
        $data = array(':u_name' => $username,':email' => $email,'u_id' =>$dbFormData['id']);
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
          debug('クエリ成功');
          header("Location:mypage.php");
        }else{
          debug('クエリ失敗');
          $err_msg['common'] = MSG07;
        }
      }catch(Exception $e){
        error_log('エラー発生：' . $e->getMessage());
        $err_msg['common'] = MSG07;
        
      }
    }

}
debug('画面表示終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'プロフィール編集';
require('head.php');
?>
<body class="page-profEdit page-2colum page-logined">
  
<!-- メニュー -->
    <?php
    require('header.php'); 
    ?>
    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
      
      <!-- Main -->
      <section id="main">
        <div class="form-container">
         
          <form action="" method="post" class="form">
           <h1 class="page-title">プロフィール編集</h1>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
              ユーザー名
              <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['username'])) echo $err_msg['username'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
              Email
              <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['email'])) echo $err_msg['email'];
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
