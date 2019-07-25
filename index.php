<?php 

require_once("configs.php");
require_once("helpers.lang.php");

$langFolder = $configs['language']['folder'];


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>PHP gettext testing</title>
</head>
<body>	
	<?php loadLanguage( 'fa_IR', $langFolder ); ?>
	<h3><?= _("Hello World"); ?></h3>
	<p><?php _e("An example"); ?></p>
	<hr/>
	
	<?php loadLanguage( 'zh_CN', $langFolder ); ?>
	<h3><?= _("Hello World"); ?></h3>
	<p><?= _("An example"); ?></p>
</body>
</html>