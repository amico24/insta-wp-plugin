<?php

namespace MIF\Admin;
class Multi_Insta_Feeds_API_Connect {

    protected $args = array(
        'client_id' => '192229297054716',
        'client_secret' => '0704eaf67e4db60ca19275c7b27548c3',
        'code' => '',
        'grant_type' => 'authorization_code',
        'redirect_uri' => 'https://localhost/wp-admin/index.php'
    );

    private $response;

    function retrieve_access_token(){
        $accounts = new Multi_Insta_Feeds_Accounts;
        
        //get auth code from query string
        $auth_code = trim($_GET['code'], '#_');
        $this -> args['code'] = $auth_code;

        //make post request to get access token
        $json_response = wp_remote_post('https://api.instagram.com/oauth/access_token', array('body' => $this -> args));

        //isolate body of reponse and make it an array
        //i only found out later that theres a function specifically for this but it messed up the code so im not using it lmao
        $response = json_decode($json_response['body'], true);

        $json_token_response = wp_remote_get('https://graph.instagram.com/access_token?grant_type=ig_exchange_token&client_secret=0704eaf67e4db60ca19275c7b27548c3&access_token='.$response['access_token']);
			
        $token_response = json_decode($json_token_response['body'], true);

        //this error checking is not it dawg i need to fix this later
        if (array_key_exists('error', $response)){
            update_option('mif_error', $response);
        } elseif (array_key_exists('error', $token_response)){
            update_option('mif_error', $token_response);
        } else {
            update_option('mif_error', null);

            //adding new account to list
            $accounts -> add_account($response['user_id'], $token_response['access_token'], time(), $accounts -> get_account_username($token_response['access_token']));
            
            //updating options to include entire account list
            update_option('mif_insta_response', $accounts -> get_account_list());
        }

    }

    function refresh_access_token($user_id){
        $accounts = new Multi_Insta_Feeds_Accounts;
        $current_acc = $accounts->get_account($user_id);

        $json_refresh_response = wp_remote_get('https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token='.$accounts -> get_account($user_id)['access_token']);

        $refresh_response = json_decode($json_refresh_response['body'], true);

        //update database with new token (idk if the token already changes when u refresh it but i added this jic)
        $accounts->update_account($user_id, array(
            'user_id' => $current_acc['user_id'],
            'access_token' => $refresh_response['access_token'],
            'creation_time' => time(),
            'username' => $current_acc['username']
        ));

    }

    function get_media_list($user_id){
        $accounts = new Multi_Insta_Feeds_Accounts;
        $current_acc = $accounts->get_account($user_id);
        $json_media_response = wp_remote_get('https://graph.instagram.com/me/media?fields=id,media_type,media_url,timestamp,username&access_token='. $current_acc['access_token']);

        
        $media_response = json_decode($json_media_response['body'], true);
        if (array_key_exists('error', $media_response)){
            update_option('mif_error', $media_response);
            return 'error';
        }else {
            return $media_response['data'];
        }
    }

    function get_image_url($user_id){
        $media_list = $this -> get_media_list($user_id);
        return $media_list[0]['media_url'];
    }

}