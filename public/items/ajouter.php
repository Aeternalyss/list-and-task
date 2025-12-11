<?php $niveau="../";
include($niveau . "liaisons/inc/config.inc.php"); 
// Inclusion du fichier erreur json
$strFichierTexte = file_get_contents($niveau.'liaisons/json/message-erreur.json');
$jsonMessageErreur=json_decode($strFichierTexte);

//Liste des champs fautifs
$arrChampErreur=array();
$arrMessagesErreur = [
    "nom_item" => "",
    "echeance"  => ""
];
$arrChampFini=array();
$arrMessagesFini =[
	"item"=>""
];

$arrMois = array ("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
$StrAnnee= date("Y",strtotime("+25 year"));

// récupération Query sting
if(isset($_GET['id_liste'])){
    $strIdListe = $_GET['id_liste'];
}else{
    $strIdListe = '';
    echo "Aucune liste sélectionnée";
}

// récupérer la couleur et nom de la liste en cours
$strRequeteListe = "SELECT listes.nom, couleurs.hexadecimal 
FROM listes
JOIN couleurs ON listes.couleur_id = couleurs.id
WHERE listes.id = $strIdListe
";

$pdosResultat = $pdoConnexion->query($strRequeteListe);
$arrListe = $pdosResultat->fetch();
$pdosResultat->closeCursor();



// établir la requête pour obtenir les items
$strRequete = "SELECT id,liste_id, nom, echeance, est_complete,DAY(echeance) AS jour, MONTH(echeance) AS mois, YEAR(echeance) AS annee FROM items WHERE liste_id = ".$strIdListe;
$pdosResultat = $pdoConnexion->query($strRequete);
$arrItems = array();
for($cptItem=0;$ligneItem=$pdosResultat->fetch();$cptItem++){
    $arrItems[$cptItem]['id']=$ligneItem['id'];
    $arrItems[$cptItem]['liste_id']=$ligneItem['liste_id'];
    $arrItems[$cptItem]['nom']=$ligneItem['nom'];
    $arrItems[$cptItem]['echeance']=$ligneItem['echeance'];
    $arrItems[$cptItem]['est_complete']=$ligneItem['est_complete'];
    $arrItems[$cptItem]['jour']=$ligneItem['jour'];
    $arrItems[$cptItem]['mois']=$ligneItem['mois'];
    $arrItems[$cptItem]['annee']=$ligneItem['annee'];
}
$pdosResultat->closeCursor();

$strCodeOperation ="";
if(isset ($_GET["btn_ajouter"])){
	$strCodeOperation = "ajouter";
}else{
	$strCodeOperation = "";
}


if($strCodeOperation=="ajouter"){
	$arrItems["liste_id"]=$_GET["id_liste"];
	$arrItems["nom"]=$_GET["nom_item"];
	$arrItems["jour"]=$_GET["jour"];
    $arrItems["mois"]=$_GET["mois"];
    $arrItems["annee"]=$_GET["annee"];
	$arrItems["est_complete"]=0;
	$strCodeErreur ="";
	$arrChampErreur=[];

	$intAnnee = intval($_GET['annee']);
    $intMois = intval($_GET['mois']);
    $intJour = intval($_GET['jour']);
	if($intJour == 0 && $intMois == 0 && $intAnnee == 0){
		$arrItems["echeance"]=null;}
		else{
			if(checkdate($intMois, $intJour, $intAnnee)){
				$arrItems["echeance"]=$intAnnee . "-" . $intMois . "-" . $intJour;
			}else{
				$strCodeErreur="-1";
				$arrChampErreur[] = "echeance";
			}
		}
	

	if($arrItems["nom"]=="" || strlen($arrItems["nom"]) > 50){
		$strCodeErreur = "-1";
		$arrChampErreur[] = "nom_item";
	}
	if ($strCodeErreur != "") {
        for($cpt=0;$cpt<count($arrChampErreur);$cpt++){
            $champ=$arrChampErreur[$cpt];
            $arrMessagesErreur[$champ]=$jsonMessageErreur->{$champ}->erreurs->vide;
        }
    } else {
		$strRequeteInsert = "INSERT INTO items (liste_id,nom,echeance,est_complete)"."VALUES (:liste_id, :nom, :echeance,0)";
		$pdosResultat = $pdoConnexion->prepare($strRequeteInsert);
		$pdosResultat->bindparam(':liste_id',$strIdListe);
		$pdosResultat->bindparam(':nom',$arrItems["nom"]);
		$pdosResultat->bindvalue(":echeance", $arrItems["echeance"]);
		$pdosResultat->execute();
		$arrChampFini[]="item";

		$arrMessagesFini["item"]=$jsonMessageErreur->retroactions->item->ajouter;
		header("Location:ajouter.php?id_liste=" . $strIdListe . "&success=1");
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
	<title>Ajouter tâches
    </title>
	<link href="../liaisons/css/styles.css?v=2" rel="stylesheet">
	<link href="../liaisons/fonts/typos.css" rel="stylesheet">
</head>
<body>
	<?php include($niveau.  "liaisons/inc/fragments/entete.inc.php"); ?>
	<main>
		<div class="ml-10 p-3">
    		<a class="text-3xl font-medium text-pink-800 underline" href="afficher.php?id_liste=<?=$strIdListe?> #contenu" >Retour aux tâches</a>
		</div>
		
		<div id="contenu" class=" ml-10 mr-10 border-t border-b border-black" style="background-color:#<?= $arrListe['hexadecimal'] ?>;">
			<h1 class="text-black ml-8">Ajouter une tâche</h1>
		</div>
		<div id="contenu" class=" mt-10 ml-10 mr-10 border-t border-b border-black" style="background-color:#<?= $arrListe['hexadecimal'] ?>;">
			<h2 class="text-black ml-8"> Liste : <?php echo $arrListe['nom'] ?></h2>
		</div>
		<?php
$jour = 0;
$mois = 0;
$annee = 0;
?>
<?php 
if(isset($_GET['success'])){ ?>
<div class="ml-10 mr-10 mt-6 p-4 bg-green-200 border border-green-700 text-green-900 rounded">
	<span><?php echo $jsonMessageErreur->retroactions->item->ajouter ?></span>
</div>
<?php }else{ ?>
	<div class="mx-15 flex  items-center justify-center">
		<form action="ajouter.php?" method="GET">
		<input type="hidden" name="id_liste" value="<?= $strIdListe ?>">

		<h3 class="flex items-center justify-center">Ajouter une tâche</h3>

			<div class="flex flex-col  w-full items-center justify-center">
				<div class="mt-4 flex flex-col sm:flex-row gap-2 items-center">
					<label for="nom_item">Nom de la nouvelle tâche</label>
					<input 
					type="text" 
					id="nom_item" 
					name="nom_item" 
					<?php if($strCodeOperation=="ajouter" ) {?>
					value="<?= $arrItems['nom'] ?>" <?php } ?>
					class="items-start text-left border-2 border-black rounded hover:shadow-[1px_3px_3.5px_rgba(0,0,0,0.25)] hover:bg-pink-200 focus:outline-red-700 focus:outline-2">
				</div>
				<?php if($strCodeOperation == "ajouter" && !empty($arrMessagesErreur)){ ?>
				<span class="ml-10 text-sm" style="color:red;"><?php echo $arrMessagesErreur["nom_item"] ?></span>
				<?php } ?>

				<div class="mt-4 flex flex-col sm:flex-row gap-2 items-center">
					<div class="flex flex-row items-center gap-2">
						<img src="<?php echo $niveau; ?>/liaisons/images/svg/calendrier.svg" alt="" class="w-4 h-4">
						<label for="echeance_item">Échéance (optionnel) </label>
					</div>

						<div class="flex flex-col sm:flex-row">
        					<select name="jour" id="jour" class="border-2 border-black p-3 rounded-lg  gap-2 bg-white text-black hover:bg-pink-200 focus:outline-red-700">
								<option value="0" >Jour</option>
        						<?php 
								for($cpt=1;$cpt<=31;$cpt++){ ?>
        					    <option value="<?php echo $cpt;?>"
        					    <?php if($jour==$cpt){echo 'selected="selected"';}?>>
        					    <?php echo $cpt;?></option>
        						<?php } ?>
        					</select>	
        					<!-- mois -->
        					<select name="mois" id="mois" class="border-2 border-black p-3 rounded-lg  gap-2 bg-white text-black hover:bg-pink-200 focus:outline-red-700">
								<option value="0">Mois</option>
        					<?php 

        					for ($cpt=0;$cpt<12;$cpt++){ ?>
        					    <option value="<?php echo $cpt+1;?>"
        					    <?php if($mois==$cpt+1){echo 'selected="selected"';}?>>
        					    <?php echo $arrMois[$cpt];?></option>
        					<?php } ?>
							
        					</select>  
        					<!-- annee -->
        					<select name="annee" id="annee" class=" border-2 border-black p-3 rounded-lg  gap-2 bg-white text-black hover:bg-pink-200 focus:outline-red-700">
							<option value="0">Année</option>
        					<?php 
        					for($cpt=$StrAnnee;$cpt>=$StrAnnee-26;$cpt--){ 
        					    ?>
        					    <option value="<?php echo $cpt;?>"
        					    <?php if($annee==$cpt){echo 'selected="selected"';}?>>
        					    <?php echo $cpt;?></option>
        					<?php } ?>
        					</select>	
						</div>				
					</div>
					<?php if($strCodeOperation == "ajouter" && !empty($arrMessagesErreur)){ ?>
					<span class="ml-10 text-sm" style="color:red;"><?php echo $arrMessagesErreur["echeance"] ?></span>
					<?php } ?>
				</div>
			<div class="flex flex-col sm:flex-row gap-4  mt-6 w-full items-center justify-center">
				<a 
					href="afficher.php?id_liste=<?=$strIdListe?> #contenu"
					class="border-2 border-black p-3 rounded-lg flex items-center gap-2 text-black hover:bg-pink-200 focus:outline-red-700">
					<img src="../liaisons/images/svg/annuler.svg" alt="">
					Annuler l'ajout
				</a>

				<button 
					type="submit" 
					name="btn_ajouter"
					class=" p-3 rounded-lg flex items-center gap-2 bg-black text-white hover:bg-pink-700 focus:outline-red-700">
					<img src="../liaisons/images/svg/check.svg" alt="">
					Ajouter la tâche
				</button>

			</div>
		</form>
	</div>
<?php }?>

		
	</main>
    <?php include($niveau . "liaisons/inc/fragments/pied_de_page.inc.php"); ?>
</body>
</html>
