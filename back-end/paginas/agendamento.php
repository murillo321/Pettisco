<?php
    require_once __DIR__ . '/../funcoes/conexao.php';

    //se existir mês/ano na url usa, se não usa o atual
    $mes = isset($_GET['mes']) ? (int) $_GET['mes'] : (int) date('m');
    $ano = isset($_GET['ano']) ? (int) $_GET['ano'] : (int) date('Y');

    if ($mes < 1 || $mes > 12 || $ano < (int)date('Y') || $ano > 2100) {
        $mes = (int) date('m'); //date() função nativa do php para formatar datas
        $ano = (int) date('Y');
    }

    $primeiro_dia = new DateTime("$ano-$mes-01"); //DateTime é classe nativa do PHP, $primeiro_dia é objeto
    $quantidade_dias = (int) $primeiro_dia->format('t'); //usa metodo format(t) para pegar a quantidade de dias do mês
    $dia_semana = (int) $primeiro_dia->format('w');// format(w) retorna um nmr q representa um dia da semana (0 = dom)

    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
        4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
        7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
        10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ]; // array para associar o numero com o mês

    // Navegação entre meses
    $mes_anterior = (clone $primeiro_dia)->modify('-1 month');//usa metodo clone para o modify n alterar a data original
    $proximo_mes = (clone $primeiro_dia)->modify('+1 month');//modify add períodos a uma data

    $hoje = new DateTime('today'); //cria objeto com a data atual

    // Horários disponíveis para agendamento
    $horarios = [
        '08:00:00',
        '10:00:00',
        '12:00:00',
        '14:00:00',
        '16:00:00'
    ];

    // Define o início e o fim do mês para a consulta
    $inicio_mes = $primeiro_dia->format('Y-m-d');
    $fim_mes = (clone $primeiro_dia)->modify('+1 month')->format('Y-m-d');

    // Consulta os agendamentos ocupados do mês
    $select_dias_ocupados = "select data, horario from agendamentos where data >= :inicio and data < :fim and status = 1";
    $stmt = $pdo->prepare($select_dias_ocupados);
    $stmt->execute([
        ':inicio' => $inicio_mes,
        ':fim' => $fim_mes
    ]);
    $agendamentos = $stmt->fetchAll();

    // Organiza os horários ocupados
    $horarios_ocupados = [];
    foreach ($agendamentos as $agendamento) {
        $data_agendamento = $agendamento['data'];
        $horario_agendamento = $agendamento['horario'];
        $horarios_ocupados[$data_agendamento][] = $horario_agendamento;
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../front-end/css/main.css">
    <link rel="stylesheet" href="../../front-end/css/pages/agendamento.css">
    <link rel="shortcut icon" href="../../front-end/img/logos/favicon.ico" type="image/x-icon"> 
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <title>Pettisco - Agendamentos</title>
</head>
<body>

    <?php require_once __DIR__ . '/../componentes/header.php'; ?>

    <main class="container">

        <h1>Agendamento de serviços</h1>
        <p>Selecione um dia disponível para realizar seu agendamento.</p>

        <section class="calendario">

            <header class="calendario-header">

                <?php if ($mes_anterior >= new DateTime(date('Y-m-01'))): //verifica se mês anterior n é anterior ao atual?>
                    <a href="?mes=<?= $mes_anterior->format('n') ?>&ano=<?= $mes_anterior->format('Y') ?>"><!--passa os dados do mês para a URL-->
                        <
                    </a>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>

                <h2>
                    <?= $meses[$mes] ?> de <?= $ano ?>
                </h2>

                <a href="?mes=<?= $proximo_mes->format('n') ?>&ano=<?= $proximo_mes->format('Y') ?>">
                    >
                </a>

            </header>

            <section class="dias-semana">
                <?php foreach (['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'] as $dia): ?>
                    <span><?= $dia ?></span>
                <?php endforeach; ?>
            </section>

            <section class="dias">

                <?php
                    // faz espaços vazios até o primeiro dia mês
                    for ($i = 0; $i < $dia_semana; $i++):
                ?>
                    <section class="dia vazio"></section>
                <?php endfor; ?>

                <?php for ($dia = 1; $dia <= $quantidade_dias; $dia++): ?>

                    <?php
                        $data = new DateTime("$ano-$mes-" . str_pad($dia, 2, '0', STR_PAD_LEFT)); // str_pad para completar a string, colocando 0 a esquerda para deixar o formato correto
                        $dia_da_semana = (int) $data->format('w'); //identifica o dia da semana
                        $fim_de_semana = ($dia_da_semana === 0 || $dia_da_semana === 6); //verifica se é fim de semana
                        $passado = $data < $hoje; //verifica se ja passou

                        $data_formatada = $data->format('Y-m-d');
                        $ocupados = $horarios_ocupados[$data_formatada] ?? [];

                        $quantidade_ocupados = count(array_intersect($horarios, $ocupados));
                        $dia_lotado = $quantidade_ocupados === count($horarios);

                        // Define se o dia estará indisponível
                        $indisponivel = $fim_de_semana || $passado || $dia_lotado;
                    ?>

                    <?php if ($indisponivel): //exibe os dias ?>
                        <section class="dia indisponivel">
                            <?= $dia ?>
                        </section>
                    <?php else: ?>
                        <button type="button" class="dia disponivel"
                            data-data="<?= $data_formatada ?>"
                            data-ocupados="<?= htmlspecialchars(json_encode($ocupados)) ?>">
                            <?= $dia ?>
                        </button>
                    <?php endif; ?>
                <?php endfor; ?>
            </section>
        </section>

        <dialog id="modalHorarios"class="modal-horarios">
            <section class="modal-conteudo">
                <button id="fecharModal" class="fechar-modal">x</button>
                <h2>Escolha um horário:</h2>
                <p id="dataSelecionada"></p>
                <section id="listaHorarios" class="lista-horarios"></section>
                <p id="msgHorario"></p>
            </section>
        </dialog>
    </main>

    <?php require_once __DIR__ . '/../componentes/footer.php'; ?>

    <script>
        lucide.createIcons();

        const modal = document.getElementById('modalHorarios');
        const botoesDias = document.querySelectorAll('.dia.disponivel');
        const fecharModal = document.getElementById('fecharModal');
        const dataSelecionada = document.getElementById('dataSelecionada');
        const listaHorarios = document.getElementById('listaHorarios');
        const mensagemHorario = document.getElementById('msgHorario');

        const horarios = [
            '08:00',
            '10:00',
            '12:00',
            '14:00',
            '16:00'
        ];

        botoesDias.forEach(botao => {
            botao.addEventListener('click', () => {
                const data = botao.dataset.data;
                const ocupados = JSON.parse(botao.dataset.ocupados);
                const partes = data.split('-');
                const dataFormatada = `${partes[2]}/${partes[1]}/${partes[0]}`;
                dataSelecionada.textContent = dataFormatada;

                // limpa os horários anteriores
                listaHorarios.innerHTML = '';
                mensagemHorario.textContent = '';
                // cria os horários
                horarios.forEach(horario => {
                    const ocupado = ocupados.some(item => {
                        return item.substring(0, 5) === horario;
                    });
                    const botaoHorario = document.createElement('button');
                    botaoHorario.type = 'button';
                    botaoHorario.classList.add('horario');
                    botaoHorario.textContent = horario;

                    if (ocupado) {
                        botaoHorario.classList.add('horario-ocupado');
                        botaoHorario.disabled = true;
                        botaoHorario.textContent += ' - Ocupado';
                    } else {
                        botaoHorario.classList.add('horario-livre');
                        botaoHorario.addEventListener('click', () => {
                            const destino = new URL('agendar_servico.php',window.location.href);
                            destino.searchParams.set('data', data);
                            destino.searchParams.set('horario', horario);
                            window.location.href = destino.href;
                        });
                    }

                    listaHorarios.appendChild(botaoHorario);
                });
                modal.showModal();
            });
        });

        fecharModal.addEventListener('click', () => {
            modal.close();
        });

        modal.addEventListener('click', (evento) => {
            if (evento.target === modal) {
                modal.close();
            }
        });
    </script>
</body>
</html>