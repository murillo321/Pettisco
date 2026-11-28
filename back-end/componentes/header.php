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
    <div class="site-header-conteudo">
        <a class="site-header-logo" href="home.php" aria-label="Pettisco — página inicial">
            <img src="../../front-end/img/logos/logo-fundo-escuro-deitada.png" alt="Pettisco" width="180" height="70">
        </a>
        <form class="site-header-busca" action="pesquisageral.php" method="get" role="search" aria-label="Pesquisar produtos">
            <input type="search" name="pesquisa" placeholder="O que seu pet precisa?" aria-label="Pesquisar produtos">
            <button type="submit" aria-label="Buscar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
            </button>
        </form>
        <nav class="site-header-nav" aria-label="Navegação principal">
            
            <a href="produtos.php">Produtos</a>
            <a href="agendamentos.php">Agendamentos</a>

            <a href="carrinho.php" class="site-header-carrinho">
                <i data-lucide="shopping-cart" aria-hidden="true"></i>
                <span class="site-header-contador" aria-hidden="true"><?php echo $contador?></span>
            </a>
            <a class="site-header-perfil" href="perfil.php">
                <img src="<?= htmlspecialchars($fotoHeader) ?>" alt="Foto do usuário" width="44" height="44" onerror="this.onerror=null;this.src='../../front-end/img/perfil/usuario/avatar-sem-foto.svg';">
            </a>
        </nav>
    </div>
</header>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
    lucide.createIcons();
</script>
