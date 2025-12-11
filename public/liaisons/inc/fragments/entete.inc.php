<?php 
if($strIdListe!=''||$strIdItem!=''){
$niveau="../";
}else{
$niveau="./";
}

include($niveau . "liaisons/inc/config.inc.php");
include($niveau . "liaisons/inc/fragments/requeteSQL.php");
?>
<header class="entete">
    <a class="entete__titre" href="<?php echo $niveau;?>index.php"><h1>N'oublie pas le tofu va voir tes tâches</h1></a>
</header>  
