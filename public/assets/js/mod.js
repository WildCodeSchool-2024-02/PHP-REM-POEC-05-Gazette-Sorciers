document.addEventListener('DOMContentLoaded', function() {
    const confirmationModal = document.getElementById('confirmationModal');
    const closeButton = document.getElementById('closeButton');
    const formMessages = document.getElementById('formMessages');

    if (formMessages && formMessages.querySelector('.success')) {
        confirmationModal.showModal();

        closeButton.addEventListener('click', function() {
            confirmationModal.close();
        });
    }
});
