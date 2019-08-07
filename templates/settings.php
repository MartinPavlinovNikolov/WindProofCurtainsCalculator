<div class="wrap">
	
	<?php settings_errors(); ?>

	<form method="POST" action="options.php">
		<?php 
		settings_fields('wpcc_options_group');
		do_settings_sections('wpcc_settings');
		submit_button();
		?>
	</form>
</div>