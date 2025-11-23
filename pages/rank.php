<?php
require_once 'check.php';
require_once '../db-connection.php';

try {
    // Uma partida vencedora por tamanho de tabuleiro:
    // - Apenas vitórias
    // - Menor número de jogadas para cada tamanho
    // - Ordenado pelos maiores tabuleiros (área) e depois por menos jogadas
    $sql = "
        SELECT 
            u.username,
            p.tamanho_tabuleiro,
            p.modo_jogo,
            p.jogadas,
            p.data_hora
        FROM partidas p
        INNER JOIN usuarios u ON p.id_usuario = u.id
        WHERE 
            p.resultado = 'Vitória'
            AND p.jogadas = (
                SELECT MIN(p2.jogadas)
                FROM partidas p2
                WHERE 
                    p2.tamanho_tabuleiro = p.tamanho_tabuleiro
                    AND p2.resultado = 'Vitória'
            )
        ORDER BY 
            (CAST(SUBSTRING_INDEX(p.tamanho_tabuleiro, 'x', 1) AS UNSIGNED) *
             CAST(SUBSTRING_INDEX(p.tamanho_tabuleiro, 'x', -1) AS UNSIGNED)) DESC,
            p.jogadas ASC,
            p.data_hora ASC
        LIMIT 10
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar ranking: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <title>Memória - Ranking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="../css/rank.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Quantico:wght@400;700&display=swap" rel="stylesheet"/>
  </head>
  <body>
    <div class="container">
      <div id="logo">
        <h1>
          <img src="../img/head-logo.svg" alt="logo" class="img01" />
          MEMÓRIA
          <img src="../img/head-logo.svg" alt="head-logo" class="img02" />
        </h1>
        <a href="logout.php" class="btn-sair">
          <img src="../img/logout.svg" alt="Sair" /> Sair
        </a>
      </div>
    </div>

    <div id="menu">
      <div class="container">
        <nav>
          <a href="hub_partida.php">
            <img src="../img/play-fill.svg" class="menu-icon" alt="jogar" />
            <p>Jogar</p>
          </a>
          <a href="profile.php">
            <img src="../img/person-circle.svg" class="menu-icon" alt="perfil" />
            <p>Perfil</p>
          </a>
          <a href="history.php">
            <img src="../img/Subtract.svg" class="menu-icon" alt="historico" />
            <p>Histórico</p>
          </a>
          <a href="rank.php" class="active">
            <img src="../img/trophy-fill.svg" class="menu-icon" alt="ranking" />
            <p>Ranking</p>
          </a>
        </nav>
      </div>
    </div>

    <div class="container">
      <h2 class="title-hub">RANKING GLOBAL (TOP 10)</h2>

      <main class="history-container">
        <div class="ranking-list">
          <div class="list-header">
            <span>Data</span>
            <span>Tabuleiro</span>
            <span>Modo</span>
            <span>Resultado</span>
            <span>Jogadas</span>
          </div>

          <?php if (count($ranking) > 0): ?>
            <?php foreach ($ranking as $linha): ?>
              <div class="list-item">
                <div class="player-info">
                  <img src="../img/person-circle.svg" alt="user icon" />
                  <span><?php echo htmlspecialchars($linha['username']); ?></span>
                </div>
                <div class="match-info">
                  <span>
                    <?php echo date('d/m/Y H:i', strtotime($linha['data_hora'])); ?>
                  </span>
                  <span><?php echo htmlspecialchars($linha['tamanho_tabuleiro']); ?></span>
                  <span>
                    <?php echo ($linha['modo_jogo'] === 'contratempo') ? 'Contratempo' : 'Clássico'; ?>
                  </span>
                  <span style="color: #4CAF50;">Vitória</span>
                  <span><?php echo (int)$linha['jogadas']; ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="text-align:center; margin-top:20px; color:white;">
              O Ranking está vazio. Seja o primeiro a ganhar!
            </p>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </body>
</html>
