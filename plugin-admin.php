<?php

/**
 * Plugin WP para administrator
 *  Ejemplo de un plugin WordPress
 */
class Cc_Proyect_Admin extends Wp_Plugin_Admin  
{
    const UPDATE_HIDDEN_FIELD = 'update-options';
    const SYNC_HIDDEN_FIELD   = 'sync-options';
    const NONCE_ACTION = 'admin-save';
    const NONCE_FIELD = 'admin-nonce';
    
    /**
     * Unique instance
     * @var object 
     */
    private static $instance;
    
    /**************************************************************************/
    /**
     * Static functions
     * @return object 
     */
    public static function instance() 
    {
        if ( !isset( self::$instance ) ) {
            $class_name = __CLASS__;
            self::$instance = new $class_name();
        }

        return self::$instance;
    }
    
    /** 
     * hooks for plugin administration
     *
     */
    protected function __construct()
    {
        parent::__construct(__FILE__);
        
        add_action('login_enqueue_scripts', array($this, 'login_scripts') );
                        
        add_action('save_post', array($this, 'posts_save_postdata'), 10, 2);
        
        add_action('publish_' . Jab_Fnographr::CUSTOM_TYPE_NAME, array($this, 'posts_onpublish'), 10, 2);
        
        add_action('add_meta_boxes', array($this, 'post_meta_boxes'), 10, 2);
    }
    
    /**
     * Add metabox for posts
     */
    public function post_meta_boxes() 
    {
        add_meta_box( 'posts_metabox', __( 'Post Details' ), 
                      array($this, 'posts_meta_box_content'), Jab_Fnographr::CUSTOM_TYPE_NAME, 'normal', 'high');
        
        add_meta_box( 'posts_side_metabox', __( 'Rewards Details' ), 
                      array($this, 'posts_side_meta_box_content'), Jab_Fnographr::CUSTOM_TYPE_NAME, 'side', 'high');
    }
    
    /**
     * Fill the metabox
     * 
     * @global object $post
     */
    function posts_meta_box_content()
    {
        global $post;
                        
        $posts_info = get_post_meta($post->ID, 'posts_info', true);
                
        include 'admin/views/form.php';
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_FIELD);
    }
    
    /**
     * Fill the metabox
     * 
     * @global object $post
     */
    function posts_side_meta_box_content()
    {
        global $post;
        
        $award = get_post_meta($post->ID, 'award', true);
        
        include 'admin/views/side-form.php';
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_FIELD);
    }
    
    /**
     * Save extra info from metabox form
     * 
     * @global object $wpdb
     * @param object $post_id
     * @return boolean
     */
    function posts_save_postdata( $post_id ) 
    {
        global $wpdb;
    
        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
        // to do anything
    
        if ( !isset($_POST[self::NONCE_FIELD]) ) {
            return $post_id;
        }
            
        if ( !wp_verify_nonce( $_POST[self::NONCE_FIELD], self::NONCE_ACTION ) ) {
            return $post_id;
        }
            
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post_id;
        }
            
        // Check permissions
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        } else {
            if ( !current_user_can( 'edit_posts' ) ) {
                return $post_id;
            }
        }
        
        $fields = $this->valid_fields();
        
        foreach ($fields as $field) {
            update_post_meta($post_id, $field, $_POST[$field]);
        }
    }
    
    public static function valid_fields()
    {
        return array(
            'posts_info', 'award'
        );
    }
    
    /**
     * 
     * @global object $wpdb
     * @param object $post_id
     * @return boolean
     */
    function posts_onpublish( $post_id, $post ) 
    {
        $push_sent_field = 'field_push_sent';
        
        // Only main posts sends push notifications
        if ($post->post_parent > 0) {
            return false;
        }
        
        // Check if push is already sent
        $push_is_already_sent = intval( get_post_meta($post_id, $push_sent_field, true) );
        
        if ( $push_is_already_sent ) {
            return false;
        }
        
        update_post_meta($post_id, $push_sent_field, 1);
    }
    
    function login_scripts()
    {
        ?>
        <style type="text/css">
          body {
            padding-top: 120px;
            padding-bottom: 40px;
            background: #f5f5f5 url('http://cesarecontreras.io/img/bg.jpg') center top !important;
          }
          
          body.login div#login h1 a {
            background-image: url('http://cesarecontreras.io/img/logo.png') !important;
            padding-bottom: 70px;
            height: 110px;
            width: 99%;
            background-size: auto !important;
          }
          
          #login {
              width: 400px !important;
          }
        </style>
        <?php
    }

} // end class

Cc_Proyect_Admin::instance();