<?php
session_start();

require_once('../moteur/dbconfig.php');
require_once('../core/session.php');
require_once('../core/requetes.php');

// Vérification des autorisations de l'utilisateur et des variables de session requises pour l'affichage de cette page:
if (isset($_SESSION['id'])
  && $_SESSION['systeme'] === "oressource"
  && is_allowed_bilan()) {

  // On convertit les deux dates en un format compatible avec la bdd
  $date1ft = date_create_from_format('d-m-Y', $_GET['date1']);
  $time_debut = $date1ft->format('Y-m-d');
  $time_debut = $time_debut . " 00:00:00";
  $date1 = $date1ft->format('d-m-Y');

  $date2ft = date_create_from_format('d-m-Y', $_GET['date2']);
  $time_fin = $date2ft->format('Y-m-d');
  $time_fin = $time_fin . " 23:59:59";
  $date2 = $date2ft->format('d-m-Y');

  $date_query = "date1=$date1&date2=$date2";

  $numero = filter_input(INPUT_GET, 'numero', FILTER_VALIDATE_INT);

  $bilans = [];
  $bilans_types = [];
  $bilans_pesees_types = [];
  $nb_ventes = 0;
  $remb_nb = 0;
  $chiffre_affaire = [];

  if ($numero === 0) {
    $bilans = bilan_ventes($bdd, $time_debut, $time_fin);
    $bilans_types = bilan_ventes_par_type($bdd, $time_debut, $time_fin);
    $bilans_pesees_types = bilan_ventes_pesees($bdd, $time_debut, $time_fin);
    $chiffre_affaire = chiffre_affaire_par_mode_paiement($bdd, $time_debut, $time_fin);
    $nb_ventes = nb_ventes($bdd, $time_debut, $time_fin);
    $remb_nb = nb_remboursements($bdd, $time_debut, $time_fin);
  } else {

    $bilans = bilan_ventes_point_vente($bdd, $date1, $date2, $numero);
    $bilans_types = bilan_ventes_par_type_point_vente(
      $bdd, $date1, $date2, $numero);
    $bilans_pesees_types = bilan_ventes_pesees_point_vente($bdd, $date1, $date2, $numero);
    $chiffre_affaire = chiffre_affaire_mode_paiement_point_vente($bdd, $date1, $date2, $numero);
    $nb_ventes = nb_ventes_point_vente($bdd, $time_debut, $time_fin, $numero);
    $remb_nb = nb_remboursements_point_vente($bdd, $time_debut, $time_fin, $numero);
  }

  $bilan_pesee_mix = array_reduce(array_keys($bilans_pesees_types), function ($acc, $e)
    use ($bilans_pesees_types, $bilans_types) {
    if (isset($bilans_types[$e])) {
      $acc[$e] = array_merge($bilans_types[$e], $bilans_pesees_types[$e]);
      return $acc;
    } else {
      return $acc;
    }
  }, []);


  $graphMv = data_graphs_from_bilan($bilans_pesees_types, 'vendu_masse');
  $graphPv = data_graphs_from_bilan($bilans_types, 'chiffre_degage');
  ?>

  <!DOCTYPE html>
  <html>
    <head>
      <meta charset="utf-8">
      <link href="../css/bootstrap.min.css" rel="stylesheet">
      <link href="../fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
      <link rel="stylesheet" type="text/css" media="all" href="../css/daterangepicker-bs3.css" />
    </head>

    <div class="container">
      <div class="row">
        <div class="col-md-11" >
          <h1>Bilan global</h1>
          <div class="col-md-4 col-md-offset-8" >
            <label for="reportrange">Choisissez la période à inspecter:</label><br>
            <div id="reportrange" class="pull-left"
                 style="background: #fff; cursor: pointer;
                 padding: 5px 10px; border: 1px solid #ccc">
              <i class="fa fa-calendar"></i>
              <span></span> <b class="caret"></b>
            </div>
          </div>

          <ul class="nav nav-tabs">
            <li>
              <a href="bilanc.php?<?= $date_query ?>&numero=0">Collectes</a>
            </li>
            <li>
              <a href="bilanhb.php?<?= $date_query ?>&numero=0">Sorties hors-boutique</a>
            </li>
            <li class="active"><a href="#">Ventes</a></li>
          </ul>
        </div>
      </div> <!-- row -->
    </div> <!-- container -->

    <hr/>
    <div class="row">
      <div class="col-md-8 col-md-offset-1" >
        <h2>Bilan des ventes de la structure</h2>
        <ul class="nav nav-tabs">
          <?php foreach (points_ventes($bdd) as $point_vente) { ?>
            <li class="<?= ($numero === $point_vente['id'] ? 'active' : '') ?>">
              <a href="bilanv.php?<?= $date_query ?>&numero=<?= $point_vente['id'] ?>"><?= $point_vente['nom'] ?></a>
            </li>
          <?php } ?>
          <li class="<?= ($numero === 0 ? 'active' : '') ?>">
            <a href="bilanv.php?<?= $date_query ?>&numero=0">Tous les points</a>
          </li>
        </ul>

        <div class="row">
          <h2><?= ($date1 === $date2) ? "Le $date1" : "Du $date1 au $date2" ?> :</h2>
          <?php if (!($bilans['chiffre_degage'] > 0)) { ?>
            <img src="../images/nodata.jpg" class="img-responsive" alt="Responsive image">
          <?php } else { ?>
            <div class="row">
              <div class="col-md-6">
                <table class='table table-hover'>
                  <tbody>
                    <?php if ($numero === 0) { ?>
                      <tr>
                        <td>- Nombre de points de vente :</td>
                        <td><?= nb_points_ventes($bdd) ?></td>
                      </tr>
                    <?php } ?>
                    <tr>
                      <td>- Chiffre total dégagé  :</td>
                      <td><?= $bilans['chiffre_degage'] ?> €</td>
                    </tr>
                    <tr>
                      <td>- Nombre d'objets vendus :</td>
                      <td><?= $bilans['vendu_quantite'] ?></td>
                    </tr>
                    <tr>
                      <td>- Nombre de ventes :</td>
                      <td><?= $nb_ventes ?></td>
                    </tr>
                    <tr>
                      <td>- Panier moyen :</td>
                      <td><?= $bilans['chiffre_degage'] / $nb_ventes ?> €</td>
                    </tr>
                    <tr>
                      <td>- Nombre d'objets remboursés :</td>
                      <td><?= $bilans['remb_quantite'] ?>
                      </td>
                    </tr>
                    <tr>
                      <td>- Nombre de remboursemments :</td>
                      <td><?= $remb_nb ?></td>
                    </tr>
                    <tr>
                      <td>- Somme remboursée :</td>
                      <td><?= $bilans['remb_somme'] ?> €</td>
                    </tr>
                    <tr>
                      <td>- Masse pesée en caisse :</td>
                      <td><?= $bilans['vendu_masse'] ?> Kgs</td>
                    </tr>
                  </tbody>

                  <tfoot>
                    <tr>
                      <td align=center colspan=3>
                        <a href="../moteur/export_bilanv.php?numero=<?= $numero ?>&<?= $date_query ?>">
                          <button type="button" class="btn btn-default btn-xs">Exporter les ventes de cette période (.csv)</button>
                        </a>
                      </td>
                    </tr>
                  </tfoot>
                </table>

                <h3>Récapitulatif par mode de paiement</h3>

                <table class='table table-hover'>
                  <thead>
                    <tr>
                      <th>Moyen de Paiement</th>
                      <th>Nombre de Ventes</th>
                      <th>Chiffre Dégagé</th>
                      <th>Somme remboursée</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php foreach ($chiffre_affaire as $ligne) { ?>
                      <tr>
                        <td><?= $ligne['moyen'] ?></td>
                        <td><?= $ligne['quantite_vendue'] ?></td>
                        <td><?= $ligne['total'] ?> €</td>
                        <td><?= $ligne['remboursement'] ?> €</td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <h4>Chiffre dégagé par type d'objet: </h4>
                <div id="graphPV" style="height: 180px;"></div>
              </div>

              <div class="col-md-6 ">
                <h3 style="text-align:center;">Chiffre de caisse : <?=
                  $bilans['chiffre_degage'] - $bilans['remb_somme']
                  ?> €</h3>
                <h4>=Récapitulatif par type d'objet=</h4>
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>type d'objet</th>
                      <th>chiffre dégagé</th>
                      <th>quantité vendue</th>
                      <th>somme remboursée</th>
                      <th>quantité rembour.</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php foreach ($bilans_types as $id => $bilan_type) { ?>
                      <tr>
                        <th scope="row">
                          <a href="./jours.php?<?= $date_query ?>&type=<?= $id ?>"><?= $bilan_type['nom'] ?></a>
                        </th>
                        <td><?= $bilan_type['chiffre_degage'] ?> €</td>
                        <td><?= $bilan_type['vendu_quantite'] ?></td>
                        <td><?= $bilan_type['remb_somme'] ?> €</td>
                        <td><?= $bilan_type['remb_quantite'] ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>

                <h3>Récapitulatif des masses pesées à la caisse</h3>

                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>type d'objet</th>
                      <th>masse pésee</th>
                      <th>nombre de pesées</th>
                      <th>nombre d'objets pesés</th>
                      <th>nombre d'objets vendus</th>
                      <th>masse sortie totale estimée</th>
                      <th>prix à la tonne estimé</th>
                      <th>certitude de l'estimation</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    // TODO: Mettre des noms de variables explicites.
                    foreach ($bilan_pesee_mix as $id => $bilan_mix) {
                      $chiffre_degage = $bilan_mix['chiffre_degage'];
                      $id_type_dechet = $id;
                      $vendus_pesses = $bilan_mix['quantite_pesee_vendu'];
                      $Mtpe = (double) $bilan_mix['vendu_masse'];
                      $Ntpe = (int) $bilan_mix['nb_pesees_ventes'];
                      $Notpe = (int) $bilan_mix['quantite_pesee_vendu'];
                      $obj_vendu = (int) $bilan_mix['vendu_quantite'];
                      $moy_masse_vente = (double) $bilan_mix['moy_masse_vente'];

                      /*
                        echo "toto".$Mm."toto";
                        estimation de la masse totale vendue sur la periode pour tout les points de vente
                        masse moyenne d'un objet dans toute la base (pour le type d'objet en cours) = $Mm
                        nombre d'objets vendus (tout types confondus) = $Nt
                        nombre d'objets pesées sur la periode = $Np
                        masse totale d'objets peses sur cette periode = $Mtpe
                        nombre de pesées sur la periode pour le type d'objet = $Ntpe
                        nombre d'objets pesés sur la periode pour le type d'objet = $Notpe
                        nombre d'objets vendus sur la periode pour le type d'objet = $ov

                        if($ov == $Notpe) {
                        $mtee = $Mtpe;
                        $certitude = 100;
                        } else {
                        $mtee = (($Mm*$ov)-($Mm*$Np))+$Mtpe;
                        $certitude = 0;
                        }

                        $mtee = round((($Mm*$Nt)-($Mm*$Mp))+$Mtpe, 2);
                       */

                      // TODO faire une fonction.
                      if ($obj_vendu == $Notpe) {
                        $mtee = $Mtpe;
                        $certitude = 100;
                      } else {
                        $masse_vente_moyenne_totale = $moy_masse_vente * $obj_vendu;
                        $masse_pesees_vendu_esp = $moy_masse_vente * $Mtpe;
                        $prix_tonne_estime = ($masse_vente_moyenne_totale - $masse_pesees_vendu_esp) +
                          $Mtpe;
                        $certitude = round(($Notpe / $obj_vendu) * 100, 2);
                      }

                      //on traduit le pourcentage en valeur de vert 100% = tout vert
                      $Gvalue = round($certitude * 2.55, 0);
                      //on traduit le pourcentage en valeur de rouge 0% = tout rouge
                      $Rvalue = round(255 - $Gvalue, 0);
                      ?>
                      <tr>
                        <th scope="row">
                          <a href="./jours.php?date1=<?= $date_query ?>&type=<?= $id ?>"><?= $bilan_mix['nom'] ?></a>
                        </th>
                        <td><?= round($Mtpe, 2) ?> Kgs.</td>
                        <td><?= round($Ntpe, 2) ?></td>
                        <td><?= $Notpe ?></td>
                        <td><?= $obj_vendu ?></td>
                        <td><?= round($prix_tonne_estime, 2) ?> Kgs</td>
                        <td><?= round(($chiffre_degage / $prix_tonne_estime) * 1000, 2); ?> €</td>
                        <td>
                          <span class='badge'
                                id='Bcertitude'
                                style='background-color: RGB(<?= $Rvalue ?>,<?= $Gvalue ?>,0);'
                                ><?= $certitude ?> %</span>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>

                <h4>Masses pesées en caisse par type d'objet :</h4>

                <div id="graphMV" style="height: 180px;"></div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <script src="../js/raphael.js"></script>
    <script src="../js/morris/morris.js"></script>
    <script type="text/javascript" src="../js/moment.js"></script>
    <script type="text/javascript" src="../js/daterangepicker.js"></script>
    <script type="text/javascript">
      'use strict';
      function process_get() {
        let val = {};
        const query = new URLSearchParams(window.location.search.slice(1)).entries();
        for (const pair of query) {
          val[pair[0]] = pair[1];
        }
        return val;
      }

      function cb(start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
        $('#reportrange span').html(`${start.format('DD, MMMM, YYYY')} - ${end.format('DD, MMMM, YYYY')}`);
      }

      const get = process_get();
      const startDate = moment(get.date1, 'DD-MM-YYYY');
      const endDate = moment(get.date1, 'DD-MM-YYYY');

      const now = moment();
      const optionSet1 = {
        startDate: startDate.format('DD/MM/YYYY'),
        endDate: endDate.format('DD/MM/YYYY'),
        minDate: '01/01/2010',
        maxDate: '12/31/2020',
        dateLimit: {days: 800},
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
          "Aujoud'hui": [now, now],
          'hier': [now.subtract(1, 'days'), now.subtract(1, 'days')],
          '7 derniers jours': [now.subtract(6, 'days'), now],
          '30 derniers jours': [now.subtract(29, 'days'), now],
          'Ce mois': [
            now.startOf('month'),
            now.endOf('month')
          ],
          'Le mois deriner': [
            now.subtract(1, 'month').startOf('month'),
            now.subtract(1, 'month').endOf('month')
          ]
        },
        opens: 'left',
        buttonClasses: ['btn btn-default'],
        applyClass: 'btn-small btn-primary',
        cancelClass: 'btn-small',
        format: 'DD/MM/YYYY',
        separator: ' to ',
        locale: {
          applyLabel: 'Appliquer',
          cancelLabel: 'Anuler',
          fromLabel: 'Du',
          toLabel: 'Au',
          customRangeLabel: 'Période libre',
          daysOfWeek: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
          monthNames: ['Janvier', 'Fevrier', 'Mars'
                    , 'Avril', 'Mai', 'Juin'
                    , 'Juillet', 'Aout', 'Septembre'
                    , 'Octobre', 'Novembre', 'Decembre'],
          firstDay: 1
        }
      };

      $(document).ready(() => {
        {
          const picker_element = $('#reportrange');
          picker_element.daterangepicker(optionSet1, cb);
          $('#reportrange span').html(`${startDate.format('DD/MM/YYYY')} - ${endDate.format('DD/MM/YYYY')}`);

          picker_element.on('show.daterangepicker', () => {
            console.log("show event fired");
          });

          picker_element.on('hide.daterangepicker', () => {
            console.log("hide event fired");
          });

          picker_element.on('apply.daterangepicker',
                  (ev, picker) => {
            console.log(`apply event fired, start/end dates are ${picker.startDate.format('DD MM, YYYY')} to ${picker.endDate.format('DD MM, YYYY')}`);
            const start = picker.startDate.format('DD-MM-YYYY');
            const end = picker.endDate.format('DD-MM-YYYY');
            window.location.href = `./bilanv.php?date1=${start}&date2=${end}&numero=${get.numero}`;
          });

          picker_element.on('cancel.daterangepicker', (ev, picker) => {
            console.log("cancel event fired");
          });

          $('#options1').click(() => {
            picker_element.data('daterangepicker').setOptions(optionSet1, cb);
          });

          $('#options2').click(() => {
            picker_element.data('daterangepicker').setOptions(optionSet2, cb);
          });

          $('#destroy').click(() => {
            picker_element.data('daterangepicker').remove();
          });
        }

        try {
          const dataMv = <?= json_encode($graphMv, JSON_NUMERIC_CHECK) ?>;
          Morris.Donut({
            element: 'graphMV',
            data: dataMv.data,
            backgroundColor: '#ccc',
            labelColor: '#060',
            colors: dataMv.colors,
            formatter: (x) => {
              return `${x} Kgs.`;
            }
          });
        } catch (e) {
          console.error(e);
        }

        try {
          const dataPv = <?= json_encode($graphPv, JSON_NUMERIC_CHECK) ?>;
          Morris.Donut({
            element: 'graphPV',
            data: dataPv.data,
            backgroundColor: '#ccc',
            labelColor: '#060',
            colors: dataPv.colors,
            formatter: (x) => {
              return `${x}  €.`;
            }
          });
        } catch (e) {
          console.error(e);
        }
      });
    </script>

    <?php
    include "pied_bilan.php";
  } else {
    header('Location: ../moteur/destroy.php');
  }
