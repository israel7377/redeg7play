<?php
// Define cabeçalhos para evitar problemas de CORS e garantir retorno JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

/**
 * CONFIGURAÇÃO:
 * Certifique-se de que a URL abaixo aponta para o endpoint da API do seu painel.
 * Substitua [SENHA_DA_API] pela senha que o seu painel fornece para integrações.
 */
$api_url = "http://recruta.shop
/api.php?action=create_sub&username=israel7377@gmail.com&password=[180523Irs@]&package=ze15lgOW5K/ayb1BqPWPR";

// Utiliza cURL para fazer a requisição ao painel
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Útil se o servidor apresentar erro de certificado

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Retorna os dados para o JavaScript do front-end
if ($response !== false) {
    echo $response;
} else {
    // Retorna erro formatado para o seu script.js conseguir tratar
    echo json_encode(['error' => 'Falha de comunicação', 'details' => $error]);
}
?>
