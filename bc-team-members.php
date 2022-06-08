<?php
/**
 * Plugin Name:       BC Team Members
 * Plugin URI:        http://brandcoders.com/
 * Description:       Displays your team members with class.
 * Version:           1.0
 * Author:            BrandCoders
 * Text Domain:       bc-team-members
 */
defined('ABSPATH') or die('Naaaah');

/**
 * BC Team Members Class
 */
class BC_Team_Member
{

    //Runs on activation
    public function __construct()
    {
        add_action('init', [ $this, 'bc_teams_styles_js' ]);//Loads in styles and js
        add_action('init', [ $this, 'bc_teams_permalinks' ]);//Refreshes permalinks so you dont have to
        add_action('init', [ $this, 'register_team_shortcode' ]);//Register shortcode
        add_action('init', [ $this, 'create_bc_team_settings' ]);//Add ACF options
    }

    //What gets activated when Plugin turns on
    public function bc_team_activate()
    {
        $this -> create_bc_team_settings();
        $this -> register_team_shortcode();
    }

    //Creates the CPT
    public function create_team_cpt()
    {
        include_once(get_template_directory() . '/inc/classes/CreateCPT.php');
        include_once(get_template_directory() . '/inc/classes/TaxonomyManager.php');
        
        //Checks to see if archives included
        $inc_archive = get_field('include_archive_page', 'option') ? true : false;        
        
        if (class_exists('CreateCPT')) {
            (new CreateCPT('Team Members', 'Team Member', 'team-members'))
                ->set('menu_icon', 'dashicons-id')
                ->set('supports', ['page-attributes', 'title'])     
                ->set('has_archive', $inc_archive)                                        
                ->set('taxonomies', [''])                  
                ->create();
        }
        TaxonomyManager::addNew('Team Categories', 'team-members', true, false);

        //Create Default Category For Custom Post Type
        function bc_set_default_team_category($post_id, $post)
        {
            if ('publish' === $post->post_status && $post->post_type === 'team-members') {
                $defaults = array(
                    'team_categories' => array( 'uncategorized' )
                );
                $taxonomies = get_object_taxonomies($post->post_type);
        
                foreach ((array) $taxonomies as $taxonomy) {
                    $terms = wp_get_post_terms($post_id, $taxonomy);
                    if (empty($terms) && array_key_exists($taxonomy, $defaults)) {
                        wp_set_object_terms($post_id, $defaults[$taxonomy], $taxonomy);
                    }
                }
            }
        }
        add_action('save_post', 'bc_set_default_team_category', 100, 2);
    }

    //Grabs ACF for the CPT
    public function create_bc_team_settings()
    {
        include_once(__DIR__ . '/acf-settings/bc-team-member-settings.php');
        
        if (class_exists('BC_Team_Custom_Field_Settings')) {
            $team_settings = new BC_Team_Custom_Field_Settings();
        }

        //Add new options page for Team Members
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(array(
                'page_title' 	=> 'Team Member Options',
                'menu_title'	=> 'Team Member Options',
                'menu_slug' 	=> 'team-member-options',
                'parent_slug'    => 'edit.php?post_type=team-members',
                'position' 	    => '5',
            ));            
        }
    }

    // Refreshes permalinks
    public function bc_teams_permalinks()
    {
        flush_rewrite_rules();
    }

    //Load in styles
    public function bc_teams_styles_js()
    {
        wp_register_style('bc-team-members-style', plugins_url('dist/style.min.css', __FILE__));
        wp_enqueue_style('bc-team-members-style');
        wp_register_script('bc-team-members-js', plugins_url('dist/main.min.js', __FILE__));
        wp_enqueue_script('bc-team-members-js');
    }

    //registers shortcode
    public function register_team_shortcode()
    {
        include_once(__DIR__ . '/shortcode/bc-team-members-shortcode.php');
    }
}


/**
 * Making Sure BC_Team_Member Exists
 */
if (class_exists('BC_Team_Member')) {
    $bc_team_member = new BC_Team_Member();
    $bc_team_member -> create_team_cpt();
}

/**
 * Archive path for Team-Members CPT
 */
add_filter('archive_template', 'get_bc_team_members_archive_template');
function get_bc_team_members_archive_template($archive_template)
{
    global $post;
    if ($post->post_type == 'team-members') {
        $archive_template = dirname(__FILE__) . '/archive-team-members.php';
    }
    return $archive_template;
}

/**
 * Single path for Team-Members CPT
 */
add_filter('single_template', 'get_bc_team_members_single_template');
function get_bc_team_members_single_template($single_template)
{
    global $post;
    if ($post->post_type == 'team-members') {
        $single_template = dirname(__FILE__) . '/single-team-members.php';
    }
    return $single_template;
}

/**
 * Activation of plugin
 */
register_activation_hook(__FILE__, [$bc_team_member, 'bc_team_activate']);

/**
 * Deactives the plugin
 */
register_deactivation_hook(__FILE__, function () {

    //Unregister CPT so it is no longer in the memory
    unregister_post_type('team-members');

    //Clear permalinks so it is no longer in database
    flush_rewrite_rules();
});
