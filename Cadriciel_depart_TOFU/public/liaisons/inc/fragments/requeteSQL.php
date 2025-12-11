<?php
$arrChampErreur = array();
$arrChampErreur = array();
$arrMessagesErreur = [
    "nom_item" => "",
    "echeance" => ""
];
$arrChampFini = array();
$arrMessagesFini = [
    "item" => ""
];
$strCodeErreur = "";

// récupération Query sting
if (isset($_GET['id_liste'])) {
    $strIdListe = $_GET['id_liste'];
} else {
    $strIdListe = '';
}
if (isset($_GET['id_item'])) {
    $strIdItem = $_GET['id_item'];
} else {
    $strIdItem = '';
}
if (isset($_GET['id_itemComplete'])) {
    $strId_itemComplete = $_GET['id_itemComplete'];
} else {
    $strId_itemComplete = '';
}

$arrMois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
$StrAnnee = date("Y", strtotime("+25 year"));

$jour = 0;
$mois = 0;
$StrAnneeActuelle = 0;
switch (true) {

    case isset($_GET["btn_supprimer"]):
        $strCodeOperation = "supprimer";
        $btn_supprimer = $_GET["btn_supprimer"];
        break;
    case isset($_GET["btn_complete"]):
        $strCodeOperation = "complete";
        $btn_complete = $_GET["btn_complete"];
        break;
    case isset($_GET["btn_modifier"]):
        $strCodeOperation = "modifier";
        $btn_modifier = $_GET["btn_modifier"];
        break;
    case isset($_GET["btn_ajouter"]):
        $strCodeOperation = "ajouter";
        $btn_modifier = $_GET["btn_ajouter"];
        break;
    default:
        $strCodeOperation = "";
}
// requete pour remplir les infos des item
if ($strIdItem == '' && $strIdListe != '') {

    $strRequteItems = " SELECT id,nom,echeance,est_complete FROM items WHERE liste_id = :liste_id ORDER BY id ASC";
    $pdosResultat = $pdoConnexion->prepare($strRequteItems);
    $pdosResultat->bindparam(':liste_id', $strIdListe);
    $pdosResultat->execute();

    $arrItems = array();
    for ($cptItem = 0; $ligneItem = $pdosResultat->fetch(); $cptItem++) {
        $arrItems[$cptItem]['id'] = $ligneItem['id'];
        $arrItems[$cptItem]['nom'] = $ligneItem['nom'];
        $arrItems[$cptItem]['echeance'] = $ligneItem['echeance'];
        $arrItems[$cptItem]['est_complete'] = $ligneItem['est_complete'];
    }
    $pdosResultat->closeCursor();

    // récupérer la couleur et nom de la liste en cours
    $strRequeteListe = "SELECT listes.nom, couleurs.hexadecimal 
        FROM listes
        JOIN couleurs ON listes.couleur_id = couleurs.id
        WHERE listes.id = $strIdListe
        ";
    $pdosResultat = $pdoConnexion->query($strRequeteListe);
    $arrListe = $pdosResultat->fetch();
    $pdosResultat->closeCursor();

} else if ($strIdListe == '' && $strIdItem != '') {
    $strRequteItems = " SELECT id,nom,echeance,est_complete,DAY(echeance) AS jour, MONTH(echeance) AS mois, YEAR(echeance) AS annee FROM items WHERE id = :id ";
    $pdosResultat = $pdoConnexion->prepare($strRequteItems);
    $pdosResultat->bindparam(':id', $strIdItem);
    $pdosResultat->execute();
    $arrItems = $pdosResultat->fetch();
    $pdosResultat->closeCursor();

} else if ($strIdItem != '' && $strIdListe != '') {
    $strRequteItems = " SELECT id,nom,echeance,est_complete,DAY(echeance) AS jour, MONTH(echeance) AS mois, YEAR(echeance) AS annee FROM items WHERE id = :id ";
    $pdosResultat = $pdoConnexion->prepare($strRequteItems);
    $pdosResultat->bindparam(':id', $strIdItem);
    $pdosResultat->execute();
    $arrItems = $pdosResultat->fetch();
    $pdosResultat->closeCursor();
    if ($arrItems['echeance'] == null) {
        $jour = 0;
        $mois = 0;
        $StrAnneeActuelle = 0;
    } else {
        $jour = $arrItems['jour'];
        $mois = $arrItems['mois'];
        $StrAnneeActuelle = $arrItems['annee'];
    }

}
// requte affiche le nom des listes
if ($strIdListe == '') {
    $requeteSQL = "SELECT nom,id FROM listes";
    $objStat = $pdoConnexion->prepare($requeteSQL);
    $objStat->execute();
    $arrListes = $objStat->fetchAll();
} else {
    $requeteSQL = "SELECT nom,id FROM listes WHERE id = :liste_id";
    $objStat = $pdoConnexion->prepare($requeteSQL);
    $objStat->bindparam(':liste_id', $strIdListe);
    $objStat->execute();
    $arrListes = $objStat->fetch();
}



