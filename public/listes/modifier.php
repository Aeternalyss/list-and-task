<?php $niveau="../"; ?>
<?php include($niveau . "liaisons/inc/config.inc.php"); ?>

<?php

// Récupération de l'identifiant 
$strIdListe = $_GET['id_liste'] ?? null;

// Détection de l'opération
if (isset($_GET['btn_modifier'])) {
    $strCodeOperation = "Modifier";
} elseif (isset($_GET['btn_nouveau'])) {
    $strCodeOperation = "Nouveau";
} elseif(isset($_GET['btn_ajouter'])) {
    $strCodeOperation = "Ajouter";
} elseif(isset($_GET['btn_supprimer'])){
    $strCodeOperation ="Supprimer";
} elseif(isset($_GET['btn_annuler'])){
    $strCodeOperation ="Annuler";
} else {
    $strCodeOperation = "afficher";
}

// Code d'erreur et message
$strCodeErreur = "00000";
$strMessage = "";

// Chargement des infos de la liste
$arrListes = [];

if ($strIdListe) {
    $req = "SELECT id, nom, couleur_id FROM listes WHERE id = $strIdListe";
    $pdosResultat = $pdoConnexion->query($req);
    $arrListes = $pdosResultat->fetch();
}

// ------------------------------------------
// TRAITEMENT DE LA MODIFICATION
// ------------------------------------------
if ($strCodeOperation == "Modifier") {

    $nom = $_GET['nom'] ?? "";
    $couleur = $_GET['couleur_liste'] ?? "";
    $id = $_GET['id_liste'] ?? "";

    // --- VALIDATION : NOM VIDE ---
    if (trim($nom) === "") {

        $strCodeErreur = "VIDE";
        $strMessage = "Le nom de la liste ne peut pas être vide.";

        // Conserver la couleur sélectionnée par l'utilisateur
        if (isset($_GET['couleur_liste'])) {
            $idCouleurListe = $_GET['couleur_liste'];
        }

    } else {

        // --- MISE À JOUR SI TOUT EST OK ---
        $strRequete = "UPDATE listes SET 
            nom = :nom,
            couleur_id = :couleur
            WHERE id = :id";

        $pdosResultat = $pdoConnexion->prepare($strRequete);

        $pdosResultat->bindParam(':nom', $nom);
        $pdosResultat->bindParam(':couleur', $couleur);
        $pdosResultat->bindParam(':id', $id);

        $pdosResultat->execute();

        $strCodeErreur = "DONE";
        $strMessage = "La liste a été mise à jour avec succès !";
    }
}


// Charger le nom après modification
$strListeNom = "SELECT nom FROM listes WHERE id = ".$strIdListe;
$pdosResultat = $pdoConnexion->query($strListeNom);
$arrListeNom = $pdosResultat->fetch();
$pdosResultat->closeCursor();

// Charger les couleurs
$strCouleursListes = "SELECT * FROM couleurs";
$pdosResultat = $pdoConnexion->query($strCouleursListes);
$arrCouleurs = $pdosResultat->fetchAll();
$pdosResultat->closeCursor();

// Couleur actuelle
$idCouleurListe = $arrListes['couleur_id'] ?? 0;

// Fonction radio checked
function ecrireChecked($idCouleur, $couleurActuelle) {
    return ($idCouleur == $couleurActuelle) ? "checked" : "";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<title>Modifier une liste</title>
	<link href="<?php echo $niveau?>liaisons/css/styles.css" rel="stylesheet">
	<link href="<?php echo $niveau?>liaisons/fonts/typos.css" rel="stylesheet">
</head>

<body>

<?php include($niveau . "liaisons/inc/fragments/entete.inc.php"); ?>

<div class="ml-10 p-3"></div>

<div class="bg-pink-200 ml-10 mr-10 border-t border-b border-black">
    <h1 class="text-black ml-8">Modifier une liste</h1>
</div>


<!-- ---------------------------------------------------
     MESSAGE DE SUCCÈS (FORMULAIRE CACHÉ)
-------------------------------------------------------- -->
<?php if ($strCodeErreur == "DONE"): ?>

<div class="ml-10 mr-10 mt-6 p-4 bg-green-200 border border-green-700 text-green-900 rounded">
    ✔️ <?= htmlspecialchars($strMessage) ?>
</div>

<div class="ml-10 mt-4">
    <a class="text-xl underline text-pink-800" href="../index.php">Retour à l'accueil</a>
</div>

<?php else: ?>


<!-- ---------------------------------------------------
     FORMULAIRE (SI PAS DE SUCCESS)
-------------------------------------------------------- -->
<form action="#" method="GET">

    <!-- Champ caché ID -->  
    <input type="hidden" name="id_liste" value="<?= htmlspecialchars($strIdListe) ?>">

    <!-- Nom -->
    <div>
        <label for="nom"><h4 class="ml-10">Nom de la liste :</h4></label>
        <input class="ml-10 p-2 border border-black rounded-lg font-bold text-xl"
       type="text" id="nom" name="nom"
       value="<?php 
           if ($strCodeErreur == 'VIDE') {
               // Reprendre ce que l'utilisateur a essayé d'envoyer
               echo htmlspecialchars($_GET['nom'] ?? '');
           } else {
               // Valeur provenant de la BD
               echo htmlspecialchars($arrListeNom['nom']);
           }
       ?>">

    </div>
  <?php if ($strCodeErreur == "VIDE"): ?>
<div class="ml-10 mr-10 mt-6 text-red-500">
     <?= htmlspecialchars($strMessage) ?>
</div>
<?php endif; ?>

    <br>

    <!-- Couleurs -->
    <div>
        <legend><h4 class="ml-10">Couleur de la liste :</h4></legend>

        <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 ml-10 mr-10">
            <?php foreach ($arrCouleurs as $c): ?>
                <label class="flex items-center p-4 rounded-lg border-2"
                       style="background-color:#<?= $c['hexadecimal'] ?>;">
                    
                    <input type="radio"
                        name="couleur_liste"
                        value="<?= $c['id'] ?>"
                        <?= ecrireChecked($c['id'], $idCouleurListe); ?>
                        class="mr-2">

                    <?= htmlspecialchars($c['nom_fr']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <br>

    <!-- Boutons -->
    <div class="flex flex-col sm:flex-row gap-4 ml-10 mr-40">

       <a href="../index.php"
   class="border-2 p-3 rounded-lg flex items-center gap-2 text-black border-black  hover:bg-pink-200 focus:outline-red-700">
    <img src="../liaisons/images/svg/annuler.svg"> Annuler les modifications
</a>

        <button type="submit" name="btn_modifier"
                 onclick="window.location.href='index.php';"
            class="border-2 p-3 rounded-lg flex items-center gap-2 text-white border-black bg-black  hover:bg-pink-700 focus:outline-red-700">
            <img src="../liaisons/images/svg/check.svg"> Enregistrer les modifications
        </button>
    </div>

</form>

<?php endif; ?>


<?php include($niveau . "liaisons/inc/fragments/pied_de_page.inc.php"); ?>

</body>
</html>
