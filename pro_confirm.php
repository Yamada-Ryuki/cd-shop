<!-- ひらくと1が出る -->
<?php
	$path = '../';
	$title = '購入確認 | ヤマダショップ';
	require_once $path.'header.php';
	// 使用配列
	$prefList = ['選択してください','北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'];
?>

		<main>
			<h2>カートの中身</h2>
			<form action="pro_register.php" method="POST">
<?php
	if (isset($_SESSION['product'])) {
	// カートの空判定
?>
				<table>
					<tr>
						<th>商品画像</th>
						<th>タイトル</th>
						<th>アーティスト</th>
						<th>価格</th>
						<th>個数</th>
						<th>小計</th>
					</tr>
<?php
	$total=0;
	$count=0;
	foreach ($_SESSION['product'] as $product_code=>$product) :
?>
					<tr>
						<td><img src="../img/<?= $product['image']; ?>.jpg" class="thumb"></td>
						<!-- 商品画像 -->
						<td><a href="../detail/detail.php?product_code=<?= $product_code; ?>"> <?=$product['title']; ?></a></td>
						<!-- タイトル -->
						<td><?= $product['artist'] ;?></td>
						<!-- アーティスト -->
						<td><?= $product['price'] ;?>円</td>
						<!-- 商品価格 -->
						<td><?= $product['sub_count'] ;?></td>
<?php
	$sub_total=$product['price']*$product['sub_count'];
	$total+=$sub_total;
	$count+=$product['sub_count'];
	$_SESSION['total'] = $total;
	$_SESSION['count'] = $count;
?>
						<!-- 商品個数 -->
						<td><?= $sub_total ;?>円</td>
						<td><a href="cart_delete.php?product_code=<?= $product_code; ?>">削除</a></td>
					</tr>
<?php
	endforeach;
?>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>合計</td>
						<td><?= $total ;?>円</td>
					</tr>
				</table>
<?php
	} else {
		echo 'カートに商品がありません。';
	}
	if (!isset($_SESSION['users'])) :
?>
				<p>購入手続きを進めるには<a href="../login/login.php">ログイン</a> (会員登録がお済でない場合は<a href="../register/register.php">新規登録</a>) をお願いいたします。</p>
<?php
	else :
?>
				<h2>支払方法</h2>
				<div class="payment-container">
					<div class="payment-item">
						<label><input type="radio" name="payment" value="0" id="selbox" checked>クレジットカード</label>
<?php
	if (isset($_SESSION['creditError'])) :
?>
						<p class="alert-msg"><?=  $_SESSION['creditError']; ?></p>
<?php
	unset($_SESSION['creditError']);
	endif;
?>
						<table id="input0">
<?php
	$pdo=new PDO('mysql:host=localhost;dbname=yamada;charset=utf8', 'staff', 'password');
	$sql = $pdo -> prepare('SELECT * FROM payment WHERE admin_id = ?');
	$sql -> execute([$_SESSION['users']['admin_id']]);
	foreach ($sql as $row) {
?>
							<tr id="input0">
								<th id="input0">カード種類</th>
								<td colspan="2" id="input0"><input type="text" name="card_co" value="<?=$row['card_co'];?>" id="input0"></td>
							</tr>
							<tr id="input0">
								<th id="input0">カード名義(名)(姓)<br>※半角英数字のみ</th>
								<td id="input0"><input type="text" name="card_first_name" value="<?=$row['card_first_name'];?>" id="input0"></td>
								<td id="input0"><input type="text" name="card_last_name" value="<?=$row['card_last_name'];?>" id="input0"></td>
							</tr>
							<tr id="input0">
								<th id="input0">カード番号<br>※半角英数字のみ</th>
								<td colspan="2" id="input0"><input type="text" name="card_num" value="<?=$row['card_num'];?>" id="input0"></td>
							</tr>
							<tr id="input0">
								<th id="input0">有効期限(月)(年)<br>※ともに半角数字2桁</th>
								<td id="input0"><input type="text" name="card_m" value="<?=$row['card_m'];?>" id="input0"></td>
								<td id="input0"><input type="text" name="card_y" value="<?=$row['card_y'];?>" id="input0"></td>
							</tr>
							<tr id="input0">
								<th id="input0">カードセキュリティコード<br>※3桁または4桁の半角数字</th>
								<td colspan="2" id="input0"><input type="text" name="card_sec_code" value="<?=$row['card_sec_code'];?>" id="input0"></td>
							</tr>
<?php
	}
