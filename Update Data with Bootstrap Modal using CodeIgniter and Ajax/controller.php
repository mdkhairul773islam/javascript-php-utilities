
<?php class Field_doctorsk extends Admin_Controller {

    function __construct() {
        parent::__construct();
    }
    
    public function getAreaNameById(){
        $id = $this->input->post('id');
        $data = [];
        if(!empty($id)){
            $data = get_row('areas', ['id'=>$id, 'trash'=>0]);
        }
        
        echo json_encode($data);
    }

    // if you learn more than visit this link:  https://youtu.be/WzfOXuOZeHA
}