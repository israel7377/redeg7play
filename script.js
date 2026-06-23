// Smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        // Only run smooth scroll if it's an internal # anchor
        if(this.getAttribute('href').startsWith('#') && this.getAttribute('href').length > 1) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if(target) {
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        }
    });
});

// Lógica de Geração do Teste
const btnTeste = document.getElementById('btn-gerar-teste');
const resultadoBox = document.getElementById('resultado-teste');
const erroBox = document.getElementById('erro-teste');
const msgErro = document.getElementById('msg-erro');
const spanUser = document.getElementById('teste-user');
const spanPass = document.getElementById('teste-pass');

if(btnTeste) {
    btnTeste.addEventListener('click', async function() {
        btnTeste.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GERANDO ACESSO...';
        btnTeste.disabled = true;
        resultadoBox.style.display = 'none';
        erroBox.style.display = 'none';

        try {
            const resposta = await fetch('gerar_teste.php');
            const dados = await resposta.json();
            
            if(resposta.status === 429 || dados.limite_excedido) {
                msgErro.textContent = dados.error || "Você já gerou um teste. Aguarde 72 horas para solicitar outro.";
                erroBox.style.display = 'block';
                return;
            }

            let user = dados.username || (dados.user_info && dados.user_info.username);
            let pass = dados.password || (dados.user_info && dados.user_info.password);

            if(user && pass) {
                spanUser.textContent = user;
                spanPass.textContent = pass;
                resultadoBox.style.display = 'block';
            } else {
                msgErro.textContent = "Erro: O teste foi processado, mas o painel não retornou os dados esperados.";
                erroBox.style.display = 'block';
            }
        } catch (error) {
            console.error("Erro na requisição HTTP:", error);
            msgErro.textContent = "Sistema temporariamente fora do ar. Chame no WhatsApp.";
            erroBox.style.display = 'block';
        } finally {
            btnTeste.innerHTML = '<i class="fas fa-play"></i> GERAR TESTE 4H AGORA';
            btnTeste.disabled = false;
        }
    });
}
