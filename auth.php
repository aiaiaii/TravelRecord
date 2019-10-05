<?php
if(!empty($_SESSION['login_date'])){
  debug('ログイン済ユーザー');
  
  if($_SESSION['login_date'] + $_SESSION['login_limit'] < time()){
    debug('ログイン有効期限オーバーです');
    session_destroy();
    header("Location:login.php");
  }else{
    debug('ログイン期限有効期限内です');
    $_SESSION['login_date'] = time();
    header("Location:mypage.php");
  }
  
}else{
  debug('セッションがありません');
}
?>