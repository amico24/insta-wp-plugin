<?php

namespace MIF\Admin;

class Multi_Insta_Feeds_Graph_Groups{
    /**
     * Array of currently existing groups. Groups are stored as just arrays of usernames.
     * @var array
     */
    private $groups;

    /**
     * Name of database entry that stores group list.
     * @var string
     */
    private $db_groups = 'mif_business_groups';

    /**
     * Grabs already existing arrays from database. If database is empty, initializes the variable to an array to avoid errors.
     */
    function __construct(){
        $this -> groups = get_option($this -> db_groups, array());
        if(empty($this -> groups)){
            $this -> groups = array();
        }
    }

    /**
     * Creates new group based on selected usernames in post request
     * @return void
     */
    function create_group(){
        $form_response = $_POST['select_business_account'];
        array_push($this->groups, $form_response);
        update_option($this -> db_groups, $this->groups);
    }

    /**
     * Gets number of existing groups
     * @return int
     */
    function get_total_groups(){
        return count($this -> groups);
    }

    /**
     * Returns array of users in a specific group.
     * @param int $group_index
     * @return array
     */
    function get_user_list($group_index){
        return $this -> groups[$group_index];
    }

    /**
     * Deletes a specific group from array and updates database.
     * @param int $group_index
     * @return void
     */
    public function delete_group($group_index){
        unset($this -> groups[$group_index]);
        $this -> groups = array_values($this -> groups);
        update_option($this -> db_groups, $this->groups);
    }

}