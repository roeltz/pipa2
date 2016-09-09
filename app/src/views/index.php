<?php
	$layout
		->template("layout-main.php")
		->content("header", "header.php")
		->content("footer", "footer.php")
	;
?>

<?php $layout->begin("main") ?>

¡HOLA MUNDO!

<?php $layout->end("main") ?>
<?php echo "XD"?>
