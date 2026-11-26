<?php
$fotoHeader = '../../front-end/img/avatar-sem-foto.svg';
$contador = (int) ($_SESSION['quantidade_carrinho'] ?? 0);
if (!empty($_SESSION['id_user'])) {
    require_once __DIR__ . '/../funcoes/conexao.php';
    $consultaFotoHeader = $pdo->prepare('SELECT foto_url FROM user WHERE id_user = :id');
    $consultaFotoHeader->execute(['id' => $_SESSION['id_user']]);
    $fotoUsuarioHeader = $consultaFotoHeader->fetchColumn();
    if (is_string($fotoUsuarioHeader) && trim($fotoUsuarioHeader) !== '') {
        $fotoHeader = '../../front-end/img/' . ltrim($fotoUsuarioHeader, '/');
    }

}
?>
<header class="site-header">
    <div class="site-header__conteudo">
        <a class="site-header__logo" href="home.php" aria-label="Pettisco — página inicial">
            <img src="../../front-end/img/logos/logo-fundo-escuro-deitada.png" alt="Pettisco" width="180" height="70">
        </a>
        <form class="site-header__busca" action="pesquisageral.php" method="get" role="search" aria-label="Pesquisar produtos">
            <svg class="site-header__lupa" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
            <input type="search" name="pesquisa" placeholder="O que seu pet precisa?" aria-label="Pesquisar produtos">
            <button type="submit" aria-label="Buscar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
            </button>
        </form>
        <nav class="site-header__nav" aria-label="Navegação principal">
            
            <a href="produtos.php">Produtos</a>
            <a href="agendamento.php">Agendamentos</a>
            <!-- Apenas apresentação: a integração e a contagem do carrinho serão feitas depois. -->
            <span class="site-header__carrinho" role="img" aria-label="Carrinho — em breve">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h3l3 13h11l3-9H6"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                <span class="site-header__contador" aria-hidden="true"><?php echo $contador?></span>
            </span>
            <a class="site-header__perfil" href="perfil.php" aria-label="Meu perfil">
                <img src="<?= htmlspecialchars($fotoHeader, ENT_QUOTES, 'UTF-8') ?>" alt="Foto do usuário" width="44" height="44" onerror="this.onerror=null;this.src='../../front-end/img/perfil/usuario/usuario-sem-foto.png';">
            </a>
        </nav>
    </div>
</header>
