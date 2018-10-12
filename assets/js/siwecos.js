/*
Plugin Name: SIWECOS
Plugin URI:  https://www.siwecos.de
Version:     1.0.0
Description: Validate your Wordpress site against the SIWECOS.de security check
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

;(function($)
{
	$(function()
	{
		$('.GaugeMeter').gaugeMeter();
	})
})(jQuery);