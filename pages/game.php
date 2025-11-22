<?php require_once 'check.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Memoria - GO</title>
    <link rel="stylesheet" href="../css/game.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quantico:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
  </head>

<body>
  <div class="container">
    <div id="logo">
      <h1>
        <img src="/img/head-logo.svg" alt="head-logo" class="img01" />
        MEMÓRIA
        <img src="/img/head-logo.svg" alt="head-logo" class="img02" />
      </h1>
      <a href="logout.php" class="btn-sair">
        <img src="../img/logout.svg" alt="Sair" /> Sair
      </a>
      <button class="circle small">desistir da partida</button>
    </div>
  </div>

  <div class="modo">
    <h2>2x2</h2>
  </div>

  <main class="background-game">
    <div class="main-bar">
      <aside class="sidebar">
        <div class="time">
          <img src="/img/hourglass-split.svg" alt="tempo" />
          <p class="timer">0:00</p>
        </div>

        <div class="tries">
          <img src="/img/b06df9c6aaadac34909aaf30e60c838d506f33ab.png" alt="cartas" />
          <p class="tries-count">x 01</p>
        </div>

        <button class="btn-orange">DICA</button>
        <button class="btn-orange outlined">TRAPAÇA (revela 2s)</button>

        <button class="btn-trapaca-toggle">Mostrar Todas (trapaça)</button>
        <button class="btn-trapaca-reset">Restaurar Exibição</button>

      </aside>
    </div>

    <section class="board-area">
      <div class="board" aria-label="Tabuleiro do jogo">
        </div>
    </section>

    <aside class="history">
      <h3>Histórico de Partidas</h3>
      <div class="history-list"></div>
    </aside>
  </main>

  <script src="/js/app.js"></script>
  <script src="/js/jogo.js"></script>
</body>

</html>