// Logika halaman soal: kirim jawaban (AJAX), beri feedback benar/salah,
// lalu tampilkan tombol "Soal berikutnya".
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('jawaban-container');
    if (!container) return;

    const answerUrl = container.dataset.answerUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const nextForm = document.getElementById('next-form');
    const nextBtn = document.getElementById('btn-next');
    const selectedInput = document.getElementById('selected-jawaban-id');
    const feedback = document.getElementById('feedback');
    const buttons = container.querySelectorAll('.answer');

    let busy = false;

    const setLocked = (locked) => {
        container.classList.toggle('is-locked', locked);
        buttons.forEach((btn) => { btn.disabled = locked; });
    };

    async function pilihJawaban(button) {
        if (busy) return;
        busy = true;

        const idJawaban = button.dataset.id;
        setLocked(true);

        try {
            const response = await fetch(answerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ id_jawaban: idJawaban }),
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            // { is_correct: bool, correct_answer_id: int }
            const data = await response.json();

            if (data.is_correct) {
                button.classList.add('is-correct');
                feedback.textContent = 'Jawabanmu benar!';
            } else {
                button.classList.add('is-wrong');
                feedback.textContent = 'Jawabanmu salah.';

                // Tandai jawaban yang benar (hijau) setelah jeda singkat
                const correctBtn = container.querySelector(
                    `.answer[data-id="${CSS.escape(String(data.correct_answer_id))}"]`
                );
                if (correctBtn) correctBtn.classList.add('is-correct', 'is-reveal');
            }

            // Isi hidden input untuk form "Soal berikutnya", lalu tampilkan tombolnya
            selectedInput.value = idJawaban;
            nextForm.hidden = false;
            nextBtn.focus({ preventScroll: true });

        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
            setLocked(false); // izinkan mencoba lagi
        } finally {
            busy = false;
        }
    }

    // Satu listener untuk semua tombol jawaban
    container.addEventListener('click', (e) => {
        const button = e.target.closest('.answer');
        if (button && !button.disabled) pilihJawaban(button);
    });
});