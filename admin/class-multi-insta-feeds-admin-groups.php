<?php
namespace MIF\Admin;
class Multi_Insta_Feeds_Groups{
    /**
     * name of database entry which stores groups data
     * @var string
     */
    private $db_groups = 'mif_groups';

    /**
     * Array of groups and the users included in each group
     * @var array
     */
    private $groups = array();

    /**
     * Grab the list of existing groups from database
     */
    function __construct(){
        $this -> groups = get_option($this->db_groups, array());
    }

    /**
     * Create new group based on selected form response
     * @return void
     */
    public function create_group(){
        $form_response = $_POST['select_account'];
        array_push($this->groups, $form_response);
        update_option($this->db_groups, $this->groups);
    }

    /**
     * Returns the total number of groups
     * @return int
     */
    public function get_total_groups(){
        return count($this->groups);
    }

    /**
     * Grabs list of users in a specific group based on specific user attribute
     * @param int $group_index
     * @param string $attr user attribute to return: user_id, username, access_token, or creation_time
     * @return array
     */
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

    /**
     * Deletes group from groups array and database
     * @param int $group_index
     * @return void
     */
    public function delete_group($group_index){
        unset($this -> groups[$group_index]);
        $this -> groups = array_values($this -> groups);
        update_option($this->db_groups, $this->groups);
    }

}