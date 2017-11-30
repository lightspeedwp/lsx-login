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
				<?php endif;
			?>

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
						'post_status'  => 'publish',
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
							<option value="" <?php if ( '' === $current_page ) { echo 'selected="selected"'; } ?>><?php esc_html_e( 'Select a page', 'lsx-login' ); ?></option>

							<?php foreach ( $pages as $page ) : ?>
								<option value="<?php echo esc_attr( $page->ID ); ?>" <?php if ( $current_page === $page->ID ) { echo 'selected="selected"'; } ?>><?php echo esc_html( $page->post_title ); ?></option>
							<?php endforeach; ?>
						<?php else : ?>
							<option value="" {{#if my_account_id value=""}}selected="selected"{{/if}}><?php esc_html_e( 'You have no page available', 'lsx-login' ); ?></option>
						<?php endif; ?>
					</select>
				</td>
			</tr>

			<tr class="form-field">
				<th scope="row" colspan="2">
					<h3><?php esc_html_e( 'Welcome Email', 'lsx-login' ); ?></h3>
				</th>
			</tr>

			<tr class="form-field">
				<th scope="row">
					<label for="welcome_email_from_name"><?php esc_html_e( 'From Name', 'lsx-login' ); ?></label>
				</th>
				<td>
					<input type="text" name="welcome_email_from_name" {{#if welcome_email_from_name}} value="{{welcome_email_from_name}}" {{/if}}>
				</td>
			</tr>

			<tr class="form-field">
				<th scope="row">
					<label for="welcome_email_from_email"><?php esc_html_e( 'From Email', 'lsx-login' ); ?></label>
				</th>
				<td>
					<input type="text" name="welcome_email_from_email" {{#if welcome_email_from_email}} value="{{welcome_email_from_email}}" {{/if}}>
				</td>
			</tr>

			<tr class="form-field">
				<th scope="row">
					<label for="welcome_email_subject"><?php esc_html_e( 'Subject', 'lsx-login' ); ?></label>
				</th>
				<td>
					<input type="text" name="welcome_email_subject" {{#if welcome_email_subject}} value="{{welcome_email_subject}}" {{/if}}>
				</td>
			</tr>

			<?php
				$content = '';

				if ( isset( $lsx_login->options ) && ! empty( $lsx_login->options['login']['welcome_email_message'] ) ) {
					$content = $lsx_login->options['login']['welcome_email_message'];
				}

				$settings = array(
					'media_buttons' => false,
					'textarea_rows' => 5,
				);
			?>

			<tr class="form-field">
				<th scope="row">
					<label for="welcome_email_message"><?php esc_html_e( 'Message', 'lsx-login' ); ?></label>
				</th>
				<td>
					<textarea rows="8" name="welcome_email_message">{{welcome_email_message}}</textarea>
				</td>
			</tr>

			<tr class="form-field">
				<th scope="row" colspan="2">
					<h3><?php esc_html_e( 'Send Message', 'lsx-login' ); ?></h3>
				</th>
			</tr>

			<?php
				$roles = get_editable_roles();

				if ( count( $roles ) > 0 ) : ?>
					<tr class="form-field">
						<th scope="row">
							<label for="welcome_email_user_roles"><?php esc_html_e( 'User Roles', 'lsx-login' ); ?></label>
						</th>

						<td>
							<?php foreach ( $roles as $role => $data ) : ?>
								<input type="checkbox" name="welcome_email_user_roles[]" value="<?php echo esc_attr( $role ); ?>" />
								<small><?php echo esc_html( $data['name'] ); ?></small>
								<br>
							<?php endforeach; ?>
						</td>
					</tr>
				<?php endif;
			?>

			<tr class="form-field">
				<th scope="row"></th>
				<td>
					<button type="button" class="button button-primary" name="welcome_email_invite"><?php esc_html_e( 'Send Message', 'lsx-login' ); ?></button>
				</td>
			</tr>
		</tbody>
	</table>
</div>
