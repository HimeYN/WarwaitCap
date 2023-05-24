<!DOCTYPE html>
<html>
    <head>
        <title>Warwait</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style/warwait.css">
    </head>
    <body>
        <h1>la warwait</h1> 
        <form action="warwait.php" method="get">
        <input type="number" name="semaine" placeholder="Quelle semaine ?" min="1" max="52"/><input type="submit" value="OK">
        </form> 
        <form action="warwait.php" method="get">
        <input type="number" name ="nbsem" placeholder ="Combien de semaines ?" min="5" max ="13" /><input type="submit" value="OK"/></form>
        <?php   

        $inputplus = "";
        if (isset($_GET['semaine'])) {
            $semaine = strval(intval($_GET['semaine']%53));
            $semaine = ($semaine +1)%52;

            if (isset($_GET['nbsem'])) {
                $inputplus= "<a href=\"warwait.php?semaine=$semaine&nbsem=".$_GET['nbsem']."\"><div class=\"plus\">+</div></a>";
            } else {
                $inputplus= "<a href=\"warwait.php?semaine=$semaine\"><div class=\"plus\">+</div></a>";
            }
        
        }
        else {
            if (isset($_GET['nbsem'])) {
                $inputplus= "<a href=\"warwait.php?semaine=17&nbsem=".$_GET['nbsem']."\"><div class=\"plus\">+</div></a>";
            } else {
                $inputplus= "<a href=\"warwait.php?semaine=17\"><div class=\"plus\">+</div></a>";
            }
            
        }

        echo $inputplus;

        if (isset($_GET['semaine'])) {
            echo "<p>".$_GET['semaine'] ."</p>";
        }
        else {

            echo "<p>16</p>";
        }
        
        $inputminus = "";
        if (isset($_GET['semaine'])) {
            $semaine = strval(intval($_GET['semaine']%53));
            if ($semaine == "0") {
                $semaine = "52";
            }else {
                $semaine= ($semaine -1);
            }
            if (isset($_GET['nbsem'])) {
                $inputminus= "<a href=\"warwait.php?semaine=$semaine&nbsem=".$_GET['nbsem']."\"><div class=\"minus\">-</div></a>";
            }else {

                $inputminus= "<a href=\"warwait.php?semaine=$semaine\"><div class=\"minus\">-</div></a>";
            }
        }
        else {
            if (isset($_GET['nbsem'])) {
                $inputminus= "<a href=\"warwait.php?semaine=15&nbsem=".$_GET['nbsem']."\"><div class=\"minus\">-</div></a>";
            }else {

            $inputminus= "<a href=\"warwait.php?semaine=15\"><div class=\"minus\">-</div></a>";
            }
        }

        echo $inputminus;
        ?></p>

        
        
        

        <form action = "warwait.php" method = "get">
        <input type = "search" name = "terme">
        <input type = "submit" name = "s" value = "Rechercher">
        </form>
        <?php
            $servername = 'localhost';
            $username = 'root';
            $password = '#Admin31415';
            $database = 'cis_nord_warwait';
            
            try{
                //$conn = new PDO("mysql:host=$servername,dbname=$database", $username, $password);
                $bdd = new PDO('mysql:host=localhost;dbname=cis_nord_warwait', $username, $password);
                $bdd ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                

                if (isset($_GET["s"]) AND $_GET["s"] == "Rechercher")
                {
                    $_GET["terme"] = htmlspecialchars($_GET["terme"]); //pour sécuriser le formulaire contre les failles html
                    $terme = $_GET["terme"];
                    $terme = trim($terme); //pour supprimer les espaces dans la requête de l'internaute
                    $terme = strip_tags($terme); //pour supprimer les balises html dans la requête

                    if (isset($terme))
                    {
                        $terme = strtolower($terme);
                        $resultat = $bdd->prepare("SELECT * FROM warwait WHERE nom LIKE ? OR competences LIKE ?");
                        $resultat->execute(array("%".$terme."%", "%".$terme."%"));
                    }
                }
                else
                {
                    $requete1 = 'SELECT * FROM warwait';
                    $resultat = $bdd->prepare($requete1);
                    $resultat->execute();
                }
                
                


                // le prepare (avec le execute) est comme un query mais beaucoup plus sécurisé (voir ci-dessous)
                // l'opérateur flèche -> permer d'accéder aux éléments d'une classe (méthode ou attribut)
                

                // récupe d'infos (pas utilisés ici)
                $nbreResult = $resultat->rowCount(); // Nbre de ligne de résultat
                $colcount = $resultat->columnCount(); // Nombre de colonne

                if (!$resultat) {
                    echo "Problème de requete";
                } else {
                ?>


                Liste des collaborateurs :<br>

                <?php
                if (isset($_GET['semaine'])) {
                    $semaine = strval(intval($_GET['semaine']%53));
                    //$semaine = $_GET['semaine'];

                }
                else {
                    $semaine = "16";
                }
                $nsemaine = $semaine;
                if (isset($_POST['+'])) {
                    $semaine ++;
                }

                $semaine= "s".$semaine;
                
                if (isset($_GET['nbsem'])) {
                    $nbsem = $_GET['nbsem'];
                } else {
                    $nbsem = 5;
                }


                $TAB = "<table><thead><tr><th> Nom Prénom</th><th>Grade</th><th>site</th>";
                for ($i=0; $i<$nbsem;$i++) {
                    if (($nsemaine+$i)%53 == 0) {
                        $nsemaine++;
                    }
                    $TAB .= "<th>".(($nsemaine+$i)%53)."</th>";
                }
                $TAB.="</th><th>Réussite</th><th>Positionnement</th><th>Compétences</th></tr></thead><tbody>";
                while($ligne = $resultat->fetch()) {
                        $TAB .= "<tr class=\"warwait\">"."<td>". "<a href=\"skillmatrix.php?name=".$ligne['nom']."\">"."<div>".$ligne['nom']."</td><td>".$ligne['grade']."</td><td>".$ligne['site']."</td>";
                        for ($i = 0; $i<$nbsem;$i++) {
                            if (($nsemaine+$i)%53 ==0) {
                                $nsemaine++;
                            }
                            if ($ligne['s'.($nsemaine+$i)%53] == "NULL") {
                                $TAB .= "<td><select name=\"selectsem\"><option>NULL</option><option>IP+</option><option>nan</option></select></td>";

                            } else {
                                $TAB .= "<td>".$ligne['s'.($nsemaine+$i)%53]."</div></td>";

                            }
                        }
                        $TAB .="</td><td>".$ligne['reussite']."%</td><td>".$ligne['positionnement']."</td><td>".$ligne['competences']."</td></a></tr>";
                    }  
                $TAB .= "</tbody></table";
                echo $TAB;
                    ?>


                <?php
                    } // fin du else
                $resultat->closeCursor(); // libère le résultat
                
            }
            
            catch(PDOException $e){
              echo "Erreur : " . $e->getMessage();
            }
        ?>
    </body>
</html>