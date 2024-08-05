
  document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {

            if (!confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Sa suppression entraînera la suppression des topics et commentaires associés de manière irréversible.')) {

                event.preventDefault();
            }
        });
    });
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('deleted')) {
        alert('Catégorie supprimée avec succès.');
    }

