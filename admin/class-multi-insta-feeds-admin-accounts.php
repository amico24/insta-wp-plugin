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
    private $accounts = array();

    private $db_accounts = 'mif_insta_response';

    function __construct(){

        //update list of accounts with already existing accounts

        $this -> accounts = get_option($this->db_accounts, null);
    }
    
    function add_account($user_id, $access_token, $creation_time, $username){
        $this -> accounts [$user_id] = array(
            'user_id' => $user_id,
            'access_token' => $access_token,
            'creation_time' => $creation_time,
            'username' => $username
        );
    }

    function update_account($user_id, $args){
        $this->accounts[$user_id] = $args;
        
        update_option($this ->db_accounts, $this -> accounts);

    }

    function get_total_accounts(){
        return count($this -> accounts);
    }
    
    function get_account($user_id){
        return $this -> accounts[$user_id];
    }

    function delete_account($user_id){
        unset($this -> accounts[$user_id]);
        update_option($this ->db_accounts, $this -> accounts);
    }

    function get_account_list(){
        return $this -> accounts;
    }

    function get_account_username($access_token){
        
        $raw_response = wp_remote_get('https://graph.instagram.com/me?fields=username&access_token='.$access_token);
        $response = json_decode($raw_response['body'], true);

        if(array_key_exists('error', $response)){
            return 'error encountered';
        } else{
            return $response['username'];
        }
    }

    function get_key_list(){
        return array_keys($this -> accounts);
    }
}