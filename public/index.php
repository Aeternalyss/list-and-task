<?php $niveau = "./"; ?>

<?php include($niveau . "liaisons/inc/config.inc.php"); ?>
<?php
$arrMois = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

$strRequete = '
SELECT listes.id, listes.nom, couleurs.hexadecimal,
       COUNT(items.id) AS nb_items
FROM listes
JOIN couleurs ON listes.couleur_id = couleurs.id
LEFT JOIN items ON items.liste_id = listes.id
GROUP BY listes.id, listes.nom, couleurs.hexadecimal
ORDER BY listes.nom
';

$pdosResultat = $pdoConnexion->query($strRequete);

$arrListes = [];
while ($ligne = $pdosResultat->fetch()) {
    $arrListes[] = $ligne;
}
$pdosResultat->closeCursor();


//Requête pour les tâches arrivant à échéances bientôt 

// Requête pour les tâches arrivant à échéances bientôt 
$strTacheEcheance = '
    SELECT items.*, 
           listes.nom AS nom_liste,
           couleurs.hexadecimal AS couleur_liste,items.id as item,listes.id as liste_id,DAY(echeance) AS jour, MONTH(echeance) AS mois, YEAR(echeance) AS annee
    FROM items
    JOIN listes ON items.liste_id = listes.id
    JOIN couleurs ON listes.couleur_id = couleurs.id
    WHERE items.est_complete = 0
      AND items.echeance IS NOT NULL
    ORDER BY items.echeance ASC
    LIMIT 3;
';

$pdosResultat = $pdoConnexion->query($strTacheEcheance);

$arrTacheEcheance = [];
while ($ligne = $pdosResultat->fetch()) {
    $arrTacheEcheance[] = $ligne;
}
$pdosResultat->closeCursor();

// --- Suppression ---
//
if (isset($_GET['btn_suppression']) && $_GET['btn_suppression'] == 'Suppression') {

    if (isset($_GET['id'])) {
        $ids = is_array($_GET['id']) ? $_GET['id'] : [$_GET['id']];
        $ids = array_map('intval', $ids);
        $strIds = implode(',', $ids);
        $strRequete = "DELETE FROM listes WHERE id IN ($strIds)";
        $pdoConnexion->query($strRequete);
    }
}

//

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="">
    <meta charset="utf-8">
    <title>Accueil</title>
    <link href="liaisons/css/styles.css?v=2" rel="stylesheet">
    <link href="liaisons/fonts/typos.css" rel="stylesheet">
</head>

<body>

    <?php include($niveau . "liaisons/inc/fragments/entete.inc.php"); ?>

    <section class="bg-pink-200">
        <label class="text-black ml-10 ">Recherchez un nom de liste ou une tâche</label>
        <br>
        <input type="search" id="site_recherche" name="recherche" class="border border-black p-4 rounded w-80 ml-10 max-h-7 bg-white mb-4 hover:shadow-[1px_3px_3.5px_rgba(0,0,0,0.25)] focus:outline focus:outline-red-700 focus:outline-2
           focus:shadow-none" />
    </section>
    <main>
        <div id="contenu">
            <div class="ml-10 mr-10 mt-6">

                <h1 class="text-black text-3xl font-medium ml-10 mt-10">Tâches arrivant à échéance </h1>

                <?php foreach ($arrTacheEcheance as $tache): ?>

                    <div class="border border-black rounded mb-3 p-2
                flex items-center justify-between" style="background-color:#<?= $tache['couleur_liste'] ?>">

                        <div>
                            <p class=" font-bold">
                                <?= htmlspecialchars($tache['nom']) ?>
                            </p>
                            <p class=" text-gray-700">
                                Liste : <?= htmlspecialchars($tache['nom_liste']) ?>
                            </p>
                            <p class="text-md italic">
                                Échéance :
                                <?php echo $tache['jour'] . " " . $arrMois[$tache['mois']] . " " . $tache['annee'] ?>
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="items/modifier.php?id_item=<?= $tache['item'] ?>&id_liste=<?= $tache['liste_id'] ?>"
                                class="bg-black text-white px-4 py-1 rounded-full flex items-center gap-2 hover:bg-pink-700 focus:outline-red-700 ">
                                <img src="<?php echo $niveau; ?>/liaisons/images/svg/crayon.svg"> <span>Modifier</span>
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>


            </div>

            <div class="flex justify-end mt-10 mr-10">
                <form action="listes/ajouter.php">

                    <button
                        class="bg-black text-white px-4 py-1 rounded-full flex justify-end items-center gap-2  hover:bg-pink-700 focus:outline-red-700"
                        type="submit" value="Submit">
                        <img class="size-7" src="liaisons/images/svg/ajouter.svg">
                        Ajouter une liste
                    </button>
                </form>
            </div>
            <style>
                dialog {
                    position: fixed;
                    inset: 0;
                    margin: auto;
                }

                dialog::backdrop {
                    background: rgba(0, 0, 0, 0.55);
                }
            </style>


            <h1 class="text-black text-3xl font-medium ml-10 mt-10">Vos listes </h1>
            <?php foreach ($arrListes as $liste): ?>
                <a href="items/afficher.php?id_liste=<?= $liste['id'] ?>" class="text-xl block">
                    <div class="border border-black mr-10 p-3 mb-2 ml-10 rounded flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                        style="background-color:#<?= $liste['hexadecimal'] ?>;">
                        <?= htmlspecialchars($liste['nom']) ?><br>
                        <span class="text-sm"><?= $liste['nb_items'] ?> tâches</span>
                </a>

                <!-- Boutons -->
                <div class="flex items-center gap-4">
                    <button name="btn_suppression" value="Suppression"
                        class="open-dialog bg-white text-black border border-black px-4 py-1 rounded-full flex items-center gap-2  hover:bg-pink-200 focus:outline-red-700"
                        data-id="<?= $liste['id'] ?>" data-nom="<?= htmlspecialchars($liste['nom']) ?>">
                        <img src="liaisons/images/svg/poubelle.svg"> <span>Supprimer</span>
                    </button>

                    <a href="listes/modifier.php?id_liste=<?= $liste['id'] ?>"
                        class="bg-black text-white px-4 py-1 rounded-full flex items-center gap-2  hover:bg-pink-700 focus:outline-red-700">
                        <img src="liaisons/images/svg/crayon.svg"> <span>Modifier</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
        <dialog id="delete-dialog" class="rounded-[50px] p-10 w-full max-w-3xl text-center
           bg-pink-300 backdrop:bg-black/60 backdrop:backdrop-blur-sm">

            <h1 class="text-5xl font-[cursive] font-bold mb-8">
                Suppression de liste
            </h1>

            <p id="dialog-message" class="text-2xl leading-relaxed mb-12"></p>

            <div class="flex justify-center gap-12">
                <button id="cancel-btn" class="px-10 py-5 bg-white text-3xl rounded-full
                   border-[3px] border-pink-700 shadow-md hover:scale-[1.03] transition">
                    Annuler
                </button>

                <button id="confirm-delete" class="px-10 py-5 bg-white text-3xl rounded-full”
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




</body>

</html>