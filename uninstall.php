<?php

/**
 * Trigger this file on unistall
 */

 if (! defined('WP_UNINSTALL_PLUGIN')) {
     die;
 }
global $wpdb;
$cptName = 'team-members';
$tablePostMeta = $wpdb->prefix . 'postmeta';
$tablePosts = $wpdb->prefix . 'posts';
$postMetaDeleteQuery = "DELETE FROM $tablePostMeta".
                      " WHERE post_id IN".
                      " (SELECT id FROM $tablePosts WHERE post_type='$cptName'";
$postDeleteQuery = "DELETE FROM $tablePosts WHERE post_type='$cptName'";
$wpdb->query($postMetaDeleteQuery);
$wpdb->query($postDeleteQuery);
