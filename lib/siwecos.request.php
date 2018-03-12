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

abstract class SiwecosRequest
{
	static protected $apiurl = SIWECOS_API_URL;

	static function request($url, $headers = [], $body = null, $method = 'post')
	{
		$methodname = 'wp_remote_post';

		if ($method == 'get')
		{
			$methodname = 'wp_remote_get';
		}

		$attr = [
			'headers' => [
						'Accept' => 'application/json',
						'Content-Type' => 'application/json;charset=UTF-8'
			]
		];

		if (is_array($headers))
		{
			$attr['headers'] = array_merge($attr['headers'], $headers);
		}

		if (is_array($body))
		{
			$attr['body'] = is_array($body) ? json_encode($body) : $body;
		}

		$response = [];

		if ($method == 'get')
		{
			$response = wp_remote_get(static::$apiurl . $url, $attr);
		}
		else
		{
			$response = wp_remote_post(static::$apiurl . $url, $attr);
		}

		if (wp_remote_retrieve_response_code($response) != 200)
		{
			return false;
		}

		$body = wp_remote_retrieve_body($response);

		$content = json_decode($body);

		if (json_last_error() !== JSON_ERROR_NONE || !empty($content->hasFailed))
		{
			return false;
		}

		return $content;
	}
}