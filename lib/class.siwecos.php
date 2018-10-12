<?php
/*
Plugin Name: SIWECOS
Plugin URI:  https://siwecos.de
Version:      1.0.0
Description: Validate your Wordpress Homepage against the SIWECOS security check
Author:      Benjamin Trenkle
Author URI:  https:/www.wicked-software.de
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

defined('SIWECOS_VERSION') or die;

require_once SIWECOS_LIB_DIR . 'view.siwecos.php';

class SIWECOS
{
	protected $isInit = false;

	protected $login = '/users/login';

	protected $listDomains = '/domains/listDomains';

	protected $addDomain = '/domains/addNewDomain';

	protected $verifyDomain = '/domains/verifyDomain';

	protected $scanStart = '/scan/start';

	public function __construct()
	{
	}

	public function init()
	{
		if ($this->isInit)
		{
			return;
		}

		$page = filter_input(INPUT_GET, 'page');

		if (is_admin() && $page == 'siwecos')
		{

			$nonce = filter_input(INPUT_POST, '_wpnonce');
			$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
			$password = filter_input(INPUT_POST, 'password');

			if (wp_verify_nonce($nonce, 'siwecos') && is_email($email) && $password)
			{
				$this->generateToken($email, $password);
			}

			$token = $this->getToken();

			if ($token !== false)
			{
				$action = filter_input(INPUT_GET, 'action');

				$this->validateDomain($action == 'scan');
			}
		}

		$this->isInit = true;
	}

	public function activate()
	{

	}

	public function deactivate()
	{

	}

	public function uninstall()
	{

	}

	public function createMenu()
	{
		add_menu_page(
			'SEWECOS Validator',
			'SEWECOS',
			'manage_options',
			'siwecos',
			[new SIWECOSView, 'display']
		);
	}

	protected function validateDomain($scan = false)
	{
		$domains = $this->getDomains();

		$found = false;
		$verified = false;

		$siteurl = get_option('home');

		foreach ($domains as $domain)
		{
			if ($domain->domain == $siteurl)
			{
				$found = true;

				$verified = $domain->verificationStatus;

				$domaintoken = $this->getDomainToken();

				if (empty($domaintoken))
				{
					$this->saveDomainToken($domain->domainToken);
				}

				break;
			}
		}

		if (!$found)
		{
			$this->saveDomainToken('');

			$found = $this->registerDomain();
		}

		if ($found && !$verified)
		{
			$verified = $this->verifyDomain();

			// New domain => init scan
			$scan = $scan || $verified;
		}

		if ($scan)
		{
			$this->startScan();
		}
	}

	protected function getDomains()
	{
		$token = $this->getToken();

		if ($token === false)
		{
			return [];
		}

		$domains = SiwecosRequest::request($this->listDomains, ['userToken' => $token]);

		if (empty($domains->domains))
		{
			return [];
		}

		return $domains->domains;
	}

	protected function registerDomain()
	{
		$token = $this->getToken();

		if ($token === false)
		{
			return false;
		}

		$siteurl = get_option('home');

		$status = SiwecosRequest::request($this->addDomain, ['userToken' => $token], ['domain' => untrailingslashit($siteurl), 'danger_level' => 10]);

		if (empty($status->domainId) || empty($status->domainToken))
		{
			return false;
		}

		$this->saveDomainToken($status->domainToken);

		return true;
	}

	protected function verifyDomain()
	{
		$token = $this->getToken();

		if ($token === false)
		{
			return false;
		}

		$siteurl = get_option('home');

		$status = SiwecosRequest::request($this->verifyDomain, ['userToken' => $token], ['domain' => untrailingslashit($siteurl)]);

		if (empty($status->domainId) || empty($status->domainToken))
		{
			return false;
		}

		return true;
	}

	protected function generateToken($email, $password)
	{
		$token = $this->requestToken($email, $password);

		if ($token !== false)
		{
			$this->saveToken($token);
		}

		return $token;
	}

	/**
	 * Try to loads an existing token from the user data
	 *
	 * @return string | bool
	 */
	protected function getToken()
	{

		return get_option('siwecos_token', true) ?: false;
	}

	protected function saveToken($token)
	{
		return update_option('siwecos_token', $token);
	}

	/**
	 * Try to loads an existing token from the options
	 *
	 * @return string | bool
	 */
	protected function getDomainToken()
	{
		return get_option('siwecos_domaintoken');
	}

	protected function saveDomainToken($token)
	{
		return update_option('siwecos_domaintoken', $token);
	}

	protected function requestToken($email, $password)
	{
		$user = SiwecosRequest::request($this->login, [], ['email' => $email, 'password' => $password]);

		if (empty($user->token))
		{
			return false;
		}

		return $user->token;
	}

	public function addMetaTag()
	{
		$token = $this->getDomainToken();

		if ($token !== false)
		{
			echo '<meta name="siwecostoken" content="' . esc_html($token) . '" />';
		}
	}

	protected function startScan()
	{
		$token = $this->getToken();

		if ($token === false)
		{
			return false;
		}

		$siteurl = get_option('home');

		SiwecosRequest::request($this->scanStart, ['userToken' => $token], ['domain' => untrailingslashit($siteurl), 'dangerLevel' => 10]);
	}

	public function run()
	{
		register_activation_hook(SIWECOS_PLUGIN_FILE, [$this, 'activate']);
		register_deactivation_hook(SIWECOS_PLUGIN_FILE, [$this, 'deactivate']);
		register_uninstall_hook(SIWECOS_PLUGIN_FILE, [$this, 'uninstall']);

		add_action('init', [$this, 'init']);

		if (is_admin())
		{
			add_action('admin_menu', [$this, 'createMenu']);
		}
		else
		{
			add_action('wp_head', [$this, 'addMetaTag']);
		}
	}
}