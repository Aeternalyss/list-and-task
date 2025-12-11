<link rel="stylesheet" href="liaisons/css/styles.css">

 <!-- Boutons -->
  <?php foreach($arrListes as $liste): ?>
        <div class="flex items-center gap-4">
           <button  name="btn_suppression" value="Suppression" class="open-dialog bg-white text-black border border-black px-4 py-1 rounded-full flex items-center gap-2"data-id="<?= $liste['id'] ?>" data-nom="<?= htmlspecialchars($liste['nom']) ?>">
    <img src="liaisons/images/svg/poubelle.svg"> <span>Supprimer</span>
</button>



            <a href="listes/modifier.php?id_liste=<?= $liste['id'] ?>"
               class="bg-black text-white px-4 py-1 rounded-full flex items-center gap-2">
                <img src="liaisons/images/svg/crayon.svg"> <span>Modifier</span>
            </a>
        </div>
    </div>
<?php endforeach; ?>
<dialog id="delete-dialog"
    class="rounded-[50px] p-10 w-full max-w-3xl text-center
           bg-pink-300 backdrop:bg-black/60 backdrop:backdrop-blur-sm">

    <h1 class="text-5xl font-[cursive] font-bold mb-8">
        Suppression de liste
    </h1>

    <p id="dialog-message" class="text-2xl leading-relaxed mb-12"></p>

    <div class="flex justify-center gap-12">
        <button id="cancel-btn"
            class="px-10 py-5 bg-white text-3xl rounded-full
                   border-[3px] border-pink-700 shadow-md hover:scale-[1.03] transition">
            Annuler
        </button>

        <button id="confirm-delete"
            class="px-10 py-5 bg-white text-3xl rounded-full
                   border-[3px] border-pink-700 shadow-md hover:scale-[1.03] transition">
            Supprimer
        </button>
    </div>
</dialog>

	</main>
	
<?php include($niveau . "liaisons/inc/fragments/pied_de_page.inc.php"); ?>

<script>
const dialog = document.getElementById("delete-dialog");
const dialogMessage = document.getElementById("dialog-message");

// Sélectionne tous les boutons "Supprimer"
document.querySelectorAll(".open-dialog").forEach(btn => {
    btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        const nom = btn.dataset.nom;

        // Insère dynamiquement le texte
        dialogMessage.innerHTML = `
            Voulez-vous supprimer la liste 
            <span class="font-semibold">“${nom}”</span>
            et tout son contenu ?
        `;

        // Stocke l'ID dans le bouton de confirmation
        document.getElementById("confirm-delete").dataset.id = id;

        dialog.showModal();
    });
});

// Annuler
document.getElementById("cancel-btn").onclick = () => dialog.close();

// Confirmer la suppression
document.getElementById("confirm-delete").onclick = (e) => {
    const id = e.target.dataset.id;

    // Envoi vers la même page pour que ton PHP exécute la suppression
    window.location.href = "?btn_suppression=Suppression&id=" + id;
};



</script>