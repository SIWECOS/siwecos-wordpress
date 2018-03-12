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
along with SIWECOS. If not, see LICENCE file.
*/

defined('SIWECOS_VERSION') or die;

class SIWECOSView
{
	protected $scanResult = '/scan/result';

	public function display()
	{
		$token = $this->getToken();
		$domaintoken = $this->getDomainToken();

		wp_enqueue_style('siwecos.css',  plugins_url('assets/css/siwecos.css', SIWECOS_PLUGIN_FILE));

		if (empty($token) || empty($domaintoken))
		{
			require SIWECOS_VIEW_DIR . 'form.php';

			return;
		}

		$result = $this->getScanResult();

		if ($result !== false)
		{
			wp_enqueue_script('jquery.gauge.min.js', plugins_url('assets/js/jquery.gauge.min.js', SIWECOS_PLUGIN_FILE));
			wp_enqueue_script('siwecos.js', plugins_url('assets/js/siwecos.js', SIWECOS_PLUGIN_FILE));
		}

		require SIWECOS_VIEW_DIR . 'validation.php';
	}

	public function loadJS()
	{

	}

	protected function getScanResult()
	{
		$token = $this->getToken();

		if ($token === false)
		{
			return false;
		}

		$siteurl = get_option('home');

		$scanresult = SiwecosRequest::request($this->scanResult . '?domain=' . urlencode(untrailingslashit($siteurl)), ['userToken' => $token], null, 'get');

		if (empty($scanresult->scanFinished) || empty($scanresult->scanners) || !is_array($scanresult->scanners))
		{
			return false;
		}

		return $scanresult;
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

	/**
	 * Try to loads an existing token from the user data
	 *
	 * @return string | bool
	 */

	protected function getDomainToken()
	{
		return get_option('siwecos_domaintoken');
	}
}