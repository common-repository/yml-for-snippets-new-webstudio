<?php
/*
Plugin Name: YML for Snippets New-Webstudio
Plugin URI: https://new-webstudio.ru
Description: Создание сниппетов с ценами
Version: 1.1
Author: New-Webstudio (N.Dyachenko)
*/

include('functions.php');

function YML_for_snippets_admin_scripts() {
    if ( is_admin() ){ // for Admin Dashboard Only
    // Embed the Script on our Plugin's Option Page Only
        if ( isset($_GET['page']) && $_GET['page'] == 'YML_for_snippets' ) {
            wp_enqueue_script( 'YML_for_snippets_script',plugin_dir_url( __FILE__ ) .'/scripts/script.js');
            wp_enqueue_script('jquery');
            wp_enqueue_script( 'jquery-form' );
            
        }
    }
}
add_action( 'admin_init', 'YML_for_snippets_admin_scripts' );
?>