<?php

/**
 * Plugin Name: WooCommerce RTL
 * Plugin URI: http://ar-wc.com
 * Description: Adds full RTL support to WooCommerce admin area, front-end, and email interface.
 * Version: 1.0.0
 * Author: Abdullah Helayel
 * Author URI: http://updu.la/
 * Text Domain: wcrtl
 * Domain Path: /languages/
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

  /**
   * Admin styles
   */
  add_action( 'admin_enqueue_scripts', 'wcrtl_admin_styles', 11 );

  function wcrtl_admin_styles() {

    if ( is_rtl() ) {

      // menu.css
      wp_dequeue_style( 'woocommerce_admin_menu_styles' );
      wp_enqueue_style( 'woocommerce_admin_menu_styles_rtl', plugin_dir_url( __FILE__ ) . 'assets/css/menu.css', array(), WC_VERSION );

      $screen = get_current_screen();

      // admin.css
      if ( in_array( $screen->id, wc_get_screen_ids() ) ) {
        wp_dequeue_style( 'woocommerce_admin_styles' );
        wp_enqueue_style( 'woocommerce_admin_styles_rtl', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', array(), WC_VERSION );
        wp_enqueue_style( 'wcrtl_admin_styles', plugin_dir_url( __FILE__ ) . 'assets/css/wcrtl-admin.css', array(), WC_VERSION );
      }

      // dashboard.css
      if ( in_array( $screen->id, array( 'dashboard' ) ) ) {
        wp_dequeue_style( 'woocommerce_admin_dashboard_styles' );
        wp_enqueue_style( 'woocommerce_admin_dashboard_styles_rtl', plugin_dir_url( __FILE__ ) . 'assets/css/dashboard.css', array(), WC_VERSION );
      }

      // reports-print.css
      if ( in_array( $screen->id, array( 'woocommerce_page_wc-reports' ) ) ) {
        wp_dequeue_style( 'woocommerce_admin_dashboard_styles' );
        wp_enqueue_style( 'woocommerce_admin_print_reports_styles_rtl', plugin_dir_url( __FILE__ ) . 'assets/css/reports-print.css', array(), WC_VERSION, 'print' );
      }

    }

  }


  /**
   * Front-end styles
   */
  add_filter( 'woocommerce_enqueue_styles', 'wcrtl_dequeue_styles' );

  function wcrtl_dequeue_styles( $enqueue_styles ) {
    if ( is_rtl() ) {
      unset( $enqueue_styles['woocommerce-layout'] );
      unset( $enqueue_styles['woocommerce-smallscreen'] );
      unset( $enqueue_styles['woocommerce-general'] );
      return $enqueue_styles;
    }
  }


  function wcrtl_enqueue_woocommerce_style() {

    wp_register_style( 'woocommerce-layout-rtl', plugin_dir_url( __FILE__ ) . 'assets/css/woocommerce-layout.css', array(), WC_VERSION, 'all' );
    wp_register_style( 'woocommerce-smallscreen-rtl', plugin_dir_url( __FILE__ ) . 'assets/css/woocommerce-smallscreen.css', array( 'woocommerce-layout' ), WC_VERSION, 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', $breakpoint = '768px' ) . ')' );
    wp_register_style( 'woocommerce-general-rtl', plugin_dir_url( __FILE__ ) . 'assets/css/woocommerce.css', array(), WC_VERSION, 'all' );
    wp_register_style( 'wcrtl-general', plugin_dir_url( __FILE__ ) . 'assets/css/wcrtl-woocommerce.css', array(), WC_VERSION, 'all' );

    if ( class_exists( 'woocommerce' ) && is_rtl() ) {

      wp_enqueue_style( 'woocommerce-layout-rtl' );
      wp_enqueue_style( 'woocommerce-smallscreen-rtl' );
      wp_enqueue_style( 'woocommerce-general-rtl' );
      wp_enqueue_style( 'wcrtl-general' );

      if ( is_checkout() || is_page( get_option( 'woocommerce_myaccount_page_id' ) ) ) {
        wp_enqueue_style( 'wcrtl-select2', plugin_dir_url( __FILE__ ) . 'assets/css/wcrtl-select2.css' );
      }

    }

  }

  add_action( 'wp_enqueue_scripts', 'wcrtl_enqueue_woocommerce_style', 11 );


  /**
   * Email interface
   */
  add_filter( 'woocommerce_locate_template', 'wcrtl_woocommerce_locate_template', 10, 3 );

  function wcrtl_woocommerce_locate_template( $template, $template_name, $template_path ) {

    global $woocommerce;

    $_template = $template;

    if ( ! $template_path ) $template_path = $woocommerce->template_url;

    $plugin_path = plugin_dir_path( __FILE__ ) . 'woocommerce/';

    // Look within passed path within the theme - this is priority
    $template = locate_template(
      array(
        $template_path . $template_name,
        $template_name
      )
    );

    // Modification: Get the template from this plugin, if it exists
    if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
      $template = $plugin_path . $template_name;
    }

    // Use default template
    if ( ! $template ) {
      $template = $_template;
    }

    // Return what we found
    return $template;

  }

}
