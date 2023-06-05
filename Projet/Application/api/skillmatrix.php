
<?php 



require_once('../lib/bdd.php');
$data = new BDD('salut');
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['devl'])) {
            $api = $data->get_devl_skillmatrix($_GET['devl']);
            echo json_encode($api);
        }
        elseif (isset($_GET['get_search'])) {
            $api = $data->get_search_for_matrix();
            echo json_encode($api);

        } else if (isset($_GET['search_bar_matrix'])) {
            $data->search_bar_matrix($_GET['search_bar_matrix']);
        } else {
            $api = $data->get_skillmatrix();
            echo json_encode($api);
            
        }
        break;

    case 'POST':

        break;
        
    case 'PUT':
        $body = file_get_contents('php://input');
        $array = json_decode($body,true);
        if (isset($array['search_matrix'])) {
            $data->set_search_for_matrix($array['search_matrix']);
        } else if (isset($array['search_bar_matrix'])) {
            $data->search_bar_matrix($array['search_bar_matrix']);
        }
        break;


}



?>