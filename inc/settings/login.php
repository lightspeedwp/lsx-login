<div class="uix-field-wrapper">

	<table class="form-table">
		<tbody>
			<tr class="form-field">
				<th scope="row">
					<label for="description"><?php esc_html_e('Disable site lockout','tour-operator'); ?></label>
				</th>
				<td>
					<input type="checkbox" {{#if disable_archives}} checked="checked" {{/if}} name="disable_archives" />
					<small><?php esc_html_e('This will make your site more "Public". You will need to choose which pages you want to hide via the ','tour-operator'); ?></small>
				</td>
			</tr>
		</tbody>
	</table>

	<?php do_action( 'lsx_framework_display_tab_bottom', 'display' ); ?>
</div>
