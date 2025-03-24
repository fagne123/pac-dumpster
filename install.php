<?php
/**
 * Instalador do Site PAC Dumpster
 * 
 * Este script facilita a instalação do site no WordPress
 * Copie este arquivo para a raiz do seu WordPress e acesse-o pelo navegador
 */

// Verificar se está sendo executado no WordPress
if (!file_exists('./wp-load.php')) {
    die('Este instalador deve ser executado na raiz do WordPress.');
}

// Carregar WordPress
require_once('./wp-load.php');

// Verificar se o usuário está logado e é administrador
if (!current_user_can('manage_options')) {
    wp_die('Você precisa estar logado como administrador para executar este instalador.');
}

// Definir constantes
define('PAC_SOURCE_DIR', __DIR__);
define('PAC_TARGET_DIR', WP_CONTENT_DIR . '/themes/' . get_stylesheet() . '/pac-dumpster');

// Função para criar diretório
function pac_create_directory($dir) {
    if (!file_exists($dir)) {
        return mkdir($dir, 0755, true);
    }
    return true;
}

// Função para copiar arquivos
function pac_copy_files() {
    // Criar diretório de destino
    if (!pac_create_directory(PAC_TARGET_DIR)) {
        return array(
            'success' => false,
            'message' => 'Não foi possível criar o diretório de destino.'
        );
    }
    
    // Lista de arquivos para copiar
    $files = array(
        'index.html',
        'styles.css',
        'script.js',
        'wordpress-config.php'
    );
    
    $copied = 0;
    $errors = array();
    
    // Copiar cada arquivo
    foreach ($files as $file) {
        $source = PAC_SOURCE_DIR . '/' . $file;
        $target = PAC_TARGET_DIR . '/' . $file;
        
        if (file_exists($source)) {
            if (copy($source, $target)) {
                $copied++;
            } else {
                $errors[] = "Não foi possível copiar o arquivo: {$file}";
            }
        } else {
            $errors[] = "Arquivo de origem não encontrado: {$file}";
        }
    }
    
    return array(
        'success' => count($errors) === 0,
        'copied' => $copied,
        'errors' => $errors
    );
}

// Função para configurar o tema
function pac_setup_theme() {
    // Copiar o arquivo de configuração para o diretório do tema
    $config_source = PAC_TARGET_DIR . '/wordpress-config.php';
    $config_target = WP_CONTENT_DIR . '/themes/' . get_stylesheet() . '/functions-pac.php';
    
    if (!copy($config_source, $config_target)) {
        return array(
            'success' => false,
            'message' => 'Não foi possível copiar o arquivo de configuração para o tema.'
        );
    }
    
    // Adicionar inclusão do arquivo no functions.php
    $functions_file = WP_CONTENT_DIR . '/themes/' . get_stylesheet() . '/functions.php';
    $include_code = "\n// PAC Dumpster Integration\nrequire_once get_stylesheet_directory() . '/functions-pac.php';\n";
    
    if (file_exists($functions_file)) {
        $functions_content = file_get_contents($functions_file);
        
        // Verificar se o código já foi adicionado
        if (strpos($functions_content, 'functions-pac.php') === false) {
            if (file_put_contents($functions_file, $functions_content . $include_code)) {
                return array(
                    'success' => true,
                    'message' => 'Arquivo de configuração adicionado ao tema com sucesso.'
                );
            } else {
                return array(
                    'success' => false,
                    'message' => 'Não foi possível modificar o arquivo functions.php.'
                );
            }
        } else {
            return array(
                'success' => true,
                'message' => 'A configuração já está incluída no tema.'
            );
        }
    } else {
        return array(
            'success' => false,
            'message' => 'Arquivo functions.php não encontrado no tema.'
        );
    }
}

// Função para criar a página
function pac_create_page() {
    // Verificar se a função existe
    if (function_exists('pac_dumpster_create_page')) {
        if (pac_dumpster_create_page()) {
            return array(
                'success' => true,
                'message' => 'Página criada com sucesso.'
            );
        } else {
            return array(
                'success' => false,
                'message' => 'A página já existe ou não foi possível criá-la.'
            );
        }
    } else {
        return array(
            'success' => false,
            'message' => 'Função de criação de página não encontrada.'
        );
    }
}

// Processar a instalação
$result = array(
    'copy_files' => null,
    'setup_theme' => null,
    'create_page' => null
);

if (isset($_POST['install'])) {
    // Executar a instalação
    $result['copy_files'] = pac_copy_files();
    
    if ($result['copy_files']['success']) {
        $result['setup_theme'] = pac_setup_theme();
        
        if ($result['setup_theme']['success']) {
            // Recarregar WordPress para ter acesso às novas funções
            require_once('./wp-load.php');
            $result['create_page'] = pac_create_page();
        }
    }
}

