<?php
/**
 * The template for displaying all single Team Member posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
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
    ],
];

//Banner
$team_b = [
    'inc'       => get_field('include_team_member_banner'),
    'img'       => get_field('team_member_banner_image'),
    'title'     => get_field('team_member_banner_title'),
    'sub'       => get_field('team_member_banner_sub_title'),
    'over_lay'  => get_field('include_team_member_banner_overlay'),
];

//Team Member
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
$clean_phone = preg_replace('/\D+/', '', $team_m['phone']);
?>

<?php if ($team_b['inc']): ?>
<section class="bc-plugin-single-team-member-banner bg-cover" style="background-image:url(<?php echo $team_b['img']['url'] ?: $team_options['single']['img']['url']; ?>)">
    <div class="container">
        <div class="bc-plugin-single-team-member-banner__content">
            <h1>
                <?php
                if ($team_b['title']): echo $team_b['title'];
                    elseif (!$team_b['title'] && $team_options['single']['title']): echo $team_options['single']['title'];
                    elseif (!$team_b['title'] && !$team_options['single']['title'] && $team_m['name']): echo $team_m['name'];
                    else: the_title();
                endif;
                ?>
            </h1>
            <?php if ($team_b['sub'] || $team_options['single']['sub_title']): ?>
                <p class="h2">
                    <?php
                    if ($team_b['sub']): echo $team_b['sub'];
                        elseif (!$team_b['sub'] && $team_options['single']['sub_title']): echo $team_options['single']['sub_title'];
                    endif;
                    ?>                    
                </p>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($team_b['over_lay']): ?>
        <div class="bc-plugin-single-team-member-banner__overlay"></div>
    <?php endif; ?>
</section>
<?php endif; ?>

<section class="bc-plugin-single-team-member">
    <div class="bc-plugin-single-team-member__content">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="bc-plugin-single-team-member__left">
                        <!-- Image -->
                        <?php if ($team_m['img']): ?>
                            <img src="<?php echo $team_m['img']['url']; ?>" alt="<?php echo $team_m['img']['title']; ?>" class="img-fluid">
                        <?php endif; ?>
                        <!-- Email -->
                        <?php if ($team_m['email']): ?>
                            <a href="mailto:<?php echo $team_m['email']; ?>" class="-email"><i class="fas fa-envelope"></i> <?php echo $team_m['email']; ?></a>
                        <?php endif; ?> 
                        
                        <!-- Phone -->
                        <?php if ($team_m['phone']): ?>
                            <a href="tel:<?php echo $clean_phone; ?>" class="-phone"><i class="fas fa-phone"></i> <?php echo $team_m['phone']; ?></a>
                        <?php endif; ?>

                        <?php
                        if ($team_m['social']):
                            foreach ($team_m['social'] as $social):
                        ?>
                            <a href="<?php echo $social['social_media_link']; ?>" class="-social"><?php echo $social['social_media_icon']; ?></a>
                        <?php
                            endforeach;
                        endif;
                        ?>

                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-12">
                    <div class="bc-plugin-single-team-member__right">
                        <h2 class="h2"><?php echo $team_m['name'] ?: the_title(); ?></h2>
                        <?php if ($team_m['title']): ?>
                            <p class="-title"><?php echo $team_m['title']; ?></p>
                        <?php endif; ?><!--//Title -->
                                                
                        <?php if ($team_m['desc']): ?>
                            <div class="bc-plugin-single-team-member__desc">
                                <span><?php echo $team_m['desc']; ?></span>
                            </div>
                        <?php endif; ?><!--//Desc -->

                        <?php if ($team_m['repeater']): ?>
                            <div class="bc-plugin-single-team-member__tabs">
                                <nav class="bc-nav-list">
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <?php $i = 0; ?>
                                        <?php
                                        if ($team_m['repeater']):
                                            foreach ($team_m['repeater'] as $nav): ?>                                                                                                           
                                            <a class="nav-item nav-link <?php echo $i === 0 ? "active" : null; ?>" id="nav-home-tab" data-toggle="tab" href="#bc-team-tab-<?php echo $i; ?>" role="tab" aria-controls="bc-team-tab-<?php echo $i; ?>" aria-selected="true">
                                                <?php echo $nav['achievement_tab_title']; ?>
                                            </a>            
                                        <?php
                                                $i++;
                                            endforeach;
                                        endif;
                                        ?>                        
                                    </div>
                                </nav> <!-- //nav -->
                                <div class="tab-content bc-tab-content" id="nav-tabContent">
                                    <?php $j = 0; ?>
                                    <?php
                                    if ($team_m['repeater']):
                                        foreach ($team_m['repeater'] as $content): ?>                                                                                                   
                                        <div class="tab-pane fade <?php echo $j === 0 ? "show active" : null;  ?>" id="bc-team-tab-<?php echo $j; ?>" role="tabpanel" aria-labelledby="nav-home-tab">
                                            <?php echo $content['achievement_description']; ?>
                                        </div>              
                                    <?php
                                            $j++;
                                        endforeach;
                                    endif;
                                    ?>
                                </div><!-- //tab content -->

                            </div>
                        <?php endif; ?><!-- //tab container -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>