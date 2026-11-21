// public/js/battlePlay.js
// Mesin permainan Battle: mengirim jawaban ke server, menggerakkan karakter, dan menampilkan pemenang.
// Alur game dan data dari server TIDAK berubah. Yang berubah hanya tampilan dan animasi karakternya.

// ================================================================
// PENGATURAN (ubah angka di sini)
// ================================================================
const FALL_MS         = 500;   // lama karakter terjatuh sebelum berdiri lagi (milidetik)
const NEXT_SOAL_DELAY = 600;   // jeda sebelum soal berikutnya tampil (supaya warna benar/salah terlihat)
const BOOST_MS        = 700;   // lama efek cahaya saat speed boost

// ================================================================
// STATE (data yang diingat game selama berjalan)
// Backend adalah sumber kebenaran. State ini hanya salinan untuk animasi dan tampilan.
// ================================================================
const GameState = {
    a: {
        progress    : BATTLE_CONFIG.playerA.progress,
        streakBenar : BATTLE_CONFIG.playerA.streakBenar,
        streakSalah : BATTLE_CONFIG.playerA.streakSalah,
        isFallen    : false,
        isFinished  : false,
        isLocked    : false,   // terkunci saat jatuh atau saat menunggu server
        currentSoal : BATTLE_CONFIG.playerA.soal,
    },
    b: {
        progress    : BATTLE_CONFIG.playerB.progress,
        streakBenar : BATTLE_CONFIG.playerB.streakBenar,
        streakSalah : BATTLE_CONFIG.playerB.streakSalah,
        isFallen    : false,
        isFinished  : false,
        isLocked    : false,
        currentSoal : BATTLE_CONFIG.playerB.soal,
    },
    gameOver: false,
};

// ================================================================
// REFERENSI ELEMEN HTML (dikumpulkan di satu tempat)
// ================================================================
const DOM = {
    nameA      : document.getElementById('name-a'),
    nameB      : document.getElementById('name-b'),
    progressA  : document.getElementById('progress-info-a'),
    progressB  : document.getElementById('progress-info-b'),
    kidA       : document.getElementById('char-a'),
    kidB       : document.getElementById('char-b'),
    hopperA    : document.getElementById('hopper-a'),
    hopperB    : document.getElementById('hopper-b'),
    imgA       : document.getElementById('img-a'),
    imgB       : document.getElementById('img-b'),
    questionA  : document.getElementById('question-a'),
    questionB  : document.getElementById('question-b'),
    answersA   : document.getElementById('answers-a'),
    answersB   : document.getElementById('answers-b'),
    popupA     : document.getElementById('popup-a'),
    popupB     : document.getElementById('popup-b'),
    winOverlay : document.getElementById('win-overlay'),
    winTitle   : document.getElementById('win-title'),
    winSubtitle: document.getElementById('win-subtitle'),
    winCard    : document.getElementById('win-card'),
    streakDots : {
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
    },
};

// Mengambil semua elemen milik satu pemain ('a' atau 'b') sekaligus,
// supaya tidak perlu menulis "player === 'a' ? ... : ..." berulang kali.
function elemenPemain(player) {
    if (player === 'a') {
        return {
            kid: DOM.kidA, hopper: DOM.hopperA, img: DOM.imgA,
            question: DOM.questionA, answers: DOM.answersA,
            progress: DOM.progressA, popup: DOM.popupA,
        };
    }
    return {
        kid: DOM.kidB, hopper: DOM.hopperB, img: DOM.imgB,
        question: DOM.questionB, answers: DOM.answersB,
        progress: DOM.progressB, popup: DOM.popupB,
    };
}

// ================================================================
// INISIALISASI (dijalankan sekali saat halaman dibuka)
// ================================================================
function init() {
    // Nama pemain di judul papan
    DOM.nameA.textContent = BATTLE_CONFIG.playerA.nama;
    DOM.nameB.textContent = BATTLE_CONFIG.playerB.nama;

    // Muat dulu semua gambar karakter agar tidak berkedip saat berganti ke pose jatuh
    ['a', 'b'].forEach(player => {
        new Image().src = BATTLE_CONFIG.gambar[player].jatuh;
        elemenPemain(player).img.src = BATTLE_CONFIG.gambar[player].berdiri;
    });

    // Tampilkan soal, posisi karakter, dan titik streak awal
    ['a', 'b'].forEach(player => {
        renderSoal(player, GameState[player].currentSoal);
        updateProgress(player, GameState[player].progress);
        updateStreakDots(player, GameState[player].streakBenar);
    });
}

