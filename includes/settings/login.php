<?php
	$lsx_login = LSX_Login::get_instance();
?>

<div class="uix-field-wrapper">

	<table class="form-table">

		<tbody>
			<tr class="form-field">
				<th scope="row">
					<label for="description"><?php esc_html_e('Disable site lockout','lsx-login'); ?></label>
				</th>
				<td>
					<input type="checkbox" {{#if disable_archives}} checked="checked" {{/if}} name="disable_archives" />
					<small><?php esc_html_e('This will make your site more "Public". You will need to choose which pages you want to hide via the ','lsx-login'); ?></small>
				</td>
			</tr>

			<tr class="form-field-wrap">
				<th scope="row">
					<label for="request_form"><?php esc_html_e('My Account Page','lsx-login'); ?></label>
				</th>

				<?php
				$args = array(
					'sort_order' => 'asc',
					'sort_column' => 'post_title',
					'hierarchical' => 1,
					'child_of' => 0,
					'parent' => -1,
					'post_type' => 'page',
					'post_status' => 'publish'
				);
				$pages = get_pages($args);
				$current_page = '';
				if(isset($lsx_login->options) && isset($lsx_login->options['login']['my_account_id'])){
					$current_page = $lsx_login->options['login']['my_account_id'];
				}
				?>

				<td>
					<select value="{{my_account_id}}" name="my_account_id">
						<?php
						if(false !== $pages && '' !== $pages){ ?>
							<option value="" <?php if('' === $current_page) { echo 'selected="selected"'; } ?>><?php esc_html_e('Select a form','lsx-login'); ?></option>
							<?php
							foreach($pages as $page){ ?>
								<option value="<?php echo esc_attr( $page->ID ); ?>" <?php if($current_page === $page->ID) { echo 'selected="selected"'; } ?>><?php echo esc_html( $page->post_title ); ?></option>
								<?php
							}
						}else{ ?>
							<option value="" {{#if my_account_id value=""}}selected="selected"{{/if}}><?php esc_html_e('You have no form available','lsx-login'); ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>

	<?php do_action( 'lsx_framework_display_tab_bottom', 'display' ); ?>
</div>