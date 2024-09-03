<?php

require "app.php";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['pass']) && !empty($_POST['pass'])) {
		if($_POST['pass'] == $config['pass']) {
			$_SESSION['login'] = $config['pass'];
			echo "<script>window.location=window.location</script>";
		} else {
			echo "invalid pass";
		}
	}
}

if(isset($_GET['logout']) && isset($_SESSION['login'])) {
	session_destroy();
	session_abort();
	session_unset();
	echo "<script>window.location.href=window.location.href</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
	<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
	<style>
		*{
			margin:0;
			padding:0;
			box-sizing: border-box;
		}
		.dz-complete .dz-image{
			background: #0f0 !important;
		}
		input{
			display: block;width: 100%;padding: 5px;margin: 1px;color: #fff;border-radius: 100px;border: solid;
		}
	</style>
	<title>Document</title>	
</head>
<body>

<?php if(isset($_SESSION['login']) && $_SESSION['login'] == $config['pass']) { ?>
	<a href="?logout">logout</a>
	<script>
		Dropzone.autoDiscover = false;
		window.addEventListener("load", function() {
			var myDropzone = new Dropzone("#my-dropzone", {
				dictDefaultMessage: "hello"
			});
			myDropzone.on("sending", function(file, xhr, formData) {
				formData.append("file", (file.fullPath ?? file.name));
			});
		})
	</script>
	<form action="file_upload_parser.php" class="dropzone" id="my-dropzone"></form>	
<?php } else { ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post' style="width: max-content;margin: 0 auto;padding: 10px;background: #fff;box-shadow: 0 0 10px #ddd;position: fixed;top: 30%;left: 50%;transform: translate(-50%);">
	<div class="dz-message" data-dz-message><span>Your Custom Message</span></div>
		<input type="text" name='pass' style="border: 1px solid #0087ff">
		<input type="submit" style="background-color: #0087ff;">
	</form>
<?php } ?>
</body>
</html>