// ================================================================
// TAMPILKAN SOAL DAN PILIHAN JAWABAN
// ================================================================
function renderSoal(player, soal) {
    const el = elemenPemain(player);

    if (!soal || !soal.id_soal) {
        el.question.textContent = 'Memuat soal...';
        el.answers.innerHTML = '';
        return;
    }

    el.question.textContent = soal.pertanyaan;

    // Buat tombol untuk tiap jawaban
    el.answers.innerHTML = '';
    soal.jawabans.forEach(jawaban => {
        const btn = document.createElement('button');
        btn.className = 'answer-btn';
        btn.textContent = jawaban.teks_jawaban;
        btn.dataset.id = jawaban.id_jawaban;   // disimpan untuk dikirim ke server
        // pointerdown: bereaksi saat jari menyentuh, dan tiap jari dihitung sendiri-sendiri
        btn.addEventListener('pointerdown', (e) => {
            if (e.pointerType === 'mouse' && e.button !== 0) return;   // abaikan klik kanan
            handleAnswer(player, jawaban.id_jawaban);
        });

        // Cadangan untuk keyboard (Enter/Spasi). Klik dari keyboard punya detail 0,
        // sedangkan klik susulan dari sentuhan/mouse (detail 1) diabaikan.
        btn.addEventListener('click', (e) => {
            if (e.detail === 0) handleAnswer(player, jawaban.id_jawaban);
        });
        el.answers.appendChild(btn);
    });
}

// ================================================================
// SAAT PEMAIN MENEKAN JAWABAN
// ================================================================
function handleAnswer(player, idJawaban) {
    const state = GameState[player];

    // Abaikan kalau pemain sedang jatuh, sudah finish, sedang menunggu server, atau game selesai
    if (state.isLocked || state.isFinished || GameState.gameOver) return;

    state.isLocked = true;      // kunci agar tidak bisa ditekan dua kali
    lockAnswers(player);

    fetch(BATTLE_CONFIG.answerUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept'      : 'application/json',
            'X-CSRF-TOKEN': BATTLE_CONFIG.csrfToken,
        },
        body: JSON.stringify({ player: player, id_jawaban: idJawaban }),
    })
    .then(res => {
        if (!res.ok) throw new Error('Server membalas ' + res.status);
        return res.json();
    })
    .then(data => processResponse(player, idJawaban, data))
    .catch(err => {
        console.error('Error:', err);
        // Kalau gagal, buka kunci supaya pemain bisa mencoba lagi
        state.isLocked = false;
        unlockAnswers(player);
    });
}

// ================================================================
// PROSES JAWABAN DARI SERVER
// ================================================================
function processResponse(player, idJawaban, data) {
    const state = GameState[player];

    // Simpan hasil dari server
    state.progress    = data.progress;
    state.streakBenar = data.streak_benar;
    state.streakSalah = data.streak_salah;
    state.isFallen    = data.is_fallen;
    state.isFinished  = data.is_finished;

    // 1. Warnai tombol: hijau kalau benar, merah kalau salah
    showAnswerFeedback(player, idJawaban, data.correct_answer_id, data.is_correct);

    // 2. Geser karakter dan perbarui skor dan titik streak
    updateProgress(player, data.progress);
    updateStreakDots(player, data.streak_benar);

    // 3. Animasi karakter
    if (data.is_correct) {
        lompat(player, data.is_boost);                 // melangkah dengan egrang (melompat kecil)
        if (data.is_boost) {
            applyBoostEffect(player);
            showPopup(player, 'SPEED BOOST!', 'boost', 1500);
        }
    } else if (data.is_fallen) {
        applyFallAnimation(player, data, state);       // salah 3x: pakai gambar jatuh
    } else {
        applyShakeAnimation(player);                   // salah 1-2x: goyang sedikit
    }

    // 4. Ada pemenang?
    if (data.winner) {
        GameState.gameOver = true;
        setTimeout(() => showWinScreen(data.winner, data.winner_nama), 800);   // tunggu animasi selesai
        return;
    }

    // 5. Soal berikutnya (kalau tidak jatuh. Kalau jatuh, soal tampil setelah berdiri lagi)
    if (!data.is_fallen && data.next_soal) {
        setTimeout(() => transitionToNextSoal(player, data.next_soal), NEXT_SOAL_DELAY);
    }
}

