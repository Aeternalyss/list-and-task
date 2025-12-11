Ajouter un item!
<?php $niveau="../";
include($niveau . "liaisons/inc/config.inc.php");
include($niveau . "liaisons/inc/fragments/requeteSQL.php");


?>
<a href="afficher.php?id_liste=<?=$strIdListe?>">Retour</a>
<div >
		<form action="ajouter.php?" method="GET">
		<input type="hidden" name="id_liste" value="<?= $strIdListe ?>">

		<h3 >Ajouter une tâche</h3>

			<div >
				<div >
					<label for="nom_item">Nom de la nouvelle tâche</label>
					<input 
					type="text" 
					id="nom_item" 
					name="nom_item" 
					<?php if($strCodeOperation=="ajouter" ) {?>
					value="<?= $arrItems['nom'] ?>" 
                    <?php } ?>
					>
				</div>
                		<?php if($strCodeOperation == "ajouter" && !empty($arrMessagesErreur)){ ?>
				<span class="ml-10 text-sm" style="color:red;"><?php echo $arrMessagesErreur["nom_item"] ?></span>
				<?php } ?>
				<div >
					<div >
						<img src="<?php echo $niveau; ?>/liaisons/images/svg/calendrier.svg" alt="" >
						<label for="echeance_item">Échéance (optionnel) </label>
					</div>

						<div >
        					<select name="jour" id="jour" >
								<option value="0" >Jour</option>
        						<?php 
								for($cpt=1;$cpt<=31;$cpt++){ ?>
        					    <option value="<?php echo $cpt;?>"
        					    <?php if($jour==$cpt){echo 'selected="selected"';}?>>
        					    <?php echo $cpt;?></option>
        						<?php } ?>
        					</select>	
        					<!-- mois -->
        					<select name="mois" id="mois" >
								<option value="0">Mois</option>
        					<?php 

        					for ($cpt=0;$cpt<12;$cpt++){ ?>
        					    <option value="<?php echo $cpt+1;?>"
        					    <?php if($mois==$cpt+1){echo 'selected="selected"';}?>>
        					    <?php echo $arrMois[$cpt];?></option>
        					<?php } ?>
							
        					</select>  
        					<!-- annee -->
        					<select name="annee" id="annee" >
							<option value="0">Année</option>
        					<?php 
        					for($cpt=$StrAnnee;$cpt>=$StrAnnee-25;$cpt--){ 
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
			<div >
				<a 
					href="afficher.php?id_liste=<?=$strIdListe?> #contenu">
					Annuler l'ajout
				</a>

				<button 
					type="submit" 
					name="btn_ajouter">
					Ajouter la tâche
				</button>

			</div>
		</form>
	</div>

		
	</main>
    <?php include($niveau . "liaisons/inc/fragments/pied_de_page.inc.php"); ?>
</body>
</html>
