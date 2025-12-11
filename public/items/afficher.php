<?php $niveau = "../";
include($niveau . "liaisons/inc/config.inc.php");


// récupération Query sting
if (isset($_GET['id_liste'])) {
    $strIdListe = $_GET['id_liste'];
} else {
    $strIdListe = '';
    echo "Aucune liste sélectionnée";
}
if (isset($_GET["complete"])) {
    $strId_itemComplete = $_GET["complete"];
    verifierCheckbox();
}
$arrMois = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
// récupérer la couleur et nom de la liste en cours
$strRequeteListe = "SELECT listes.nom, couleurs.hexadecimal 
FROM listes
JOIN couleurs ON listes.couleur_id = couleurs.id
WHERE listes.id = $strIdListe
";

$pdosResultat = $pdoConnexion->query($strRequeteListe);
$arrListe = $pdosResultat->fetch();
$pdosResultat->closeCursor();


switch (true) {

    case isset($_GET["btn_supprimer"]):
        $strCodeOperation = "supprimer";
        $strIditem = $_GET["btn_supprimer"];
        break;
    default:
        $strCodeOperation = "";
}
switch (true) {
    case isset($_GET["echeance"]):
        $strTri = "echeance";
        break;
    case isset($_GET["complet"]):
        $strTri = "complet";
        break;
    default:
        $strTri = "lesdeux";
        break;
}
// établir la requête pour obtenir les items
$strRequete = "SELECT id,liste_id, nom, echeance, est_complete,DAY(echeance) AS jour, MONTH(echeance) AS mois, YEAR(echeance) AS annee FROM items WHERE liste_id = $strIdListe";
if ($strTri == "echeance") {
    $strRequete .= " ORDER BY (echeance IS NULL),echeance ";
} else if ($strTri == "complet") {
    $strRequete .= " ORDER BY est_complete ASC ";
} else if ($strTri == "lesdeux") {
    $strRequete .= " ORDER BY est_complete ASC,(echeance IS NULL) ,echeance ASC ";
}

$pdosResultat = $pdoConnexion->query($strRequete);
$arrItems = array();
for ($cptItem = 0; $ligneItem = $pdosResultat->fetch(); $cptItem++) {
    $arrItems[$cptItem]['id'] = $ligneItem['id'];
    $arrItems[$cptItem]['liste_id'] = $ligneItem['liste_id'];
    $arrItems[$cptItem]['nom'] = $ligneItem['nom'];
    $arrItems[$cptItem]['echeance'] = $ligneItem['echeance'];
    $arrItems[$cptItem]['est_complete'] = $ligneItem['est_complete'];
    $arrItems[$cptItem]['jour'] = $ligneItem['jour'];
    $arrItems[$cptItem]['mois'] = $ligneItem['mois'];
    $arrItems[$cptItem]['annee'] = $ligneItem['annee'];
}
$pdosResultat->closeCursor();


// change l'échéance des taches cochées en complete
// si la checkbox box est cochée, elle envoie la valeur de l'échéance et on refresh avec la nouvelle valeur de 0 a 1 ou de 1 a 0

function verifierCheckbox()
{
    global $pdoConnexion, $strId_itemComplete, $strIdListe;
    $strRequete = "UPDATE items
        SET est_complete = 1 - est_complete
        WHERE id = :id";
    $objStat = $pdoConnexion->prepare($strRequete);
    $objStat->bindparam(':id', $strId_itemComplete);
    // https://stackoverflow.com/questions/603835/mysql-simple-way-to-toggle-a-value-of-an-int-field

    $objStat->execute();

}
if ($strCodeOperation == "supprimer") {
    $strRequete = "DELETE FROM items WHERE id =" . $strIditem;
    $pdoConnexion->query($strRequete);

    header("Location: afficher.php?id_liste=$strIdListe&$strTri#contenu");
    exit();

}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="">
    <meta charset="utf-8">
    <title>Consulter</title>
    <link href="../liaisons/css/styles.css?v=2" rel="stylesheet">
    <link href="../liaisons/fonts/typos.css" rel="stylesheet">
</head>

