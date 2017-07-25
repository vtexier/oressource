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
if (isset($_SESSION['id']) && $_SESSION['systeme'] === 'oressource') {

  //on obtien le pass en db (md5) dans $bddpass

  try {
    include('../moteur/dbconfig.php');
  } catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
  }


  // SELECT SUM(masse),timestamp FROM pesees_collectes WHERE  `timestamp`BETWEEN '2014-09-18 00:00:00' AND '2014-09-24 23:59:59'
  $req = $bdd->prepare('SELECT pass FROM utilisateurs WHERE  id = :id ');
  $req->execute(['id' => $_SESSION['id']]);
  $donnees = $req->fetch();

  $bddpass = $donnees['pass'];

  $req->closeCursor();
  // si le mot de passe actuel == le nouveau mot de passe == la verif , message fun
  if ($_POST['pass1'] === $_POST['pass2'] && $_POST['pass2'] === $_POST['passold']) {
    header('Location: ../ifaces/edition_mdp_utilisateur.php?msg=' . $_SESSION['nom'] . ', vous venez de tenter de modifier votre mot de passe par le même mot de passe, à quoi bon? Par faute de sens dans cette operation administrative, oressource ne procedera à aucun changement.');
  } else {

    // si passold = pass en bdd

    if (md5($_POST['passold']) === $bddpass) {

      // et si pass = pass1
      if ($_POST['pass1'] === $_POST['pass2']) {

        // inscription du nouveau mot de passe en bdd
        try {
          include('dbconfig.php');
        } catch (Exception $e) {
          die('Erreur : ' . $e->getMessage());
        }

        $req = $bdd->prepare('UPDATE utilisateurs SET pass = :pass WHERE id = :id');
        $req->execute(['pass' => md5($_POST['pass1']), 'id' => $_SESSION['id']]);

        $req->closeCursor();

        header('Location: ../ifaces/edition_mdp_utilisateur.php?msg=Mot de passe modifié avec succes, utilisateur: ' . $_SESSION['nom'] . ' mail: ' . $_SESSION['mail']);
      } else {
        header('Location: ../ifaces/edition_mdp_utilisateur.php?err=Veuillez inscrire deux mots de passe semblables');
      }
    } else {
      header('Location: ../ifaces/edition_mdp_utilisateur.php?err=Mauvais mot de passe actuel');
    }
  }
} else {
  header('Location:../moteur/destroy.php');
}
