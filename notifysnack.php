<?php

/*
Plugin Name: NotifySnack for WordPress plugin
Plugin URI: http://www.notifysnack.com/
Description: NotifySnack for WordPress plugin
Version: 1.3
Author: Snacktools
Author URI: http://www.snacktools.com
License: GPL2

Copyright 2013 Snacktools
 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class NotifySnackPlugin {
    var $namespace = 'notifysnack-plugin';
    var $version = '1.3';
	
    var $defaults = array(
        'notifyscript' => "",
        'notifyscript_location' => 'body',
    );
    
    function __construct() {
        $this->url_path = WP_PLUGIN_URL . "/" . plugin_basename( dirname( __FILE__ ) );
        
        if( isset( $_SERVER['HTTPS'] ) && (boolean) $_SERVER['HTTPS'] === true ) {
            $this->url_path = str_replace( 'http://', 'https://', $this->url_path );
        }
        
        $this->option_name = '_' . $this->namespace . '--options';
        
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        
        if( is_admin() ) {
        	
            wp_register_style( $this->namespace, $this->url_path . '/css/styles.css', array(), $this->version );
            wp_enqueue_style( $this->namespace );

            wp_register_script( $this->namespace, $this->url_path . '/js/scripts.js', array( 'jquery' ), $this->version );
            wp_enqueue_script( $this->namespace );			
        } else {
			if($this->get_option( 'notifyscript_location' ) == 'body') {
				add_filter('template_include', array( &$this, 'custom_include'), 1);
				add_filter('shutdown', array( &$this, 'body_inject' ), 0);
			} else if( $this->get_option( 'notifyscript_location' ) == 'header' ){
		        add_action( 'wp_head', array( &$this, 'add_notify_script' ) );
        	} else {
			    if( function_exists( 'wp_print_footer_scripts' ) ) {
			        add_action( 'wp_print_footer_scripts', array( &$this, 'add_notify_script' ) );
			    } else {
			        add_action( 'wp_footer', array( &$this, 'add_notify_script' ) );
			    }
        	}
			
        }
    }
	
	function custom_include($template) {
		ob_start();
		return $template;
	}
		
	function body_inject() {
		$inject = $this->get_notify_script();
		$content = ob_get_clean();
		$content = preg_replace('#<body([^>]*)>#i',"<body$1>{$inject}",$content);
		echo $content;
	}

	function get_notify_script () {
	    $notifyscript = $this->get_option( 'notifyscript' );
		global $post;
		
		if( !empty( $notifyscript ) ) {
			$notifyscript = html_entity_decode( $notifyscript );
			return $notifyscript;
		}
	}
	
	function add_notify_script () {
	    $notifyscript = $this->get_option( 'notifyscript' );
		global $post;
		
		if( !empty( $notifyscript ) ) {
			$notifyscript = html_entity_decode( $notifyscript );
            
			if( $this->get_option( 'notifyscript_location' ) == 'header' ) {
				$output = preg_replace( "@<noscript[^>]*?.*?</noscript>@siu", "", $notifyscript );
			} else {
				$output = $notifyscript;
			}
	
			echo "\n" . $output;
		}
	}
	
    function admin_menu() {
        add_menu_page( 'NotifySnack', 'NotifySnack', 2, basename( __FILE__ ), array( &$this, 'admin_options_page' ), ( $this->url_path.'/images/icon.png' ) );
    }
    function admin_options_page() {
        if( !current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page' );
        }
        
        if( isset( $_POST ) && !empty( $_POST ) ) {
            if( wp_verify_nonce( $_REQUEST[$this->namespace . '_update_wpnonce'], $this->namespace . '_options' ) ) {
                $data = array();

                foreach( $_POST as $key => $val ) {
                    $data[$key] = $this->sanitize_notifyscript( $val );
                }
	
				
                switch( $data['form_action'] ) {
                    case "update_options":
                        $options = array(
                            'notifyscript' => (string) $data['notifyscript'],
							'notifyscript_location' => (string) $data['notifyscript_location'],
                        );

                        update_option( $this->option_name, $options );
                        $this->options = get_option( $this->option_name );
                    break;
                }
            }
        }
        
        $page_title  = 'NotifySnack Plugin Settings';
        $namespace   = $this->namespace;
        $options     = $this->options;
        $defaults    = $this->defaults;
        $plugin_path = $this->url_path;
        
        foreach( $this->defaults as $name => $default_value ) {
            $$name = $this->get_option( $name );
        }
		
		include( dirname( __FILE__ ) . '/template.php' ); 

    }
        
    private function get_option( $option_name ) {
        if( !isset( $this->options ) || empty( $this->options ) ) {
            $this->options = get_option( $this->option_name, $this->defaults );
        }
        
        if( isset( $this->options[$option_name] ) ) {
            return $this->options[$option_name];    
        } elseif( isset( $this->defaults[$option_name] ) ) {
            return $this->defaults[$option_name];   
        }
        return false;
    }
        
    private function sanitize_notifyscript( $str="" ) {
        if ( !function_exists( 'wp_kses' ) ) {
             require_once( ABSPATH . 'wp-includes/kses.php' );
        }        global $allowedposttags;
        global $allowedprotocols;
        
        if ( is_string( $str ) ) {
            $str = htmlentities( stripslashes( $str ), ENT_QUOTES, 'UTF-8' );
	        $str = wp_kses( $str, $allowedposttags, $allowedprotocols );
        }
		
        return $str;
    }
    
}

add_action( 'init', 'NotifySnackPlugin' );
function NotifySnackPlugin() {
    global $NotifySnackPlugin;
    $NotifySnackPlugin = new NotifySnackPlugin();
}

?>