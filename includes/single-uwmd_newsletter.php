<!DOCTYPE html>
<html>
<head>
	<?php wp_head(); ?>
	<title><?php the_title(); ?></title>
	<style type="text/css">
		* {
			unset: all;
		}
	</style>
</head>
<body>
	<?php
		echo uwmd_newsletter_get_meta( 'uwmd_newsletter_email_html' );
	?>
	<style>
		html, body {
			margin: 0;
			padding: 0;
			margin-top: 0 !important;
		}
	</style>
</body>
</html>