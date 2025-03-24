<?php
/**
 * Configuração para facilitar a integração com WordPress
 * 
 * Este arquivo contém funções que facilitam a integração do site com WordPress
 * Ele deve ser incluído no tema WordPress ou como um plugin
 */

// Evitar acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe para gerenciar a integração do site de aluguel de caminhões de lixo com WordPress
 */
class PAC_Dumpster_Integration {
    
    /**
     * Inicializa a integração
     */
    public static function init() {
        // Adicionar scripts e estilos
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
        
        // Adicionar shortcode para inserir o conteúdo do site
        add_shortcode('pac_dumpster_site', array(__CLASS__, 'site_shortcode'));
        
        // Adicionar suporte para alternância de idiomas
        add_action('wp_footer', array(__CLASS__, 'language_toggle'));
    }
    
    /**
     * Registra e enfileira os arquivos CSS e JS
     */
    public static function enqueue_assets() {
        // Registrar e enfileirar o CSS
        wp_register_style(
            'pac-dumpster-style',
            get_stylesheet_directory_uri() . '/pac-dumpster/styles.css',
            array(),
            '1.0.0'
        );
        wp_enqueue_style('pac-dumpster-style');
        
        // Registrar e enfileirar o JavaScript
        wp_register_script(
            'pac-dumpster-script',
            get_stylesheet_directory_uri() . '/pac-dumpster/script.js',
            array('jquery'),
            '1.0.0',
            true
        );
        wp_enqueue_script('pac-dumpster-script');
        
        // Adicionar Font Awesome
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css',
            array(),
            '6.0.0-beta3'
        );
        
        // Adicionar Google Fonts
        wp_enqueue_style(
            'google-fonts',
            'https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800,900|Lato:400,700,900&display=swap',
            array(),
            null
        );
    }
    
    /**
     * Shortcode para inserir o conteúdo do site
     */
    public static function site_shortcode($atts) {
        // Iniciar buffer de saída
        ob_start();
        
        // Incluir o arquivo HTML principal
        include_once(get_stylesheet_directory() . '/pac-dumpster/index.html');
        
        // Retornar o conteúdo do buffer
        return ob_get_clean();
    }
    
    /**
     * Adiciona o botão de alternância de idiomas
     */
    public static function language_toggle() {
        echo '<div class="language-toggle">
            <button class="lang-btn active" data-lang="en">EN</button>
            <button class="lang-btn" data-lang="pt">PT</button>
        </div>';
    }
}

// Inicializar a integração
add_action('init', array('PAC_Dumpster_Integration', 'init'));

/**
 * Função auxiliar para criar uma página com o site completo
 */
function pac_dumpster_create_page() {
    // Verificar se a página já existe
    $page_exists = get_page_by_path('dumpster-rental');
    
    if (!$page_exists) {
        // Criar a página
        $page_id = wp_insert_post(array(
            'post_title'     => 'Dumpster Rental',
            'post_name'      => 'dumpster-rental',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_content'   => '[pac_dumpster_site]',
            'comment_status' => 'closed'
        ));
        
        if ($page_id && !is_wp_error($page_id)) {
            // Definir como página inicial (opcional)
            // update_option('page_on_front', $page_id);
            // update_option('show_on_front', 'page');
            
            return true;
        }
    }
    
    return false;
}

// Função para registrar a ativação
register_activation_hook(__FILE__, 'pac_dumpster_activate');

function pac_dumpster_activate() {
    // Criar a página ao ativar o plugin
    pac_dumpster_create_page();
}
