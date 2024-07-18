document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("contactForm");
    const dialog = document.getElementById("confirmationModal");
    const closeButton = document.getElementById("closeButton");

    form.addEventListener("submit", function (event) {
        // Vérifier si les champs requis sont vides
        if (!form.checkValidity()) {
            return; // Ne rien faire si le formulaire n'est pas valide
        }

        event.preventDefault();

        // Display the dialog for confirmation
        dialog.showModal();

        // Optionally, you can submit the form data via AJAX here
        // Or simply submit the form normally
        // form.submit();
    });

    closeButton.addEventListener("click", () => {
        dialog.close();
    });
});