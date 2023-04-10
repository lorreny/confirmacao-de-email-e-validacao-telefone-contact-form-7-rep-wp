/*
Plugin Name: Adicionar validação de email e máscara de telefone
Description: Plugin para adicionar email de confirmação ocultando o envio e adiciona máscara no telefone - Contact Form 7
Version: 1.2
Author: Escola Ninja WP
License: GPL2
*/

// Enqueue scripts
function cfc_register_scripts() {
    if ( !is_admin() ) {
        // Include your script
        wp_enqueue_script( 'email-confirm', plugin_dir_url( __FILE__ ) . 'js/email-confirm.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'inputmask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js', array( 'jquery' ), '5.0.6', true );
    }
}
add_action( 'wp_enqueue_scripts', 'cfc_register_scripts' );

// Validate phone number
add_filter( 'wpcf7_validate_tel', 'custom_phone_validation_filter', 20, 2 );
add_filter( 'wpcf7_validate_tel*', 'custom_phone_validation_filter', 20, 2 );
function custom_phone_validation_filter( $result, $tag ) {
    $tag = new WPCF7_FormTag( $tag );

    if ( 'tel' == $tag->type ) {
        $value = isset( $_POST[$tag->name] ) ? trim( wp_unslash( strtr( (string) $_POST[$tag->name], "\n", " " ) ) ) : '';

        // Validate phone number according to the country pattern
        $value = preg_replace( '/\s+|\-|\(|\)/', '', $value );
        if ( !preg_match( '/^[0-9]{11}$/', $value ) ) { // Example for countries that use 11 digits
            $result->invalidate( $tag, 'Por favor, insira um telefone válido.' );
        }
    }

    return $result;
}

// Check plugin vulnerability
add_action( 'admin_init', 'cfc_check_vulnerabilities' );
function cfc_check_vulnerabilities() {
    // Check if vulnerable plugins are active
    $vulnerable_plugins = array( 'plugin-name-1', 'plugin-name-2', 'plugin-name-3' );
    $active_plugins = get_option( 'active_plugins' );
    foreach ( $vulnerable_plugins as $plugin ) {
        if ( in_array( $plugin, $active_plugins ) ) {
            // If a vulnerable plugin is active, deactivate and display an error message
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( 'Plugin desativado devido à segurança.' );
        }
    }
}