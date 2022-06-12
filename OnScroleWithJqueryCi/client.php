<?php

class Client extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('action');
        $this->load->model('retrieve');
        $this->load->library('upload');
    }

    public function index()
    {
        $this->data['meta_title']   = 'add';
        $this->data['active']       = 'data-target="client_menu"';
        $this->data['subMenu']      = 'data-target="add"';
        $this->data['confirmation'] = $type = null;

        $this->data['clientInfo'] = $this->action->read("parties", array('type' => 'client', 'trash' => 0));

        // get all godowns
        $this->data['allGodowns'] = getAllGodown();

        if (isset($_POST['add']) && !empty($_POST['godown_code'])) {

            $godown_code   = $_POST['godown_code'];
            $gPrefix       = get_name('godowns', 'prefix', ['code' => $_POST['godown_code']]);
            $customer_type = $this->input->post('customer_type');

            $query          = $this->db->query("SELECT
                                     COUNT(CODE) AS count_customer
                                        FROM
                                            `parties`
                                        WHERE
                                            `type` = 'client' AND `customer_type` = '$customer_type'  and godown_code = '$godown_code'"
            );
            $count_customer = $query->row();

            $counter = $count_customer->count_customer + 1;

            if (strlen($counter) == 1) {
                $counter = $type . '00000' . $counter;
            } elseif (strlen($counter) == 2) {
                $counter = $type . '0000' . $counter;
            } elseif (strlen($counter) == 3) {
                $counter = $type . '000' . $counter;
            } elseif (strlen($counter) == 4) {
                $counter = $type . '00' . $counter;
            } elseif (strlen($counter) == 5) {
                $counter = $type . '0' . $counter;
            } else {
                $counter = $counter;
            }

            $code = $counter;
            if ($_POST['customer_type'] == 'hire') {
                $client_code = $gPrefix . 'H' . $code;
            } elseif ($_POST['customer_type'] == 'weekly') {
                $client_code = $gPrefix . 'W' . $code;
            } else {
                $client_code = $gPrefix . 'D' . $code;
            }

            $guarantorOneName = ($this->input->post('previous_guarantor_one') != null) ? $this->input->post('previous_guarantor_one') : $this->input->post('guarantor_name');
            $guarantorTwoName = ($this->input->post('previous_guarantor_two') != null) ? $this->input->post('previous_guarantor_two') : $this->input->post('guarantor_name2');
            $data             = array(
                'opening'            => date('Y-m-d'),
                'code'               => $client_code,
                'godown_code'        => $this->input->post('godown_code'),
                'name'               => $this->input->post('name'),
                'zone'               => $this->input->post('zone'),
                'father_name'        => $this->input->post('father_name'),
                'mobile'             => $this->input->post('contact'),
                'address'            => $this->input->post('address'),
                'id_card'            => $this->input->post('id_card'),
                'guarantor_name'     => $guarantorOneName,
                'guarantor_code'     => $this->input->post('guarantor_code'),
                'guarantor_mobile'   => $this->input->post('guarantor_mobile'),
                'guarantor_address'  => $this->input->post('guarantor_address'),
                'guarantor_name2'    => $guarantorTwoName,
                'guarantor_code2'    => $this->input->post('guarantor_code2'),
                'guarantor_mobile2'  => $this->input->post('guarantor_mobile2'),
                'guarantor_address2' => $this->input->post('guarantor_address2'),
                'type'               => "client",
                'customer_type'      => $this->input->post('customer_type'),
                'path'               => file_upload('attachFile', 'customer', '', 'customer'),
                'initial_balance'    => ($_POST['status'] == 'payable') ? (0 - $this->input->post('balance')) : $this->input->post('balance'),
                'credit_limit'       => $this->input->post('credit_limit')
            );

            if (!empty($_POST['dealer_type'])) {
                $data['dealer_type'] = $this->input->post('dealer_type');
            }

            // insert data into parties table
            $options = array(
                'title' => 'success',
                'emit'  => 'Customer Successfully Saved. Your Customer ID is : ' . '<b>' . $client_code . '</b>',
                'btn'   => true
            );

            $options2 = array(
                'title' => 'warning',
                'emit'  => 'This Client already exists !',
                'btn'   => true
            );

            $showroom     = $this->input->post('godown_code');
            $clientType   = $this->input->post('customer_type');
            $clientMobile = $this->input->post('contact');

            $status = $this->action->exists("parties", array('godown_code' => $showroom, 'customer_type' => $clientType, 'mobile' => $clientMobile, 'trash' => 0));

            if ($status == false) {
                $this->data['confirmation'] = message($this->action->add("parties", $data), $options);
            } else {
                $this->data['confirmation'] = message('warning', $options2);
            }

            $this->session->set_flashdata("confirmation", $this->data['confirmation']);
            redirect("client/client", "refresh");
        }

        $this->load->view($this->data['privilege'] . '/includes/header', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/aside', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/headermenu', $this->data);
        $this->load->view('components/client/nav', $this->data);
        $this->load->view('components/client/add', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/footer');
    }


    public function view_all()
    {
        $this->data['meta_title'] = 'all';
        $this->data['active']     = 'data-target="client_menu"';
        $this->data['subMenu']    = 'data-target="all"';

        // get all godown
        $this->data['allGodowns'] = getAllGodown();

        $this->load->view($this->data['privilege'] . '/includes/header', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/aside', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/headermenu', $this->data);
        $this->load->view('components/client/nav', $this->data);
        //$this->load->view('components/client/view-all', $this->data);
        $this->load->view('components/client/onscroll_client', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/footer');
    }


    public function view_all_scroll()
    {
        $this->data['meta_title'] = 'all';
        $this->data['active']     = 'data-target="client_menu"';
        $this->data['subMenu']    = 'data-target="all"';

        $this->data['allGodowns'] = getAllGodown();
        $where                    = array('trash' => 0, 'type' => 'client', 'godown_code' => $_POST['godown_code']);
        $getData                  = $this->action->read('parties', $where);

        $this->load->view($this->data['privilege'] . '/includes/header', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/aside', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/headermenu', $this->data);
        $this->load->view('components/client/nav', $this->data);
        $this->load->view('components/client/onscroll_client', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/footer');
    }


    public function allClientList()
    {
        $where = [
            'parties.type' => 'client', 
            'parties.trash' => 0
        ];


        if (!empty($_POST['godown_code'])) {
            if($_POST['godown_code'] != 'all'){
                $where['parties.godown_code'] = $_POST['godown_code'];
            }
        }else{
             $where['parties.godown_code'] = $this->data['branch'];
        }

        if (!empty($_POST['code'])) {
              $where['parties.code'] = $_POST['code'];
        }
        
        if (!empty($_POST['zone'])) {
              $where['parties.zone'] = $_POST['zone'];
        }

        if (!empty($_POST['customer_type'])) {
            $where['parties.customer_type'] = $_POST['customer_type'];
        }
        
        $limit  = $_POST['limit'];
        $offset = $_POST['offset'];

        // get all client
        $select     = ['parties.code', 'parties.path', 'parties.name', 'parties.dealer_type', 'parties.customer_type', 'parties.zone', 'parties.address', 'parties.mobile', 'godowns.name AS godown_name'];
        $clientList = get_join('parties', 'godowns', 'parties.godown_code=godowns.code', $where, $select, '', '', '', $limit, $offset);
        
        
        $results = [];  
        if(!empty($clientList)){
            foreach($clientList as $row){
                
                $balanceInfo = get_client_balance($row->code);
                
                $action = '';
                $item = [];
                
                $item['code']          = $row->code;
                $item['avatar']        = (!empty($row->path) ? '<img src="'.site_url($row->path).'" width="60">' : '');
                $item['name']          = $row->name;
                $item['dealer_type']   = filter($row->dealer_type);
                $item['customer_type'] = filter($row->customer_type);
                $item['zone']          = filter($row->zone);
                $item['address']       = $row->address;
                $item['mobile']        = $row->mobile;
                $item['balance']       = abs($balanceInfo['balance']);
                $item['status']        = $balanceInfo['status'];
                $item['main_balance']  = $balanceInfo['balance'];
                $item['godown_name']   = $row->godown_name;
                
                
                $action .= '<a title="View" class="btn btn-info"  href="'. site_url("client/client/preview?partyCode=$row->code") .'"><i class="fa fa-eye" aria-hidden="true"></i></a>&nbsp;';
                $action .= '<a title="Edit" class="btn btn-warning"  href="'. site_url("client/client/edit?partyCode=$row->code") .'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp;';
                $action .= '<a title="Delete" class="btn btn-danger" onclick="return confirm("Do you want to delete this data?");"  href="'. site_url("client/client/delete/$row->code") .'"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                
                $item['action'] = $action;
                
                array_push($results, (object)$item);
            }
        }
        
        echo json_encode($results);
    }


    public function preview()
    {
        $this->data['meta_title'] = 'view';
        $this->data['active']     = 'data-target="client_menu"';
        $this->data['subMenu']    = 'data-target="all"';

        $this->data['partyInfo'] = get_row_join("parties", 'godowns', 'godowns.code=parties.godown_code', ['parties.code' => $_GET['partyCode']], ['parties.*', 'godowns.name as godown_name']);

        $this->data['commitments'] = get_result('commitments', ['party_code' => $_GET['partyCode']]);
        $this->data['sales']       = get_result('saprecords', ['party_code' => $_GET['partyCode']]);

        $this->load->view($this->data['privilege'] . '/includes/header', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/aside', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/headermenu', $this->data);
        $this->load->view('components/client/nav', $this->data);
        $this->load->view('components/client/preview', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/footer');
    }


    public function edit()
    {
        $this->data['meta_title']   = 'edit';
        $this->data['active']       = 'data-target="client_menu"';
        $this->data['subMenu']      = 'data-target="all"';
        $this->data['confirmation'] = null;
        
        
        

        $where = array("code" => $this->input->post("client_code"));
        $code  = $this->input->get('partyCode');

        if (isset($_POST['update'])) {

            if ($this->input->post('status') == 'receivable') {
                $initial_balance = $this->input->post('initial_balance');
            } else {
                $initial_balance = 0 - $this->input->post('initial_balance');
            }

            $photo = NULL;

            //Image Upload Start here
            if ($_FILES["attachFile"]["name"] != null or $_FILES["attachFile"]["name"] != "") {

                $config['upload_path']   = './public/customer';
                $config['allowed_types'] = 'png|jpeg|jpg|gif';
                $config['max_size']      = '4096';
                $config['max_width']     = '3000'; /* max width of the image file */
                $config['max_height']    = '3000';
                $config['file_name']     = "employee_" . rand(1111, 99999);
                $config['overwrite']     = true;

                $this->upload->initialize($config);

                if ($this->upload->do_upload("attachFile")) {
                    $upload_data = $this->upload->data();
                    $photo       = "public/customer/" . $upload_data['file_name'];
                }

            }
            //Image Upload End here

            if ($photo != NULL) {
                $data = array(
                    'name'               => $this->input->post('name'),
                    'zone'               => $this->input->post('zone'),
                    'father_name'        => $this->input->post('father_name'),
                    'mobile'             => $this->input->post('contact'),
                    'address'            => $this->input->post('address'),
                    'id_card'            => $this->input->post('id_card'),
                    'guarantor_name'     => $this->input->post('guarantor_name'),
                    'guarantor_mobile'   => $this->input->post('guarantor_mobile'),
                    'guarantor_address'  => $this->input->post('guarantor_address'),
                    'guarantor_name2'    => $this->input->post('guarantor_name2'),
                    'guarantor_mobile2'  => $this->input->post('guarantor_mobile2'),
                    'guarantor_address2' => $this->input->post('guarantor_address2'),
                    //'customer_type'     => $this->input->post('customer_type'),
                    'initial_balance'    => $initial_balance,
                    'credit_limit'       => $this->input->post('credit_limit'),
                    "path"               => $photo
                );
            } else {
                $data = array(
                    'name'               => $this->input->post('name'),
                    'zone'               => $this->input->post('zone'),
                    'father_name'        => $this->input->post('father_name'),
                    'mobile'             => $this->input->post('contact'),
                    'address'            => $this->input->post('address'),
                    'id_card'            => $this->input->post('id_card'),
                    'guarantor_name'     => $this->input->post('guarantor_name'),
                    'guarantor_mobile'   => $this->input->post('guarantor_mobile'),
                    'guarantor_address'  => $this->input->post('guarantor_address'),
                    'guarantor_name2'    => $this->input->post('guarantor_name2'),
                    'guarantor_mobile2'  => $this->input->post('guarantor_mobile2'),
                    'guarantor_address2' => $this->input->post('guarantor_address2'),
                    'initial_balance'    => $initial_balance,
                    //'customer_type'     => $this->input->post('customer_type'),
                    'credit_limit'       => $this->input->post('credit_limit')
                );
            }

            if (!empty($_POST['dealer_type'])) {
                $data['dealer_type'] = $this->input->post('dealer_type');
            }

            $options = array(
                'title' => 'success',
                'emit'  => 'Customer Successfully Update!',
                'btn'   => true
            );

            $options2 = array(
                'title' => 'warning',
                'emit'  => 'This Client already exists !',
                'btn'   => true
            );

            $showroom     = $this->input->post('godown_code');
            $clientType   = $this->input->post('customer_type');
            $clientMobile = $this->input->post('contact');

            /* $existsWhere = array('code !=' => $code, 'godown_code' => $showroom, 'customer_type' => $clientType, 'mobile' => $clientMobile, 'trash' => 0);
             //print_r($existsWhere);
             $status = $this->action->exists("parties", $existsWhere);*/

            //if($status == false){
            $this->data['confirmation'] = message($this->action->update('parties', $data, $where), $options);
            /*  }else{
                  $this->data['confirmation'] = message('warning', $options2);
              }*/

            $this->session->set_flashdata("confirmation", $this->data["confirmation"]);
            redirect("client/client/edit?partyCode=$code", "refresh");
        }

        $this->data['info'] = $this->action->read("parties", array('code' => $code));

        $this->load->view($this->data['privilege'] . '/includes/header', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/aside', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/headermenu', $this->data);
        $this->load->view('components/client/nav', $this->data);
        $this->load->view('components/client/edit', $this->data);
        $this->load->view($this->data['privilege'] . '/includes/footer');
    }

    /**
     * table: partytransaction,partytransactionmeta,saprecords,sapitems,parties
     *
     * update sapmeta table using voucher-number from saprecords
     * update sapitems table using voucher-number from saprecords
     * update saprecords table using party-code
     *
     * update partytransactionmeta table using transaction_id from partytransaction:id
     * update partytransaction table using party_code
     *
     * update partybalance table using code
     * update partymeta table using party_code
     * update parties table using code
     *
     */
    public function delete($id)
    {
        $data = array('trash' => 1);

        // update sapmeta, sapitems and saprecords table using voucher-number from saprecords 
        $where  = array('party_code' => $id);
        $sapRec = $this->action->read('saprecords', $where);

        if ($sapRec != null) {
            foreach ($sapRec as $key => $value) {
                $where = array('voucher_no' => $value->voucher_no);

                // update sapmeta
                $this->action->update('sapmeta', $data, $where);

                // update sapitems
                $this->action->update('sapitems', $data, $where);
            }

            // update saprecords
            $where = array('party_code' => $id);
            $this->action->update('saprecords', $data, $where);
        }

        // update partytransactionmeta table using transaction_id from partytransaction:id 
        $transactionRec = $this->action->read('partytransaction', $where);
        if ($transactionRec != null) {
            foreach ($transactionRec as $key => $value) {
                $where = array('transaction_id' => $value->id);
                $this->action->update('partytransactionmeta', $data, $where);
            }
        }

        // update partytransaction table using party_code 
        $where = array('party_code' => $id);
        $this->action->update('partytransaction', $data, $where);


        // update parties table using code
        $where = array('code' => $id);
        $this->action->update('parties', $data, $where);

        $option = array(
            "title" => "Deleted",
            "emit"  => "Customer Successfully Deleted!",
            "btn"   => true
        );

        $this->session->set_flashdata('confirmation', message("danger", $option));

        redirect('client/client/view_all', 'refresh');
    }
    
    
    public function clientList(){
        
        $option = '<option value="" selected>Select Client</option>';
        
        if(!empty($_POST['godown_code'])){
            
            $where = ['type' => 'client', 'trash' => 0];
            
            if($_POST['godown_code'] != 'all'){
                $where['godown_code'] = $_POST['godown_code'];
            }
            
            $results = get_result('parties', $where, ['code', 'name', 'mobile', 'address']);
            
            if(!empty($results)){
                foreach($results as $item){
                    $option .= '<option value="'. $item->code .'">'. $item->code .' - '. $item->name .' - '. $item->mobile .' - '. $item->address .'</option>';
                }
            }
        }
        
        echo $option;
    }
    
    public function zoneList(){
        
        $option = '<option value="" selected>Select Zone</option>';
        
        if(!empty($_POST['godown_code'])){
            
            $where = [];
            
            if($_POST['godown_code'] != 'all'){
                $where['godown_code'] = $_POST['godown_code'];
            }
            
            $results = get_result('zone', $where);
            
            if(!empty($results)){
                foreach($results as $item){
                    $option .= '<option value="'. $item->zone .'">'. filter($item->zone) .'</option>';
                }
            }
        }
        
        echo $option;
    }
}