// Exibir página HTML
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador do Site PAC Dumpster</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #6936F5;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .card {
            background: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .success {
            color: #28a745;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        button {
            background-color: #6936F5;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #5728d9;
        }
        .steps {
            margin-top: 20px;
        }
        .step {
            margin-bottom: 15px;
            padding-left: 20px;
            position: relative;
        }
        .step:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #28a745;
        }
        .step.pending:before {
            content: '○';
            color: #6c757d;
        }
        .step.error:before {
            content: '✗';
            color: #dc3545;
        }
    </style>
</head>
<body>
    <h1>Instalador do Site PAC Dumpster</h1>
    
    <div class="card">
        <h2>Bem-vindo ao instalador</h2>
        <p>Este assistente irá ajudá-lo a instalar o site PAC Dumpster no seu WordPress.</p>
        <p>O processo irá:</p>
        <ol>
            <li>Copiar os arquivos necessários para o seu tema</li>
            <li>Configurar o tema para incluir o site</li>
            <li>Criar uma página com o shortcode do site</li>
        </ol>
        
        <?php if (isset($_POST['install'])): ?>
            <?php if ($result['copy_files']['success'] && $result['setup_theme']['success'] && $result['create_page']['success']): ?>
                <div class="success">
                    <strong>Instalação concluída com sucesso!</strong>
                    <p>O site PAC Dumpster foi instalado com sucesso no seu WordPress.</p>
                </div>
                <p>Próximos passos:</p>
                <ul>
                    <li>Acesse a <a href="<?php echo get_permalink(get_page_by_path('dumpster-rental')); ?>" target="_blank">página criada</a> para ver o site</li>
                    <li>Personalize o conteúdo conforme necessário usando o Elementor</li>
                </ul>
            <?php else: ?>
                <div class="error">
                    <strong>A instalação encontrou alguns problemas:</strong>
                    <ul>
                        <?php if (!$result['copy_files']['success']): ?>
                            <li>Erro ao copiar arquivos: <?php echo implode(', ', $result['copy_files']['errors']); ?></li>
                        <?php endif; ?>
                        
                        <?php if ($result['setup_theme'] && !$result['setup_theme']['success']): ?>
                            <li><?php echo $result['setup_theme']['message']; ?></li>
                        <?php endif; ?>
                        
                        <?php if ($result['create_page'] && !$result['create_page']['success']): ?>
                            <li><?php echo $result['create_page']['message']; ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="steps">
                <h3>Status da instalação:</h3>
                <div class="step <?php echo (!$result['copy_files']['success']) ? 'error' : ''; ?>">
                    Cópia de arquivos: <?php echo ($result['copy_files']['success']) ? 'Concluído' : 'Falhou'; ?>
                    <?php if ($result['copy_files']['success']): ?>
                        (<?php echo $result['copy_files']['copied']; ?> arquivos copiados)
                    <?php endif; ?>
                </div>
                
                <div class="step <?php echo (!$result['setup_theme']) ? 'pending' : (!$result['setup_theme']['success'] ? 'error' : ''); ?>">
                    Configuração do tema: 
                    <?php 
                    if (!$result['setup_theme']) {
                        echo 'Pendente';
                    } else {
                        echo ($result['setup_theme']['success']) ? 'Concluído' : 'Falhou';
                    }
                    ?>
                </div>
                
                <div class="step <?php echo (!$result['create_page']) ? 'pending' : (!$result['create_page']['success'] ? 'error' : ''); ?>">
                    Criação da página: 
                    <?php 
                    if (!$result['create_page']) {
                        echo 'Pendente';
                    } else {
                        echo ($result['create_page']['success']) ? 'Concluído' : 'Falhou';
                    }
                    ?>
                </div>
            </div>
        <?php else: ?>
            <form method="post" action="">
                <p>Clique no botão abaixo para iniciar a instalação:</p>
                <button type="submit" name="install">Instalar Agora</button>
            </form>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>Instalação Manual</h2>
        <p>Se preferir fazer a instalação manualmente, siga estas etapas:</p>
        <ol>
            <li>Crie uma pasta chamada <code>pac-dumpster</code> no diretório do seu tema</li>
            <li>Copie os arquivos <code>index.html</code>, <code>styles.css</code> e <code>script.js</code> para essa pasta</li>
            <li>Copie o arquivo <code>wordpress-config.php</code> para o diretório do seu tema como <code>functions-pac.php</code></li>
            <li>Adicione a seguinte linha ao seu arquivo <code>functions.php</code>:
                <pre>require_once get_stylesheet_directory() . '/functions-pac.php';</pre>
            </li>
            <li>Crie uma nova página e adicione o shortcode <code>[pac_dumpster_site]</code></li>
        </ol>
    </div>
</body>
</html>
