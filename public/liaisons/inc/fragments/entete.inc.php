<?php include($niveau . "liaisons/inc/config.inc.php"); ?>

<?php

	
$strRequete = '
SELECT listes.id, listes.nom, couleurs.hexadecimal,
       COUNT(items.id) AS nb_items
FROM listes
JOIN couleurs ON listes.couleur_id = couleurs.id
LEFT JOIN items ON items.liste_id = listes.id
GROUP BY listes.id, listes.nom, couleurs.hexadecimal
ORDER BY listes.nom
';

$pdosResultat = $pdoConnexion->query($strRequete);

$arrListes = [];
while ($ligne = $pdosResultat->fetch()) {
    $arrListes[] = $ligne;
}
$pdosResultat->closeCursor();


?>



<header class="bg-pink-200 text-black text-2xl p-4">
  <section class="px-4 md:px-10 py-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

      <!-- LOGO -->
      <div class="flex justify-center sm:justify-start">
        <a href="<?php echo $niveau ?>index.php">
          <img class="h-40 w-70" src="<?php echo $niveau ?>liaisons/images/png/logo.png" alt="logo">
        </a>
      </div>

      <!-- INFOS + BOUTON MENU -->
      <div class="flex items-center justify-between w-full sm:w-auto">
        <!-- Icône + texte + notif -->
        <div class="flex items-center space-x-4">
          <img class="w-12 h-12" src="<?php echo $niveau ?>liaisons/images/svg/utilisateur.svg" alt="connexion">
          <div class="text-right leading-tight">
            Bonjour, Ariane Bouthillette<br>
            <a class="underline text-red-700">Déconnexion</a>
          </div>
          <img class="w-12 h-12" src="<?php echo $niveau ?>liaisons/images/svg/notification.svg" alt="notification">
        </div>

        <!-- Bouton menu (toujours à droite) -->
        <button id="toggleMenu" class="ml-4 p-2 bg-pink-600 text-white rounded-full shrink-0">
          Menu
        </button>
      </div>

    </div>
  </section>

  <!-- Sidebar -->
  <div id="sidebar" class="fixed top-0 right-0 h-full w-80 bg-pink-300 text-black transform translate-x-full transition-transform duration-300 z-50">
    <div class="p-4 relative">
      <button id="closeMenu" class="absolute top-4 right-4 text-black text-3xl leading-none">&times;</button>
      <h2 class="text-xl font-bold mb-4">Menu</h2>
      <!-- contenu du menu -->
       <ul class="space-y-3"> 
        <a href="<?php echo $niveau?>index.php"> 
            <p class="underline">Vos listes:</p> 
        </a> <?php foreach ($arrListes as $liste): ?> 
            <a href="<?php echo $niveau?>items/afficher.php?id_liste=<?= $liste['id'] ?>" class="block border border-black p-3 py-3 text-xl mb-2 rounded" style="background-color:#<?= $liste['hexadecimal'] ?>;"> <?= htmlspecialchars($liste['nom']) ?> (<?= $liste['nb_items'] ?>) 
        </a> <?php endforeach; ?> </ul>
    </div>
  </div>
</header>

<script>
  // JS minimal pour ouvrir/fermer le menu
  document.getElementById('toggleMenu').addEventListener('click', function() {
    document.getElementById('sidebar').classList.remove('translate-x-full');
  });
  document.getElementById('closeMenu').addEventListener('click', function() {
    document.getElementById('sidebar').classList.add('translate-x-full');
  });
</script>
