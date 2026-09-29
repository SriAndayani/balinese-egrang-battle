// {{-- ============================================================
     // JAVASCRIPT — GAME ENGINE
     // ============================================================ 
// ================================================================
// STATE MANAGEMENT
// Seluruh state frontend disimpan di satu object ini.
// State frontend bersifat TURUNAN dari state backend.
// Backend adalah sumber kebenaran utama.
// Frontend hanya mengcache untuk keperluan animasi dan UI.
// ================================================================
const GameState = {
    a: {
        progress    : BATTLE_CONFIG.playerA.progress,
        streakBenar : 0,
        streakSalah : 0,
        isFallen    : false,
        isFinished  : false,
        isLocked    : false,   // locked saat recovery atau game selesai
        currentSoal : BATTLE_CONFIG.playerA.soal,
    },
    b: {
        progress    : BATTLE_CONFIG.playerB.progress,
        streakBenar : 0,
        streakSalah : 0,
        isFallen    : false,
        isFinished  : false,
        isLocked    : false,
        currentSoal : BATTLE_CONFIG.playerB.soal,
    },
    gameOver: false,
};

// ================================================================
// DOM REFERENCES
// Kumpulkan semua referensi DOM di satu tempat
// agar mudah ditemukan dan tidak typo berulang
// ================================================================
const DOM = {
    nameA       : document.getElementById('name-a'),
    nameB       : document.getElementById('name-b'),
    progressA   : document.getElementById('progress-info-a'),
    progressB   : document.getElementById('progress-info-b'),
    charA       : document.getElementById('char-a'),
    charB       : document.getElementById('char-b'),
    trackFillA  : document.getElementById('track-fill-a'),
    trackFillB  : document.getElementById('track-fill-b'),
    questionA   : document.getElementById('question-a'),
    questionB   : document.getElementById('question-b'),
    answersA    : document.getElementById('answers-a'),
    answersB    : document.getElementById('answers-b'),
    popupA      : document.getElementById('popup-a'),
    popupB      : document.getElementById('popup-b'),
    winOverlay  : document.getElementById('win-overlay'),
    winTitle    : document.getElementById('win-title'),
    winSubtitle : document.getElementById('win-subtitle'),
    winCard     : document.getElementById('win-card'),
    streakDots  : {
        a: [
            document.getElementById('streak-a-1'),
            document.getElementById('streak-a-2'),
            document.getElementById('streak-a-3'),
        ],
        b: [
            document.getElementById('streak-b-1'),
            document.getElementById('streak-b-2'),
            document.getElementById('streak-b-3'),
        ],
    }
};

// ================================================================
// INISIALISASI
// Dipanggil sekali saat halaman pertama kali dimuat
// ================================================================
function init() {
    // Set nama pemain di panel info
    DOM.nameA.textContent = BATTLE_CONFIG.playerA.nama;
    DOM.nameB.textContent = BATTLE_CONFIG.playerB.nama;

    // ✅ Tambahkan: set label nama di atas lintasan
    document.getElementById('track-label-a').textContent =
        BATTLE_CONFIG.playerA.nama;
    document.getElementById('track-label-b').textContent =
        BATTLE_CONFIG.playerB.nama;

    // Render soal dan progress awal (tidak berubah)
    renderSoal('a', GameState.a.currentSoal);
    renderSoal('b', GameState.b.currentSoal);
    updateProgress('a', GameState.a.progress, false);
    updateProgress('b', GameState.b.progress, false);
}

// ================================================================
// RENDER SOAL
// Menampilkan soal dan pilihan jawaban untuk satu pemain
//
// @param {string} player  - 'a' atau 'b'
// @param {object} soal    - object soal dari backend
// ================================================================
function renderSoal(player, soal) {
    const questionEl = player === 'a' ? DOM.questionA : DOM.questionB;
    const answersEl  = player === 'a' ? DOM.answersA  : DOM.answersB;

    if (!soal || !soal.id_soal) {
        questionEl.textContent = 'Memuat soal...';
        answersEl.innerHTML = '';
        return;
    }

    // Tampilkan pertanyaan
    questionEl.textContent = soal.pertanyaan;

    // Buat tombol jawaban
    answersEl.innerHTML = '';
    soal.jawabans.forEach(jawaban => {
        const btn = document.createElement('button');
        btn.className = `answer-btn player-${player}-btn`;
        btn.textContent = jawaban.teks_jawaban;

        // data-id menyimpan id_jawaban untuk dikirim ke server
        btn.dataset.id = jawaban.id_jawaban;

        // Event listener jawaban
        // Menggunakan arrow function agar 'player' ter-capture dengan benar
        btn.addEventListener('click', () => handleAnswer(player, jawaban.id_jawaban));

        answersEl.appendChild(btn);
    });
}

