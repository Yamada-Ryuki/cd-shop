<?php
  session_start();
  if(!isset($_SESSION['count'],$_SESSION['total'],$_SESSION['product'],$_SESSION['users'])){
    header("Location:../index.php");
    exit;
	}
	date_default_timezone_set('Japan');
	$pdo = new PDO('mysql:host=localhost;dbname=yamada;charset=utf8', 'staff', 'password');
//配送方法の確認
	if($_SESSION['count']>5){
		$ship_method = 1;
	} else {
		$ship_method = 0;
	}

// クレジットカードの購入内容確認
	if ($_POST['payment'] == 0) {
		$errorFlg = 0;
		if ($_POST['card_co'] == '') {
			$errorFlg = 1;
		}
		if (!preg_match('/^[a-zA-Z0-9]{1,}$/', $_POST['card_first_name'])) {
			$errorFlg = 1;
		}
		if (!preg_match('/^[a-zA-Z0-9]{1,}$/', $_POST['card_last_name'])) {
			$errorFlg = 1;
		}
		if (!preg_match('/^[0-9]{1,}$/', $_POST['card_num'])) {
			$errorFlg = 1;
		}
		if (!preg_match('/^[0-9]{2}$/', $_POST['card_m'])) {
			$errorFlg = 1;
		}
		if (!preg_match('/^[0-9]{2}$/', $_POST['card_y'])) {
			$errorFlg = 1;
		}
		if (!preg_match('/^[0-9]{3,4}$/', $_POST['card_sec_code'])) {
			$errorFlg = 1;
		}
		if ($errorFlg == 1) {
			$_SESSION['creditError'] = '※正しく入力してください。';
			header('Location: pro_confirm.php');
			exit;
		}
	}

//購入時の登録作業

	//ordersにデータ追加

		$sql = $pdo -> prepare('INSERT INTO orders (order_id,admin_id,buy_date,payment,quantity,payments,receipt_num,ship_ex_date,ship_ac_date,ship_method,cancel_info) VALUES ( NULL , ? , ? , ? , ? , ? , NULL , NULL , NULL , ? , 0 )');
		if ($sql -> execute([$_SESSION['users']['admin_id'],date('Y-m-d H:i:s'),$_POST['payment'],$_SESSION['count'],$_SESSION['total'],$ship_method])){

	//order_detailとshipmentに追加するためのorder_idを取得
		$sql = $pdo -> prepare('SELECT MAX(order_id) FROM orders WHERE admin_id = ? ');
		$sql -> execute([$_SESSION['users']['admin_id']]);
		$orders = $sql -> fetchall();
		// var_dump($_SESSION['users']);
		// var_dump($orders[0]['MAX(order_id)']);
		// print_r($orders);

	//order_detailにデータ追加
		foreach ($_SESSION['product'] as $product_code => $product) {
			$sub_total = $product['sub_count']*$product['price'];
			$sql = $pdo -> prepare('INSERT INTO order_detail VALUES ( ? , ? , ? , ? )');
			 $sql -> execute([$orders[0]['MAX(order_id)'],
				$product_code,
				$product['sub_count'],
				$sub_total]);}

	//shipmentにデータ追加
		switch ($_POST['shipment']) {
			case 0:
				$name = $_SESSION['users']['last_name'].$_SESSION['users']['first_name'];
				$sql = $pdo -> prepare('INSERT INTO shipment VALUES ( ? , ? , ? , ? , ? , ? , ? )');
				$sql -> execute(
					[$orders[0]['MAX(order_id)'],
					$name,
					$_SESSION['users']['post_code'],
					$_SESSION['users']['prefecture'],
					$_SESSION['users']['city'],
					$_SESSION['users']['address'],
					$_SESSION['users']['tell_num']]);
				break;
			case 1:
				$name = $_SESSION['users']['reg_last_name'].$_SESSION['users']['reg_first_name'];
				$sql = $pdo -> prepare('INSERT INTO shipment VALUES ( ? , ? , ? , ? , ? , ? , ? )');
				$sql -> execute(
					[$orders[0]['MAX(order_id)'],
					$name,
					$_SESSION['users']['reg_postcode'],
					$_SESSION['users']['reg_prefecture'],
					$_SESSION['users']['reg_city'],
					$_SESSION['users']['reg_add'],
					$_SESSION['users']['reg_num']]);
				break;
			case 2:
				if (!preg_match('/^0[0-9]{9,10}$/', htmlspecialchars($_POST['ship_num']))) {
					$msg = '※ハイフンなしの半角数字で入力してください。';
					$errorFlg = 1;
				} if ($_POST['ship_prefecture'] == "選択してください") {
					$msg = '※都道府県を選択してください。';
					$errorFlg = 1;
				} if (htmlspecialchars($_POST['ship_city']) == "") {
					$msg = '※未入力です。';
					$errorFlg = 1;
				} if (htmlspecialchars($_POST['ship_add']) == "") {
					$msg = '※未入力です。';
					$errorFlg = 1;
				} if (htmlspecialchars($_POST['ship_name']) == "") {
					$msg = '※未入力です。';
					$errorFlg = 1;
				} if (!preg_match('/^[0-9]{7}$/', htmlspecialchars($_POST['ship_postcode']))) {
					$msg = '※ハイフンなし7桁の数字で入力してください。';
					$errorFlg = 1;
				}

				if ($errorFlg == 1) {
					$_SESSION['addError'] = '※項目を正しく入力してください。';
					header('Location: pro_confirm.php');
					exit;
				}

				$sql = $pdo -> prepare('INSERT INTO shipment VALUES ( ? , ? , ? , ? , ? , ? , ? )');
				$sql -> execute(
					[$orders[0]['MAX(order_id)'],
					htmlspecialchars($_POST['ship_name']),
					htmlspecialchars($_POST['ship_postcode']),
					$_POST['ship_prefecture'],
					htmlspecialchars($_POST['ship_city']),
					htmlspecialchars($_POST['ship_add']),
					htmlspecialchars($_POST['ship_num'])]);
		}
	//カートを削除し、ページ遷移
		unset($_SESSION['product'],$_SESSION['count'],$_SESSION['total']);
		$_SESSION['pro'] = '購入手続きが完了しました。';
		header('Location: pro_settle.php');
		exit;
	} else {
	//失敗したのでページ遷移
		$_SESSION['pro'] = '購入手続きに失敗しました。';
		header('Location: pro_settle.php');
		exit;
	}
?>


