<?php session_start(); 
 include "tete.php";
if (isset($_SESSION['id']) AND $_SESSION['systeme'] = "oressource" AND (strpos($_SESSION['niveau'], 's'.$_GET['numero']) !== false))
      {
//Oressource 2014, formulaire de sorties hors boutique
//Simple formulaire de saisie des matieres d'ouevres sortantes de la structure. (entreprise de recyclage)
//Doit etre fonctionnel avec un ecran tactille.
//Du javascript permet l'interactivité du keypad et des boutons centraux avec le bon de collecte 
//
//
//
//
//
  ?>
<script type="text/javascript">
function number_write(x)
{
  var text_box = document.getElementById("number");
  
   
 text_box.value = text_box.value + x;
  
}

function number_clear()
{
  document.getElementById("number").value = "";
}
function tdechet_add()
{
var liste = document.getElementById("id_filiere")
if (document.getElementById("number").value > 0) {
   


   document.getElementById(liste.value).innerText = parseFloat(document.getElementById(liste.value).innerText) + parseFloat(document.getElementById("number").value)  ;
document.getElementById("number").value = "";  
document.getElementById("id_filiere" ).disabled=true;
}


}
function tdechet_del()
{
var liste = document.getElementById("id_filiere")
if ((document.getElementById("number").value > 0)&&(parseFloat(document.getElementById(liste.value).innerText) > 0))
 {
   


   document.getElementById(liste.value).innerText = parseFloat(document.getElementById(liste.value).innerText) - parseFloat(document.getElementById("number").value)  ;
document.getElementById("number").value = "";  
document.getElementById("id_filiere" ).disabled=true;
}


}
function tdechet_clear()
{
<?php 
          
            try
            {
            // On se connecte à MySQL
            include('../moteur/dbconfig.php');
            }
            catch(Exception $e)
            {
            // En cas d'erreur, on affiche un message et on arrête tout
            die('Erreur : '.$e->getMessage());
            }
 
            // Si tout va bien, on peut continuer
 
            // On recupère tout le contenu de la table point de collecte
            $reponse = $bdd->query('SELECT * FROM type_dechets');
 
           // On affiche chaque entree une à une
           while ($donnees = $reponse->fetch())
           {

           ?>
    document.getElementById('<?php echo$donnees['id']?>').innerText = "0"  ;
<?php }

              $reponse->closeCursor(); // Termine le traitement de la requête
                ?>  
                document.getElementById("id_filiere" ).disabled=false;
}
</script>
<div class="panel-body">
          <fieldset>
       <legend>
        <?php 
          
            try
            {
            // On se connecte à MySQL
            include('../moteur/dbconfig.php');
            }
            catch(Exception $e)
            {
            // En cas d'erreur, on affiche un message et on arrête tout
            die('Erreur : '.$e->getMessage());
            }
 
            // Si tout va bien, on peut continuer
 
            // On recupère tout le contenu de la table point de collecte
          
            $req = $bdd->prepare("SELECT * FROM points_sortie WHERE id = :id ");
            $req->execute(array('id' => $_GET['numero']));
 
           // On affiche chaque entree une à une
           while ($donnees = $req->fetch())
           {

            echo$donnees['nom'];
            
              
            
             
   
               }
              $reponse->closeCursor(); // Termine le traitement de la requête
                ?>




       </legend>
      
        
         
     
        
      </fieldset>   
<div class="row">
    
        <div class="col-md-5 col-md-offset-1" >

 <ul class="nav nav-tabs">
  <li><a href="<?php echo  "sorties.php?numero=" . $_GET['numero']?>">Dons</a></li>
  <li><a href="<?php echo  "sortiesc.php?numero=" . $_GET['numero']?>">Don aux partenaires</a></li>
  <li class="active"><a>Recyclage</a></li>
  <li><a href="<?php echo  "sortiesp.php?numero=" . $_GET['numero']?>">Poubelles</a></li>
</ul>
    <br>  
    <p>Ajuster la masse permet de soustraire la masse des contenant vides une fois vides...</p> 
</div>
</div>          
<div class="row">
	  
        <div class="col-md-3 col-md-offset-1" >
        	
          <form>
         <label for="id_filiere">Nom de l'entrprise de recyclage</label>  
          <select name ="id_filiere" id ="id_filiere" class="form-control " autofocus required>


<?php 
          
            try
            {
            // On se connecte à MySQL
            include('../moteur/dbconfig.php');
            }
            catch(Exception $e)
            {
            // En cas d'erreur, on affiche un message et on arrête tout
            die('Erreur : '.$e->getMessage());
            }
 
            // Si tout va bien, on peut continuer
 
            // On recupère tout le contenu de la table point de collecte
            $reponse = $bdd->query('SELECT * FROM filieres_sortie WHERE visible = "oui"');
 
           // On affiche chaque entree une à une
           while ($donnees = $reponse->fetch())
           {

           ?>

  <option value = "<?php echo$donnees['id_type_dechet']?>" ><?php echo$donnees['nom']?></option>
 

     
              
            
             
   
              <?php }
              $reponse->closeCursor(); // Termine le traitement de la requête
                ?>



        




                    
        
          </select>
        
         
<br>
          
        </div>  
        <div class="col-md-4" >
          
         
         
        </div>
        
      </div>
      <div class="row">
      	<br>
        <div class="col-md-3 col-md-offset-1" >
        
<label>bon de sortie hors boutique:</label>




<?php 
          
            try
            {
            // On se connecte à MySQL
            include('../moteur/dbconfig.php');
            }
            catch(Exception $e)
            {
            // En cas d'erreur, on affiche un message et on arrête tout
            die('Erreur : '.$e->getMessage());
            }
 
            // Si tout va bien, on peut continuer
 
            // On recupère tout le contenu de la table point de collecte
            $reponse = $bdd->query('SELECT * FROM type_dechets WHERE visible = "oui"');
 
           // On affiche chaque entree une à une
           while ($donnees = $reponse->fetch())
           {

           ?>
    
            





<ul class="list-group">
  <li class="list-group-item">
   
    <span class="badge" id="<?php echo$donnees['id']?>"style="background-color:<?php echo$donnees['couleur']?>">0</span>
    <?php echo$donnees['nom']?>

  </li>






   
              <?php }

              $reponse->closeCursor(); // Termine le traitement de la requête
                ?>

</ul>
  <button class="btn btn-default btn-lg">c'est pesé!</button></form>
            <button class="btn btn-default btn-lg" onclick="tdechet_clear();" >reset</button>
           
        <br>

  

        </div> 
         <div class="col-md-1" >
         	<label>Ajustez la masse:</label><br>
           <button class="btn btn-default " onclick="tdechet_add();" ><span class="glyphicon glyphicon-plus"></span></button> 
         	 <button class="btn btn-default " onclick="tdechet_del();" ><span class="glyphicon glyphicon-minus"></span></button> 
           
           </div> 
          


<div class="col-md-3" >
          <label>Clavier</label><br>
    <div class="col-md-3" style="width: 200px;">
    	<div class="row">
    	<div class="input-group">
  
  <input type="text" class="form-control" placeholder="Masse" id="number" name="num"  ><span class="input-group-addon">Kg.</span>
</div>
</div><br>
        <div class="row">
            <button class="btn btn-default btn-lg" onclick="number_write('1');" data-value="1">1</button>
            <button class="btn btn-default btn-lg" onclick="number_write('2');" data-value="2">2</button>
            <button class="btn btn-default btn-lg" onclick="number_write('3');" data-value="3">3</button>
        </div>
        <div class="row">
            <button class="btn btn-default btn-lg" onclick="number_write('4');" data-value="4">4</button>
            <button class="btn btn-default btn-lg" onclick="number_write('5');" data-value="5">5</button>
            <button class="btn btn-default btn-lg" onclick="number_write('6');" data-value="6">6</button>
        </div>
        <div class="row">
            <button class="btn btn-default btn-lg" onclick="number_write('7');" data-value="7">7</button>
            <button class="btn btn-default btn-lg" onclick="number_write('8');" data-value="8">8</button>
            <button class="btn btn-default btn-lg" onclick="number_write('9');" data-value="9">9</button>
        </div>
        <div class="row">
            <button class="btn btn-default btn-lg" onclick="number_clear();" data-value="C">C</button>
            <button class="btn btn-default btn-lg" onclick="number_write('0');" data-value="0">0</button>
            <button class="btn btn-default btn-lg" onclick="number_write('.');" data-value=",">,</button>
        </div>














    </div>
        </div>


      </div>
      <br><br><br>
      
       
      </div>
<br>


      <?php include "pied.php";  } else
      { 
        header('Location:../moteur/destroy.php');
      }?>