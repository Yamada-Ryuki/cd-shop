<?php
	$path = '../';
	$title = 'カート | ヤマダショップ';
	require_once $path.'header.php';
	if (isset($_SESSION['product'])) {
	// カートの空判定
?>
		<main>
			<h2>ショッピングカート</h2>
<?php
	if (isset($_SESSION['msg'])) {
?>
			<p><?= $_SESSION['msg']; ?></p>
<?php
		unset($_SESSION['msg']);
	}
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
	foreach ($_SESSION['product'] as $product_code=>$product) {
?>
				<tr>
					<td><a href="../detail/detail.php?product_code=<?= $product_code; ?>"><img src="../img/<?= $product['image']; ?>.jpg" class="thumb"></a></td>
					<!-- 商品画像 -->
					<td><a href="../detail/detail.php?product_code=<?= $product_code; ?>"> <?= $product['title']; ?></a></td>
					<!-- タイトル -->
					<td><?= $product['artist']; ?></td>
					<!-- アーティスト -->
					<td><?= $product['price']; ?>円</td>
					<!-- 商品価格 -->
					<td><?= $product['sub_count']; ?></td>
<?php
	$subtotal=$product['price']*$product['sub_count'];
	$total+=$subtotal;
?>
					<!-- 商品個数 -->
					<td><?= $subtotal; ?>円</td>
					<td><a href="cart_delete.php?product_code=<?= $product_code; ?>">削除</a></td>
				</tr>
<?php
	}
?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>合計</td>
					<td><?= $total; ?>円</td>
				</tr>
			</table>
<?php
		if ($total != 0) {
?>
			<form action="pro_confirm.php" >
				<input type="submit" value="購入する">
			</form>
<?php
		}
		if(isset($_GET['product_code'])) :
?>
			<form action="../detail/detail.php" method="get">
				<input type="hidden" name="product_code" value="<?= $_GET['product_code']; ?>">
				<input type="submit" value="戻る">
			</form>
<?php
	endif;
	} else {
?>
			<p>カートに商品がありません。</p>
<?php
	}
?>
		</main>

<?php require_once $path.'footer.php'; ?>