// ================================================================
// WARNA TOMBOL JAWABAN
// ================================================================
function showAnswerFeedback(player, idJawaban, correctId, isCorrect) {
    const buttons = elemenPemain(player).answers.querySelectorAll('.answer-btn');

    buttons.forEach(btn => {
        const btnId = parseInt(btn.dataset.id);

        if (btnId === parseInt(idJawaban)) {
            btn.classList.add(isCorrect ? 'correct' : 'wrong');
        }
        // Kalau jawabannya salah, tunjukkan juga jawaban yang benar
        if (!isCorrect && correctId && btnId === parseInt(correctId)) {
            btn.classList.add('correct');
        }
    });
}

// ================================================================
// SKOR DAN POSISI KARAKTER
// Posisi diatur lewat variabel CSS --p (0 = start, 1 = finish).
// Perhitungan posisinya ada di battlePlay.css.
// ================================================================
function updateProgress(player, progress) {
    const el = elemenPemain(player);
    const persen = Math.min(progress / BATTLE_CONFIG.winTarget, 1);

    el.kid.style.setProperty('--p', persen);
    el.progress.textContent = progress + '/' + BATTLE_CONFIG.winTarget;
}

// ================================================================
// TITIK STREAK (3 titik, menyala sesuai jumlah benar berturut-turut)
// ================================================================
function updateStreakDots(player, streakBenar) {
    DOM.streakDots[player].forEach((dot, index) => {
        dot.className = 'streak-dot';
        if (index < streakBenar) {
            dot.classList.add('active-' + player);
        }
    });
}

// ================================================================
// ANIMASI KARAKTER
// ================================================================

// Karakter melompat kecil seperti melangkah dengan egrang.
// Kalau boost (maju 2 langkah), melompat dua kali.
function lompat(player, boost) {
    const hopper = elemenPemain(player).hopper;
    hopper.classList.remove('lompat', 'lompat-2', 'goyang');
    void hopper.offsetWidth;                       // memaksa animasi bisa diulang
    hopper.classList.add(boost ? 'lompat-2' : 'lompat');
    setTimeout(() => hopper.classList.remove('lompat', 'lompat-2'), 700);
}

// Salah 1-2x: karakter goyang sedikit
function applyShakeAnimation(player) {
    const hopper = elemenPemain(player).hopper;
    hopper.classList.remove('lompat', 'lompat-2', 'goyang');
    void hopper.offsetWidth;
    hopper.classList.add('goyang');
    setTimeout(() => hopper.classList.remove('goyang'), 450);
}

// Efek cahaya saat speed boost
function applyBoostEffect(player) {
    const kid = elemenPemain(player).kid;
    kid.classList.add('boost');
    setTimeout(() => kid.classList.remove('boost'), BOOST_MS);
}

// Salah 3x berturut-turut: ganti ke gambar jatuh, lalu berdiri lagi setelah FALL_MS
function applyFallAnimation(player, data, state) {
    const el = elemenPemain(player);
    const gambar = BATTLE_CONFIG.gambar[player];

    showPopup(player, 'Jatuh!', 'fall', FALL_MS + 400);

    // Pakai gambar jatuh
    el.hopper.classList.remove('lompat', 'lompat-2', 'goyang');
    el.img.src = gambar.jatuh;
    el.kid.classList.remove('bangun');
    el.kid.classList.add('jatuh');

    // Setelah FALL_MS, kembali berdiri
    setTimeout(() => {
        el.img.src = gambar.berdiri;
        el.kid.classList.remove('jatuh');
        void el.kid.offsetWidth;
        el.kid.classList.add('bangun');
        setTimeout(() => el.kid.classList.remove('bangun'), 400);

        // Pemain boleh menjawab lagi
        state.isLocked = false;
        state.isFallen = false;
        if (data.next_soal) {
            transitionToNextSoal(player, data.next_soal);
        }
    }, FALL_MS);
}

