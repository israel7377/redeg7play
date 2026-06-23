<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 1. IDENTIFICAÇÃO DO USUÁRIO PELO IP E ARQUIVO DE LOG
$ip_usuario = $_SERVER['REMOTE_ADDR'];
$arquivo_log = __DIR__ . '/log_testes.json';
$tempo_atual = time();
$limite_horas = 72;
$limite_segundos = $limite_horas * 3600;

// Carrega o arquivo de log se ele existir
$logs = [];
if (file_exists($arquivo_log)) {
    $conteudo = file_get_contents($arquivo_log);
    $logs = json_decode($conteudo, true) ?: [];
}

// 2. VERIFICAÇÃO DO LIMITE DE 72 HORAS
if (isset($logs[$ip_usuario])) {
    $tempo_ultimo_teste = $logs[$ip_usuario]['timestamp'];
    $tempo_passado = $tempo_atual - $tempo_ultimo_teste;
    
    if ($tempo_passado < $limite_segundos) {
        $horas_restantes = ceil(($limite_segundos - $tempo_passado) / 3600);
        
        http_response_code(429);
        echo json_encode([
            'limite_excedido' => true,
            'error' => "Você já gerou um teste recentemente. Por favor, aguarde $horas_restantes hora(s) para solicitar um novo teste ou contate nosso suporte."
        ]);
        exit;
    }
}

// 3. COMUNICAÇÃO COM O SEU PAINEL (Criação do Teste)
$api_url = "https://meggatop.com.br/api.php?action=create_sub&username=israel7377@gmail.com&password=180523Irs@&package=ze15lgOW5K/ayb1BqPWPR";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($response !== false) {
    $dados_painel = json_decode($response, true);
    $usuario_gerado = "Desconhecido";
    
    if($dados_painel) {
        $usuario_gerado = $dados_painel['username'] ?? ($dados_painel['user_info']['username'] ?? "N/A");
    }

    // 4. SALVAR OS DADOS DE QUEM FEZ O TESTE
    foreach ($logs as $ip => $dados) {
        if (($tempo_atual - $dados['timestamp']) > $limite_segundos) {
            unset($logs[$ip]);
        }
    }
    
    $logs[$ip_usuario] = [
        'timestamp' => $tempo_atual,
        'data_hora' => date('Y-m-d H:i:s'),
        'usuario_gerado' => $usuario_gerado
    ];
    
    file_put_contents($arquivo_log, json_encode($logs, JSON_PRETTY_PRINT));
    
    echo $response;
} else {
    echo json_encode(['error' => 'Falha de comunicação com o servidor do painel.']);
}
?>
