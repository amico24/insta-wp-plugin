<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       plugin_name.com/team
 * @since      1.0.0
 *
 * @package    PluginName
 * @subpackage PluginName/admin/partials
 */
namespace MIF\Admin;

$graph_api = new Multi_Insta_Feeds_Graph_API;
$graph_accounts = new Multi_Insta_Feeds_Graph_Accounts;
$graph_groups = new Multi_Insta_Feeds_Graph_Groups;
?>



<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>Multi Insta Feed Settings</h2>

    <hr>

    <h3>Input Short Lived Graph API Access Token</h3>

    <form method="POST">
        <input type="text" id="access_token" name="access_token">
        <button type="submit">Submit Access Token</button>
    </form>
    <?php if (isset($_POST['access_token'])): ?>
        <?php 
        $graph_api ->get_long_token($_POST['access_token']);
        ?>
    <?php endif ?>

    <p>Long Lived Access Token:</p>
    <input type="text" value="<?=get_option('mif_access_token', 'Token not found')?>" readonly>

    <p>
        Instagram User ID:
        <?= $graph_api->get_ig_id(); ?>
    </p>

    <hr>

    <h2>Connected Accounts</h2>
    <p>Add Business/Creator Account:</p>
    <form method="POST">
        <input type="text" id="ig_username" name="ig_username">
        <button type="submit">Find Account</button>
    </form>
    
    <?php if (isset($_POST['ig_username'])){
        if ($graph_api->account_exists($_POST['ig_username'])){
            $graph_accounts->add_account($_POST['ig_username']);
            new Multi_Insta_Feeds_Errors('Account Added.', 'notice-success');
        
        } else{
            new Multi_Insta_Feeds_Errors('Instagram account does not exist or is not a Business/Creator account.', 'notice-error');
        }        
    } ?>
       
    <hr>

    <h2>Create Group</h2>
    <form method="post">
        <?php foreach ($graph_accounts->get_accounts() as $user): ?>
            <input type="checkbox" id="select_business_account[]" name="select_business_account[]" value="<?= $user ?>">
            <label for="select_business_account[]">
                <?= $user ?>
            </label>
            <br>
        <?php endforeach ?>
        <br>

        <button type="submit"> Create Group with Selected Accounts </button>
    </form>

    <?php
        if (isset($_POST['select_business_account'])){
            $graph_groups->create_group();
            new Multi_Insta_Feeds_Errors('Group Created', 'notice-success');
        }
        ?>


</div>