// Script pour gérer la fenêtre modale
document.addEventListener('DOMContentLoaded', function() {
    let modal = document.getElementById("myModal");
    let span = document.getElementsByClassName("exit-button")[0];
    let cancelBtn = document.getElementById("cancel-button");
    let deleteButtons = document.querySelectorAll('.button-delete[data-user-id]');

    deleteButtons.forEach(function(button) {
        button.onclick = function() {
            let userId = this.getAttribute('data-user-id');
            let userName = this.getAttribute('data-user-name');
            let userLastname = this.getAttribute('data-user-lastname');
            document.getElementById('modal-user-name').textContent = userName;
            document.getElementById('modal-user-lastname').textContent = userLastname;
            document.getElementById('delete-user-id').value = userId;
            modal.style.display = "block";
        }
    });

    span.onclick = function() {
        modal.style.display = "none";
    }

    cancelBtn.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});