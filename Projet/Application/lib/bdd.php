<?php
$get_search = "";

class BDD {
    private $conn = null;
    private $matrix = null;
    private $search_skillmatrix = "";

    function __construct(string $log_file) {
        $logs = "uri:$log_file";
        //$bdd = new PDO($logs);
        $username = 'admin';
        $password = 'passwd';
        $this->conn = new PDO('mysql:host=localhost;dbname=cis_nord_warwait', $username, $password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->matrix = new PDO('mysql:host=localhost;dbname=skillmatrix', $username, $password);
        $this->matrix->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE IF NOT EXISTS searched_matrix (searched TEXT)";
        $this->conn->prepare($sql)->execute();
        
        
    }


    function check_if_part_of_element($element) {
        $sql = "SELECT name FROM matrix_tech";
        $stmt = $this->matrix->prepare($sql); 
        try {
            $stmt->execute();
            $arr = $stmt->fetchAll();
            $res = "";
            foreach ($arr as $key => $value) {
                if (str_contains(trim(strtolower($value[0]),' '),trim(strtolower($element),' ')) !== false) {
                    $res =  $value[0];
                    break;
                }
            }
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function search_bar_matrix($elemnt) {
        $res = $this->check_if_part_of_element($elemnt);
        $this->set_search_for_matrix($res);
    }

    function set_search_for_matrix($collab) {
        $this->search_skillmatrix = $collab;
        $sql="INSERT INTO searched_matrix (searched) VALUES (?)";
        try {
            $stmt = $this->conn->prepare("SELECT * FROM searched_matrix");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $this->conn->prepare("DELETE FROM searched_matrix")->execute();
            }
            $this->conn->prepare($sql)->execute([$collab]);
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
    }

    function add_state($state) {
        if (!empty($state)) {
            $sql = "INSERT INTO status (state) VALUES (?)";
            $stmt = $this->conn->prepare($sql);
            try {
                $stmt->execute([$state]);
            }
            catch(PDOException $e) {
                echo 'error : ' .$e->getMessage();
            }
        }
    }

    function get_options() {
        $sql = "SELECT * FROM status";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute();
            $res = $stmt->fetchAll();
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function get_search_for_matrix() {
        $sql="SELECT * FROM searched_matrix";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute();
            $res = $stmt->fetchAll();
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function get_warwait_edit() {
        $sql = "SELECT * from warwait";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute();
            $res = $stmt->fetchAll(); 
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function update_competences($competences) {
        $elt = explode('-',$competences);
        $id = intval($elt[0]);
        $value = $elt[1];

        $sql = "UPDATE warwait SET competences= ? WHERE id= $id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$value]);
            $res = array($value,$id);
        }
        catch (PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function update_grade($grade) {
        $elt = explode('-',$grade);
        $id = intval($elt[0]);
        $value = $elt[1];

        $sql = "UPDATE warwait SET grade= ? WHERE id= $id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$value]);
            $res = array($value,$id);
        }
        catch (PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function update_positionnement($positionnement) {
        $elt = explode('-',$positionnement);
        $id = intval($elt[0]);
        $value = $elt[1];

        $sql = "UPDATE warwait SET positionnement= ? WHERE id= $id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$value]);
            $res = array($value,$id);
        }
        catch (PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }
    
    function update_site($site) {
        $elt = explode('-',$site);
        $id = intval($elt[0]);
        $value = $elt[1];

        $sql = "UPDATE warwait SET site= ? WHERE id= $id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$value]);
            $res = array($value,$id);
        }
        catch (PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function get_warwait() {
        $sql = "SELECT * from warwait WHERE afficher='true'";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute();
            $res = $stmt->fetchAll(); 
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function delete($id) {
        $id = intval($id);
        $sql = "DELETE FROM warwait WHERE id = $id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute();
            $res = $stmt->fetchAll();
        }
        catch (PDOException $e) { 
            echo "Error: " . $e->getMessage();
        }
        
    }
    

    
    function get_warwait_searched(string $search) {
        $sql = "SELECT * FROM warwait WHERE (afficher='true' AND (nom LIKE ? OR competences LIKE ?))";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute(["%".$search."%","%".$search."%"]);
            $res = $stmt->fetchAll(); 
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function clear_week(string $week) {
        $semaine = 's'.$week;
        $sql = "UPDATE warwait set $semaine='NULL'";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute();
            $res = array('OK');
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function get_warwait_searched_v2(string $search) {
        $params = explode(';',$search);
        if (count($params) == 1) {
            $search = $params[0];
            $sql_test = "SELECT $search FROM skillmatrix";
            $stmt_test = $this->matrix->prepare($sql_test);
            try {
                $stmt_test->execute();
                $res_test = $stmt_test->fetchAll();
    
                $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search, positionnement, competences, cv_code, pe, id ,en_mission, afficher
                FROM cis_nord_warwait.warwait
                LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                WHERE afficher='true'
                ORDER BY skillmatrix.$search DESC";
                $stmt = $this->conn->prepare($sql_join);
                try {
                    $stmt->execute();
                    $res = $stmt->fetchAll(); 
                }
                catch(PDOException $e) {
                    echo 'error : ' .$e->getMessage();
                }
                return $res;
            }
            catch (PDOException $e) {
                return $this->get_warwait_searched($search);
            }
        } elseif (count($params) == 2) {
            $search1 = $params[0];
            $search2 = $params[1];
            $sql_test1 = "SELECT $search1 FROM skillmatrix";
            $stmt_test1 = $this->matrix->prepare($sql_test1);
            try {
                $stmt_test1->execute();
                $res_test = $stmt_test1->fetchAll();

                $sql_test2 = "SELECT $search2 FROM skillmatrix";
                $stmt_test2 = $this->matrix->prepare($sql_test2);
                try {
                    $stmt_test2->execute();
                    $res_test = $stmt_test2->fetchAll();
                    $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search2, positionnement, competences, cv_code,id , pe, en_mission, afficher
                    FROM cis_nord_warwait.warwait
                    LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                    WHERE afficher='true'
                    ORDER BY skillmatrix.$search1 DESC,skillmatrix.$search2 DESC";
                
                } catch(PDOException $e) {

                    try{
                        $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, positionnement, competences, cv_code, pe, id ,en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true' AND (nom LIKE $search2 OR competences LIKE $search2) 
                        ORDER BY skillmatrix.$search1 DESC";

                    }   
                    catch(PDOException $e) {
                        echo 'error : '. $e->getMessage();
                    }

                }


                $stmt = $this->conn->prepare($sql_join);
                $stmt->execute();
                $res = $stmt->fetchAll(); 
                return $res;
                
            }
            catch (PDOException $e) {
                return $this->get_warwait_searched($search);
            }

        } elseif (count($params) == 3) {
            $search1 = $params[0];
            $search2 = $params[1];
            $search3 = $params[2];

            $sql_test1 = "SELECT $search1 FROM skillmatrix";
            $stmt_test1 = $this->matrix->prepare($sql_test1);
            try {
                $stmt_test1->execute();
                $res_test = $stmt_test1->fetchAll();

                $sql_test2 = "SELECT $search2 FROM skillmatrix";
                $stmt_test2 = $this->matrix->prepare($sql_test2);
                try {
                    $stmt_test2->execute();
                    $res_test = $stmt_test2->fetchAll();

                    $sql_test3 = "SELECT $search3 FROM skillmatrix";
                    $stmt_test3 = $this->matrix->prepare($sql_test3);
                    try {
                        $stmt_test3->execute();
                        $res_test = $stmt_test3->fetchAll();

                        $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search2, skillmatrix.$search3, positionnement, competences,id , cv_code, pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true'
                        ORDER BY skillmatrix.$search1 DESC,skillmatrix.$search2 DESC,skillmatrix.$search3 DESC";

                    } catch(PDOException $e) {
                        $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search2, positionnement, competences, cv_code,id , pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true' AND (nom LIKE $search3 OR competences LIKE $search3) 
                        ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search2 DESC";
                    }
                
                } catch(PDOException $e) {
                    try {
                        $stmt_test3->execute();
                        $res_test = $stmt_test3->fetchAll();

                        $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search3, positionnement, competences,id , cv_code, pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true' AND (nom LIKE $search2 OR competences LIKE $search2) 
                        ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search3 DESC";

                    } catch(PDOException $e) {
                        $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, positionnement, competences, cv_code,id , pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true' AND (nom LIKE $search3 OR competences LIKE $search3 OR LIKE $search2 OR competences LIKE $search2) 
                        ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search2 DESC";
                    }
                }
                $stmt = $this->conn->prepare($sql_join);
                try {
                    $stmt->execute();
                    $res = $stmt->fetchAll(); 
                }
                catch(PDOException $e) {
                    echo 'error : ' .$e->getMessage();
                }
                return $res;
                
            }
            catch (PDOException $e) {
                return $this->get_warwait_searched($search);
            }


        } elseif (count($params)==4) {
            if (empty($params[3])) {
                $search1 = $params[0];
                $search2 = $params[1];
                $search3 = $params[2];

                $sql_test1 = "SELECT $search1 FROM skillmatrix";
                $stmt_test1 = $this->matrix->prepare($sql_test1);
                try {
                    $stmt_test1->execute();
                    $res_test = $stmt_test1->fetchAll();

                    $sql_test2 = "SELECT $search2 FROM skillmatrix";
                    $stmt_test2 = $this->matrix->prepare($sql_test2);
                    try {
                        $stmt_test2->execute();
                        $res_test = $stmt_test2->fetchAll();

                        $sql_test3 = "SELECT $search3 FROM skillmatrix";
                        $stmt_test3 = $this->matrix->prepare($sql_test3);
                        try {
                            $stmt_test3->execute();
                            $res_test = $stmt_test3->fetchAll();

                            $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search2, skillmatrix.$search3, positionnement, competences,id , cv_code, pe, en_mission, afficher
                            FROM cis_nord_warwait.warwait
                            LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                            WHERE afficher='true'
                            ORDER BY skillmatrix.$search1 DESC,skillmatrix.$search2 DESC,skillmatrix.$search3 DESC";

                        } catch(PDOException $e) {
                            $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search2, positionnement, competences, cv_code,id , pe, en_mission, afficher
                            FROM cis_nord_warwait.warwait
                            LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                            WHERE afficher='true' AND (nom LIKE $search3 OR competences LIKE $search3) 
                            ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search2 DESC";
                        }
                    
                    } catch(PDOException $e) {
                        try {
                            $stmt_test3->execute();
                            $res_test = $stmt_test3->fetchAll();

                            $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search3, positionnement, competences,id , cv_code, pe, en_mission, afficher
                            FROM cis_nord_warwait.warwait
                            LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                            WHERE afficher='true' AND (nom LIKE $search2 OR competences LIKE $search2) 
                            ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search3 DESC";

                        } catch(PDOException $e) {
                            $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, positionnement, competences, cv_code,id , pe, en_mission, afficher
                            FROM cis_nord_warwait.warwait
                            LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                            WHERE afficher='true' AND (nom LIKE $search3 OR competences LIKE $search3 OR LIKE $search2 OR competences LIKE $search2) 
                            ORDER BY skillmatrix.$search1 DESC";
                        }
                    }
                    $stmt = $this->conn->prepare($sql_join);
                    try {
                        $stmt->execute();
                        $res = $stmt->fetchAll(); 
                    }
                    catch(PDOException $e) {
                        echo 'error : ' .$e->getMessage();
                    }
                    return $res;
                    
                }
                catch (PDOException $e) {
                    $sql_test2 = "SELECT $search2 FROM skillmatrix";
                    $stmt_test2 = $this->matrix->prepare($sql_test2);
                    try {
                        $stmt_test2->execute();
                        $res_test = $stmt_test2->fetchAll();

                        $sql_test3 = "SELECT $search3 FROM skillmatrix";
                        $stmt_test3 = $this->matrix->prepare($sql_test3);
                        try {
                            $stmt_test3->execute();
                            $res_test = $stmt_test3->fetchAll();

                            $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search2, skillmatrix.$search3, positionnement, competences,id , cv_code, pe, en_mission, afficher
                            FROM cis_nord_warwait.warwait
                            LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                            WHERE afficher='true' (nom LIKE $search1 OR competences LIKE $search1) 
                            ORDER BY skillmatrix.$search2 DESC,skillmatrix.$search3 DESC";

                        } catch(PDOException $e) {
                            $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search2, positionnement, competences, cv_code,id , pe, en_mission, afficher
                            FROM cis_nord_warwait.warwait
                            LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                            WHERE afficher='true' AND (nom LIKE $search3 OR competences LIKE $search3 OR nom LIKE $search1 OR competences LIKE $search1 ) 
                            ORDER BY skillmatrix.$search2 DESC";
                        }
                    
                    } catch(PDOException $e) {
                        try {
                            $stmt_test3->execute();
                            $res_test = $stmt_test3->fetchAll();

                            $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, positionnement, competences,id , cv_code, pe, en_mission, afficher
                            FROM cis_nord_warwait.warwait
                            WHERE afficher='true' AND (nom LIKE $search2 OR competences LIKE $search2 OR nom LIKE $search3 OR competences LIKE $search3 OR nom LIKE $search1 OR competences LIKE $search1 )";

                        } catch(PDOException $e) {
                            $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, positionnement, competences, cv_code,id , pe, en_mission, afficher
                            FROM cis_nord_warwait.warwait
                            LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                            WHERE afficher='true' AND (nom LIKE $search3 OR competences LIKE $search3 OR LIKE $search2 OR competences LIKE $search2) 
                            ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search2 DESC";
                        }
                    }
                    $stmt = $this->conn->prepare($sql_join);
                    try {
                        $stmt->execute();
                        $res = $stmt->fetchAll(); 
                    }
                    catch(PDOException $e) {
                        echo 'error : ' .$e->getMessage();
                    }
                    return $res;


                } 
            } else {
                return array();
            }
        }

    }

    function get_warwait_searched_edit(string $search) {
        $sql = "SELECT * FROM warwait WHERE nom LIKE ? OR competences LIKE ?";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute(["%".$search."%","%".$search."%"]);
            $res = $stmt->fetchAll(); 
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function get_row(string $id) {
        $sql = "SELECT * FROM warwait WHERE id =$id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute();
            $res = $stmt->fetchAll(); 
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function update_warwait(string $nom, string $semaine, string $value) {
        $id = intval($nom);
        $sql = "UPDATE warwait SET $semaine= ? WHERE id= $id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$value]);
            $res = array($nom,$semaine,$value);
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function get_devl_skillmatrix($table) {
        $getcol = "SELECT * FROM $table";
        $stmt1 = $this->matrix->prepare($getcol);
        try {
            $str="";
            $all_sql="SELECT ";
            $stmt1->execute(); 
            for ($i=0; $i<$stmt1->columnCount();$i++) {
                if ($stmt1->getColumnMeta($i)['name']!='name') {
                    if ($i == $stmt1->columnCount()-1) {
                        $str.= "$table.".$stmt1->getColumnMeta($i)['name'];
                    } else {
                        $str.= "$table.".$stmt1->getColumnMeta($i)['name'].=", ";
                    }
                }
            }
            $sql = "SELECT * FROM matrix_tech";
            $stmt2 = $this->matrix->prepare($sql);
            $stmt2->execute();
            for ($i=0; $i<$stmt2->columnCount();$i++) {
                if ($stmt2->getColumnMeta($i)['name']==$table) {
                    $all_sql.= 'matrix_tech.'.$stmt2->getColumnMeta($i)['name']. ", ".$str.=', ';
                } elseif ($stmt2->getColumnMeta($i)['name']=='ANGLAIS'){
                    $all_sql.= 'matrix_tech.'.$stmt2->getColumnMeta($i)['name'];
                } else {
                    $all_sql.= 'matrix_tech.'.$stmt2->getColumnMeta($i)['name'].', ';
                }
            }


            
            
            $nom = $this->get_search_for_matrix();
            
            $nom = $nom[0][0];
            if (!empty($nom)) {
                $str="";
                $all_sql1="SELECT ";
                $stmt1->execute(); 
                for ($i=0; $i<$stmt1->columnCount();$i++) {
                    if ($stmt1->getColumnMeta($i)['name']!='name') {
                        if ($i == $stmt1->columnCount()-1) {
                            $str.= "$table.".$stmt1->getColumnMeta($i)['name'];
                        } else {
                            $str.= "$table.".$stmt1->getColumnMeta($i)['name'].=", ";
                        }
                    }
                }
                $sql = "SELECT * FROM matrix_tech";
                $stmt2 = $this->matrix->prepare($sql);
                $stmt2->execute();
                for ($i=0; $i<$stmt2->columnCount();$i++) {
                    if ($stmt2->getColumnMeta($i)['name']==$table) {
                        $all_sql1.= 'matrix_tech.'.$stmt2->getColumnMeta($i)['name']. ", ".$str.=', ';
                    } elseif ($stmt2->getColumnMeta($i)['name']=='ANGLAIS'){
                        $all_sql1.= 'matrix_tech.'.$stmt2->getColumnMeta($i)['name'];
                    } else {
                        $all_sql1.= 'matrix_tech.'.$stmt2->getColumnMeta($i)['name'].', ';
                    }
                }
                $tmp="SELECT * from skillmatrix WHERE name LIKE ?";
                $stmtmp = $this->matrix->prepare($tmp);
                $stmtmp->execute([$nom]);
                $restmp = $stmtmp->fetchAll();
                
                $rank = $this->sort_object($restmp);
                
                $all_sql.=", skillmatrix.".$rank[0].", skillmatrix.".$rank[1]." ,  skillmatrix.".$rank[2]. " FROM matrix_tech LEFT JOIN $table ON $table.name = matrix_tech.name LEFT JOIN skillmatrix ON skillmatrix.name = matrix_tech.name";
                $all_sql.=" ORDER BY skillmatrix.".$rank[0]." DESC, skillmatrix.".$rank[1]." DESC,  skillmatrix.".$rank[2]." DESC";
                $all_sql1.=" FROM matrix_tech LEFT JOIN $table ON $table.name = matrix_tech.name WHERE matrix_tech.name = '$nom'";
                $stmt1 = $this->matrix->prepare($all_sql1);
                $stmt1->execute();
                $res1 = $stmt1->fetchAll();
            } else {
                $all_sql.=" FROM matrix_tech LEFT JOIN $table ON $table.name = matrix_tech.name";
            }
            $stmt = $this->matrix->prepare($all_sql);
            $stmt->execute();
            $res = $stmt->fetchAll();
            
            if (!empty(($nom))) {
                $res =$this->my_unique_array( $res1+$res);
            }
            
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function sort_object($object) {
        $res = array();
        for ($i=0;$i<3;$i++) {
            $max="";
            $maxval=0;
            if (isset($object[0])) {
                foreach ($object[0] as $key => $value) {
                    if (is_numeric($value) && $key != 'ANGLAIS' && !is_numeric($key)) {
                        if ($maxval < $value) {
                            $maxval = $value;
                            $max = $key;
                        }
                    }
                }
                array_push($res,$max);
                $object[0][$max] = 0;
            }
        }
        return $res;
    }

    function get_skillmatrix() {
        $sql = "SELECT * from matrix_tech";
        $stmt = $this->matrix->prepare($sql);
        try {
            
            $stmt->execute();
            $res = $stmt->fetchAll(); 
            $cpy = $res;
            sort($cpy, SORT_NUMERIC);
            $cpy=array_reverse($cpy,true);
            $nom = $this->get_search_for_matrix();
            $nom = $nom[0][0];
            if (!empty($nom)) {
               // $nom = json_encode($nom);
                $sql1="SELECT * from skillmatrix WHERE skillmatrix.name LIKE ?";
                $stmt1 = $this->matrix->prepare($sql1);
                $stmt1->execute([$nom]);
                $res1 = $stmt1->fetchAll();

                $rank = $this->sort_object($res1);
                if (count($rank)>=3) {
                    $sql2 ="SELECT matrix_tech.name, matrix_tech.DISPONIBILITE, matrix_tech.CLOUD, matrix_tech.DEVELOPPEMENT, matrix_tech.SOFT_SKILLS_ET_MANAGEMENT, matrix_tech.MIDDLEWARES, matrix_tech.SUPERVISION, matrix_tech.AUTOMATISATION, matrix_tech.SYSTEMES, matrix_tech.SGBD, matrix_tech.VIRTUALISATION, matrix_tech.OUTILS, matrix_tech.SECURITE_ET_RESEAUX, matrix_tech.ANGLAIS, skillmatrix.".$rank[0].", skillmatrix.".$rank[1]." ,  skillmatrix.".$rank[2]. " from matrix_tech LEFT JOIN skillmatrix ON skillmatrix.name = matrix_tech.name WHERE matrix_tech.name LIKE ?";
                    $sql = "SELECT matrix_tech.name, matrix_tech.DISPONIBILITE, matrix_tech.CLOUD, matrix_tech.DEVELOPPEMENT, matrix_tech.SOFT_SKILLS_ET_MANAGEMENT, matrix_tech.MIDDLEWARES, matrix_tech.SUPERVISION, matrix_tech.AUTOMATISATION, matrix_tech.SYSTEMES, matrix_tech.SGBD, matrix_tech.VIRTUALISATION, matrix_tech.OUTILS, matrix_tech.SECURITE_ET_RESEAUX, matrix_tech.ANGLAIS, skillmatrix.".$rank[0].", skillmatrix.".$rank[1]." ,  skillmatrix.".$rank[2]. " from matrix_tech LEFT JOIN skillmatrix ON skillmatrix.name = matrix_tech.name ORDER BY skillmatrix.".$rank[0]." DESC, skillmatrix.".$rank[1]." DESC,  skillmatrix.".$rank[2]." DESC";
                } else {
                    $sql2 = "SELECT * from matrix_tech WHERE matrix_tech.name LIKE ?";
                    $sql = "SELECT * from matrix_tech";
                }
                $stmt = $this->matrix->prepare($sql);
                $stmt->execute();
                $res = $stmt->fetchAll(); 
                $cpy = $res;
                $stmt2 = $this->matrix->prepare($sql2);
                $stmt2->execute([$nom]);
                $res2 = $stmt2->fetchAll();
                $res =$this->my_unique_array( $res2+$res);
            }

        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function my_unique_array($arr) {
        $res = array();
        foreach ($arr as $v ) {
            if (!in_array($v, $res, true)) {
                array_push($res,$v);
            }
        }
        return $res;
    }

    function update_sucess($sucess_id) {
        $elt = explode('-',$sucess_id);
        $sucessrate = $elt[0];
        $id = intval($elt[1]);

        $sql = "UPDATE warwait SET reussite= ? WHERE id= $id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$sucessrate]);
            $res = array($sucessrate,$id);
        }
        catch (PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function update_mission_state(string $id, string $state) {
        $id = intval($id);
        $sql = "UPDATE warwait SET en_mission= ? WHERE id= $id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$state]);
            $res = array($id,$state);
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function change_checked_status(string $checked_id) {
        $elt = explode('-', $checked_id);
        $id = intval($elt[1]);
        $checked = $elt[0];
        $sql = "UPDATE warwait SET afficher= ? WHERE id= $id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$checked]);
            $res = array($checked,$id);
        }
        catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $res;
    }

    function add_collab(string $collaborateur) {
        //$sql = "INSERT INTO warwait (nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite posotionnement, competences, cv_code, pe, en_mission, afficher) VALUES ('$collaborateur', 'A', 'Lille', 'NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL','NULL', 0 , 'NULL', 'NULL', '34', 'NULL', 'false', 'true')";
        $sql_check = "SELECT * FROM warwait WHERE nom=?";
        $stmt_check = $this->conn->prepare($sql_check);
        try {
            $stmt_check->execute([$collaborateur]);
            $test = $stmt_check->fetchAll();
            if (empty($test)) {
                $sql = "INSERT INTO warwait (nom) VALUES ('$collaborateur')";
                $stmt = $this->conn->prepare($sql);
                try {
                    $stmt->execute();
                    $res = array($collaborateur);
                }
                catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                return $res;
            } else {
                return array();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    function get_warwait_searched_v3(string $search) {
        $params = explode(';',$search);
        if (count($params) == 1) {
            $search1 = $params[0];
            if (!$search) {
                return $this->get_warwait_searched($search);
            }
            $sql = "SELECT $search1 FROM skillmatrix";
            $stmt = $this->matrix->prepare($sql);
            try {
                $stmt->execute();
    
                $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, positionnement, competences,id , cv_code, pe, en_mission, afficher
                FROM cis_nord_warwait.warwait
                LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                WHERE afficher='true'
                ORDER BY skillmatrix.$search1 DESC";
    
                $stmt = $this->conn->prepare($searchsql);
                $stmt->execute();
                return $stmt->fetchAll();
    
    
            } catch (PDOException $e) {
                return $this->get_warwait_searched($search1);
            }
    
        } else if (count($params) == 2) {
            $search1 = trim($params[0],' ');
            $search2 = trim($params[1],' ');
            if (!$search2) {
                return $this->get_warwait_searched_v3($search1);
            } 
            if (!$search1) {
                return $this->get_warwait_searched_v3($search2);
            }
            $sql = "SELECT $search1 FROM skillmatrix";
            $stmt = $this->matrix->prepare($sql);
            try {
                $stmt->execute();

                $sql = "SELECT $search2 FROM skillmatrix";
                $stmt = $this->matrix->prepare($sql);
                try {
                    // ICI 1 ET 2
                    $stmt->execute();

                    $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search2, positionnement, competences,id , cv_code, pe, en_mission, afficher
                    FROM cis_nord_warwait.warwait
                    LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                    WHERE afficher='true'
                    ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search2 DESC";
                    $stmt = $this->conn->prepare($searchsql);
                    $stmt->execute();
                    return $stmt->fetchAll();
                    

                }
                catch (PDOException $e) {
                    // ICI 1 MAIS PAS 2

                    $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, positionnement, competences,id , cv_code, pe, en_mission, afficher
                    FROM cis_nord_warwait.warwait
                    LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                    WHERE afficher='true' AND (warwait.nom LIKE '".$this->check_if_part_of_element($search2)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search2)."')
                    ORDER BY skillmatrix.$search1 DESC";
                    $stmt = $this->conn->prepare($searchsql);
                    try {
                        $stmt->execute();
                        return $stmt->fetchAll();
                    }
                    catch (PDOException $e) {
                        echo 'Error : ' .$e->getMessage();
                        return $searchsql;
                    }
                }
            }
            catch (PDOException $e) {
                $sql = "SELECT $search2 FROM skillmatrix";
                $stmt = $this->matrix->prepare($sql);
                try {
                    // ICI PAS 1 MAIS 2
                    $stmt->execute();

                    $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search2, positionnement, competences,id , cv_code, pe, en_mission, afficher
                    FROM cis_nord_warwait.warwait
                    LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                    WHERE afficher='true' AND (warwait.nom LIKE '".$this->check_if_part_of_element($search1)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search1)."')
                    ORDER BY skillmatrix.$search2 DESC";
                    $stmt = $this->conn->prepare($searchsql);
                    $stmt->execute();
                    return $stmt->fetchAll();
                }
                catch (PDOException $e) {
                    // ICI NI 1 NI 2
                    $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, positionnement, competences,id , cv_code, pe, en_mission, afficher
                    FROM cis_nord_warwait.warwait
                    LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                    WHERE afficher='true' AND (warwait.nom LIKE '".$this->check_if_part_of_element($search2)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search2)."' OR warwait.nom LIKE '".$this->check_if_part_of_element($search1)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search1)."')";
                    $stmt = $this->conn->prepare($searchsql);
                    try {
                        $stmt->execute();
                        return $stmt->fetchAll();
                    }
                    catch (PDOException $e) {
                        echo 'Error : ' .$e->getMessage();
                        return $searchsql;
                    }
                }
            }
    
        } else if (count($params) == 3) {
            $search1 = trim($params[0],' ');
            $search2 = trim($params[1],' ');
            $search3 = trim($params[2],' ');
            if (!$search3) {
                return $this->get_warwait_searched_v3($search1.';'.$search2);
            }
            if (!$search2) {
                return $this->get_warwait_searched_v3($search1.';'.$search3);
            }
            if (!$search1) {
                return $this->get_warwait_searched_v3($search2.';'.$search3);
            }

            $sql = "SELECT $search1 FROM skillmatrix";
            $stmt = $this->matrix->prepare($sql);

            try {
                // 1
                $stmt->execute();

                $sql = "SELECT $search2 FROM skillmatrix";
                $stmt = $this->matrix->prepare($sql);

                try {
                    // 1 ET 2
                    $stmt->execute();

                    $sql = "SELECT $search3 FROM skillmatrix";
                    $stmt = $this->matrix->prepare($sql);

                    try {
                        // 1, 2 ET 3
                        $stmt->execute();

                        $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search2, skillmatrix.$search3, positionnement, competences,id , cv_code, pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true'
                        ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search2 DESC, skillmatrix.$search3 DESC";
                        $stmt = $this->conn->prepare($searchsql);
                        $stmt->execute();
                        return $stmt->fetchAll();
                    }
                    catch (PDOException $e) {
                        // 1, 2 MAIS PAS 3
                        $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search2, positionnement, competences,id , cv_code, pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true' AND ( warwait.nom LIKE '".$this->check_if_part_of_element($search3)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search3)."' )
                        ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search2 DESC";
                        $stmt = $this->conn->prepare($searchsql);
                        $stmt->execute();
                        return $stmt->fetchAll();

                    }
                }
                catch (PDOException $e) {
                    // 1 ET PAS 2

                    $sql = "SELECT $search3 FROM skillmatrix";
                    $stmt = $this->matrix->prepare($sql);

                    try {
                        // 1, PAS 2 ET 3
                        $stmt->execute();

                        $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search3, positionnement, competences,id , cv_code, pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true' AND ( warwait.nom LIKE '".$this->check_if_part_of_element($search2)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search2)."' )
                        ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search3 DESC";
                        $stmt = $this->conn->prepare($searchsql);
                        $stmt->execute();
                        return $stmt->fetchAll();
                    }
                    catch (PDOException $e) {
                        // 1 MAIS PAS 2 ET PAS 3
                        $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, positionnement, competences,id , cv_code, pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true' AND ( warwait.nom LIKE '".$this->check_if_part_of_element($search3)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search3)."' OR warwait.nom LIKE '".$this->check_if_part_of_element($search2)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search2)."' )
                        ORDER BY skillmatrix.$search1 DESC";
                        $stmt = $this->conn->prepare($searchsql);
                        $stmt->execute();
                        return $stmt->fetchAll();

                    }

                }
            }
            catch (PDOException $e) {
                // PAS 1

                $sql = "SELECT $search2 FROM skillmatrix";
                $stmt = $this->matrix->prepare($sql);

                try {
                    // PAS 1 MAIS 2
                    $stmt->execute();

                    $sql = "SELECT $search3 FROM skillmatrix";
                    $stmt = $this->matrix->prepare($sql);

                    try {
                        // PAS 1 MAIS 2 ET 3
                        $stmt->execute();

                        $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search2, skillmatrix.$search3, positionnement, competences,id , cv_code, pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true' AND ( warwait.nom LIKE '".$this->check_if_part_of_element($search1)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search1)."' )
                        ORDER BY skillmatrix.$search2 DESC, skillmatrix.$search3 DESC";
                        $stmt = $this->conn->prepare($searchsql);
                        $stmt->execute();
                        return $stmt->fetchAll();
                    }
                    catch (PDOException $e) {
                        // PAS 1 MAIS 2 MAIS PAS 3
                        $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search2, positionnement, competences,id , cv_code, pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true' AND ( warwait.nom LIKE '".$this->check_if_part_of_element($search3)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search3)."' OR warwait.nom LIKE '".$this->check_if_part_of_element($search1)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search1)."' )
                        ORDER BY skillmatrix.$search2 DESC";
                        $stmt = $this->conn->prepare($searchsql);
                        $stmt->execute();
                        return $stmt->fetchAll();

                    }
                }
                catch (PDOException $e) {
                    // PAS 1 ET PAS 2

                    $sql = "SELECT $search3 FROM skillmatrix";
                    $stmt = $this->matrix->prepare($sql);

                    try {
                        // PAS 1, PAS 2 MAIS 3
                        $stmt->execute();

                        $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search3, positionnement, competences,id , cv_code, pe, en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true' AND ( warwait.nom LIKE '".$this->check_if_part_of_element($search2)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search2)."' OR warwait.nom LIKE '".$this->check_if_part_of_element($search1)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search1)."' )
                        ORDER BY skillmatrix.$search3 DESC";
                        $stmt = $this->conn->prepare($searchsql);
                        $stmt->execute();
                        return $stmt->fetchAll();
                    }
                    catch (PDOException $e) {
                        // PAS 1 PAS 2 ET PAS 3
                        $searchsql = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, positionnement, competences,id , cv_code, pe, en_mission, afficher
                        WHERE afficher='true' AND ( warwait.nom LIKE '".$this->check_if_part_of_element($search3)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search3)."' OR warwait.nom LIKE '".$this->check_if_part_of_element($search2)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search2)."' OR warwait.nom LIKE '".$this->check_if_part_of_element($search1)."' OR warwait.competences LIKE '".$this->check_if_part_of_element($search1)."')";
                        $stmt = $this->conn->prepare($searchsql);
                        $stmt->execute();
                        return $stmt->fetchAll();

                    }

                }
            }
            
        } else if (count($params) == 4) {
            $search1 = trim($params[0],' ');
            $search2 = trim($params[1],' ');
            $search3 = trim($params[2],' ');
            $search4 = trim($params[3],' ');
            if (!$search4) {
                return $this->get_warwait_searched_v3($search1.';'.$search2.';'.$search3);
            } else if (!$search3) {
                return $this->get_warwait_searched_v3($search1.';'.$search2);
            } else if (!$search2) {
                return $this->get_warwait_searched_v3($search1.';'.$search3);
            } else if (!$search1) {
                return $this->get_warwait_searched_v3($search2.';'.$search3);
            } else {
                return array();
            }
        } else {
    
        }
    
    
    
    }
}




?>
