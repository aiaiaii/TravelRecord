<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('コンテンツ登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

$c_id = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';

$dbFormData = (!empty($c_id)) ? getContent($_SESSION['user_id'],$c_id):'';

//新規登録か編集か判別用
$edit_flg = (empty($dbFormData)) ? false : true ;

$dbNationData = getNation();
$dbOppotunityData = getOppotunity();
debug('コンテンツID：'.$c_id);
debug('フォーム用データ:'.print_r($dbFormData,true));

//パラメータ改ざんチェック
if(!empty($c_id) && empty($dbFormData)){
  debug('商品IDが違います。マイページへ遷移します。');
  header("Location:mypage.php");
}

if(!empty($_POST)){
  debug('POST情報があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報:'.print_r($_FILES,true));
  
  $nation = $_POST['nation_id'];
  $oppotunity = $_POST['oppotunity_id'];
  $memories = $_POST['memories'];
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic'):'';
  
  //画像を新しくPOSTしていないがすでにDBに登録されている場合、DBのパスを入れる
  
$pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;
  
  //更新の場合：DBの情報と入力情報が異なる場合にバリデーションを行う
  if(empty($dbFormData)){
    validSelect($nation,'nation_id');
    validSelect($oppotunity,'oppotunity_id');
    validMaxLen($memories,'memories',300);
  }else{
    if($dbFormData['nation_id'] !== $nation){
      validSelect($nation, 'nation_id');
    }
    if($dbFormData['oppotunity_id'] !== $oppotunity){
      validSelect($oppotunity,'oppotunity_id');
    }
    if($dbFormData['memories'] !== $comment){
      validMaxLen($comment,'memories',300);
    }
  }
  
  if(empty($err_msg)){
    debug('バリデーションOKです');
    
    try{
      $dbh = dbConnect();
      
      if($edit_flg){
        debug('DB更新');
        $sql = 'UPDATE content SET nation_id =:nation_id, oppotunity_id = :oppotunity_id, memories = :memories,pic = :pic WHERE user_id = :u_id AND id = :c_id';
        $data = array(':nation_id' => $nation,':oppotunity_id' => $oppotunity,':memories' => $memories, 'pic' => $pic,':u_id'=>$_SESSION['login_user'],':c_id' => $c_id);
      }else{
        debug('DB新規登録');
        $sql = 'INSERT INTO content (user_id,nation_id,oppotunity_id,memories,pic,create_date) VALUES (:u_id,:nation_id,:oppotunity_id,:memories,:pic,:date)';
        $data = array(':u_id' => $_SESSION['login_user'],':nation_id' => $nation, ':oppotunity_id' => $oppotunity,':memories'=> $memories, 'pic' => $pic,':date' => date('Y-m-d H:i:s'));
      }
      debug('SQL:'.$sql);
      debug('流し込みデータ：'.print_r($data,true));
      $stmt = queryPost($dbh,$sql,$data);
      
      if($stmt){
        $_SESSION['success_msg'] = SUC04;
        debug('マイページへ遷移します');
        header("Location:mypage.php");
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = (!$edit_flg) ? '新規投稿':'編集';
require('head.php');
?>
  <body class="page-profEdit page-2colum page-logined">
  
 <!-- メニュー -->
 <?php
    require('header.php');
    ?>
    
   <!-- メインコンテンツ -->
   <div id="contents" class="site-width">
     <h1 class="page-title"><?php echo (!$edit_flg) ? '新規投稿':'編集する'; ?></h1>
     <!-- Main -->
     <section id="main">
       <div class="form-container">
         <form action="" method="post" class="form" enctype="multipart/form-data" style="width:100%;box-sizing:border-box;">
           <div class="area-msg">
             <?php
             if(!empty($err_msg['common'])) echo $err_msg['common'];
             ?>
           </div>
           <label class="<?php if(!empty($err_msg['nation_id'])) echo 'err'; ?>">
             行った国<span class="label-require">必須</span>
             <select name="nation_id" id="">
               <option value="0" <?php if(getFormData('nation_id') == 0) {echo 'selected';}?>>選択してください</option>
               <?php
               foreach($dbNationData as $key => $val){
               ?>
               <option value="<?php echo $val['id'] ?>"<?php if(getFormData('nation_id') == $val['id']){echo 'selected';} ?>>
               <?php echo $val['nation_name']; ?>
               </option>
               <?php } 
               ?>
             </select>
           </label>
           <div class="area-msg">
             <?php
             if(!empty($err_msg['nation_id'])) echo $err_msg['nation_id'];
             ?>
           </div>
           <label class="<?php if(!empty($err_msg['opptunity_id'])) echo 'err'; ?>">
             旅行の目的<span class="label-require">必須</span>
             <select name="oppotunity_id" id="">
               <option value="0" <?php if(getFormData('oppotunity_id') == 0) {echo 'selected';}?>>選択してください</option>
               <?php
               foreach($dbOppotunityData as $key => $val){
               ?>
               <option value="<?php echo $val['id'] ?>"<?php if(getFormData('oppotunity_id') == $val['id']){echo 'selected';} ?>>
               <?php echo $val['name']; ?>
               </option>
               <?php } 
               ?>
             </select>
           </label>
           <div class="area-msg">
             <?php
             if(!empty($err_msg['oppotunity_id'])) echo $err_msg['oppotunity_id'];
             ?>
           </div>
           <label class="<?php if(!empty($err_msg['memories'])) echo 'err'?>">
             思い出
             <textarea name="memories" id="js-count" cols="30" rows="10" style="height:150px;"><?php echo getFormData('memories'); ?></textarea>
           </label>
           <p class="counter-text"><span id="js-count-view">0</span>/300文字</p>
           <div class="area-msg">
             <?php
             if(!empty($err_msg['memories'])) echo $err_msg['momeries'];
             ?>
           </div>
           <div class="imgDrop-container">
             画像
             <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>">
             <input type="hidden" name="MAX_FILE_SIZE" value="3145724">
             <input type="file" name="pic" class="input-file">
             <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
             ドラッグ＆ドロップ
             </label>
             <div class="area-msg">
               <?php
               if(!empty($err_msg['pic'])) echo $err_msg['pic'];
               ?>
             </div>
           </div>
           <div class="btn-container">
             <input type="submit" class="btn btn-mid" value="<?php echo (!$edit_flg) ? '投稿する' : '更新する'; ?>">
           </div>
         </form>
       </div>
     </section>
   </div>
       <!-- footer -->
    <?php
    require('footer.php'); 
    ?>