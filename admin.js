(function () {

    const els = {
        addBtn: document.getElementById('add-log-btn'),
        modal: document.getElementById('log-modal'),
        form: document.getElementById('log-form'),
        modalCancel: document.getElementById('modal-cancel')
    };

    // Open the attendance form
    function openModal() {
        els.modal.setAttribute('aria-hidden', 'false');
    }

    // Close the attendance form
    function closeModal() {
        els.modal.setAttribute('aria-hidden', 'true');

        if (els.form) {
            els.form.reset();
        }

        const dateInput = document.getElementById('log-day');

        if (dateInput) {
            dateInput.valueAsDate = new Date();
        }
    }

    // Open modal
    if (els.addBtn) {
        els.addBtn.addEventListener('click', openModal);
    }

    // Close modal
    if (els.modalCancel) {
        els.modalCancel.addEventListener('click', closeModal);
    }

    // Set today's date
    const dateInput = document.getElementById('log-day');

    if (dateInput) {
        dateInput.valueAsDate = new Date();
    }

})();