// action formulaire
if ($strCodeOperation == "supprimer" || $strCodeOperation == "complete" || $strCodeOperation == "modifier" || $strCodeOperation == "ajouter") {
    $strFichierTexte = file_get_contents($niveau . 'liaisons/inc/json/message-erreur.json');
    $jsonMessageErreur = json_decode($strFichierTexte);
    if ($strCodeOperation == "ajouter" || $strCodeOperation == "modifier") {

        $arrItems["nom"] = $_GET["nom_item"];
        $arrItems["jour"] = $_GET["jour"];
        $arrItems["mois"] = $_GET["mois"];
        $arrItems["annee"] = $_GET["annee"];

        $intAnnee = intval($_GET['annee']);
        $intMois = intval($_GET['mois']);
        $intJour = intval($_GET['jour']);
        // message d'erreur
        if ($intJour == 0 && $intMois == 0 && $intAnnee == 0) {
            $arrItems["echeance"] = null;
        } else {
            if (checkdate($intMois, $intJour, $intAnnee)) {
                $arrItems["echeance"] = $intAnnee . "-" . $intMois . "-" . $intJour;
            } else {
                $strCodeErreur = "-1";
                $arrChampErreur[] = "echeance";
            }
        }


        if ($arrItems["nom"] == "" || strlen($arrItems["nom"]) > 50) {
            $strCodeErreur = "-1";
            $arrChampErreur[] = "nom_item";
        }
    }

    if ($strCodeErreur != "") {
        for ($cpt = 0; $cpt < count($arrChampErreur); $cpt++) {
            $champ = $arrChampErreur[$cpt];
            $arrMessagesErreur[$champ] = $jsonMessageErreur->{$champ}->erreurs->vide;
        }
    } else {
        if ($strCodeOperation == "supprimer") {

            $strRequete = "DELETE FROM items WHERE id =:id";
            $objStat = $pdoConnexion->prepare($strRequete);
            $objStat->bindparam(':id', $btn_supprimer);
        } else if ($strCodeOperation == "complete") {

            $strRequete = "
        UPDATE items
        SET est_complete = 1 - est_complete
        WHERE id = :id";
            $objStat = $pdoConnexion->prepare($strRequete);
            $objStat->bindparam(':id', $strId_itemComplete);
            // https://stackoverflow.com/questions/603835/mysql-simple-way-to-toggle-a-value-of-an-int-field
        } else if ($strCodeOperation == "modifier") {
            $arrItems["nom"] = $_GET["nom_item"];
            $arrItems["jour"] = $_GET["jour"];
            $arrItems["mois"] = $_GET["mois"];
            $arrItems["annee"] = $_GET["annee"];

            $intAnnee = intval($_GET['annee']);
            $intMois = intval($_GET['mois']);
            $intJour = intval($_GET['jour']);
            if ($intJour == 0 && $intMois == 0 && $intAnnee == 0) {
                $arrItems["echeance"] = null;
            } else {
                if (checkdate($intMois, $intJour, $intAnnee)) {
                    $arrItems["echeance"] = $intAnnee . "-" . $intMois . "-" . $intJour;
                } else {
                    $strCodeErreur = "-1";
                    $arrChampErreur[] = "echeance";
                }
            }
            $strRequete = "UPDATE items SET nom=:nom,echeance= :echeance WHERE id= :id_item";
            $objStat = $pdoConnexion->prepare($strRequete);
            $objStat->bindParam(':nom', $_GET['nom_item']);
            $objStat->bindParam(':id_item', $strIdItem);
            $objStat->bindvalue(":echeance", $arrItems["echeance"]);

        } else if ($strCodeOperation == "ajouter") {
            $arrItems["liste_id"] = $_GET["id_liste"];
            $arrItems["nom"] = $_GET["nom_item"];
            $arrItems["jour"] = $_GET["jour"];
            $arrItems["mois"] = $_GET["mois"];
            $arrItems["annee"] = $_GET["annee"];
            $arrItems["est_complete"] = 0;

            $intAnnee = intval($_GET['annee']);
            $intMois = intval($_GET['mois']);
            $intJour = intval($_GET['jour']);
            if ($intJour == 0 && $intMois == 0 && $intAnnee == 0) {
                $arrItems["echeance"] = null;
            } else {
                if (checkdate($intMois, $intJour, $intAnnee)) {
                    $arrItems["echeance"] = $intAnnee . "-" . $intMois . "-" . $intJour;

                }
            }

            $strRequeteInsert = "INSERT INTO items (liste_id,nom,echeance,est_complete)" . "VALUES (:liste_id, :nom, :echeance,0)";
            $objStat = $pdoConnexion->prepare($strRequeteInsert);
            $objStat->bindparam(':liste_id', $strIdListe);
            $objStat->bindparam(':nom', $arrItems["nom"]);
            $objStat->bindvalue(":echeance", $arrItems["echeance"]);
            $arrChampFini[] = "item";


        }

        $objStat->execute();
        header("Location: afficher.php?id_liste=" . $strIdListe);
        exit();
    }
}


?>