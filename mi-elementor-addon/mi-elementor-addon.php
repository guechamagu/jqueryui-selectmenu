<?php
/**
 * Plugin Name: Mi Addon para Elementor Free
 * Description: Un addon gratuito y portable que extiende Elementor Free con widgets personalizados legales.
 * Version: 1.0.0
 * Author: Tu Nombre
 * Text Domain: mi-elementor-addon
 * Requires Plugins: elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Salir si se accede directamente
}

define( 'MEA_VERSION', '1.0.0' );
define( 'MEA_PATH', plugin_dir_path( __FILE__ ) );
define( 'MEA_URL', plugin_dir_url( __FILE__ ) );

/**
 * Verificar si Elementor está instalado y activado.
 */
function mea_check_elementor_active() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'mea_admin_notice_missing_elementor' );
		return;
	}
	
	// Registrar widgets solo si Elementor está activo
	add_action( 'elementor/widgets/register', 'mea_register_widgets' );
	add_action( 'elementor/frontend/after_enqueue_styles', 'mea_frontend_styles' );
	add_action( 'elementor/frontend/after_register_scripts', 'mea_frontend_scripts' );
}
add_action( 'plugins_loaded', 'mea_check_elementor_active' );

/**
 * Notificación si Elementor no está activo.
 */
function mea_admin_notice_missing_elementor() {
	$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor */
		esc_html__( '"%1$s" requiere "%2$s" para funcionar.', 'mi-elementor-addon' ),
		'<strong>' . esc_html__( 'Mi Addon para Elementor', 'mi-elementor-addon' ) . '</strong>',
		'<strong>' . esc_html__( 'Elementor', 'mi-elementor-addon' ) . '</strong>'
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
}

/**
 * Registrar Widgets Personalizados.
 */
function mea_register_widgets( $widgets_manager ) {
	require_once( MEA_PATH . 'widgets/advanced-card.php' );
	$widgets_manager->register( new \Mi_Elementor_Addon\Widgets\Advanced_Card_Widget() );
}

/**
 * Cargar estilos frontend.
 */
function mea_frontend_styles() {
	wp_enqueue_style(
		'mea-style',
		MEA_URL . 'assets/css/style.css',
		[],
		MEA_VERSION
	);
}

/**
 * Cargar scripts frontend.
 */
function mea_frontend_scripts() {
	wp_enqueue_script(
		'mea-script',
		MEA_URL . 'assets/js/script.js',
		[ 'jquery' ],
		MEA_VERSION,
		true
	);
}
