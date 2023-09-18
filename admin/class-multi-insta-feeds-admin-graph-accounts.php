<?php

namespace MIF\Admin;

class Multi_Insta_Feeds_Graph_Accounts {
    /**
     * Usernames of business accounts
     * @var array
     */
    private $accounts;

    /**
     * Name of database entry that stores connected account list
     * @var string
     */
    private $db_accounts = 'mif_business_accounts';

    /**
     * Grabs already connected accounts from database.
     */
    function __construct(){
        $this -> accounts = get_option($this -> db_accounts, array());
        if(empty($this -> accounts)){
            $this -> accounts = array();
        }
    }

    /**
     * Adds an account username to database if it isn't already there
     * @param mixed $username Username of account to be added
     * @return void
     */
    function add_account($username){
        if(!in_array($username, $this -> accounts)){
            $this -> accounts[] = $username;
            update_option($this -> db_accounts, $this -> accounts);
        }
    }

    /**
     * Returns array of usernames of connected accounts.
     * @return array
     */
    function get_accounts(){
        if(empty($this -> accounts)){
            return array();
        } else {
            return $this -> accounts;
        }
    }

    /**
     * Deletes account from array and updates database
     * @param mixed $username
     * @return void
     */
    function delete_account($username){
        if (($key = array_search($username, $this -> accounts)) !== false) {
            unset($this -> accounts[$key]);
            update_option($this -> db_accounts, $this -> accounts);
        }
    }

}