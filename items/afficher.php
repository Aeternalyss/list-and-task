Afficher les items!
<?php $niveau="../";
include($niveau . "liaisons/inc/config.inc.php");
include($niveau . "liaisons/inc/fragments/requeteSQL.php");

?>

<a href="<?php echo $niveau;?>index.php">Retour</a>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="keyword" content="">
	<meta name="author" content="">
	<meta charset="utf-8">
	<title>Un beau titre ici!</title>
	<?php include ($niveau . "liaisons/inc/fragments/head_links.inc.php");?>
</head>

<body>

	<?php include ($niveau . "liaisons/inc/fragments/entete.inc.php");?>


	<main>
		<div id="contenu" class="conteneur">
			<h2> <?php echo $arrListes["nom"]." : ".$arrListe["hexadecimal"] ;?></h2>
 
            <?php
            
            foreach($arrItems as $item){ ?>
            <form action="afficher.php" method="GET">
            <input type="hidden" name="id_liste" value="<?php echo $strIdListe; ?>"> 
            <ul>              
                <li><?php echo $item['nom']; ?> </li> 
                <li><?php echo $item['est_complete']; ?> </li> 
                <li><?php echo $item['echeance']; ?> </li> 
                </ul>

                    <button type="submit" name="btn_supprimer" value="<?php echo $item['id']; ?>">
                        Supprimer
                    </button>
                <?php if($item['est_complete'] == 0){ ?>
                    <input type="hidden" name="id_itemComplete" value="<?php echo $item['id']; ?>">
                    <button type="submit" name="btn_complete" value="<?php echo $item['est_complete']; ?>">
                        À compléter
                    </button>
                <?php }else{ ?>
                   <input type="hidden" name="id_itemComplete" value="<?php echo $item['id']; ?>">
                    <button type="submit" name="btn_complete" value="<?php echo $item['est_complete']; ?>">
                        Compléter
                    </button>
                <?php } ?>

                    <a href="modifier.php?id_item=<?= $item['id']?>&id_liste=<?php echo $strIdListe?>">Éditer</a>
            </form>
            <?php } ?>
            <form action="ajouter.php" method="GET">
                    <button type="submit" name="id_liste" value="<?php echo $strIdListe; ?>">
                        ajouter
                    </button>
            </form>
    </main>    

</body>
</html>