// ================================================================
// HANDLE ANSWER
// Dipanggil ketika pemain menekan tombol jawaban.
// Mengirim jawaban ke backend via fetch (AJAX).
//
// @param {string} player     - 'a' atau 'b'
// @param {number} idJawaban  - id_jawaban yang dipilih
// ================================================================
function handleAnswer(player, idJawaban) {
    const state = GameState[player];

    // GUARD: Jangan proses jika:
    // 1. Pemain sedang dalam recovery (is_fallen)
    // 2. Pemain sudah finished
    // 3. Pemain sedang locked (request sedang diproses)
    // 4. Game sudah selesai
    if (state.isLocked || state.isFinished || GameState.gameOver) return;

    // Lock pemain ini agar tidak bisa double click
    state.isLocked = true;

    // Disable semua tombol jawaban pemain ini
    lockAnswers(player);

    // Kirim ke backend
    fetch(BATTLE_CONFIG.answerUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': BATTLE_CONFIG.csrfToken,
        },
        body: JSON.stringify({
            player     : player,
            id_jawaban : idJawaban,
        }),
    })
    .then(res => res.json())
    .then(data => processResponse(player, idJawaban, data))
    .catch(err => {
        console.error('Error:', err);
        // Jika error, unlock agar pemain bisa coba lagi
        state.isLocked = false;
        unlockAnswers(player);
    });
}

// ================================================================
// PROCESS RESPONSE
// Memproses JSON response dari backend dan update semua UI
//
// @param {string} player     - 'a' atau 'b'
// @param {number} idJawaban  - jawaban yang dipilih pemain
// @param {object} data       - JSON response dari backend
// ================================================================
function processResponse(player, idJawaban, data) {
    const state = GameState[player];

    // Update state lokal dari response backend
    state.progress     = data.progress;
    state.streakBenar  = data.streak_benar;
    state.streakSalah  = data.streak_salah;
    state.isFallen     = data.is_fallen;
    state.isFinished   = data.is_finished;

    // 1. Tampilkan feedback visual pada tombol yang diklik
    showAnswerFeedback(player, idJawaban, data.correct_answer_id, data.is_correct);

    // 2. Update progress bar dan karakter
    updateProgress(player, data.progress, data.is_boost);

    // 3. Update streak indicator dots
    updateStreakDots(player, data.streak_benar);

    // 4. Animasi karakter berdasarkan hasil jawaban
    if (data.is_correct) {
        if (data.is_boost) {
            // Animasi boost: karakter bergerak cepat
            applyBoostEffect(player);
            showPopup(player, '⚡ SPEED BOOST!', 'boost', 1500);
        }
        // Tidak ada animasi khusus untuk benar biasa
        // — karakter sudah bergerak via updateProgress()

    } else {
        // Jawaban salah
        if (data.is_fallen) {
            // FALL: salah 3x berturut-turut
            applyFallAnimation(player, data, state);
        } else {
            // SHAKE: salah 1–2x
            applyShakeAnimation(player);
        }
    }

    // 5. Cek win condition
    if (data.winner) {
        GameState.gameOver = true;
        // Delay sedikit agar animasi karakter selesai dulu
        setTimeout(() => showWinScreen(data.winner, data.winner_nama), 800);
        return;
    }

    // 6. Transisi ke soal berikutnya (jika tidak fall dan tidak menang)
    if (!data.is_fallen && data.next_soal) {
        setTimeout(() => {
            transitionToNextSoal(player, data.next_soal);
        }, 600); // delay 0.6 detik untuk feedback terlihat
    }
}

