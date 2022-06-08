<?php
/**
 * The template for displaying BC Team Members archive page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bc-theme
 */

get_header();

//Team options page
$team_options = [
    'modal'     => get_field('show_team_member_info_in_modal', 'option'),
    'excerpt'   => get_field('show_excerpt_layout', 'option'),
    'single'    => [
        'img'       => get_field('single_page_banner_image', 'option'),
        'title'     => get_field('single_page_banner_title', 'option'),
        'sub_title' => get_field('single_page_banner_sub_title', 'option'),
    ],
    'archive'   => [
        'img'       => get_field('archive_page_banner_image', 'option'),
        'title'     => get_field('archive_page_banner_title', 'option'),
        'sub_title' => get_field('archive_page_sub_title', 'option'),
        'overlay'   => get_field('archive_banner_overlay', 'option'),
        'inc'       => get_field('archive_page_include_banner', 'option'),
    ],
];
?>

<?php if ($team_options['archive']['inc']): ?>
<section class="bc-plugin-team-archive-banner bg-cover" style="background-image:url(<?php echo $team_options['archive']['img']['url']; ?>)">
    <div class="container">
        <div class="bc-plugin-team-archive-banner__content">
            <h1 class="h1"><?php echo $team_options['archive']['title'] ?: "Our Team"; ?></h1>
            <p class="h2"><?php echo $team_options['archive']['sub_title'] ?: ""; ?></p>
        </div>
    </div>
    <?php if ($team_options['archive']['overlay']): ?>
        <div class="bc-plugin-team-archive-banner__overlay"></div>
    <?php endif; ?>
</section>
<?php endif; ?><!-- //Banner -->

<section class="bc-plugin-team-archive">
    <div class="container">
        <div class="row">
            
            <?php 
            while (have_posts()) : the_post(); 
            $team_m = [
                'img'       => get_field('team_member_image') ?: null,
                'name'      => get_field('team_member_name') ?: null,
                'title'     => get_field('team_member_title') ?: null,
                'email'     => get_field('team_member_email') ?: null,
                'phone'     => get_field('team_member_phone_number') ?: null,
                'desc'      => get_field('team_member_description') ?: null,
                'repeater'  => get_field('team_member_achievements') ?: null,
                'social'    => get_field('team_member_social_media') ?: null,
            ];       
            ?>
            <div class="col-md-6 col-lg-3">
                <a href="<?php echo the_permalink(); ?>">
                    <div class="bc-plugin-team-archive__inner">
                        <div class="img-container">
                            <?php if ($team_m['img']): ?>                            
                                <img src="<?php echo $team_m['img']['url']; ?>" alt="<?php echo $team_m['img']['title']; ?>" class="img-fluid">                            
                            <?php endif; ?>
                        </div>
                        <h2 class="h2"><?php echo $team_m['name'] ?: the_title(); ?></h2>
                        <a href="<?php the_permalink(); ?>" class="bc-button -button1">Read more</a>
                    </div>
                </a>
            </div><!-- //col -->      
            <?php endwhile; ?>

        </div><!-- //row -->
    </div><!-- //container -->
</section>

<?php
get_footer();
