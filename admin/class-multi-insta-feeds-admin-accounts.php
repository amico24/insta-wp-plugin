<?php
namespace MIF\Admin;
class Multi_Insta_Feeds_Accounts {

    /*
    accounts are saved as individual arrays within one big array which is saved in one option(mif_insta_response)
    apparently saving it in a single option is better bc it lessens the number of queries the program has to make to sql
    should probably change the name of the option (it made more sense when i was testing this but i forgot to change it)
    only user id and access token are saved under the account data (so far)
    update: will save time of creation for tokens to calculate time before expiry of token
    big updates: now storing username, now uses user id as index instead of default
    */
    /**
     * Array of Accounts 
     * @var array
     */
    private $accounts = array();

    /**
     * Name of database entry where accounts are stored
     * @var string
     */
    private $db_accounts = 'mif_insta_response';

    /**
     * Grab list of already existing accounts from database
     */
    function __construct(){

        //update list of accounts with already existing accounts

        $this -> accounts = get_option($this->db_accounts, null);
    }
    
    /**
     * Add new account to accounts array with the following info
     * 
     * @param string $user_id
     * @param string $access_token
     * @param int $creation_time
     * @param string $username
     * @return void
     */
    function add_account($user_id, $access_token, $creation_time, $username){
        $this -> accounts [$user_id] = array(
            'user_id' => $user_id,
            'access_token' => $access_token,
            'creation_time' => $creation_time,
            'username' => $username
        );
    }

    /**
     * Change or update existing account info
     * 
     * @param string $user_id
     * @param array $args Updated account attributes
     * @return void
     */
    function update_account($user_id, $args){
        $this->accounts[$user_id] = $args;
        
        update_option($this ->db_accounts, $this -> accounts);

    }
    
    /**
     * Returns info of specific account
     * @param string $user_id
     * @return array
     */
    function get_account($user_id){
        return $this -> accounts[$user_id];
    }

    /**
     * Deletes account from accounts array and database
     * @param string $user_id
     * @return void
     */
    function delete_account($user_id){
        unset($this -> accounts[$user_id]);
        update_option($this ->db_accounts, $this -> accounts);
    }

    /**
     * Returns entire array of accounts
     * @return array
     */
    function get_account_list(){
        return $this -> accounts;
    }

    /**
     * Accesses Instagram API to get username of account
     * @param string $access_token
     * @return string
     */
    function get_account_username($access_token){
        
        $raw_response = wp_remote_get('https://graph.instagram.com/me?fields=username&access_token='.$access_token);
        $response = json_decode($raw_response['body'], true);

        if(!array_key_exists('username', $response)){
            new Multi_Insta_Feeds_Errors($response['error']['error_message'], 'notice-error');
            return 'Error encountered';
        } else{
            return $response['username'];
        }
    }

    
    /**
     * Returns array of user id's which are used as array keys for each account
     * @return array
     */
    function get_key_list(){
        return array_keys($this -> accounts);
    }
}