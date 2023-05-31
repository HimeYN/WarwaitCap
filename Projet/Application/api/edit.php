
<?php 



require_once('../lib/bdd.php');
$data = new BDD('salut');
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['searched'])) {
            $api = $data->get_warwait_searched_edit($_GET['searched']);
            echo json_encode($api);
        }
        else {
            $api = $data->get_warwait_edit();
            echo json_encode($api);
            
        }
        break;

    case 'POST':
        if (isset($_POST['delete'])) {
            try {
                $data->delete($_POST['delete']);
                echo json_encode(array($_POST['delete']));
            } catch (PDOException) {
                echo json_encode(array('FAIL'));
            }

        }

        break;
        
    case 'PUT':
        $body = file_get_contents('php://input');
        $array = json_decode($body,true);
        if (count($array)==3) {
            $api = $data->update_warwait($array['nom'],$array['semaine'],$array['value']); 
            echo json_encode($api);
            break;
        } elseif (count($array)==2) {
            $api  = $data->update_mission_state($array['id'],$array['misison_etat']);
            echo json_encode($api);
        } else {
            if (isset($array['checked-id'])) {
                $api = $data->change_checked_status($array['checked-id']);
                echo json_encode($api);
            } elseif (isset($array['collab'])) {
                $api = $data->add_collab($array['collab']);
                echo json_encode($api);

            } elseif (isset($array['delete'])) {
                $data->delete($array['delete']);
                echo json_encode(array($array['delete']));
            } elseif (isset($array['sucessrate'])) {
                $api = $data->update_sucess($array['sucessrate']);
                echo json_encode($api);
            } elseif (isset($array['grade'])) {
                $api = $data->update_grade($array['grade']);
                echo json_encode($api);
            } elseif (isset($array['site'])) {
                $api = $data->update_site($array['site']);
                echo json_encode($api);
            } elseif (isset($array['positionnement'])) {
                $api = $data->update_positionnement($array['positionnement']);
                echo json_encode($api);
            } elseif (isset($array['competences'])) {
                $api = $data->update_competences($array['competences']);
                echo json_encode($api);
            } elseif (isset($array['week_clear'])) {
                $api = $data->clear_week($array['week_clear']);
                echo json_encode($api);
            }
        }
        

}



?>