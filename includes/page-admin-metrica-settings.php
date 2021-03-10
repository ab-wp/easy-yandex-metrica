<div class="wrap">
	<h1><?php _e('Configuration Yandex.Metrica', 'easy-yandex-metrica') ?></h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'abwp-eym-options-group' ); ?>
		<?php settings_errors(); ?>
		<table class="form-table">
			<tr valign="top">
				<td colspan="2">
					<?php _e('To display data, you must configure the following:', 'easy-yandex-metrica') ?>
					<ol>
						<li><a class="button" target="_blank" href="https://oauth.yandex.ru/authorize?response_type=token&amp;client_id=<?php echo $this->yandex_metrika_client_id; ?>"><?php _e('Allow access to the plugin and get the Token', 'easy-yandex-metrica') ?></a></li>
						<li><?php _e('Enter the Token and click "Save"', 'easy-yandex-metrica') ?></li>
						<li><?php _e('Choose the counter that need to display, and then click "Save"', 'easy-yandex-metrica') ?></li>
					</ol>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Yandex.Metrica', 'easy-yandex-metrica') ?> <?php _e('Token', 'easy-yandex-metrica') ?></th>
				<td>
					<input type="text" name="abwp_eym_token"  class="large-text code" value="<?php echo htmlspecialchars($this->yandex_metrika_token); ?>"  placeholder="<?php _e('Yandex.Metrica', 'easy-yandex-metrica') ?> <?php _e('Token', 'easy-yandex-metrica') ?>" />
				</td>
			</tr>
			<?php if ('' != $this->yandex_metrika_token) : ?>
				<tr valign="top">
					<th scope="row"><?php _e('Counter', 'easy-yandex-metrica') ?></th>
					<td><?php echo $this->get_all_counters_select(); ?></td>
				</tr>
			<?php endif; ?>
		</table>
		<?php submit_button(); ?>
	</form>

</div>