?>

						</table>
					</div>
					<div class="payment-item">
						<label><input type="radio" name="payment" value="1">銀行振込</label>
					</div>
					<div class="payment-item">
						<label><input type="radio" name="payment" value="2">コンビニ支払い</label>
					</div>
				</div>

				<h2>お届け先情報</h2>
<!-- ここにセッションで会員氏名・電話・住所など情報表示 -->
				<div class="payment-container">
					<div class="payment-item">
						<label><input type="radio" name="shipment" value="0" checked>自宅住所</label>
						<table class="address">
							<tr>
								<th>お名前</th>
								<td><?=$_SESSION['users']['last_name'].$_SESSION['users']['first_name'];?></td>
							</tr>
							<tr>
								<th>郵便番号</th>
								<td><?=$_SESSION['users']['post_code'];?></td>
							</tr>
							<tr>
								<th>住所</th>
								<td><?=$_SESSION['users']['prefecture'];?><?=$_SESSION['users']['city'];?><?=$_SESSION['users']['address'];?></td>
							</tr>
							<tr>
								<th>電話番号</th>
								<td><?=$_SESSION['users']['tell_num'];?></td>
							</tr>
						</table>
					</div>
<?php
	if(!is_null($_SESSION['users']['reg_postcode'])) :
?>
					<div class="payment-item">
						<label><input type="radio" name="shipment" value="1">お気に入り住所</label>
						<table class="address">
							<tr>
								<th>お名前</th>
								<td><?=$_SESSION['users']['reg_last_name'].$_SESSION['users']['reg_first_name'];?></td>
							</tr>
							<tr>
								<th>郵便番号</th>
								<td><?=$_SESSION['users']['reg_postcode'];?></td>
							</tr>
							<tr>
								<th>住所</th>
								<td><?=$_SESSION['users']['reg_prefecture'];?><?=$_SESSION['users']['reg_city'];?><?=$_SESSION['users']['reg_add'];?></td>
							</tr>
							<tr>
								<th>電話番号</th>
								<td><?=$_SESSION['users']['reg_num'];?></td>
							</tr>
						</table>
					</div>
<?php endif; ?>
					<div class="payment-item">
						<label><input type="radio" name="shipment" value="2">その他住所</label>
<?php
	if (isset($_SESSION['addError'])) :
?>
						<p class="alert-msg"><?= $_SESSION['addError'] ?></p>
<?php
	unset($_SESSION['addError']);
	endif;
?>
						<table class="address">
							<tr>
								<th>お名前</th>
								<td><input type="text" name="ship_name"></td>
							</tr>
							<tr>
								<th>郵便番号<br>※ハイフンなし、半角数字7桁</th>
								<td><input type="text" name="ship_postcode"></td>
							</tr>
							<tr>
								<th>都道府県</th>
								<td>
									<select name="ship_prefecture">
<?php
	foreach ($prefList as $pref){
?>
										<option value="<?= $pref ;?>"><?= $pref ;?></option>
<?php
	}
?>
									</select>
								</td>
							</tr>
							<tr>
								<th>市区町村</th>
								<td><input type="text" name="ship_city"></td>
							</tr>
							<tr>
								<th>番地・建物名・<br>部屋番号等</th>
								<td><input type="text" name="ship_add"></td>
							</tr>
							<tr>
								<th>電話番号<br>※ハイフンなし、半角数字のみ</th>
								<td><input type="text" name="ship_num"></td>
							</tr>
						</table>
					</div>
				</div>
				<p>商品を購入しますか？</p>
				<input type="submit" value='購入'>
			</form>
<?php endif; ?>
				<a href="../cart/cart.php">前のページに戻る</a>
			</main>
<?php require '../footer.php';?>
