<?php $niveau = "../";
include($niveau . "liaisons/inc/config.inc.php");
include($niveau . "liaisons/inc/fragments/requeteSQL.php");


?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="">
    <meta charset="utf-8">
    <title>Un beau titre ici!</title>
    <?php include($niveau . "liaisons/inc/fragments/head_links.inc.php"); ?>
</head>

<body>

    <?php include($niveau . "liaisons/inc/fragments/entete.inc.php"); ?>


    <main>
        <h2>Édition d'item </h2>

        <form action="modifier.php" method="GET">
            <input type="hidden" name="id_liste" value="<?= $strIdListe ?>">
            <input type="hidden" name="id_item" value="<?= $strIdItem ?>">

            <label for="nom_item">
                <h4>Nom de la l'item' :</h4>
            </label>
            <input style="width:500px" type="text" id="nom_item" name="nom_item" value="<?= $arrItems['nom'] ?>">
            <?php if ($strCodeOperation == "modifier" && !empty($arrMessagesErreur)) { ?>
                <span class="ml-10 text-sm" style="color:red;"><?php echo $arrMessagesErreur["nom_item"] ?></span>
            <?php } ?>

            <a href="afficher.php?id_liste=<?= $strIdListe ?>">annuler l'ajout</a>
            <div class="mt-4 flex flex-col sm:flex-row gap-2 items-center">
                <div class="flex flex-row items-center gap-2">
                    <img src="<?php echo $niveau; ?>/liaisons/images/svg/calendrier.svg" alt="" class="w-4 h-4">
                    <label for="echeance_item">Échéance (optionnel) </label>
                </div>

                <div class="flex flex-col sm:flex-row">
                    <!-- jour -->
                    <select name="jour" id="jour"
                        class="border-2 border-black p-3 rounded-lg  gap-2 bg-white text-black hover:bg-pink-200 focus:outline-red-700">
                        <option value="0">Jour</option>
                        <?php
                        for ($cpt = 1; $cpt <= 31; $cpt++) { ?>
                            <option value="<?php echo $cpt; ?>" <?php if ($jour == $cpt) {
                                   echo 'selected="selected"';
                               } ?>>
                                <?php echo $cpt; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <!-- mois -->
                    <select name="mois" id="mois"
                        class="border-2 border-black p-3 rounded-lg  gap-2 bg-white text-black hover:bg-pink-200 focus:outline-red-700">
                        <option value="0">Mois</option>
                        <?php

                        for ($cpt = 0; $cpt < 12; $cpt++) { ?>
                            <option value="<?php echo $cpt + 1; ?>" <?php if ($mois == $cpt + 1) {
                                     echo 'selected="selected"';
                                 } ?>>
                                <?php echo $arrMois[$cpt]; ?>
                            </option>
                        <?php } ?>

                    </select>
                    <!-- annee -->
                    <select name="annee" id="annee"
                        class=" border-2 border-black p-3 rounded-lg  gap-2 bg-white text-black hover:bg-pink-200 focus:outline-red-700">
                        <option value="0">Année</option>
                        <?php
                        for ($cpt = $StrAnnee; $cpt >= $StrAnnee - 25; $cpt--) {
                            ?>
                            <option value="<?php echo $cpt; ?>" <?php if ($StrAnneeActuelle  == $cpt) {
                                   echo 'selected="selected"';
                               } ?>>
                                <?php echo $cpt; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                
            </div>
            <?php if ($strCodeOperation == "modifier" && !empty($arrMessagesErreur)) { ?>
                <span class="ml-10 text-sm" style="color:red;"><?php echo $arrMessagesErreur["echeance"] ?></span>
            <?php } ?>
            </div>
            <div class="flex flex-col sm:flex-row gap-4  mt-6 w-full items-center justify-center">
                <a href="afficher.php?id_liste=<?= $strIdListe ?> #contenu">
                    Annuler la modification
                </a>

                <button type="submit" name="btn_modifier">
                    Modifier la tâche
                </button>

            </div>
        </form>