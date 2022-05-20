<?php
// .............................
// Get BuddyPress User Groups
// .............................
function renderAwakeCommunities($atts = []){
    ob_start();
    if ( bp_has_groups() ) : ?>
        <div class="communities">
            <?php while ( bp_groups() ) : bp_the_group();
                $groupId = bp_get_group_id();
                $coverImgUrl = get_template_directory_uri()."/img/communities-full-image.jpg";
                $groupCoverImage = bp_attachments_get_attachment('url', array(
                    'object_dir' => 'groups',
                    'item_id' => bp_get_group_id(),
                ));
                $group = groups_get_group( array( 'group_id' => $groupId ) );
                $totalMembers = 0;
                if ( bp_group_has_members( 'group_id='.bp_get_group_id()) ) :
                    while ( bp_group_members() ) : bp_group_the_member();
                        $totalMembers++;
                    endwhile;
                endif;
                if(!empty($groupCoverImage)) $coverImgUrl = $groupCoverImage; ?>
                <div>
                    <a href="<?php echo bp_get_group_permalink($group) ?>" class="single-community">
                        <div class="bg-container">
                            <img src="<?php echo $coverImgUrl; ?>" alt="">
                            <div class="thmbnail-box">
                                <?php bp_group_avatar(); ?>
                            </div>
                        </div>
                        <div class="community-content">
                            <h4 class="content-header"><?php bp_group_name() ?></h4>

                            <?php if ( bp_group_has_members( 'group_id='.bp_get_group_id().'&per_page=3') ) : ?>
                                <div class="members-container">
                                    <div class="list-inline members-list">
                                        <?php while ( bp_group_members() ) : bp_group_the_member(); ?>
                                            <div class="list-inline-item">
                                                <?php bp_group_member_avatar(); ?>
                                            </div>
                                        <?php endwhile; ?>
                                        <?php if($totalMembers > 3) : ?>
                                            <div class="list-inline-item" style="margin: 0px 10px;">+ <?php echo ($totalMembers - 3); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endwhile;?>
        </div>
    <?php endif;
    return ob_get_clean();
}
add_shortcode('awake_communities', 'renderAwakeCommunities');
?>