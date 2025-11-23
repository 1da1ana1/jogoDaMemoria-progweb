<?php
require_once 'check.php';
require_once '../db-connection.php';

$id_usuario = $_SESSION['user_id'];

try {
    $sql = "SELECT * FROM partidas WHERE id_usuario = :id ORDER BY data_hora DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar histórico: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <title>Memória - Histórico</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="../css/history.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Quantico:wght@400;700&display=swap" rel="stylesheet"/>
  </head>
  <body>
    <div class="container">
      <div id="logo">
        <h1>
          <img src="../img/head-logo.svg" alt="head-logo" class="img01" />
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
          <a href="history.php" class="active">
            <img src="../img/Subtract.svg" class="menu-icon" alt="historico" />
            <p>Histórico</p>
          </a>
          <a href="rank.php">
            <img src="../img/trophy-fill.svg" class="menu-icon" alt="ranking" />
            <p>Ranking</p>
          </a>
        </nav>
      </div>
    </div>

    <div class="container">
      <h2 class="title-hub">HISTÓRICO</h2>

      <main class="history-container">
        <div class="tabs">
          <button class="tab-button active">TODAS AS PARTIDAS</button>
        </div>

        <div class="history-list">
          <div class="list-header">
            <span>Data</span>
            <span>Tabuleiro</span>
            <span>Modo</span>
            <span>Resultado</span>
            <span>Jogadas</span>
          </div>

          <?php if (count($historico) > 0): ?>
            <?php foreach ($historico as $partida): ?>
              <div class="list-item">
                <div class="player-info">
                  <img src="../img/person-circle.svg" alt="user icon" />
                  <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <div class="match-info">
                  <span>
                    <?php echo date('H:i', strtotime($partida['data_hora'])); ?><br />
                    <?php echo date('d/m/Y', strtotime($partida['data_hora'])); ?>
                  </span>
                  <span><?php echo htmlspecialchars($partida['tamanho_tabuleiro']); ?></span>
                  <span>
                    <?php echo ($partida['modo_jogo'] === 'contratempo') ? 'Contratempo' : 'Clássico'; ?>
                  </span>
                  <span style="color: <?php echo ($partida['resultado'] == 'Vitória') ? '#4CAF50' : '#F44336'; ?>;">
                    <?php echo htmlspecialchars($partida['resultado']); ?>
                  </span>
                  <span><?php echo (int)$partida['jogadas']; ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="text-align:center; margin-top:20px; color:white;">
              Nenhuma partida encontrada. Vá jogar!
            </p>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </body>
</html>
