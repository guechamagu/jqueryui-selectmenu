<?php
/**
 * Plugin Name: YPFB Ductos Map
 * Plugin URI: https://ypfbtransporte.com/
 * Description: Plugin para visualizar ductos en un mapa interactivo usando Leaflet y servicios web REST de YPFB Transporte S.A.
 * Version: 1.0.0
 * Author: YPFB Transporte S.A.
 * Author URI: https://ypfbtransporte.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ypfb-ductos-map
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('YDM_VERSION', '1.0.0');
define('YDM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('YDM_PLUGIN_URL', plugin_dir_url(__FILE__));

class YPFB_Ductos_Map {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('ypfb_ductos_map', array($this, 'render_map_shortcode'));
        add_action('wp_ajax_ypfb_get_ductos', array($this, 'ajax_get_ductos'));
        add_action('wp_ajax_nopriv_ypfb_get_ductos', array($this, 'ajax_get_ductos'));
        add_action('wp_ajax_ypfb_get_ducto_vertices', array($this, 'ajax_get_ducto_vertices'));
        add_action('wp_ajax_nopriv_ypfb_get_ducto_vertices', array($this, 'ajax_get_ducto_vertices'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function enqueue_assets() {
        // Leaflet CSS
        wp_enqueue_style(
            'leaflet-css',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            array(),
            '1.9.4'
        );
        
        // Custom CSS
        wp_enqueue_style(
            'ypfb-ductos-map-style',
            YDM_PLUGIN_URL . 'assets/css/style.css',
            array('leaflet-css'),
            YDM_VERSION
        );
        
        // Leaflet JS
        wp_enqueue_script(
            'leaflet-js',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            array('jquery'),
            '1.9.4',
            true
        );
        
        // Custom JS
        wp_enqueue_script(
            'ypfb-ductos-map-script',
            YDM_PLUGIN_URL . 'assets/js/map.js',
            array('jquery', 'leaflet-js'),
            YDM_VERSION,
            true
        );
        
        // Localize script with AJAX URL and settings
        wp_localize_script('ypfb-ductos-map-script', 'ydm_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ydm_nonce'),
            'ws_base_url' => get_option('ydm_ws_base_url', ''),
            'search_radius' => get_option('ydm_search_radius', '5000'),
            'contact_email' => get_option('ydm_contact_email', 'contacto@ypfbtransporte.com'),
            'message1' => get_option('ydm_message1', ''),
            'message2' => get_option('ydm_message2', '')
        ));
    }
    
    public function render_map_shortcode($atts) {
        ob_start();
        ?>
        <div id="ypfb-ductos-map-container">
            <div id="ypfb-ductos-map"></div>
            <div id="ypfb-ductos-info" class="hidden">
                <h3>Información de Ductos</h3>
                <div id="ypfb-ductos-message"></div>
                <div id="ypfb-ductos-list"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function ajax_get_ductos() {
        check_ajax_referer('ydm_nonce', 'nonce');
        
        $x = isset($_POST['x']) ? floatval($_POST['x']) : 0;
        $y = isset($_POST['y']) ? floatval($_POST['y']) : 0;
        $spatial_ref = isset($_POST['spatial_ref']) ? sanitize_text_field($_POST['spatial_ref']) : '';
        
        $token = $this->get_auth_token();
        
        if (is_wp_error($token)) {
            wp_send_json_error(array('message' => 'Error de autenticación: ' . $token->get_error_message()));
        }
        
        $ws_url = get_option('ydm_ws_geolocalizacion', '');
        $radius = get_option('ydm_search_radius', '5000');
        
        $request_url = add_query_arg(array(
            'x' => $x,
            'y' => $y,
            'spatialRef' => $spatial_ref,
            'token' => $token,
            'distance' => $radius
        ), $ws_url);
        
        $response = wp_remote_get($request_url, array(
            'timeout' => 30,
            'sslverify' => true
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => 'Error al conectar con el servicio web: ' . $response->get_error_message()));
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array('message' => 'Error al procesar la respuesta del servicio web'));
        }
        
        wp_send_json_success($data);
    }
    
    public function ajax_get_ducto_vertices() {
        check_ajax_referer('ydm_nonce', 'nonce');
        
        $codigo_ducto = isset($_POST['codigo_ducto']) ? sanitize_text_field($_POST['codigo_ducto']) : '';
        
        $token = $this->get_auth_token();
        
        if (is_wp_error($token)) {
            wp_send_json_error(array('message' => 'Error de autenticación: ' . $token->get_error_message()));
        }
        
        $ws_url = get_option('ydm_ws_vertices', '');
        
        $request_url = add_query_arg(array(
            'codigoDucto' => $codigo_ducto,
            'token' => $token
        ), $ws_url);
        
        $response = wp_remote_get($request_url, array(
            'timeout' => 30,
            'sslverify' => true
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => 'Error al conectar con el servicio web: ' . $response->get_error_message()));
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array('message' => 'Error al procesar la respuesta del servicio web'));
        }
        
        wp_send_json_success($data);
    }
    
    private function get_auth_token() {
        $username = get_option('ydm_ws_username', '');
        $password = get_option('ydm_ws_password', '');
        $auth_url = get_option('ydm_ws_autenticacion', '');
        
        if (empty($auth_url) || empty($username) || empty($password)) {
            return new WP_Error('missing_credentials', 'Faltan credenciales configuradas');
        }
        
        $response = wp_remote_post($auth_url, array(
            'method' => 'POST',
            'body' => array(
                'username' => $username,
                'password' => $password
            ),
            'timeout' => 30,
            'sslverify' => true
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_response', 'Respuesta inválida del servicio de autenticación');
        }
        
        // Ajustar según la estructura real de la respuesta del token
        if (isset($data['token'])) {
            return $data['token'];
        } elseif (isset($data['access_token'])) {
            return $data['access_token'];
        }
        
        return new WP_Error('no_token', 'No se recibió un token válido');
    }
    
    public function add_admin_menu() {
        add_options_page(
            'YPFB Ductos Map',
            'YPFB Ductos Map',
            'manage_options',
            'ypfb-ductos-map',
            array($this, 'render_admin_page'),
            'dashicons-location-alt',
            30
        );
    }
    
    public function register_settings() {
        register_setting('ydm_settings_group', 'ydm_ws_autenticacion');
        register_setting('ydm_settings_group', 'ydm_ws_geolocalizacion');
        register_setting('ydm_settings_group', 'ydm_ws_vertices');
        register_setting('ydm_settings_group', 'ydm_ws_username');
        register_setting('ydm_settings_group', 'ydm_ws_password');
        register_setting('ydm_settings_group', 'ydm_ws_base_url');
        register_setting('ydm_settings_group', 'ydm_search_radius');
        register_setting('ydm_settings_group', 'ydm_spatial_ref');
        register_setting('ydm_settings_group', 'ydm_contact_email');
        register_setting('ydm_settings_group', 'ydm_message1');
        register_setting('ydm_settings_group', 'ydm_message2');
    }
    
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>Configuración de YPFB Ductos Map</h1>
            <form method="post" action="options.php">
                <?php settings_fields('ydm_settings_group'); ?>
                <?php do_settings_sections('ydm_settings_group'); ?>
                
                <h2>Servicios Web</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="ydm_ws_autenticacion">URL Servicio Autenticación</label></th>
                        <td><input type="url" name="ydm_ws_autenticacion" id="ydm_ws_autenticacion" value="<?php echo esc_attr(get_option('ydm_ws_autenticacion')); ?>" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="ydm_ws_geolocalizacion">URL Servicio Geolocalización</label></th>
                        <td><input type="url" name="ydm_ws_geolocalizacion" id="ydm_ws_geolocalizacion" value="<?php echo esc_attr(get_option('ydm_ws_geolocalizacion')); ?>" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="ydm_ws_vertices">URL Servicio Vértices</label></th>
                        <td><input type="url" name="ydm_ws_vertices" id="ydm_ws_vertices" value="<?php echo esc_attr(get_option('ydm_ws_vertices')); ?>" class="regular-text" required></td>
                    </tr>
                </table>
                
                <h2>Credenciales</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="ydm_ws_username">Usuario</label></th>
                        <td><input type="text" name="ydm_ws_username" id="ydm_ws_username" value="<?php echo esc_attr(get_option('ydm_ws_username')); ?>" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="ydm_ws_password">Contraseña</label></th>
                        <td><input type="password" name="ydm_ws_password" id="ydm_ws_password" value="<?php echo esc_attr(get_option('ydm_ws_password')); ?>" class="regular-text" required></td>
                    </tr>
                </table>
                
                <h2>Parámetros de Consulta</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="ydm_search_radius">Radio de Búsqueda (metros)</label></th>
                        <td><input type="number" name="ydm_search_radius" id="ydm_search_radius" value="<?php echo esc_attr(get_option('ydm_search_radius', '5000')); ?>" class="small-text"></td>
                    </tr>
                    <tr>
                        <th><label for="ydm_spatial_ref">Referencia Espacial</label></th>
                        <td><input type="text" name="ydm_spatial_ref" id="ydm_spatial_ref" value="<?php echo esc_attr(get_option('ydm_spatial_ref', '')); ?>" class="regular-text"></td>
                    </tr>
                </table>
                
                <h2>Mensajes y Contacto</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="ydm_contact_email">Email de Contacto</label></th>
                        <td><input type="email" name="ydm_contact_email" id="ydm_contact_email" value="<?php echo esc_attr(get_option('ydm_contact_email', 'contacto@ypfbtransporte.com')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="ydm_message1">Mensaje con Ductos</label></th>
                        <td>
                            <textarea name="ydm_message1" id="ydm_message1" rows="4" class="large-text"><?php echo esc_textarea(get_option('ydm_message1', 'En la ubicación seleccionada se tienen los siguientes Ductos cercanos: "{DUCTOS}", puede porfavor ponerse en contacto con la siguiente dirección de correo para re-confirmar y seguir instrucciones: {EMAIL}.\n*Los datos entregados no son finales, deben ser reconfirmados por personal de YPFB Transporte S.A.')); ?></textarea>
                            <p class="description">Use {DUCTOS} para listar ductos y {EMAIL} para el email de contacto.</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="ydm_message2">Mensaje sin Ductos</label></th>
                        <td>
                            <textarea name="ydm_message2" id="ydm_message2" rows="4" class="large-text"><?php echo esc_textarea(get_option('ydm_message2', 'En la ubicación seleccionada no se tienen registros de Ductos cercanos. Sin embargo, puede por favor ponerse en contacto con la siguiente dirección de correo para re-confirmar y seguir instrucciones: {EMAIL}.\n*Los datos entregados no son finales, deben ser reconfirmados por personal de YPFB Transporte S.A.')); ?></textarea>
                            <p class="description">Use {EMAIL} para el email de contacto.</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2>Uso del Shortcode</h2>
            <p>Para mostrar el mapa en una página o entrada, use el siguiente shortcode:</p>
            <code>[ypfb_ductos_map]</code>
        </div>
        <?php
    }
}

// Initialize the plugin
YPFB_Ductos_Map::get_instance();