// ================================================================
// SHOW ANSWER FEEDBACK
// Memberi warna pada tombol jawaban yang dipilih
// ================================================================
function showAnswerFeedback(player, idJawaban, correctId, isCorrect) {
    const answersEl = player === 'a' ? DOM.answersA : DOM.answersB;
    const buttons   = answersEl.querySelectorAll('.answer-btn');

    buttons.forEach(btn => {
        const btnId = parseInt(btn.dataset.id);

        if (btnId === parseInt(idJawaban)) {
            btn.classList.add(isCorrect ? 'correct' : 'wrong');
        }

        // Jika jawaban salah, highlight jawaban yang benar
        if (!isCorrect && correctId && btnId === parseInt(correctId)) {
            btn.classList.add('correct');
        }
    });
}

// ================================================================
// UPDATE PROGRESS
// Menggerakkan karakter dan mengupdate progress bar
//
// @param {string}  player  - 'a' atau 'b'
// @param {number}  progress - nilai progress (0–7)
// @param {boolean} isBoosted - apakah ini hasil speed boost
// ================================================================
function updateProgress(player, progress, isBoosted = false) {
    const charEl     = player === 'a' ? DOM.charA     : DOM.charB;
    const fillEl     = player === 'a' ? DOM.trackFillA : DOM.trackFillB;
    const progressEl = player === 'a' ? DOM.progressA : DOM.progressB;

    // Hitung posisi dalam persen (0% = start, 95% = finish)
    // Kita batas 95% agar karakter tidak keluar dari track
    const pct = Math.min((progress / BATTLE_CONFIG.winTarget) * 95, 95);

    // Jika boost, karakter bergerak lebih cepat
    if (isBoosted) {
        charEl.classList.add('boosted');
        setTimeout(() => charEl.classList.remove('boosted'), 700);
    }

    charEl.style.left  = pct + '%';
    fillEl.style.width = pct + '%';
    progressEl.textContent = `${progress} / ${BATTLE_CONFIG.winTarget}`;
}

// ================================================================
// UPDATE STREAK DOTS
// Update indikator titik streak di atas karakter
// ================================================================
function updateStreakDots(player, streakBenar) {
    const dots = DOM.streakDots[player];
    dots.forEach((dot, index) => {
        dot.className = 'streak-dot';
        if (index < streakBenar) {
            dot.classList.add(`active-${player}`);
        }
    });
}

// ================================================================
// ANIMASI SHAKE (salah 1–2x)
// ================================================================
function applyShakeAnimation(player) {
    const charEl = player === 'a' ? DOM.charA : DOM.charB;
    charEl.classList.remove('shaking');

    // Force reflow agar animasi bisa di-trigger ulang
    void charEl.offsetWidth;
    charEl.classList.add('shaking');

    setTimeout(() => charEl.classList.remove('shaking'), 400);
}

// ================================================================
// ANIMASI FALL + RECOVERY (salah 3x berturut-turut)
// ================================================================
function applyFallAnimation(player, data, state) {
    const charEl = player === 'a' ? DOM.charA : DOM.charB;

    // Tampilkan popup jatuh
    showPopup(player, '😵 Jatuh! Recovery...', 'fall', 1200);

    // Animasi jatuh
    charEl.classList.remove('shaking', 'recovering');
    void charEl.offsetWidth;
    charEl.classList.add('falling');

    // Setelah jatuh (0.5 detik), mulai recovery
    setTimeout(() => {
        charEl.classList.remove('falling');
        void charEl.offsetWidth;
        charEl.classList.add('recovering');

        // Setelah recovery (0.6 detik), unlock pemain
        setTimeout(() => {
            charEl.classList.remove('recovering');
            state.isLocked = false;
            state.isFallen = false;

            // Tampilkan soal berikutnya setelah recovery
            if (data.next_soal) {
                transitionToNextSoal(player, data.next_soal);
            }
        }, 600);

    }, 500);

    // Pemain TETAP terkunci selama fall + recovery
    // (isLocked = true sampai recovery selesai)
}

