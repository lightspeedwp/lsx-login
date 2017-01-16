<?php
	$lsx_login = LSX_Login::get_instance();
?>

<div class="uix-field-wrapper">
	<table class="form-table">
		<tbody>
			<tr class="form-field">
				<th scope="row">
					<label for="description"><?php esc_html_e( 'Enable site lockout', 'lsx-login' ); ?></label>
				</th>

				<td>
					<input type="checkbox" {{#if lock_site}} checked="checked" {{/if}} name="lock_site" />
					<small><?php esc_html_e('This will make your entire website private.','lsx-login'); ?></small>
				</td>
			</tr>

			<tr class="form-field-wrap">
				<th scope="row">
					<label for="my_account_id"><?php esc_html_e( 'My Account Page', 'lsx-login' ); ?></label>
				</th>

				<?php
					$args = array(
						'sort_order'   => 'asc',
						'sort_column'  => 'post_title',
						'hierarchical' => 1,
						'child_of'     => 0,
						'parent'       => -1,
						'post_type'    => 'page',
						'post_status'  => 'publish'
					);

					$pages = get_pages( $args );
					$current_page = '';

					if ( isset( $lsx_login->options ) && isset( $lsx_login->options['login']['my_account_id'] ) ) {
						$current_page = $lsx_login->options['login']['my_account_id'];
					}
				?>

				<td>
					<select value="{{my_account_id}}" name="my_account_id">
						<?php if ( false !== $pages && '' !== $pages ) : ?>
							<option value="" <?php if(  '' === $current_page ) { echo 'selected="selected"'; } ?>><?php esc_html_e( 'Select a page', 'lsx-login' ); ?></option>
							
							<?php foreach( $pages as $page ) : ?>
								<option value="<?php echo esc_attr( $page->ID ); ?>" <?php if ( $current_page === $page->ID ) { echo 'selected="selected"'; } ?>><?php echo esc_html( $page->post_title ); ?></option>
							<?php endforeach; ?>
						<?php else : ?>
							<option value="" {{#if my_account_id value=""}}selected="selected"{{/if}}><?php esc_html_e( 'You have no page available', 'lsx-login' ); ?></option>
						<?php endif; ?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</div>