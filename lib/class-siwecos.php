<?php
/*
Plugin Name: SIWECOS
Plugin URI:  https://www.siwecos.de
Version:     1.0.0
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

defined( 'SIWECOS_VERSION' ) or die;

require_once SIWECOS_LIB_DIR . 'class-siwecosview.php';

class SIWECOS {
	protected $is_init = false;

	protected $login = '/users/login';

	protected $list_domains = '/domains/listDomains';

	protected $add_domain = '/domains/addNewDomain';

	protected $verify_domain = '/domains/verifyDomain';

	protected $scan_start = '/scan/start';

	public function init() {
		if ( $this->is_init ) {
			return;
		}

		$page = filter_input( INPUT_GET, 'page' );

		if ( is_admin() && 'siwecos' === $page ) {

			$nonce    = filter_input( INPUT_POST, '_wpnonce' );
			$email    = filter_input( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL );
			$password = filter_input( INPUT_POST, 'password' );

			if ( wp_verify_nonce( $nonce, 'siwecos' ) && is_email( $email ) && $password ) {
				$this->generate_token( $email, $password );
			}

			$token = $this->get_token();

			if ( false !== $token ) {
				$action = filter_input( INPUT_GET, 'action' );

				$this->validate_domain( 'scan' === $action );
			}
		}

		$this->is_init = true;
	}

	public function activate() {
	}

	public function deactivate() {
	}

	public function uninstall() {
	}

	public function create_menu() {
		add_submenu_page(
			'tools.php',
			'SIWECOS Validator',
			'SIWECOS',
			'manage_options',
			'siwecos',
			array( new SIWECOSView, 'display' )
		);
	}

	protected function validate_domain( $scan = false ) {
		$domains = $this->get_domains();

		$found    = false;
		$verified = false;

		$siteurl = get_option( 'home' );

		foreach ( $domains as $domain ) {
			if ( $domain->domain === $siteurl ) {
				$found = true;

				// @codingStandardsIgnoreLine
				$verified = $domain->verificationStatus;

				$domaintoken = $this->get_domain_token();

				// @codingStandardsIgnoreLine
				if ( empty( $domaintoken ) ) {
					// @codingStandardsIgnoreLine
					$this->safe_domain_token( $domain->domainToken );
				}

				break;
			}
		}

		if ( ! $found ) {
			$this->safe_domain_token( '' );

			$found = $this->register_domain();
		}

		if ( $found && ! $verified ) {
			$verified = $this->verify_domain();

			// New domain => init scan
			$scan = $scan || $verified;
		}

		if ( $scan ) {
			$this->start_scan();
		}
	}

	protected function get_domains() {
		$token = $this->get_token();

		if ( false === $token ) {
			return array();
		}

		$domains = SiwecosRequest::request( $this->list_domains, array( 'userToken' => $token ) );

		if ( empty( $domains->domains ) ) {
			return array();
		}

		return $domains->domains;
	}

	protected function register_domain() {
		$token = $this->get_token();

		if ( false === $token ) {
			return false;
		}

		$siteurl = get_option( 'home' );

		$status = SiwecosRequest::request(
			$this->add_domain,
			array(
				'userToken' => $token,
			),
			array(
				'domain'       => untrailingslashit( $siteurl ),
				'danger_level' => 10,
			)
		);

		// @codingStandardsIgnoreLine
		if ( empty( $status->domainId ) || empty( $status->domainToken ) ) {
			return false;
		}

		// @codingStandardsIgnoreLine
		$this->safe_domain_token( $status->domainToken );

		return true;
	}

	protected function verify_domain() {
		$token = $this->get_token();

		if ( false === $token ) {
			return false;
		}

		$siteurl = get_option( 'home' );

		$status = SiwecosRequest::request(
			$this->verify_domain,
			array(
				'userToken' => $token,
			),
			array(
				'domain' => untrailingslashit( $siteurl ),
			)
		);

		// @codingStandardsIgnoreLine
		if ( empty( $status->domainId ) || empty( $status->domainToken ) ) {
			return false;
		}

		return true;
	}

	protected function generate_token( $email, $password ) {
		$token = $this->request_token( $email, $password );

		if ( false !== $token ) {
			$this->save_token( $token );
		}

		return $token;
	}

	/**
	 * Try to loads an existing token from the user data
	 *
	 * @return string | bool
	 */
	protected function get_token() {
		return get_option( 'siwecos_token', true ) ?: false;
	}

	protected function save_token( $token ) {
		return update_option( 'siwecos_token', $token );
	}

	/**
	 * Try to loads an existing token from the options
	 *
	 * @return string | bool
	 */
	protected function get_domain_token() {
		return get_option( 'siwecos_domaintoken' );
	}

	protected function safe_domain_token( $token ) {
		return update_option( 'siwecos_domaintoken', $token );
	}

	protected function request_token( $email, $password ) {
		$user = SiwecosRequest::request(
			$this->login,
			array(),
			array(
				'email'    => $email,
				'password' => $password,
			)
		);

		if ( empty( $user->token ) ) {
			return false;
		}

		return $user->token;
	}

	public function add_meta_tag() {
		$token = $this->get_domain_token();

		if ( false !== $token ) {
			echo '<meta name="siwecostoken" content="' . esc_html( $token ) . '" />';
		}
	}

	protected function start_scan() {
		$token = $this->get_token();

		if ( false === $token ) {
			return false;
		}

		$siteurl = get_option( 'home' );

		SiwecosRequest::request(
			$this->scan_start,
			array(
				'userToken' => $token,
			),
			array(
				'domain'      => untrailingslashit( $siteurl ),
				'dangerLevel' => 10,
			)
		);
	}

	public function run() {
		register_activation_hook( SIWECOS_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( SIWECOS_PLUGIN_FILE, array( $this, 'deactivate' ) );
		register_uninstall_hook( SIWECOS_PLUGIN_FILE, array( $this, 'uninstall' ) );

		add_action( 'init', array( $this, 'init' ) );

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'create_menu' ) );
		} else {
			add_action( 'wp_head', array( $this, 'add_meta_tag' ) );
		}
	}
}
