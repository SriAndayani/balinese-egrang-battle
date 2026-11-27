
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

            
            const data = await response.json();

            if (data.is_correct) {
                button.classList.add('is-correct');
                feedback.textContent = 'Jawabanmu benar!';
            } else {
                button.classList.add('is-wrong');
                feedback.textContent = 'Jawabanmu salah.';

                
                const correctBtn = container.querySelector(
                    `.answer[data-id="${CSS.escape(String(data.correct_answer_id))}"]`
                );
                if (correctBtn) correctBtn.classList.add('is-correct', 'is-reveal');
            }

            
            selectedInput.value = idJawaban;
            nextForm.hidden = false;
            nextBtn.focus({ preventScroll: true });

        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
            setLocked(false); 
        } finally {
            busy = false;
        }
    }

    
    container.addEventListener('click', (e) => {
        const button = e.target.closest('.answer');
        if (button && !button.disabled) pilihJawaban(button);
    });
});