// ================================================================
// NOTIFIKASI KECIL ("SPEED BOOST!", "Jatuh!")
// ================================================================
function showPopup(player, text, type, duration) {
    const popup = elemenPemain(player).popup;
    popup.textContent = text;
    popup.className = 'popup-notify ' + type;

    void popup.offsetWidth;
    popup.classList.add('show');

    setTimeout(() => popup.classList.remove('show'), duration);
}

// ================================================================
// KUNCI DAN BUKA TOMBOL JAWABAN
// ================================================================
function lockAnswers(player) {
    elemenPemain(player).answers.querySelectorAll('.answer-btn').forEach(btn => {
        btn.disabled = true;
    });
}

function unlockAnswers(player) {
    elemenPemain(player).answers.querySelectorAll('.answer-btn').forEach(btn => {
        btn.disabled = false;
    });
}

// ================================================================
// PINDAH KE SOAL BERIKUTNYA
// ================================================================
function transitionToNextSoal(player, soal) {
    const state = GameState[player];
    state.currentSoal = soal;
    renderSoal(player, soal);
    state.isLocked = false;
}

// ================================================================
// LAYAR PEMENANG
// ================================================================
function showWinScreen(winner, winnerNama) {
    lockAnswers('a');
    lockAnswers('b');

    const loserNama = (winner === 'a')
        ? BATTLE_CONFIG.playerB.nama
        : BATTLE_CONFIG.playerA.nama;

    DOM.winTitle.textContent    = winnerNama + ' MENANG!';
    DOM.winSubtitle.textContent = 'Selamat telah mengalahkan ' + loserNama + '!';
    DOM.winCard.style.borderColor = (winner === 'a') ? '#5b8ff7' : '#f4a09b';

    DOM.winOverlay.classList.add('show');
    launchConfetti();
}

// ================================================================
// CONFETTI
// ================================================================
function launchConfetti() {
    const warna = ['#ff6b6b', '#ffd93d', '#6bcb77', '#4d96ff', '#ff922b', '#cc5de8', '#f8f9fa', '#74c0fc'];

    for (let i = 0; i < 80; i++) {
        setTimeout(() => {
            const kepingan = document.createElement('div');
            kepingan.className = 'confetti-piece';
            kepingan.style.left       = Math.random() * 100 + 'vw';
            kepingan.style.top        = '-20px';
            kepingan.style.background = warna[Math.floor(Math.random() * warna.length)];
            kepingan.style.width      = (Math.random() * 10 + 8) + 'px';
            kepingan.style.height     = (Math.random() * 10 + 8) + 'px';
            kepingan.style.animationDuration = (Math.random() * 2 + 2) + 's';
            kepingan.style.animationDelay    = Math.random() * 0.5 + 's';
            document.body.appendChild(kepingan);

            setTimeout(() => kepingan.remove(), 4000);
        }, i * 30);
    }
}

// ================================================================
// TOMBOL LAYAR PENUH (pojok kanan bawah)
// ================================================================
const tombolLayar = document.getElementById('btn-layar');

function layarPenuhDidukung() {
    return document.fullscreenEnabled || document.webkitFullscreenEnabled;
}
function sedangLayarPenuh() {
    return document.fullscreenElement || document.webkitFullscreenElement;
}

if (!layarPenuhDidukung()) {
    tombolLayar.hidden = true;     // contoh: iPhone tidak mendukung
} else {
    tombolLayar.addEventListener('click', () => {
        const halaman = document.documentElement;
        if (sedangLayarPenuh()) {
            if (document.exitFullscreen) { document.exitFullscreen(); }
            else { document.webkitExitFullscreen(); }
        } else {
            if (halaman.requestFullscreen) { halaman.requestFullscreen(); }
            else { halaman.webkitRequestFullscreen(); }
        }
    });
}

// ================================================================
// MULAI GAME
// ================================================================
init();