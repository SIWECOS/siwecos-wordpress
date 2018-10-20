=== SIWECOS ===
Contributors: justsnipy
Tags: security
Requires at least: 4.9.0
Tested up to: 5.0
Requires PHP: 5.6.0
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SIWECOS is a free security scanning service trying to help website owners making there site more secure.
In order to do so, SIWECOS utilizes a total of 5 scanners checking several different areas of your site.

== Description ==
SIWECOS is a free security scanning service trying to help website owners making there site more secure. In order to do so, SIWECOS utilizes a total of 5 scanners checking several different areas of your site.:
- HTTP-Header Scanner: ensuring that your server tells the browser to enable additional security features
- TLS Scanner: checks your HTTPS encryption for known issues, broken chains-of-trust, outdated certificates etc
- Info Leak Scanner: checks if your sites exposes security relevant information (used WordPress version, plaintext emails)
- DOMXSS Scanner: verifies that your site is protected against so called DOMXSS attacks
- Initiative-S Scanner: utilizes the scanning technology of the free Initative-S.de service to check for malware in your websites source code

This plugin makes it much easier to use WordPress with SIWECOS by providing two main features:
- Easier onboarding: the verification handshake with SIWECOS is automated by the plugin, no need to upload specially named files
- Direct access to results: you\'ll get access to your current SIWECOS score right from the backend of your WordPress site

Please note:
The actual scanning functionality is done by siwecos.de, this plugin retrieves result data from this service - the plugin itself is not able to work without the data fetched from the service.
You can find the service's general terms and conditions here: https://siwecos.de/en/terms-and-conditions
SIWECOS' privacy policy is found here: https://international.eco.de/legal-notice/privacy-policy/


== Installation ==
Get an account on siwecos.de, install the plugin and follow the instructions