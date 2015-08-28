<?php

if ( class_exists( 'BP_Group_Extension' ) ) :
 
	class Group_Extension_BP_MEDIA extends BP_Group_Extension {
	    /**
	     * Your __construct() method will contain configuration options for 
	     * your extension, and will pass them to parent::init()
	     */
	    function __construct() {
	        $args = array(
	            'slug' => 'media',
	            'name' => 'Media',
	        );
	        parent::init( $args );
	    }
	 
	    /**
	     * display() contains the markup that will be displayed on the main 
	     * plugin tab
	     */
	    function display( $group_id = NULL ) {
	        $group_id = bp_get_group_id();
	        bp_media_get_template_part( 'group/image-loop');
	    }
	 
	}
endif; // if ( class_exists( 'BP_Group_Extension' ) )



/**
 * bp_media_group_settings function.
 * 
 * @access public
 * @return void
 */
function bp_media_group_settings() {
        ?>
       <h4><?php _e( 'Media Settings', 'bp-media' ); ?></h4>
                
		<label><input type="checkbox" name="group-enable-media" id="group-enable-media" value="1"<?php bp_media_show_media_setting(); ?> /> <?php _e( 'Allow Group Attachments:', 'bp-media' ); ?></label>
        
        <?php
}
add_action( 'bp_after_group_settings_admin', 'bp_media_group_settings' );



/**
 * bp_media_show_media_setting function.
 * 
 * @access public
 * @return void
 */
function bp_media_show_media_setting() {
		
	$meta = groups_get_groupmeta( bp_get_group_id() );
		
	if ( isset( $meta['enable_media'] ) && '1' === $meta['enable_media'][0] )
		echo ' checked="checked"';
}


/**
 * bp_media_save_enable_media function.
 * 
 * @access public
 * @param int $group_id (default: 0)
 * @return void
 */
function bp_media_save_enable_media( $group_id = 0 ) {

	$enable_media  = ( isset($_POST['group-enable-media'] ) ) ? 1 : 0;
	groups_update_groupmeta( $group_id, 'enable_media', $enable_media );

}
add_action( 'groups_group_settings_edited', 'bp_media_save_enable_media' );


/**
 * bp_media_group_is_enabled function.
 * 
 * @access public
 * @return void
 */
function bp_media_group_is_enabled() {
	global $bp;
	if ( isset( $bp->groups->current_group->slug ) && $bp->groups->current_group->slug == $bp->current_item ) {
         $meta = groups_get_groupmeta( $bp->groups->current_group->id );
         if( '1' === $meta['enable_media'][0] || defined('ENABLE_MEDIA') ) 
         bp_register_group_extension( 'Group_Extension_BP_MEDIA' );	 		 
	}
}
add_action( 'bp_setup_nav', 'bp_media_group_is_enabled' );



/**
 * bp_media_group_image_loop function.
 * 
 * @access public
 * @return void
 */
function bp_media_group_image_loop() {

	$attachment_args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => null, // attachments don't have statuses
		'meta_query' => array(
		   array(
		       'key'     => 'secondary_item_id',
		       'value'   => bp_get_group_id(),
		       'compare' => '='
		   )
		)
    );
    // get the attachments
    $this_posts_attachments = get_posts( $attachment_args );
	
	foreach( $this_posts_attachments as $key => $image ) {
	
		$image_url = wp_get_attachment_image( $this_posts_attachments[$key]->ID, 'thumbnail' );
		
		echo '<a href="' . bp_media_get_group_image_link( $this_posts_attachments[$key]->ID, $this_posts_attachments[$key]->post_author ) . '">';
		echo '<li>';
		echo $image_url;
		echo '</li>';
		echo '</a>';
		
	}
}