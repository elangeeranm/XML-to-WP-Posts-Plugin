<?php

/*
  Plugin Name: Xml to Post
  Description: Xml to Post plugin.
  Version: 1.0.0
  Author: elangeeran
  Author URI: https://www.test.com/
  License: Ela
 */

if (!defined('ABSPATH')) {
    exit('restricted access');
}

add_action('admin_menu', 'xml_feed_menu');

function xml_feed_menu() {

    //this is the main item for the menu
    add_menu_page('Xml To Post', //page title
        'Xml To Post', //menu title
        'manage_options', //capabilities
        'get_xml_data', //menu slug
        'get_xml_data' //function
    );
	
	add_submenu_page('get_xml_data', 'Xml Settings', 'Xml Settings', 'manage_options', 'xml_option', 'xml_option_setting');
}

define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'xmlfeed-list.php');
require_once(ROOTDIR . 'xmlfeed-setting.php');