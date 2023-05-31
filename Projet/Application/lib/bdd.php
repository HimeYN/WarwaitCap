<?php

class BDD {
    private $conn = null;
    private $matrix = null;

    function __construct(string $log_file) {
        $logs = "uri:$log_file";
        //$bdd = new PDO($logs);
        $username = 'root';
        $password = '#Admin31415';
        $this->conn = new PDO('mysql:host=localhost;dbname=cis_nord_warwait', $username, $password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->matrix = new PDO('mysql:host=localhost;dbname=skillmatrix', $username, $password);
        $this->matrix->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
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
                    $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, positionnement, competences, cv_code, pe, id ,en_mission, afficher
                    FROM cis_nord_warwait.warwait
                    LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                    WHERE afficher='true'
                    ORDER BY skillmatrix.$search1 DESC";
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
                        WHERE afficher='true'
                        ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search2 DESC";
                    }
                
                } catch(PDOException $e) {
                    $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, positionnement, competences, cv_code, pe, id ,en_mission, afficher
                    FROM cis_nord_warwait.warwait
                    LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                    WHERE afficher='true'
                    ORDER BY skillmatrix.$search1 DESC";
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
                            ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search2 DESC, skillmatrix.$search3 DESC";
    
                        } catch(PDOException $e) {
                            $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, skillmatrix.$search2, positionnement, competences, cv_code,id , pe, en_mission, afficher
                            FROM cis_nord_warwait.warwait
                            LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                            WHERE afficher='true'
                            ORDER BY skillmatrix.$search1 DESC, skillmatrix.$search2 DESC";
                        }
                    
                    } catch(PDOException $e) {
                        $sql_join = "SELECT warwait.nom, grade, site, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, s14, s15, s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s26, s27, s28, s29, s30, s31, s32, s33, s34, s35, s36, s37, s38, s39, s40, s41, s42, s43, s44, s45, s46, s47, s48, s49, s50, s51, s52, reussite, skillmatrix.$search1, positionnement, competences, cv_code, pe, id ,en_mission, afficher
                        FROM cis_nord_warwait.warwait
                        LEFT JOIN skillmatrix.skillmatrix ON cis_nord_warwait.warwait.nom = skillmatrix.skillmatrix.name
                        WHERE afficher='true'
                        ORDER BY skillmatrix.$search1 DESC";
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
            $all_sql.=" FROM matrix_tech LEFT JOIN $table ON $table.name = matrix_tech.name";
            $stmt = $this->matrix->prepare($all_sql);
            $stmt->execute();
            $res = $stmt->fetchAll();

        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
        }
        return $res;
    }

    function get_skillmatrix() {
        $sql = "SELECT * from matrix_tech";
        $stmt = $this->matrix->prepare($sql);
        try {
            $stmt->execute();
            $res = $stmt->fetchAll(); 
        }
        catch(PDOException $e) {
            echo 'error : ' .$e->getMessage();
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
}


?>