<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script type="text/javascript">
	//$(document).ready(function() {
		var domain = "https://shielded-everglades-47676.herokuapp.com";
		$('#sub').click(function(event) {
			event.preventDefault();
			$.ajax({
				type: "POST",
				url: domain + '/core/parser/index.php',
				dataType: 'json',
				data: {page : $("#page").val()},
				success: function(data) {
					alert(data);
				}
			});
		});
	//});
</script>
</head>
<body>
	<div class="container">
		<div class="content-main">
			<h2>Парсер картинок</h2>
		</div>
	</div>
	<div class="container">
		<div class="col-sm-6">
			<form class="form-horizontal" role="form" name="feedback" method="post">
				<div class="form-group">
					<label class="col-sm-2 control-label">Количество страниц для </label>
					<div class="col-sm-10">
						<input type="text" name="page" id="page" class="form-control" id="inputFIO" placeholder="Количество страниц"/>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" id="sub" class="btn btn-default">Отправить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="container">
		<div class="content-main">
			<p>
				<a href="/main.html">Форма обратной связи</a>
			</p>
		</div>
	</div>
</body>
</html>

<?php
include 'parser.php';
if(!empty($_POST['page'])){
	$parse = new parse();
	$page = htmlspecialchars($_POST['page']);
	$parse->run($page);
	echo '<p>Парсинг завершен</p>';
}

