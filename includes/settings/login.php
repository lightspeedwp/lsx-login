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
					<input type="checkbox" {{#if lock_site}} checked="checked" {{/if}} name="lock_site" value="1" />
					<small><?php esc_html_e( 'Restrict access to your entire website', 'lsx-login' ); ?></small>
				</td>
			</tr>

			<?php
				$args = array(
					'public'   => true,
					//'_builtin' => false,
				);

				$post_types = get_post_types( $args, 'objects' );

				if ( count( $post_types ) > 0 ) : ?>
					<tr class="form-field">
						<th scope="row">
							<label for="description"><?php esc_html_e( 'Restrict access to post types', 'lsx-login' ); ?></label>
						</th>

						<td>
							<?php foreach ( $post_types as $post_type ) : ?>
								<?php if ( 'attachment' === $post_type->name ) continue; ?>
								<input type="checkbox" {{#if lock_post_type_<?php echo esc_attr( $post_type->name ); ?>}} checked="checked" {{/if}} name="lock_post_type_<?php echo esc_attr( $post_type->name ); ?>" value="1" />
								<small><?php echo esc_html( $post_type->label ); ?></small>
								<br>
							<?php endforeach; ?>
						</td>
					</tr>
				<?php endif; ?>

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