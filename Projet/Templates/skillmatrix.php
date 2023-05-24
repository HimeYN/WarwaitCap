<!DOCTYPE html>
<html>
    <head>
        <title>Warwait</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style/skillmatrix.css">
    </head>
    <body>
</form> 
<h1>Matrice de compétences :</h1><br>

        <a href="warwait.php" class="homepage"><div class="homepage"><p2> Home <p2></div></a>

        <form action = "skillmatrix.php" method = "get">
        <input type = "search" name = "name" placeholder="    Rechercher">
        <input type = "submit" value = "">
        </form>
        <?php
            $servername = 'localhost';
            $username = 'root';
            $password = '#Admin31415';
            $database = 'skillmatrix';


            
            try{

                //$conn = new PDO("mysql:host=$servername,dbname=$database", $username, $password);
                $bdd = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
                $bdd ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                if (isset($_GET['name'])) {
                    $_GET['name']= htmlspecialchars($_GET['name']);
                    $name = $_GET['name'];
                    $name = trim($name);
                    $name = strip_tags($name);
                    
                    if (isset($name)) {
                        
                        $name = strtolower($name);
                        $resultat_s = $bdd->prepare("SELECT * FROM skillmatrix WHERE name LIKE ?");
                        $resultat_s->execute(array("%".$name."%"));
                        while ($resultat_s->rowCount()==0) {
                            $name = substr($name,0,-1);
                            $resultat_s = $bdd->prepare("SELECT * FROM skillmatrix WHERE name LIKE ?");
                            $resultat_s->execute(array("%".$name."%"));
                        }
                    }
                }
                
                $requete1 = 'SELECT * FROM skillmatrix';
                    
                    
                // le prepare (avec le execute) est comme un query mais beaucoup plus sécurisé (voir ci-dessous)
                // l'opérateur flèche -> permer d'accéder aux éléments d'une classe (méthode ou attribut)
                $resultat = $bdd->prepare($requete1);
                $resultat->execute();
            
            // récupe d'infos (pas utilisés ici)
            $nbreResult = $resultat->rowCount(); // Nbre de ligne de résultat
            $colcount = $resultat->columnCount(); // Nombre de colonne
            
            if (!$resultat) {
                echo "Problème de requete";
            } else {
                ?>




    <?php
                    if (($resultat_s->rowCount()==0 || $resultat_s->rowCount()>1)) {
                        echo "<div class=\"unfound\"><p1>".$_GET['name']." n'est pas dans la matrice de compétences </br> Voici les résultats de la base de données après la recherche</p1></div>";
                    }
                    $already_displayed = array();
                    
                    $TAB = "<div class=\"tscroll\"><table><tr>";
                    $TAB.="<th class=\"sticky-col_h\">".$resultat->getColumnMeta(0)["name"]."</th>";
                    for ($i = 1;$i <$colcount;$i++){
                        $colname = $resultat->getColumnMeta($i);
                        $TAB.= "<th>". $colname["name"]."</th>";
                    }
                    $TAB .= "</tr>";
                    if (isset($resultat_s)) {
                        while ($ligne = $resultat_s->fetch()) {
                            $TAB .= "<tr class=\"searched\">";
                            for ($i = 0;$i <$colcount;$i++){
                                if (is_numeric($ligne[$i])) {
                                    if ($ligne[$i] == 0) {
                                        $TAB .= "<td class=\"zero_s\">". $ligne[$i]. "</td>";

                                    } elseif ($ligne[$i] == 1) {
                                        $TAB .= "<td class=\"un_s\">". $ligne[$i]. "</td>";
                                    } elseif ($ligne[$i] == 2) {
                                        $TAB .= "<td class=\"deux_s\">". $ligne[$i]. "</td>";
                                    } elseif ($ligne[$i] == 3) {
                                        $TAB .= "<td class=\"trois_s\">". $ligne[$i]. "</td>";
                                    } else {
                                        $TAB .= "<td>". $ligne[$i]."</td>";
                                    }
                                    
                                    

                                } else {
                                    if ($i == 0) {
                                        $TAB .= "<td class=\"sticky-col_searched\">". $ligne[$i]."</td>";
                                    } else {
                                        $TAB .= "<td>". $ligne[$i]."</td>";
                                    }
                                }
                            }
                            $TAB.="</tr>";
                            array_push($already_displayed,$ligne['name']);
                        }
                    }

                    while($ligne = $resultat->fetch()) {
                        if (!(in_array($ligne['name'],$already_displayed))){
                            $TAB .= "<tr class=\"not-searched\">";
                                for ($i = 0;$i <$colcount;$i++){
                                    if (is_numeric($ligne[$i])) {
                                        if ($ligne[$i] == 0) {
                                            $TAB .= "<td class=\"zero\">". $ligne[$i]. "</td>";

                                        } elseif ($ligne[$i] == 1) {
                                            $TAB .= "<td class=\"un\">". $ligne[$i]. "</td>";
                                        } elseif ($ligne[$i] == 2) {
                                            $TAB .= "<td class=\"deux\">". $ligne[$i]. "</td>";
                                        } elseif ($ligne[$i] == 3) {
                                            $TAB .= "<td class=\"trois\">". $ligne[$i]. "</td>";
                                        } else {
                                            $TAB .= "<td>". $ligne[$i]."</td>";
                                        }
                                        
                                        

                                    } else {
                                        if ($i == 0) {
                                            $TAB .= "<td class=\"sticky-col\">". $ligne[$i]."</td>";
                                        } else {
                                            $TAB .= "<td>". $ligne[$i]."</td>";
                                        }
                                    }
                                }
                            $TAB.="</tr>";
                        }
                        
                    }  
                    $TAB .="</table></div>";
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