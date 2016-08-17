<?php
	// // ページのリフレッシュ(postをgetで上書きする)
	header("Location: index.php");
	// // 再度getでindex.phpにアクセス。
	exit(); // これいかの処理を強制終了;
?>