<?php

namespace MIF\Admin;

class Multi_Insta_Feeds_Graph_Accounts {
    /**
     * Usernames of business accounts
     * @var array
     */
    private $accounts = array();

    /**
     * Name of database entry that stores connected account list
     * @var string
     */
    private $db_accounts = 'mif_accounts';

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
     * Adds an account data to database if it isn't already there
     * @param mixed $username Username of account to be added
     * @return void
     */
    function add_account($username){
        /*
        if(!in_array($username, $this -> accounts)){
            $this -> accounts[] = $username;
            update_option($this -> db_accounts, $this -> accounts);
        }*/

        if(!array_key_exists($username, $this -> accounts)){
            $api = new Multi_Insta_Feeds_Graph_API;
            $acc_data = $api -> retrieve_full_acc_data($username);
            $this -> accounts[$username] =  $acc_data;
            update_option($this -> db_accounts, $this -> accounts);
            //var_dump($this -> accounts);
            //die();
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
            return array_keys($this -> accounts);
        }
    }

    /**
     * Deletes account from array and updates database
     * @param string $username
     * @return void
     */
    function delete_account($username){
        unset($this -> accounts[$username]);
        update_option($this -> db_accounts, $this -> accounts);
    }

    /**
     * Returns array of media from specific account (only returns 5 most recent).
     * @param string $username
     * @return mixed
     */
    function get_media_list($username, $length = 5){
        //var_dump($this -> accounts);
        //die();
        $raw_media_list = $this -> accounts[$username]['media']['data'];
        $media_list = array_slice($raw_media_list, 0, $length);
        return $media_list;
    }

    /**
     * Returns full account data of an account
     * @param string $username
     * @return mixed
     */
    function get_account($username){
        return $this -> accounts[$username];
    }

    /**
     * Refreshes every account saved to update according to changes in the account.
     * Warning: Calling this makes the website lag a lot especially if there are a lot of accounts.
     * idk if theres a better way to do this
     * @return void
     */
    function refresh_accounts(){
        foreach($this->accounts as $username => $account){
            unset($this->accounts[$username]);
            $this -> add_account($username);
        }
    }
}