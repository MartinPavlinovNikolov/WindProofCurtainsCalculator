<?php
if( ! defined('WP_UNINSTALL_PLUGIN') ){
	die;
}

$orders = get_post([
	'post_type' => 'order',
	'number_posts' => -1
]);

global $wpdb;

$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'order'");
$wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id  FROM wp_posts)");
$wpdb->query("DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id  FROM wp_posts)");