<?php
namespace MIF\Admin;
class Multi_Insta_Feeds_Groups{
    private $db_groups = 'mif_groups';
    private $groups = array();

    function __construct(){
        //grab the list of existing groups
        $this -> groups = get_option($this->db_groups, array());
    }

    public function create_group(){
        $form_response = $_POST['select_account'];
        array_push($this->groups, $form_response);
        update_option($this->db_groups, $this->groups);
    }

    public function get_total_groups(){
        return count($this->groups);
    }

    public function get_user_list($group_index, $attr){
        $accounts = new Multi_Insta_Feeds_Accounts();
        $user_list = array();
        foreach($this->groups[$group_index] as $user_id){
            if(array_key_exists($user_id, $accounts ->get_account_list())){
                $user = $accounts -> get_account($user_id)[$attr];
                array_push($user_list, $user);
            }
        }
        return $user_list;
    }

    public function delete_group($group_index){
        settype($group_index, "integer");
        unset($this -> groups[$group_index]);
        $this -> groups = array_values($this -> groups);
        update_option($this->db_groups, $this->groups);
    }

}