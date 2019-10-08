<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug('プロフィール編集画面');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

$dbFormData = gteUser($_SESSION('login_user'));

if(!empty($_POST)){
  debug('POST送信があります');
  $username =  $_POST['username'];
  $email = $_POST['email'];
  
  if($dbFormData['username'] !== $username){
    validUsernameMinLen($username,'username');
  }
  if((dbFormData['email']) !== $email){
    valid
  }
  if(empty($err_msg)){
    try{
      $dbh = dbConnect();
      $sql = 'UPDATE users SET :username,:email WEHERE id = :u_id'
      $data = array(':username' => $username, ':email' =>$email, ':u_id' =>$_SESSION['login_user']);
      
      $stmt = queryPost($dbh, $sql, $data);
      
      if($stmt){
        debug('クエリ成功');
        header("Location:mypage.php");
      }else{
        debug('クエリ失敗');
        $err_msg['common'] =MSG07;
      }
    }catch(Exception $e){
      error_log('エラー発生：' .$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
?>