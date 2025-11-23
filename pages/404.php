<?php require_once 'check.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <title>Memória - Página não encontrada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Quantico:wght@400;700&display=swap" rel="stylesheet" />
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

    <div class="container" style="margin-top: 2rem;">
      <main class="history-container" style="text-align: center;">
        <h2 class="title-hub">404 - Página não encontrada</h2>
        <p style="margin-top: 1.5rem; color: var(--color-text-general);">
          Desculpe, a página que você tentou acessar não existe ou foi movida.
        </p>
        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
          <a href="hub_partida.php" class="btn">
            Voltar ao jogo
          </a>
          <a href="profile.php" class="btn" style="background-color: var(--color-field-input);">
            Ir para o perfil
          </a>
        </div>
      </main>
    </div>
  </body>
</html>
