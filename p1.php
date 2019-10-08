<?php
function getUser($u_id){
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM users WHERE id = :u_id';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
    }else{
      debug('クエリ失敗');
      $err_msg['common'] = MSG07;
    }
  }catch(Exception $e){
    $error_log('エラー発生'. $e->getMessage());
    $err_msg['common'] = MSG07;
  }
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getFormData($str){
  global $dbFormData;
  if(!empty($dbFormData)){
    if(!empty($err_msg[$str])){
      if(isset($_POST[$str])){
        return $_POST[$str];
      }else{
        return $dbFormData;
      }
      if(isset($_POST[$str]) && $_POST[$str] !== $dbFormData){
        return $_POST[$str];
      }
    }else{
      return $dbFormData;
    }
    }else{
    $_POST[$str];
  }
  }
}
?>