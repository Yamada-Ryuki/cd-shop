<?php
	session_start();

	// 不正遷移対策
	if (!isset($_GET['product_code'])) {
		header('Location: ../index.php');
		exit;
	}

	unset($_SESSION['product'][$_GET['product_code']]);
	$_SESSION['msg'] = 'カートから商品を削除しました。';
	header('Location: cart.php');
	exit;
?>
