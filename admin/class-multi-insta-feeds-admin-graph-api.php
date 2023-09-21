<?php
//RAAAAAAAAGGGGGGHHHHHHHHH
namespace MIF\Admin;

class Multi_Insta_Feeds_Graph_API{

    /**
     * API Access token
     * @var string
     */
    private $access_token;

    /**
     * Instagram User ID of admin account
     * @var string
     */
    private $ig_user_id;

    /**
     * Client ID of Facebook app
     * @var string
     */
    private $client_id = 'sample';

    /**
     * App secret of Facebook app
     * @var string
     */
    private $app_secret = 'sample';

    /**
     * Name of database entry that stores long-lived access token
     * @var string
     */
    private $db_access_token = 'mif_access_token';

    /**
     * Name of database entry that stores admin Instagram user ID
     * @var string
     */
    private $db_ig_user_id = 'mif_ig_user_id';

    /**
     * Initializes Graph API class
     */
    function __construct(){
        $this -> access_token = get_option($this -> db_access_token,'');
        $this -> ig_user_id = get_option($this -> db_ig_user_id,'');
    }

    /**
     * Retrieves long lived access token from short lived one and stores it in database
     * @param string $short_token
     * @return void
     */
    function get_long_token($short_token){
        $token_response_json = wp_remote_get('https://graph.facebook.com/v18.0/oauth/access_token?grant_type=fb_exchange_token&client_id='.$this->client_id.'&client_secret='.$this->app_secret.'&fb_exchange_token='.$short_token);
        $token_response = json_decode($token_response_json['body'], true);
        if (!array_key_exists('access_token', $token_response)){
            new Multi_Insta_Feeds_Errors($token_response['error']['message'], 'notice-error');
        } else {
            $this -> access_token = $token_response['access_token'];
            new Multi_Insta_Feeds_Errors('Access Token Updated', 'notice-success');
            update_option($this -> db_access_token,$token_response['access_token']);
        }
    }


    /**
     * Saves Instagram ID for when access token is updated
     * @return void
     */
    function retrieve_ig_id(){
        $fb_page_data_json = wp_remote_get('https://graph.facebook.com/v18.0/me/accounts?access_token='.$this -> access_token);
        $fb_page_data = json_decode($fb_page_data_json['body'], true);
        //var_dump($fb_page_data);
        if(array_key_exists('error',$fb_page_data)){
            new Multi_Insta_Feeds_Errors($fb_page_data['error']['message'], 'notice-error');
        }else {
            //var_dump($fb_page_data);
            //die('end');
            $fb_page_id = $fb_page_data['data'][0]['id'];
            $insta_acc_data_json = wp_remote_get('https://graph.facebook.com/v18.0/'.$fb_page_id.'?fields=instagram_business_account&access_token='.$this -> access_token);
            $insta_acc_data = json_decode($insta_acc_data_json['body'], true);
            //var_dump($insta_acc_data);
            //die('end');
            if(array_key_exists('error',$insta_acc_data)){
                new Multi_Insta_Feeds_Errors($insta_acc_data['error']['message'], 'notice-error');
            }else{
                //$this -> ig_user_id = $insta_acc_data['instagram_business_account']['id'];
                update_option($this -> db_ig_user_id, $insta_acc_data['instagram_business_account']['id']);
            }
        }
    }

    /**
     * Checks if account is accesible. False if account does not exist or if account is not a business/creator account
     * @param string $username Username of Instragram account
     * @return bool
     */
    function account_exists($username){
        $search_result_json = wp_remote_get('https://graph.facebook.com/v18.0/'.$this -> ig_user_id.'?fields=business_discovery.username('.$username.')&access_token='.$this -> access_token);
        $search_result = json_decode($search_result_json['body'], true);
        if(array_key_exists('error', $search_result)){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns Instagram User ID of admin account
     * @return string
     */
    function get_ig_id(){
        if(empty($this -> ig_user_id)){
            return 'Instagram ID not found';
        }
        else {
            return $this -> ig_user_id;
        }
    }

    /**
     * Calls api and returns all required information from an account.
     * @param string $username
     * @return mixed
     */
    function retrieve_full_acc_data($username){
        $acc_data_json = wp_remote_get('
        https://graph.facebook.com/v18.0/'.$this -> ig_user_id.'?fields=business_discovery.username('.$username.')%7Busername%2Cname%2Cprofile_picture_url%2Cbiography%2Cmedia%7Bmedia_url%2Cid%2Cusername%2Ctimestamp%2Cmedia_type%7D%7D&access_token='.$this -> access_token);
        if(is_wp_error($acc_data_json)){
            var_dump($acc_data_json -> get_error_message());
            die();
        }else{
            $acc_data = json_decode($acc_data_json['body'], true);
            if(array_key_exists('error', $acc_data)){
                new Multi_Insta_Feeds_Errors($acc_data['error']['message'], 'notice-error');
            } else {
                return $acc_data['business_discovery'];
            }
        }
    }

}