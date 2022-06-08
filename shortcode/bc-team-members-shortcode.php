<?php
/**
 * Displays Team Members in a grid
 */
add_shortcode('bc-team', function ($arg) {
    $col = isset($arg['col']) ? $arg['col'] : null;//Number of posts per collumn
    $cat = isset($arg['category']) ? $arg['category'] : 'uncategorized';//Which Category
    $post_id = isset($arg['id']) ? $arg['id'] : ''; //ID of post
    $count = isset($arg['count']) && is_numeric($arg['count']) ? $arg['count'] : '-1'; //How many posts on a page
    $order_type = isset($arg['order-type']) ? $arg['order-type'] : "";
    $order_by = isset($arg['order-by']) && $arg['order-by'] === "menu"  ? "menu_order" : '';

    $order_logic = !empty($order_by) && empty($order_type) ? 'asc' : (!empty($order_type) && empty($order_by) ? $order_type : (!empty($order_by) && !empty($order_type) ?: 'desc'));
    $clean_name = trim($cat);
    $slug_clean = strtolower(str_replace(' ', '-', $clean_name));

    //Gathers IDs into usable data
    if (!empty($post_id)) {
        $post_id = explode(', ', $post_id);
    } else {
        $post_id = [];
    }
       
    //WP Query
    $query = new WP_Query(array(
        'post_type'             => 'team-members',
        'posts_per_page'        => $count,
        'post__in'              => $post_id,
        'order'                 => $order_logic,
        'orderby'               => $order_by,
        'tax_query'             => array(
            array(
                'taxonomy' => 'team_categories',
                'field'    => 'slug',
                'terms'    => $slug_clean,
            ),
        ),
    ));

    //Team options page
    $team_options = [
        'modal'     => get_field('show_team_member_info_in_modal', 'option'),
        'excerpt'   => get_field('show_excerpt_layout', 'option'),
    ];

    ob_start(); ?>

    <?php if ($query): ?>
        <section class="bc-plugin-team-shortcode">
            <div class="container">
                <div class="bc-plugin-team-shortcode__grid 
                <?php
                if (isset($col) || $col = null) {
                    echo $col === "1" ? "-one" : false; 
                    echo $col === "2" ? "-two" : false; 
                    echo $col === "3" ? "-three" : false; 
                    echo $col === "4" ? "-four" : false; 
                    echo $col === "5" ? "-five" : false; 
                    echo $col === "6" ? "-six" : false; 
                } else {
                    echo "-three";
                }
                                                                                  
                echo $team_options['excerpt'] ? " -excerpt" : ""; ?>">
                <?php
                if ($query->have_posts()):
                    $i = 0;
                    while ($query->have_posts()) :
                                        $query->the_post();
                    //Team Member Variable
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
                        $excerpt = wp_trim_words($team_m['desc'], 40, '...'); ?>                
                    <div class="bc-plugin-team-shortcode__single <?php echo $team_options['excerpt'] ? "-excerpt" : ""; ?>">
                        <?php if ($team_m['img']): ?>
                        <a href="<?php echo $team_options['modal'] ? "#" : the_permalink(); ?>" <?php if ($team_options['modal']): ?> data-toggle="modal" data-target="#bcTeamMemberModal<?php echo $i; ?>" <?php endif; ?>>
                            <div class="img-container">
                                <img src="<?php echo $team_m['img']['url']; ?>" alt="<?php echo $team_m['img']['alt']; ?>" class="img-fluid">
                                <div class="bc-plugin-team-shortcode__hidden">
                                    <p class="-title h2"><?php echo $team_m['name'] ?: the_title(); ?></p>
                                    <span class="bc-button -button1">Learn More</span>
                                    <?php if ($team_m['social']): ?>
                                        <div class="-social-media">
                                            <?php foreach ($team_m['social'] as $social): ?>
                                                <a target="_blank" href="<?php echo $social['social_media_link']; ?>"><?php echo $social['social_media_icon']; ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                        <?php endif; ?>
                        <div class="content-container">
                            <p class="-name h2"><?php echo $team_m['name'] ?: the_title(); ?></p>                        

                            <!-- Email -->
                            <?php if ($team_m['email']): ?>
                                <a href="mailto:<?php echo $team_m['email']; ?>" class="-email"><i class="fas fa-envelope"></i> <?php echo $team_m['email']; ?></a>
                            <?php endif; ?> 
                            
                            <!-- Phone -->
                            <?php if ($team_m['phone']): ?>
                                <a href="tel:<?php echo $clean_phone; ?>" class="-phone"><i class="fas fa-phone"></i> <?php echo $team_m['phone']; ?></a>
                            <?php endif; ?>

                            <?php if ($team_options['excerpt']): ?>
                                <div class="single-excerpt">
                                    <?php echo $excerpt; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($team_options['excerpt']): ?>
                                <div class="excerpt-btn-container">
                                    <a href="<?php echo $team_options['modal'] ? "#" : the_permalink(); ?>" <?php if ($team_options['modal']): ?> data-toggle="modal" data-target="#bcTeamMemberModal<?php echo $i; ?>" <?php endif; ?> class="bc-button -button1">Read More <i class="far fa-long-arrow-right"></i></a>
                                </div>                                
                            <?php endif; ?>
                        </div>

                        <!-- Pop Up -->                    
                        <div class="modal fade" id="bcTeamMemberModal<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="bcTeamMemberModal<?php echo $i; ?>Title" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">                            
                                    <div class="modal-body bc-plugin-team-shortcode__pop-up">
                                        <button class="bc-close-modal" data-dismiss="modal">X</button>
                                        <?php if ($team_m['img']): ?>
                                            <div class="img-container">
                                                <img src="<?php echo $team_m['img']['url']; ?>" alt="<?php echo $team_m['img']['alt']; ?>" class="img-fluid">
                                            </div>
                                            <div class="content-container">
                                                <p class="-name h3"><?php echo $team_m['name'] ?: the_title(); ?></p> <!-- //Name -->
                                                <?php if ($team_m['title']): ?>
                                                    <p class="-title"><strong><?php echo $team_m['title']; ?></strong></p>
                                                <?php endif; ?> <!-- //Title -->

                                                <?php
                                                if ($team_m['social']):
                                                    foreach ($team_m['social'] as $social_pop):
                                                ?>
                                                    <a target="_blank" href="<?php echo $social_pop['social_media_link']; ?>"><?php echo $social_pop['social_media_icon']; ?></a>
                                                <?php
                                                    endforeach;
                                                endif; ?> <!-- //Social media -->
                                                <span class="desc"><?php echo $team_m['desc']; ?></span> <!-- //Desc -->
                                            </div>
                                            <div class="clear"></div>                                                                                  
                                        <?php endif; ?>                 
                                        <!-- Tabs -->                   
                                        <?php if ($team_m['repeater']): ?>
                                            <div class="pop-up-tabs">
                                                <nav class="bc-nav-list">
                                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                        <?php 
                                                        $j = 0;                                                        
                                                        ?>
                                                        <?php
                                                        if ($team_m['repeater']):
                                                            foreach ($team_m['repeater'] as $nav): ?>                                                                                                           
                                                            <a class="nav-item nav-link <?php echo $j === 0 ? "active" : null; ?>" id="nav-home-tab" data-toggle="tab" href="#bc-team-tab-<?php echo $j . get_the_ID(); ?>" role="tab" aria-controls="bc-team-tab-<?php echo $j + $k; ?>" aria-selected="true">
                                                                <?php echo $nav['achievement_tab_title'] ?: ""; ?>
                                                            </a>            
                                                        <?php
                                                                $j++;
                                                            endforeach;
                                                        endif; ?>                        
                                                    </div>
                                                </nav> <!-- //nav -->
                                                <div class="tab-content bc-tab-content" id="nav-tabContent">
                                                    <?php $k = 0; ?>
                                                    <?php
                                                    if ($team_m['repeater']):
                                                        foreach ($team_m['repeater'] as $content): ?>                                                                                                   
                                                        <div class="tab-pane fade <?php echo $k === 0 ? "show active" : null; ?>" id="bc-team-tab-<?php echo $k . get_the_ID(); ?>" role="tabpanel" aria-labelledby="nav-home-tab">
                                                            <?php echo $content['achievement_description']; ?>
                                                        </div>              
                                                    <?php
                                                            $k++;
                                                        endforeach;
                                                    endif; ?>
                                                </div><!-- //tab content -->
                                            </div>
                                        <?php endif; ?>
                                    </div>                            
                                </div>
                            </div>
                        </div>                                     
                        <!--//Pop Up -->

                    </div> <!-- //Single -->                                         
                <?php
                    $i++;
                    $k++;
                    endwhile;
                endif;
                wp_reset_query(); ?>

                </div><!-- //Grid -->
            </div> 
        </section> 
    <?php endif; ?>
<?php
    return ob_get_clean();
});
?>