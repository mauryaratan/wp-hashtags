<?php
/**
 * Plugin Name: WP Hashtags
 * Plugin URI: http://wordpress.org/plugins/wp-hashtags
 * Description: Power of Hashtags on your WordPress
 * Author: Ram Ratan Maurya
 * Author URI: http://mauryaratan.me
 * Version: 0.1-alpha
 * Text Domain: wph
 * Domain Path: languages
 *
 * WP Hashtags is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Hashtags is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Hashtags. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package WPH
 * @category Core
 * @author Ram Ratan Maurya
 * @version 0.1-alpha
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


final class WP_Hashtags {

	public function __construct() {
		add_filter( 'the_content', array( $this, 'wph_filter_content' ), 1000, 2 );
		add_action( 'init', array( $this, 'wph_init' ) );
		register_activation_hook( __FILE__, array( $this, 'wph_api_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'wph_api_activation' ) );
	}

	/**
	 * Filter post content to display linked anchor tags.
	 * 
	 * @param  string 	$content Post content
	 * @param  int 		$id Post ID.
	 * @return string 	Filtered content containing hashtags
	 */
	public function wph_filter_content( $content, $id = null ) {
		if ( empty( $id ) ) {
			$id = get_the_ID();
		}
		
		return preg_replace("/(#([_a-z0-9\-]+))/i", "<a href='". hashtag_url('/$2/') ."' title=\"Hashtag $1\">$1</a>", $content);
	}

	/**
	 * Register our rewrite rules for the API
	 *
	 * @return void
	 */
	public function wph_init() {
		add_rewrite_rule( '^hashtag/?$','index.php?hashtag=/','top' );
		add_rewrite_rule( '^hashtag(.*)?','index.php?hashtag=$matches[1]','top' );

		global $wp;
		$wp->add_query_var('hashtag');
	}

	/**
	 * Flush the rewrite rules on activation
	 *
	 * @return void
	 */
	public function wph_api_activation() {
		flush_rewrite_rules();
	}

	/**
	 * Also flush the rewrite rules on deactivation
	 *
	 * @return void
	 */
	public function wph_api_deactivation() {
		flush_rewrite_rules();
	}
}

function get_hashtag_url( $blog_id = null, $path = '' ) {
	$url = get_home_url( $blog_id, 'hashtag' );

	if ( !empty( $path ) && is_string( $path ) && strpos( $path, '..' ) === false )
		$url .= '/' . ltrim( $path, '/' );

	return apply_filters( 'hashtag_url', $url, $path, $blog_id );
}

function hashtag_url( $path = '' ) {
	return get_hashtag_url( null, $path );
}

function wp_hashtags(){
	return new WP_Hashtags();
}
wp_hashtags();