<body>
    <?php include($niveau . "liaisons/inc/fragments/entete.inc.php"); ?>

    <main>
        <div class="ml-10 p-3">
            <a id="contenu" class="text-3xl font-medium text-pink-800 underline"
                href="<?php echo $niveau ?>index.php">Retour à l'accueil</a>
        </div>
        <!-- filtre -->
        <div class="bg-pink-950 flex flex-col p-2 ml-15 mr-10 mb-10 border-t border-b border-black w-fit ">
            <p class=" w-auto px-4 py-2 mb-2 ">trier par : </p>
            <div class="flex flex-col  sm:flex-row  gap-4 w-fit">

                <a class=" w-auto px-4 py-2 bg-pink-200 rounded-full"
                    href="afficher.php?id_liste=<?= $strIdListe ?>&echeance">Échéance</a>

                <a class=" w-auto px-4 py-2 bg-pink-200 rounded-full"
                    href="afficher.php?id_liste=<?= $strIdListe ?>&complet">Complétés</a>

                <a class=" w-auto px-4 py-2 bg-pink-200 rounded-full"
                    href="afficher.php?id_liste=<?= $strIdListe ?>&lesdeux">Les deux</a>
            </div>
        </div>

        <div class=" ml-10 mr-10 border-t border-b border-black"
            style="background-color:#<?= $arrListe['hexadecimal'] ?>;">
            <h1 class="text-black ml-8">Consultation la liste : <?php echo $arrListe['nom'] ?> </h1>
        </div>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 ml-15 mr-10">
            <h2 class=" ">Tâches à réaliser</h2>
            <form action="ajouter.php" method="GET">
                <button type="submit" name="id_liste" value="<?php echo $strIdListe; ?>"
                    class="bg-black text-white border p-3  rounded-full flex items-center gap-2 hover:bg-pink-700 focus:outline-red-700 focus:outline-2">
                    <img src="<?php echo $niveau; ?>/liaisons/images/svg/ajouter.svg" alt="ajouter" class="w-4 h-4">
                    Ajouter une tache
                </button>
            </form>
        </div>


        <form action="#contenu" method="GET">
            <input type="hidden" name="id_liste" value="<?php echo $strIdListe; ?>"> <?php
               foreach ($arrItems as $item) { ?>

                <div class="border-b border-t border-black mr-10 p-3 mb-4 ml-10 pb-4"
                    style="background-color:#<?= $arrListe['hexadecimal'] ?>;">
                    <div class="flex flex-col sm:flex-row  justify-between ">
                        <!-- checkbox pour changer status de compléter -->
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="echeances<?php echo $item['id']; ?>"
                                onclick="window.location='afficher.php?id_liste=<?php echo $strIdListe; ?>&complete=<?php echo $item['id']; ?>&<?= $strTri ?>';"
                                value="<?php echo $item['id']; ?>"
                                class="appearance-none w-4 h-4 bg-white border-2 border-black rounded-sm checked:bg-black checked:border-black focus:outline-red-700 transition-all duration-200 ease-in-out"
                                <?php if ($item["est_complete"] != 0) {
                                    echo "checked";
                                } ?>>

                            <label for="echeances<?php echo $item['id']; ?>">
                                <?php echo $item['nom']; ?>
                            </label>
                        </div>

                        <!--    btn sup et modif                        -->
                        <div class="flex items-center gap-4 ">
                            <button type="button"
                                class="open-dialog bg-white text-black border border-black px-4 py-1 rounded-full flex items-center gap-2 hover:bg-pink-200 focus:outline-red-700"
                                data-id="<?php echo $item['id']; ?>" data-nom="<?php echo $item['nom']; ?>">
                                <img src="<?php echo $niveau; ?>/liaisons/images/svg/poubelle.svg" alt="Supprimer"
                                    class="w-4 h-4">
                                Supprimer
                            </button>
                            <a href="modifier.php?id_item=<?= $item['id'] ?>&id_liste=<?php echo $strIdListe ?>"
                                class="bg-black text-white px-4 py-1 rounded-full flex items-center gap-2 hover:bg-pink-700 focus:outline-red-700 ">
                                <img src="<?php echo $niveau; ?>/liaisons/images/svg/crayon.svg"> <span>Modifier</span>
                            </a>
                        </div>
                    </div>
                    <!-- échéance -->
                    <div class="flex items-center gap-2 ">
                        <img src="<?php echo $niveau; ?>/liaisons/images/svg/calendrier.svg" alt="Supprimer"
                            class="w-4 h-4">
                        <?php if ($item['echeance'] != null && $item['echeance'] != '') { ?>
                            <p> Échéance : <?php echo $item['jour'] . " " . $arrMois[$item['mois']] . " " . $item['annee'] ?>
                            </p>
                        <?php } else { ?>
                            <p> Pas d'échéance </p>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </form>
        <dialog id="delete-dialog"
            class="rounded-[50px] p-10 w-full max-w-3xl text-center bg-pink-300 backdrop:bg-black/60 backdrop:backdrop-blur-sm"
            style="inset:0; margin:auto;">

            <h1 class="text-5xl font-[cursive] font-bold mb-8">
                Suppression de l'item
            </h1>

            <p id="dialog-message" class="text-2xl leading-relaxed mb-12"></p>

            <div class="flex justify-center gap-12">
                <button id="cancel-btn"
                    class="px-10 py-5 bg-white text-3xl rounded-full border-[3px] border-pink-700 shadow-md hover:scale-[1.03] transition">
                    Annuler
                </button>

                <button id="confirm-delete"
                    class="px-10 py-5 bg-white text-3xl rounded-full border-[3px] border-pink-700 shadow-md hover:scale-[1.03] transition">
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
           ?
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
            window.location.href = "?id_liste=<?= $strIdListe ?>&btn_supprimer=" + id + "&<?= $strTri ?>";

        };

    </script>
</body>

</html>