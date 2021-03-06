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

// Make sure we don't expose any info if called directly!
if ( ! defined( 'WP_ADMIN' ) || ! current_user_can( 'manage_options' ) ) {
	wp_die();
}

?>
<div id="siwecos-validation" class="wrap">
	<h1>SIWECOS</h1>
	<?php if ( empty( $result ) ) : ?>
	<?php echo __( 'The scan is in progress, please refresh the page and try again', 'siwecos' ); ?>
	<?php else : ?>
		<div class="gauge-box">
            <?php // @codingStandardsIgnoreLine ?>
			<div class="GaugeMeter" data-size="500" data-width="20" data-style="Arch" data-animate_gauge_colors="1" data-percent="<?php echo round( $result->weightedMedia ); ?>">

			</div>
			<div class="scanner-name"><?php echo esc_html( __( 'Result', 'siwecos' ) ); ?></div>
		</div>
		<?php foreach ( $result->scanners as $id => $scanner ) : ?>
		<div class="gauge-box">
		<div class="GaugeMeter" data-size="200" data-width="20" data-style="Arch" data-animate_gauge_colors="1" id="GaugeMeter_<?php echo (int) $id; ?>" data-percent="<?php echo (int) $scanner->score; ?>">

		</div>
		<div class="scanner-name"><?php echo esc_html( $scanner->scanner_type ); ?></div>
		</div>
		<?php endforeach; ?>
		<div class="clear"></div>
		<div class="siwecos-buttons">
			<a href="https://www.siwecos.de/app/#/domains" target="_blank" class="button button-primary large"><?php echo __( 'Open siwecos.de', 'siwecos' ); ?></a>
			<a href="<?php echo admin_url( '/admin.php?page=siwecos&action=scan' ); ?>" class="button button-primary large"><?php echo __( 'Scan again', 'siwecos' ); ?></a>
		</div>
	<?php endif; ?>
</div>
