<?php
/*
  Oressource
  Copyright (C) 2014-2017  Martin Vert and Oressource devellopers

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();
require_once('../moteur/dbconfig.php');

//Vérification des autorisations de l'utilisateur et des variables de session requisent pour l'affichage de cette page:
if (isset($_SESSION['id']) && $_SESSION['systeme'] === 'oressource' && (strpos($_SESSION['niveau'], 'l') !== false)) {
  require_once 'tete.php';
  ?>

  <div class="container">
    <h1>Édition du profil utilisateur n°:<?= $_POST['id']; ?>, <?= $_POST['mail']; ?></h1>
    <br>
    <div class="panel-body">
      <div class="row">
        <form action="../moteur/modification_utilisateur_post.php" method="post">
          <div class="col-md-2"><label for="nom">Nom:</label> <input type="text" value ="<?= $_POST['nom']; ?>" name="nom" id="nom" class="form-control " required autofocus><br>
            <label for="prenom">Prénom:</label> <input type="text" value ="<?= $_POST['prenom']; ?>" name="prenom" id="prenom" class="form-control " required><br>
            <label for="mail">Mail:</label> <input type="email" value ="<?= $_POST['mail']; ?>" name="mail" id="mail" class="form-control" required><br>
            <a href="edition_mdp_admin.php?id=<?= $_POST['id']; ?>&mail=<?= $_POST['mail']; ?>">
              <button name="creer" type="button" class="btn btn btn-danger">Changer le mot de passe</button>
            </a>

          </div>
          <div class="col-md-4"><div class="alert alert-info"><label>Permissions d'accès</label> <br>

              <input type="checkbox" name="niveaubi" id="niveaubi" value="bi"<?php
              if ((strpos($_POST['niveau'], 'bi') !== false)) {
                echo 'checked';
              }
              ?>> <label for="niveaubi">Bilans</label><br>

              <input type="checkbox" name="niveaug" id="niveaug" value="g"<?php
              if ((strpos($_POST['niveau'], 'g') !== false)) {
                echo 'checked';
              }
              ?>> <label for="niveaug">Gestion quotidienne</label><br>
              <input type="checkbox" name="niveauh" id="niveauh" value="h"<?php
              if ((strpos($_POST['niveau'], 'h') !== false)) {
                echo 'checked';
              }
              ?>> <label for="niveauh">Verif. formulaires</label><br>
              <input type="checkbox" name="niveaul" id="niveaul" value="l"<?php
              if ((strpos($_POST['niveau'], 'l') !== false)) {
                echo 'checked';
              }
              ?>> <label for="niveaul">Utilisateurs</label><br>
              <input type="checkbox" name="niveauj" id="niveauj" value="j"<?php
              if ((strpos($_POST['niveau'], 'j') !== false)) {
                echo 'checked';
              }
              ?>> <label for="niveauj">Recycleurs et convention partenaires</label><br>
              <input type="checkbox" name="niveauk" id="niveauk" value="k"<?php
              if ((strpos($_POST['niveau'], 'k') !== false)) {
                echo 'checked';
              }
              ?>> <label for="niveauk">Configuration de Oressource</label><br>
                     <?php if ($_SESSION['saisiec'] === 'oui') { ?>
                <input type="checkbox" name="niveaue" id="niveaue" value="e"<?php
                if ((strpos($_POST['niveau'], 'e') !== false)) {
                  echo 'checked';
                }
                ?>> <label for="niveaue">Saisir la date dans les formulaires</label><br>
                     <?php } ?>

            </div>

            <input type="hidden" name ="id" id="id" value="<?= $_POST['id']; ?>">
          </div>
          <div class="col-md-4"><div class="alert alert-info"><label for="niveauc">Points de collecte:</label><br>
              <?php
              $reponse = $bdd->query('SELECT * FROM points_collecte');

              while ($donnees = $reponse->fetch()) { ?>
                <input type="checkbox" name="niveauc<?= $donnees['id']; ?>" id="niveauc<?= $donnees['id']; ?>" <?php
                if ((strpos($_POST['niveau'], 'c' . $donnees['id']) !== false)) {
                  echo 'checked';
                }
                ?>> <?= '<label for="niveauc' . $donnees['id'] . '">' . $donnees['nom'] . '</label>'; ?> <br><br>
                       <?php
                     }
                     $reponse->closeCursor();
                     ?>
            </div>

            <div class="alert alert-info"><label for="niveauv">Points de vente:</label><br>
              <?php
              $reponse = $bdd->query('SELECT * FROM points_vente');

              while ($donnees = $reponse->fetch()) { ?>
                <input type="checkbox" name="niveauv<?= $donnees['id']; ?>" id="niveauv<?= $donnees['id']; ?>" value="v<?= $donnees['id']; ?>"<?php
                if ((strpos($_POST['niveau'], 'v' . $donnees['id']) !== false)) {
                  echo 'checked';
                }
                ?>> <?= '<label for="niveauv' . $donnees['id'] . '">' . $donnees['nom'] . '</label>'; ?> <br><br>
                       <?php
                     }
                     $reponse->closeCursor();
                     ?></div>
            <div class="alert alert-info"><label for="niveaus">Points de sortie hors-boutique:</label><br>
              <?php
              $reponse = $bdd->query('SELECT * FROM points_sortie');

              while ($donnees = $reponse->fetch()) { ?>
                <input type="checkbox" name="niveaus<?= $donnees['id']; ?>" id="niveaus<?= $donnees['id']; ?>" value="s<?= $donnees['id']; ?>"<?php
                if ((strpos($_POST['niveau'], 's' . $donnees['id']) !== false)) {
                  echo 'checked';
                }
                ?>> <?= '<label for="niveaus' . $donnees['id'] . '">' . $donnees['nom'] . '</label>'; ?> <br><br>

                <?php
              }
              $reponse->closeCursor();
              ?>
            </div></div>
          <div class="col-md-4"><br></div>
      </div>
      <div class="row"><div class="col-md-3 col-md-offset-3"><br><button name="modifier" class="btn btn-warning">MODIFIER!</button></form>
          <a href="edition_utilisateurs.php">
            <button name="creer" class="btn btn">Annuler</button>
          </a>
        </div></div>
    </div>
  </div>

  <?php
  require_once 'pied.php';
} else {
  header('Location: ../moteur/destroy.php');
}
?>
