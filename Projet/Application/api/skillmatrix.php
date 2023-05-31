
<?php 



require_once('../lib/bdd.php');
$data = new BDD('salut');
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['devl'])) {
            $api = $data->get_devl_skillmatrix($_GET['devl']);
            echo json_encode($api);
        }
        else {
            $api = $data->get_skillmatrix();
            echo json_encode($api);
            
        }
        break;

    case 'POST':

        break;
        
    case 'PUT':
        $body = file_get_contents('php://input');
        $array = json_decode($body,true);
        $api = $data->update_warwait($array['nom'],$array['semaine'],$array['value']); 
        echo json_encode($api);
        break;


}



?>