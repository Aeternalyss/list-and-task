<?php $niveau = "../";
include($niveau . "liaisons/inc/config.inc.php");
$strFichierTexte = file_get_contents($niveau . 'liaisons/json/message-erreur.json');
$jsonMessageErreur = json_decode($strFichierTexte);

//Liste des champs fautifs
$arrChampErreur = array();
$arrMessagesErreur = [
	"nom_item" => "",
	"echeance" => ""
];
$arrChampFini = array();
$arrMessagesFini = [
	"item" => ""
];

if (isset($_GET['id_liste'])) {
	$strIdListe = $_GET['id_liste'];
} else {
	$strIdListe = '';
	echo "Aucune liste sélectionnée";
}
if (isset($_GET['id_item'])) {
	$strIdItem = $_GET['id_item'];
} else {
	$strIdItem = '';
	echo "Aucune item sélectionnée";
}

$arrMois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
$StrAnnee = date("Y", strtotime("+25 year"));


// établir la requête pour obtenir les items
$strRequete = "SELECT id,liste_id, nom, echeance, est_complete,DAY(echeance) AS jour, MONTH(echeance) AS mois, YEAR(echeance) AS annee FROM items WHERE id = :id_item";

$pdosResultat = $pdoConnexion->prepare($strRequete);
$pdosResultat->bindParam(':id_item', $strIdItem);
$pdosResultat->execute();
$item = $pdosResultat->fetch();
$pdosResultat->closeCursor();
if ($item['echeance'] == null) {
	$jour = 0;
	$mois = 0;
	$StrAnneeActuelle = 0;
} else {
	$jour = $item['jour'];
	$mois = $item['mois'];
	$StrAnneeActuelle = $item['annee'];

}

$strCodeOperation = "";
if (isset($_GET["btn_modifier"])) {
	$strCodeOperation = "modifier";
} else {
	$strCodeOperation = "";
}