// ================================================================
// ANIMASI SPEED BOOST
// ================================================================
function applyBoostEffect(player) {
    const charEl = player === 'a' ? DOM.charA : DOM.charB;
    charEl.classList.add('boosted');
    setTimeout(() => charEl.classList.remove('boosted'), 700);
}

// ================================================================
// SHOW POPUP
// Menampilkan notifikasi kecil di atas sisi pemain
//
// @param {string} player   - 'a' atau 'b'
// @param {string} text     - teks popup
// @param {string} type     - 'boost' atau 'fall'
// @param {number} duration - durasi tampil dalam ms
// ================================================================
function showPopup(player, text, type, duration) {
    const popup = player === 'a' ? DOM.popupA : DOM.popupB;
    popup.textContent = text;
    popup.className   = `popup-notify ${type}`;

    // Trigger animasi
    void popup.offsetWidth;
    popup.classList.add('show');

    setTimeout(() => popup.classList.remove('show'), duration);
}

// ================================================================
// LOCK / UNLOCK ANSWERS
// Disable atau enable semua tombol jawaban satu pemain
// ================================================================
function lockAnswers(player) {
    const el = player === 'a' ? DOM.answersA : DOM.answersB;
    el.querySelectorAll('.answer-btn').forEach(btn => {
        btn.disabled = true;
    });
}

function unlockAnswers(player) {
    const el = player === 'a' ? DOM.answersA : DOM.answersB;
    el.querySelectorAll('.answer-btn').forEach(btn => {
        btn.disabled = false;
    });
}

// ================================================================
// TRANSITION KE SOAL BERIKUTNYA
// Render soal baru dan unlock tombol jawaban pemain
//
// @param {string} player  - 'a' atau 'b'
// @param {object} soal    - data soal berikutnya dari backend
// ================================================================
function transitionToNextSoal(player, soal) {
    const state = GameState[player];

    // Update soal di state lokal
    state.currentSoal = soal;

    // Render soal baru ke DOM
    renderSoal(player, soal);

    // Unlock pemain agar bisa menjawab lagi
    state.isLocked = false;
}

// ================================================================
// WIN SCREEN
// Tampilkan overlay pemenang dengan confetti
// ================================================================
function showWinScreen(winner, winnerNama) {
    // Lock semua tombol
    lockAnswers('a');
    lockAnswers('b');

    const loserNama = winner === 'a'
        ? BATTLE_CONFIG.playerB.nama
        : BATTLE_CONFIG.playerA.nama;

    // Set konten win card
    DOM.winTitle.textContent    = `🏆 ${winnerNama} MENANG!`;
    DOM.winSubtitle.textContent = `Selamat telah mengalahkan ${loserNama}!`;

    // Warna kartu berdasarkan pemenang
    DOM.winCard.style.borderColor = winner === 'a' ? '#60a5fa' : '#f87171';

    // Tampilkan overlay
    DOM.winOverlay.classList.add('show');

    // Luncurkan confetti 🎉
    launchConfetti();
}

// ================================================================
// CONFETTI
// Membuat confetti berwarna-warni yang jatuh dari atas layar
// ================================================================
function launchConfetti() {
    const colors = [
        '#ff6b6b', '#ffd93d', '#6bcb77',
        '#4d96ff', '#ff922b', '#cc5de8',
        '#f8f9fa', '#74c0fc',
    ];

    // Buat 80 potongan confetti
    for (let i = 0; i < 80; i++) {
        setTimeout(() => {
            const piece       = document.createElement('div');
            piece.className   = 'confetti-piece';
            piece.style.left  = Math.random() * 100 + 'vw';
            piece.style.top   = '-20px';
            piece.style.background = colors[Math.floor(Math.random() * colors.length)];
            piece.style.width  = (Math.random() * 10 + 8) + 'px';
            piece.style.height = (Math.random() * 10 + 8) + 'px';
            piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
            piece.style.animationDelay    = Math.random() * 0.5 + 's';
            document.body.appendChild(piece);

            // Hapus confetti setelah animasi selesai
            setTimeout(() => piece.remove(), 4000);
        }, i * 30);
    }
}

// ================================================================
// MULAI GAME
// ================================================================
init();