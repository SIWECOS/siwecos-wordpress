<?php
/*
Plugin Name: SIWECOS
Plugin URI:  https://www.siwecos.de
Version:     1.0.1
Description: Validate your WordPress site against the SIWECOS.de security check
Author:      SIWECOS.de
Author URI:  https:/www.siwecos.de
License:     GPL2

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
if ( ! function_exists( 'run_siwecos' ) ) {
	return;
}

/**
 * Main function
 */
function run_siwecos() {
	define( 'SIWECOS_VERSION', '1.0.0' );

	define( 'SIWECOS_PLUGIN_FILE', __FILE__ );
	define( 'SIWECOS_PLUGIN_DIR', plugin_dir_path( SIWECOS_PLUGIN_FILE ) );
	define( 'SIWECOS_LIB_DIR', trailingslashit( SIWECOS_PLUGIN_DIR . 'lib' ) );
	define( 'SIWECOS_VIEW_DIR', trailingslashit( SIWECOS_PLUGIN_DIR . 'output' ) );

	define( 'SIWECOS_API_URL', 'https://bla.siwecos.de/api/v1' );

	require SIWECOS_LIB_DIR . 'class-siwecos.php';
	require SIWECOS_LIB_DIR . 'class-siwecosrequest.php';

	$siwecos = new SIWECOS();

	$siwecos->run();
}

run_siwecos();