if ($strCodeOperation == "modifier") {
	$items["nom"] = $_GET["nom_item"];
	$items["jour"] = $_GET["jour"];
	$items["mois"] = $_GET["mois"];
	$items["annee"] = $_GET["annee"];
	$items["est_complete"] = $_GET["est_complete"];
	$strCodeErreur = "";
	$arrChampErreur = [];

	$intAnnee = intval($_GET['annee']);
	$intMois = intval($_GET['mois']);
	$intJour = intval($_GET['jour']);

	if (isset($_GET['reset_echeance']) || $intJour == 0 && $intMois == 0 && $intAnnee == 0) {
		$items["echeance"] = null;
		$jour = 0;
		$mois = 0;
		$annee = 0;
	} else {
		$intAnnee = intval($_GET['annee']);
		$intMois = intval($_GET['mois']);
		$intJour = intval($_GET['jour']);

		if (checkdate($intMois, $intJour, $intAnnee)) {
			$items["echeance"] = $intAnnee . "-" . $intMois . "-" . $intJour;
		} else {
			$strCodeErreur = "-1";
			$arrChampErreur[] = "echeance";
		}

	}


	if ($items["nom"] == "" || strlen($items["nom"]) > 50) {
		$strCodeErreur = "-1";
		$arrChampErreur[] = "nom_item";
	}
	if ($strCodeErreur != "") {
		for ($cpt = 0; $cpt < count($arrChampErreur); $cpt++) {
			$champ = $arrChampErreur[$cpt];
			$arrMessagesErreur[$champ] = $jsonMessageErreur->{$champ}->erreurs->vide;
		}
	} else {
		$strRequeteUpdate = "UPDATE items SET nom=:nom,echeance= :echeance, est_complete=:est_complete WHERE id= :id_item";
		$pdosUpdate = $pdoConnexion->prepare($strRequeteUpdate);
		$pdosUpdate->bindParam(':nom', $_GET['nom_item']);
		$pdosUpdate->bindParam(':id_item', $strIdItem);
		$pdosUpdate->bindvalue(":echeance", $items["echeance"]);
		$pdosUpdate->bindParam(':est_complete', $items["est_complete"]);
		$pdosUpdate->execute();
		$pdosUpdate->closeCursor();
		header("Location:modifier.php?id_liste=" . $strIdListe . "&id_item=" . $strIdItem . "&success=1");
		exit();
	}
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
	<title>Modifier tâches
	</title>
	<link href="../liaisons/css/styles.css?v=2" rel="stylesheet">
	<link href="../liaisons/fonts/typos.css" rel="stylesheet">
</head>

<body>
	<?php include($niveau . "liaisons/inc/fragments/entete.inc.php"); ?>
	<main>
		<div class="ml-10 p-3">
			<a class="text-3xl font-medium text-pink-800 underline"
				href="afficher.php?id_liste=<?= $strIdListe ?> #contenu">Retour aux tâches</a>
		</div>
		<div id="contenu" class=" ml-10 mr-10 border-t border-b border-black bg-pink-200">
			<h1 class="text-black ml-8">Modification de la tâche : <?= $item['nom'] ?></h1>
		</div>
		<?php
		if (isset($_GET['success'])) { ?>
			<div class="ml-10 mr-10 mt-6 p-4 bg-green-200 border border-green-700 text-green-900 rounded">
				<span><?php echo $jsonMessageErreur->retroactions->item->modifier ?></span>
			</div>
		<?php } else { ?>
			<div class="mx-15 flex  items-center justify-center p-10">
				<form action="modifier.php?" method="GET">
					<input type="hidden" name="id_liste" value="<?= $strIdListe ?>">
					<input type="hidden" name="id_item" value="<?= $strIdItem ?>">

					<div class="flex flex-col  w-full items-center justify-center">
						<div class="mt-4 flex flex-col sm:flex-row gap-2 items-center ">
							<label for="nom_item">Nom de la tâche</label>
							<input type="text" id="nom_item" name="nom_item" value="<?= $item['nom'] ?>"
								class=" sm:w-96 items-start text-left border-2 border-black rounded hover:shadow-[1px_3px_3.5px_rgba(0,0,0,0.25)] hover:bg-pink-200 focus:outline-red-700 focus:outline-2">
						</div>
						<?php if ($strCodeOperation == "modifier" && !empty($arrMessagesErreur)) { ?>
							<span class="ml-10 text-sm" style="color:red;"><?php echo $arrMessagesErreur["nom_item"] ?></span>
						<?php } ?>
						<div class="flex flex-row items-center gap-2 mt-5">
							<img src="<?php echo $niveau; ?>/liaisons/images/svg/calendrier.svg" alt="" class="w-4 h-4">
							<label for="echeance_item">Échéance (optionnel) </label>
						</div>
						<div class="mt-4 flex flex-col sm:flex-row gap-2 items-center">


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
									for ($cpt = $StrAnnee; $cpt >= $StrAnnee - 26; $cpt--) {
										?>
										<option value="<?php echo $cpt; ?>" <?php if ($StrAnneeActuelle == $cpt) {
											   echo 'selected="selected"';
										   } ?>>
											<?php echo $cpt; ?>
										</option>
									<?php } ?>
								</select>
							</div>
							<?php
							if ($item['echeance'] != null) { ?>
								<input type="checkbox" id="reset_echeance" name="reset_echeance" value="1"
									class="appearance-none w-4 h-4 bg-white border-2 border-black rounded-sm checked:bg-black checked:border-black focus:outline-red-700 transition-all duration-200 ease-in-out">
								<label for="reset_echeance">Supprimer l'échéance</label>
							<?php } ?>
						</div>
						<?php if ($strCodeOperation == "modifier" && !empty($arrMessagesErreur)) { ?>
							<span class="ml-10 text-sm" style="color:red;"><?php echo $arrMessagesErreur["echeance"] ?></span>
						<?php } ?>
						<!-- tache complete -->
						<div class=" flex flex-col sm:flex-row gap-4 mt-5"> La tâche est-t-elle compléter
							<div class="flex items-center gap-2">
								<label for="est_complete_oui">oui</label>
								<input
									class="appearance-none w-4 h-4 bg-white border-2 border-black rounded-full checked:bg-black checked:border-black focus:outline-red-700 transition-all duration-200 ease-in-out"
									type="radio" name="est_complete" id="est_complete_oui" value="1" <?php if ($item['est_complete'] == 1)
										echo 'checked'; ?>>
							</div>
							<div class="flex items-center gap-2">
								<label for="est_complete_non">non</label>
								<input
									class="appearance-none w-4 h-4 bg-white border-2 border-black rounded-full checked:bg-black checked:border-black focus:outline-red-700 transition-all duration-200 ease-in-out"
									type="radio" name="est_complete" id="est_complete_non" value="0" <?php if ($item['est_complete'] == 0)
										echo 'checked'; ?>>
							</div>
						</div>

					</div>
					<div class="flex flex-col sm:flex-row gap-4  mt-6 w-full items-center justify-center">
						<a href="afficher.php?id_liste=<?= $strIdListe ?> #contenu"
							class="border-2 border-black p-3 rounded-lg flex items-center gap-2 text-black hover:bg-pink-200 focus:outline-red-700">
							<img src="../liaisons/images/svg/annuler.svg" alt="">
							Annuler la modification
						</a>

						<button type="submit" name="btn_modifier"
							class=" p-3 rounded-lg flex items-center gap-2 bg-black text-white hover:bg-pink-700 focus:outline-red-700">
							<img src="../liaisons/images/svg/check.svg" alt="">
							Modifier la tâche
						</button>

					</div>
				</form>
			</div>
		<?php } ?>
	</main>
	<?php include($niveau . "liaisons/inc/fragments/pied_de_page.inc.php"); ?>
</body>

</html>