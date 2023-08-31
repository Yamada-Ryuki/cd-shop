<?php
	session_start();

	// 不正遷移対策
	if (!isset($_POST['product_code'])) {
		header('Location: ../index.php');
		exit;
	}

	$product_code = $_POST['product_code'];
	if (!isset($_SESSION['product'])) {
		$_SESSION['product']=[];
		$sub_count=0;
	}

	if (isset($_SESSION['product'][$product_code])) {
		$sub_count=$_SESSION['product'][$product_code]['sub_count'];
		$sub_total=$_SESSION['product'][$product_code]['sub_total'];
	}

	$_SESSION['product'][$product_code]=[
		'title'=>$_POST['title'],
		'image'=>$_POST['image'],
		'price'=>$_POST['price'],
		'sub_count'=>$sub_count+$_POST['sub_count'],
		'sub_total'=>$sub_count*$_POST['price'],
		'artist'=>$_POST['artist'],
		'release_date'=>$_POST['release_date'],
		'distributor'=>$_POST['distributor']
	];

	$_SESSION['msg'] = "カートに商品を追加しました。";

	header('Location: cart.php?product_code='.$product_code);
	exit;
?>