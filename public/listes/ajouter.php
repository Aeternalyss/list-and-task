<?php
// démarrer la session tout de suite (important pour l'INSERT plus tard)
session_start();

$niveau = "../";
include($niveau . "liaisons/inc/config.inc.php");
$pdoConnexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// Inclusion du fichier erreur json
$strFichierTexte = file_get_contents($niveau.'liaisons/json/message-erreur.json');
$jsonMessageErreur=json_decode($strFichierTexte);

//Liste des champs fautifs
$arrChampErreur=array();
// avant tout traitement
$arrMessagesErreur = [
    "nom_liste" => "",
    "couleurs"  => ""
];

$arrMessagesErreur["couleur_liste"]="";
$arrMessagesErreur["nom_liste"]="";

$strIdListe = intval($_GET['id_liste'] ?? 0);

// Détection de l'opération
if (isset($_GET['btn_modifier'])) {
    $strCodeOperation = "Modifier";
} elseif (isset($_GET['btn_nouveau'])) {
    $strCodeOperation = "Nouveau";
} elseif (isset($_GET['btn_ajouter'])) {
    $strCodeOperation = "Ajouter";
} elseif (isset($_GET['btn_supprimer'])) {
    $strCodeOperation = "Supprimer";
} elseif (isset($_GET['btn_annuler'])) {
    $strCodeOperation = "Annuler";
} else {
    $strCodeOperation = "afficher";
}

$strCodeErreur = "00000";
$strMessage = "";

// Chargement des infos de la liste (si id > 0)
$arrListes = [];
if ($strIdListe > 0) {
    $req = "SELECT id, nom, couleur_id FROM listes WHERE id = :id";
    $pdosResultat = $pdoConnexion->prepare($req);
    $pdosResultat->execute([':id' => $strIdListe]);
    $arrListes = $pdosResultat->fetch(PDO::FETCH_ASSOC) ?: [];
    $pdosResultat->closeCursor();
}

// ------------------------------------------
// TRAITEMENT DE L'AJOUT (si demandé)
// ------------------------------------------
if ($strCodeOperation == "Ajouter") {

    $nom = trim($_GET['nom'] ?? "");
    $couleur = intval($_GET['couleur_liste'] ?? 0);

    // Validation AVANT l'INSERT
    if ($nom === "" || strlen($nom) > 50) {
        $strCodeErreur = "-1";
        $arrChampErreur[] = "nom_liste";
    }

    if ($couleur === 0) {
        $strCodeErreur = "-1";
        $arrChampErreur[] = "couleurs";
    }

    // Si erreur → message + conserver les infos saisies
    if ($strCodeErreur != "00000") {

        foreach ($arrChampErreur as $champ) {
            $arrMessagesErreur[$champ] = $jsonMessageErreur->{$champ}->erreurs->vide;
        }

        // Garder les données validées
        // (pour réaffichage dans le formulaire)
        $valeurNom = $nom;           
        $idCouleurListe = $couleur;  

    } else {

        // INSERT SEULEMENT SI 0 ERREUR
        $req = $pdoConnexion->prepare("
            INSERT INTO listes (nom, couleur_id, utilisateur_id)
            VALUES (:nom, :couleur, 1)
        ");

        $req->execute([
            ':nom' => $nom,
            ':couleur' => $couleur
        ]);

        $strMessage = $jsonMessageErreur->retroactions->liste->ajouter; 
        $strCodeErreur = "DONE"; 
    }
}



// ------------------------------------------
// TRAITEMENT DE LA MODIFICATION
// ------------------------------------------
if ($strCodeOperation == "Modifier") {

    $nom = $_GET['nom'] ?? "";
    $couleur = intval($_GET['couleur_liste'] ?? 0);
    $id = intval($_GET['id_liste'] ?? 0);

    $strRequete = "UPDATE listes SET nom = :nom, couleur_id = :couleur WHERE id = :id";
    $pdosResultat = $pdoConnexion->prepare($strRequete);
    $pdosResultat->execute([
        ':nom' => $nom,
        ':couleur' => $couleur,
        ':id' => $id
    ]);

    $strIdListe = $id;
    $strCodeErreur = "DONE";
    $strMessage = "La liste a été mise à jour avec succès !";
}

$arrListeNom = ['nom' => ''];
if ($strIdListe > 0) {
    $sql = "SELECT nom FROM listes WHERE id = :id";
    $pdos = $pdoConnexion->prepare($sql);
    $pdos->execute([':id' => $strIdListe]);
    $row = $pdos->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $arrListeNom = $row;
    }
    $pdos->closeCursor();
}

// Charger les couleurs
$strCouleursListes = "SELECT * FROM couleurs";
$pdosResultat = $pdoConnexion->query($strCouleursListes);
$arrCouleurs = $pdosResultat->fetchAll(PDO::FETCH_ASSOC);
$pdosResultat->closeCursor();


// Couleur actuelle — garder celle choisie si erreur
if (!isset($idCouleurListe)) {
    $idCouleurListe = $arrListes['couleur_id'] ?? 0;
}


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

<div class="ml-10 p-3">
    
</div>

<div class="bg-pink-200 ml-10 mr-10 border-t border-b border-black">
    <h1 class="text-black ml-8">Ajouter une liste</h1>
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
        <?php if (!empty($arrMessagesErreur["nom_liste"])): ?>
    <p class="ml-10 text-red-600 text-sm">
        <?= htmlspecialchars($arrMessagesErreur["nom_liste"]) ?>
    </p>
<?php endif; ?>
        <input class="ml-10 p-2 border border-black rounded-lg font-bold text-xl max-w-"
               type="text" id="nom" name="nom"
               value="<?= htmlspecialchars($valeurNom ?? '') ?>"
    </div>
    

    <br>

    <!-- Couleurs -->
    <div>
        <legend><h4 class="ml-10">Couleur de la liste :</h4></legend>
         <?php if (!empty($arrMessagesErreur["couleurs"])): ?>
    <p class="ml-10 text-red-600 text-sm">
        <?= htmlspecialchars($arrMessagesErreur["couleurs"]) ?>
    </p>
<?php endif; ?>

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

        <button type="submit" name="btn_ajouter"
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
<!--A faire !! :D garder la partie des date mal remplit?-->
