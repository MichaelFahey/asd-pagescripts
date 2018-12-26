<?php
/**
 *
 * This is the root file of the ASD PageScripts WordPress plugin
 *
 * @package ASD_PageScripts
 * Plugin Name:    ASD PageScripts
 * Plugin URI:     https://artisansitedesigns.com/products/asd-pagescripts/
 * Description:    Adds a custom field to a standard post type,
				   only accessible to admin, for contining and outputting
				   scripts-per-page.
 * Author:         Michael H Fahey
 * Author URI:     https://artisansitedesigns.com/staff/michael-h-fahey/
 * Text Domain:    asd_pagescripts
 * License:        GPL3
 * Version:        1.201812042
 *
 * ASD PageScripts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * ASD PageScripts is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ASD PageScripts. If not, see
 * https://www.gnu.org/licenses/gpl.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '' );
}

if ( ! defined( 'ASD_PAGESCRIPTS_DIR' ) ) {
	define( 'ASD_PAGESCRIPTS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ASD_PAGESCRIPTS_URL' ) ) {
	define( 'ASD_PAGESCRIPTS_URL', plugin_dir_url( __FILE__ ) );
}

require_once 'includes/asd-admin-menu/asd-admin-menu.php';
require_once 'includes/register-pagescripts.php';


/** ----------------------------------------------------------------------------
 *   Function asd_pagescript_plugin_action_links()
 *   Adds links to the Dashboard Plugin page for this plugin.
 *   Hooks to admin_menu action.
 *  ----------------------------------------------------------------------------
 *
 *   @param Array $actions -  Returned as an array of html links.
 */
function asd_pagescript_plugin_action_links( $actions ) {
	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		$actions[0] = '<a target="_blank" href="https://artisansitedesigns.com/plugins/asd-pagescripts#support/">Help</a>';
		/* $actions[1] = '<a href="' . admin_url()   . '">' .  'Settings'  . '</a>';  */
	}
		return apply_filters( 'pagescripts_actions', $actions );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'asd_pagescript_plugin_action_links' );

