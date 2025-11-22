// jogo.js — lógica completa do jogo (Parcial 2)

(function () {
  const board = document.querySelector(".board");
  const modoTitulo = document.querySelector(".modo h2");
  const timerDisplay = document.querySelector(".timer");
  const triesDisplay = document.querySelector(".tries-count");
  const historyContainer = document.querySelector(".history-list");
  const btnMostrarTodas = document.querySelector('.btn-trapaca-toggle');
  const btnRestaurar = document.querySelector('.btn-trapaca-reset');
  const btnDica = document.querySelector('.btn-orange');
  const btnTrapaca = document.querySelector('.btn-orange.outlined');
  const btnDesistir = document.querySelector('.btn-desistir');

  let primeiraCarta = null;
  let segundaCarta = null;
  let travado = false;
  let movimentos = 0;
  let timerIntervalo = null;
  let segundos = 0;
  let paresEncontrados = 0;
  let totalDePares = 0;
  let modo = 'classica';
  let tempoRestanteInicial = 0;
  let emTrapaca = false;

  const HISTORICO_KEY = 'memoria_historico_v2';
  const USERNAME_KEY = 'memoria_username';

  const arrayCartas = [
    'apple.svg','burguer.svg','carrot.svg','cattle.svg','cherry.svg','chicken.svg',
    'coffee.svg','coffee_drink.svg','coffee_beam.svg','cup.svg','dingos.svg','drink.svg',
    'drink4.svg','egg.svg','garlic.svg','gift.svg','grapes.svg','guava.svg','house.svg',
    'ice_cone.svg','icecream.svg','juice.svg','leaf.svg','pimentao.svg','song.svg',
    'strawberry.svg','sweet.svg','toffe.svg','tomato.svg','wine.svg'
  ];

  function getUsername() {
    return localStorage.getItem(USERNAME_KEY) || 'Jogador';
  }

  function agoraISO() { return new Date().toISOString(); }

  function tempoPad(nSegundos) {
    const minutos = Math.floor(nSegundos / 60);
    const seg = nSegundos % 60;
    return `${minutos.toString().padStart(2,'0')}:${seg.toString().padStart(2,'0')}`;
  }

  function definirTempoPorTabuleiro(linhas, colunas) {
    const pares = (linhas * colunas) / 2;
    if (pares <= 2) return 30;
    if (pares <= 8) return 90;
    if (pares <= 18) return 180;
    return 300;
  }

  function criarTabuleiro(linhas, colunas) {
    if (!board) return;
    clearInterval(timerIntervalo);
    timerIntervalo = null;
    primeiraCarta = null;
    segundaCarta = null;
    travado = false;
    movimentos = 0;
    segundos = 0;
    paresEncontrados = 0;

    triesDisplay && (triesDisplay.textContent = `x ${movimentos.toString().padStart(2,'0')}`);
    timerDisplay && (timerDisplay.textContent = "00:00");

    board.innerHTML = "";
    modoTitulo && (modoTitulo.textContent = `${linhas}x${colunas}`);
    board.style.display = "grid";
    board.style.gridTemplateColumns = `repeat(${colunas}, 1fr)`;
    board.style.gridTemplateRows = `repeat(${linhas}, 1fr)`;

    const totalDeCartas = linhas * colunas;
    totalDePares = totalDeCartas / 2;

    const imagensUnicas = [];
    const arrayCopia = [...arrayCartas];
    for (let i = 0; i < totalDePares; i++) {
      const idx = Math.floor(Math.random() * arrayCopia.length);
      imagensUnicas.push(arrayCopia.splice(idx, 1)[0]);
    }

    const baralhoDePares = [...imagensUnicas, ...imagensUnicas];
    for (let i = baralhoDePares.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [baralhoDePares[i], baralhoDePares[j]] = [baralhoDePares[j], baralhoDePares[i]];
    }

    for (let i = 0; i < totalDeCartas; i++) {
      const card = document.createElement("div");
      card.classList.add("card");
      card.setAttribute('data-imagem', baralhoDePares[i]);

      const frente = document.createElement('div'); frente.classList.add('face','frente');
      const img = document.createElement('img'); img.src = `../img/fruits/${baralhoDePares[i]}`; img.alt = '';
      frente.appendChild(img);

      const verso = document.createElement('div'); verso.classList.add('face','verso');
      verso.innerHTML = '<img src="../img/question-mark.png" alt="?" />';

      card.appendChild(frente);
      card.appendChild(verso);

      card.addEventListener('click', virarCarta);

      board.appendChild(card);
    }

    if (modo === 'contra') {
      tempoRestanteInicial = definirTempoPorTabuleiro(linhas, colunas);
      segundos = tempoRestanteInicial;
      timerDisplay && (timerDisplay.textContent = tempoPad(segundos));
    } else {
      segundos = 0;
      timerDisplay && (timerDisplay.textContent = "00:00");
    }

    renderHistorico();
  }

  function iniciarTimer() {
    if (timerIntervalo) return;
    if (!timerDisplay) return;

    if (modo === 'classica') {
      segundos = 0;
      timerDisplay.textContent = "00:00";
      timerIntervalo = setInterval(() => {
        segundos++;
        timerDisplay.textContent = tempoPad(segundos);
      }, 1000);
      return;
    }

    if (modo === 'contra') {
      if (!tempoRestanteInicial) tempoRestanteInicial = 60;
      timerIntervalo = setInterval(() => {
        segundos--;
        timerDisplay.textContent = tempoPad(Math.max(0, segundos));
        if (segundos <= 0) {
          clearInterval(timerIntervalo);
          timerIntervalo = null;
          setTimeout(() => exibirDerrota('Tempo esgotado'), 300);
        }
      }, 1000);
    }
  }

  function contarMovimento() {
    movimentos++;
    triesDisplay && (triesDisplay.textContent = `x ${movimentos.toString().padStart(2, '0')}`);
  }

  function virarCarta() {
    if (travado) return;
    if (this.classList.contains('flipped')) return;
    if (!timerIntervalo) iniciarTimer();

    this.classList.add('flipped');

    if (!primeiraCarta) {
      primeiraCarta = this;
      return;
    }

    segundaCarta = this;
    contarMovimento();
    checarPar();
  }

  function checarPar() {
    const ehPar = primeiraCarta.dataset.imagem === segundaCarta.dataset.imagem;
    ehPar ? desabilitarCartas() : desvirarCartas();
  }

  function desabilitarCartas() {
    primeiraCarta.removeEventListener('click', virarCarta);
    segundaCarta.removeEventListener('click', virarCarta);
    paresEncontrados++;
    if (paresEncontrados === totalDePares) {
      clearInterval(timerIntervalo);
      timerIntervalo = null;
      setTimeout(() => exibirVitoria(), 800);
    }
    resetarTabuleiro();
  }

  function desvirarCartas() {
    travado = true;
    setTimeout(() => {
      primeiraCarta && primeiraCarta.classList.remove('flipped');
      segundaCarta && segundaCarta.classList.remove('flipped');
      resetarTabuleiro();
    }, 1200);
  }

  function resetarTabuleiro() {
    [primeiraCarta, segundaCarta] = [null, null];
    travado = false;
  }

function salvarPartida(dadosPartida) {
    // PARTE 1: Salvar no localStorage (mantendo a funcionalidade local)
    try {
      const arr = JSON.parse(localStorage.getItem(HISTORICO_KEY) || '[]');
      arr.unshift(dadosPartida);
      localStorage.setItem(HISTORICO_KEY, JSON.stringify(arr));
      renderHistorico();
    } catch (e) { console.error('Erro salvando histórico local', e); }

    const formData = new FormData();
    formData.append('modo_jogo', modo); 
    formData.append('tamanho_tabuleiro', dadosPartida.dimensoes);
    formData.append('resultado', dadosPartida.resultado); 
    formData.append('jogadas', dadosPartida.numero_jogadas);

    fetch('../save_game.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log("Partida salva no servidor com sucesso!", data.message);
        } else {
            console.error("Erro ao salvar partida no servidor:", data.message);
        }
    })
    .catch(error => {
        console.error("Erro de conexão ou processamento:", error);
    });
}

  function renderHistorico() {
    if (!historyContainer) return;
    historyContainer.innerHTML = '';
    const arr = JSON.parse(localStorage.getItem(HISTORICO_KEY) || '[]');
    if (!arr.length) {
      historyContainer.innerHTML = '<p>Nenhuma partida registrada ainda.</p>';
      return;
    }
    const ul = document.createElement('ul');
    arr.forEach(item => {
      const li = document.createElement('li');
      li.innerHTML = `<strong>${item.nome}</strong> — ${item.modalidade} — ${item.dimensoes} — ${item.tempo_gasto} — ${item.numero_jogadas} jogadas — ${item.resultado} — <small>${item.datahora}</small>`;
      ul.appendChild(li);
    });
    historyContainer.appendChild(ul);
  }

  function exibirVitoria() {
    const tempoFinal = timerDisplay ? timerDisplay.textContent : tempoPad(segundos);
    const movimentosFinais = movimentos;

    salvarPartida({
      nome: getUsername(),
      dimensoes: modoTitulo ? modoTitulo.textContent : '',
      modalidade: modo === 'contra' ? 'Contra o Tempo' : 'Clássica',
      tempo_gasto: modo === 'classica' ? tempoFinal : tempoPad(tempoRestanteInicial - segundos),
      numero_jogadas: movimentosFinais,
      resultado: 'Vitória',
      datahora: agoraISO()
    });

    const modalOverlay = document.createElement('div'); modalOverlay.className = 'modal-overlay';
    const modalBox = document.createElement('div'); modalBox.className = 'modal';
    modalBox.innerHTML = `
      <h2>Parabéns!</h2>
      <p>Você venceu o jogo!</p>
      <p><strong>Tempo:</strong> ${tempoFinal}</p>
      <p><strong>Movimentos:</strong> ${movimentosFinais}</p>
      <div style="display:flex;gap:8px;justify-content:center;margin-top:10px">
        <button id="jogarNovamente">Jogar Novamente</button>
        <button id="voltarConfig">Nova Configuração</button>
      </div>
    `;
    modalOverlay.appendChild(modalBox);
    document.body.appendChild(modalOverlay);

    document.getElementById('jogarNovamente').addEventListener('click', () => window.location.reload());
    document.getElementById('voltarConfig').addEventListener('click', () => window.location.href = '/pages/hub_partida.php');
  }

  function exibirDerrota(motivo='Derrota') {
    clearInterval(timerIntervalo);
    timerIntervalo = null;

    const tempoFinal = timerDisplay ? timerDisplay.textContent : tempoPad(segundos);
    const movimentosFinais = movimentos;

    salvarPartida({
      nome: getUsername(),
      dimensoes: modoTitulo ? modoTitulo.textContent : '',
      modalidade: modo === 'contra' ? 'Contra o Tempo' : 'Clássica',
      tempo_gasto: modo === 'classica' ? tempoFinal : tempoPad(tempoRestanteInicial - segundos),
      numero_jogadas: movimentosFinais,
      resultado: 'Derrota',
      datahora: agoraISO()
    });

    const modalOverlay = document.createElement('div'); modalOverlay.className = 'modal-overlay';
    const modalBox = document.createElement('div'); modalBox.className = 'modal';
    modalBox.innerHTML = `
      <h2>${motivo}</h2>
      <p>Sua partida terminou como derrota.</p>
      <p><strong>Tempo:</strong> ${tempoFinal}</p>
      <p><strong>Movimentos:</strong> ${movimentosFinais}</p>
      <div style="display:flex;gap:8px;justify-content:center;margin-top:10px">
        <button id="tentarNovamente">Tentar Novamente</button>
        <button id="voltarConfig2">Voltar às Configurações</button>
      </div>
    `;
    modalOverlay.appendChild(modalBox);
    document.body.appendChild(modalOverlay);

    document.getElementById('tentarNovamente').addEventListener('click', () => window.location.reload());
    document.getElementById('voltarConfig2').addEventListener('click', () => window.location.href = '/pages/hub_partida.php');
  }

  /* Trapaça persistente: mostrar todas ocultas (mantém status real do jogo) */
  if (btnMostrarTodas) {
    btnMostrarTodas.addEventListener('click', () => {
      const todas = document.querySelectorAll('.card:not(.flipped)');
      todas.forEach(c => c.classList.add('flipped', 'cheat-shown'));
      emTrapaca = true;
    });
  }
  if (btnRestaurar) {
    btnRestaurar.addEventListener('click', () => {
      const todas = document.querySelectorAll('.card.cheat-shown');
      todas.forEach(c => c.classList.remove('flipped', 'cheat-shown'));
      emTrapaca = false;
    });
  }

  /* Compatibilidade com botões existentes: dica rápida e revelar por 2s */
  if (btnTrapaca) {
    btnTrapaca.addEventListener('click', () => {
      if (travado) return;
      const todasAsCartas = document.querySelectorAll('.card:not(.flipped)');
      travado = true;
      todasAsCartas.forEach(card => card.classList.add('flipped'));
      setTimeout(() => {
        todasAsCartas.forEach(card => card.classList.remove('flipped'));
        travado = false;
      }, 2000);
    });
  }
  if (btnDica) {
    btnDica.addEventListener('click', () => {
      if (travado) return;
      const cartasNaoViradas = Array.from(document.querySelectorAll('.card:not(.flipped)'));
      let parEncontrado = null;
      for (let i = 0; i < cartasNaoViradas.length; i++) {
        for (let j = i + 1; j < cartasNaoViradas.length; j++) {
          if (cartasNaoViradas[i].dataset.imagem === cartasNaoViradas[j].dataset.imagem) {
            parEncontrado = [cartasNaoViradas[i], cartasNaoViradas[j]];
            break;
          }
        }
        if (parEncontrado) break;
      }
      if (parEncontrado) {
        travado = true;
        parEncontrado.forEach(c => c.classList.add('flipped'));
        setTimeout(() => {
          parEncontrado.forEach(c => c.classList.remove('flipped'));
          travado = false;
        }, 1500);
      }
    });
  }

  if (btnDesistir) {
    btnDesistir.addEventListener('click', () => {
      clearInterval(timerIntervalo);
      timerIntervalo = null;
      window.location.href = '/pages/hub_partida.php';
    });
  }

  /* Inicialização na página game.html */
  if (window.location.pathname.endsWith("game.php")) {
    let config = { linhas: 8, colunas: 8 };
    try {
      const salvo = localStorage.getItem("tabuleiro");
      if (salvo) config = JSON.parse(salvo);
      const modoSalvo = localStorage.getItem("modo_partida");
      modo = modoSalvo ? modoSalvo : 'classica';
    } catch (e) {}
    criarTabuleiro(config.linhas, config.colunas);
    renderHistorico();
  }
})();