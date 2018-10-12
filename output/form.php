<?php
/*
SIWECOS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

SIWECOS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SIWECOS. If not, see http://www.gnu.org/licenses/gpl-2.0.html file.
*/

// Make sure we don't expose any info if called directly
if ( ! defined( 'WP_ADMIN' ) || ! current_user_can( 'manage_options' ) ) {
	wp_die();
}

$title = 'SIWECOS';

?>
<div id="siwecos-form" class="wrap">
	<h1><?php echo esc_html( $title ); ?></h1>

	<form method="post" action="<?php echo admin_url( '/admin.php?page=siwecos' ); ?>" novalidate="novalidate">
		<table class="form-table">
			<tr>
				<td><label for="email"><?php _e( 'Email' ); ?></label></td>
				<td><input name="email" required type="email" id="email" value="" class="regular-text" /></td>
			</tr>
			<tr>
				<td><label for="password"><?php _e( 'Password' ); ?></label></td>
				<td><input name="password" required type="password" id="password" value="" class="regular-text" /></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="siwecos-buttons">
						<?php submit_button(); ?>
						<a href="https://www.siwecos.de/app/#/register" target="_blank" class="button button-link"><?php echo __( 'Register on siwecos.de', 'siwecos' ); ?></a>
					</div>
				</td>
			</tr>
		</table>
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'siwecos' ) ); ?>" />
	</form>
</div>
