<?php
  // 変数初期化
  $id_edit = '';
  $nickname_edit = '';
  $comment_edit = '';
  $btn_content = 'つぶやく';
  try {
    // DBへ接続
    require('db_connection.php');

    if (!empty($_POST)) {
      $nickname = htmlspecialchars($_POST['nickname']);
      $comment = htmlspecialchars($_POST['comment']);
      // 前提条件チェック
      if(($nickname != '') || ($comment != '') || !(mb_strlen($nickname, 'UTF-8') < 4)) {

        // 新規入力処理
        if ($_POST['action'] == 'input') {
          $sql = 'INSERT INTO `posts` SET `nickname`=?,
                                          `comment`=?,
                                          `created`=NOW(),
                                          `thumbsup`=0,
                                          `thumbsdown`=0';
          $data = array($nickname,$comment);
          $stml = $dbh->prepare($sql);
          $stml->execute($data);
        }
        // 修正SQL処理
        if ($_POST['action'] == 'edit') {
          $sql = 'UPDATE `posts` SET `nickname`=?,
                                     `comment`=? 
                                 WHERE `id`=?';
          $data = array($nickname,$comment,$_POST['id']);
          $stml = $dbh->prepare($sql);
          $stml->execute($data);
        }
      }
      //DBとの切断
      $dbh = null;
      //ページのリフレッシュ(postをgetで上書きする)
      require('refresh.php');
    }

    if (!empty($_GET)) {
      // 修正内容の取得処理
      if ($_GET['action'] == 'edit') {
        $btn_content = '更新する';
        $id_edit = $_GET['id'];

        // 修正内容のSQLからデータ取得処理
        $sql = 'SELECT * FROM `posts` WHERE id = ?';
        $data = array($id_edit);
        $stml = $dbh->prepare($sql);
        $stml->execute($data);

        while ($rec = $stml->fetch(PDO::FETCH_ASSOC)) {
          $nickname_edit = $rec['nickname'];
          $comment_edit = $rec['comment'];
        }
      }

      // 削除処理
      if ($_GET['action'] == 'delete') {
        $id_edit = $_GET['id'];

        // データ取得処理
        $sql = 'DELETE FROM `posts` WHERE id = ?';
        $data = array($id_edit);
        $stml = $dbh->prepare($sql);
        $stml->execute($data);
        // DBとの切断
        $dbh = null;
        
        // ページのリフレッシュ(postをgetで上書きする)
        require('refresh.php');
      }

      // thumbsup処理
      if ($_GET['action'] == 'thumbsup') {
        $id_edit = $_GET['id'];
        // データ取得処理
        $sql = 'UPDATE `posts` SET thumbsup = thumbsup + 1 WHERE id = ' . $id_edit;
        $data = array($id_edit);
        $stml = $dbh->prepare($sql);
        $stml->execute($data);
        // DBとの切断
        $dbh = null;
        
        // ページのリフレッシュ(postをgetで上書きする)
        require('refresh.php');
      }

      // thumbsdown処理
      if ($_GET['action'] == 'thumbsdown') {
        $id_edit = $_GET['id'];
        // データ取得処理
        $sql = 'UPDATE `posts` SET thumbsdown = thumbsdown + 1 WHERE id = ' . $id_edit;
        $data = array($id_edit);
        $stml = $dbh->prepare($sql);
        $stml->execute($data);
        // DBとの切断
        $dbh = null;
        
        // ページのリフレッシュ(postをgetで上書きする)
        require('refresh.php');
      }
    }

    // 全件表示処理
    $sql = 'SELECT * FROM posts ORDER BY created DESC';
    $stml = $dbh->prepare($sql); // DBオブジェクトにSQL文をセット
    $stml->execute(); // セットされたSQLをDBに対して実行
    // // ↑ $stmlに取得したデータが代入される

    // // ③DBとの切断
    $dbh = null;
  }catch (PDOException $e){
      print('Error:'.$e->getMessage());
      die();
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/form.css">
  <link rel="stylesheet" href="assets/css/timeline.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <!-- ナビゲーションバー -->
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header page-scroll">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php">
          <span class="strong-title"><i class="glyphicon glyphicon-tree-deciduous"></i> セブつぶやき</span>
        </a>
      </div>
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-right">
        </ul>
      </div>
      <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
  </nav>

  <!-- Bootstrapのcontainer -->
  <div class="container">
    <!-- Bootstrapのrow -->
    <div class="row">
      <!-- 画面左側 -->
      <div class="col-md-4 content-margin-top">
        <!-- form部分 -->
        <form action="index.php" method="post">
          <!-- nickname -->
          <div class="form-group">
            <div class="input-group">
              <?php if ($id_edit != ''): ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?php print $id_edit; ?>">
                <input type="text" name="nickname" class="form-control" id="validate-text" placeholder="nickname" value="<?php print $nickname_edit; ?>" required>
              <?php else: ?>
                <input type="hidden" name="action" value="input">
                <input type="text" name="nickname" class="form-control" id="validate-text" placeholder="nickname" required>
              <?php endif; ?>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <!-- comment -->
          <div class="form-group">
            <div class="input-group" data-validate="length" data-length="4">
              <textarea type="text" class="form-control" name="comment" id="validate-length" placeholder="comment" required><?php print $comment_edit; ?></textarea>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <!-- つぶやくボタン -->
          <button type="submit" class="btn btn-primary col-xs-12">
            <?php echo $btn_content; ?>
          </button>
        </form>
      </div>

      <!-- 画面右側 -->
      <div class="col-md-8 content-margin-top">
        <div class="timeline-centered">
<!--
          <article class="timeline-entry begin">
            <a href src="#">
              <div class="timeline-entry-inner">
                  <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                     <i class="entypo-flight"></i> +
                  </div>
              </div>
            </a>
          </article>
-->
          <?php while ($rec = $stml->fetch(PDO::FETCH_ASSOC)): // whileの開始カッコ{の代わりに:を使う ?> 
          <?php 
              $id = $rec['id'];
              $nickname = $rec['nickname'];
              $comment = $rec['comment'];
              $created = $rec['created'];
              $thumbsup = $rec['thumbsup'];
              $thumbsdown = $rec['thumbsdown'];
          ?>
          <article class="timeline-entry">
            <div class="timeline-entry-inner">
              <div class="timeline-icon bg-success">
                <?php print '<a href="index.php?id=' . $id . '&action=edit" style="color: white">'; ?>
                  <i class="entypo-feather"></i>
                  <?php if($nickname != "kneeprayer"): ?>
                    <i class="fa fa-cogs"></i>
                  <?php else: ?>
                    <i class="glyphicon glyphicon-user"></i>
                  <?php endif; ?>
                </a>
              </div>
              <div class="timeline-label">
                <h2>
                  <?php print '<a href="index.php?id='. $id . '&action=edit">'; ?>
                    <?php print $nickname; ?>
                  </a>
                  <?php print '<span>' . $comment . '</span>'; ?>
                  
                </h2>
                <p>
                  <?php print $created; ?>
                  <?php print '<a href="index.php?id='. $id . '&action=thumbsup">'; ?>
                    <i class="fa fa-thumbs-o-up" aria-hidden="true">
                      <?php print ' ' . $thumbsup; ?>
                    </i>
                  </a>
                  <?php print '<a href="index.php?id='. $id . '&action=thumbsdown">'; ?>
                    <i class="fa fa-thumbs-o-down" aria-hidden="true">
                      <?php print ' ' . $thumbsdown; ?>
                    </i>
                  </a>
                </p>
                <p>
                  <?php print '<a href="index.php?id=' . $id . '&action=delete">'; ?>
                    <i class="glyphicon glyphicon-trash"></i> 
                  </a>
                </P>
              </div>
            </div>
          </article>
          <?php endwhile; // whileの開始カッコ{の代わりに:を使う ?> 
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/form.js"></script>
</body>
</html>



