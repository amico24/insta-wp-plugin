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

$accounts = new Multi_Insta_Feeds_Accounts;
$groups = new Multi_Insta_Feeds_Groups;
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>Multi Insta Feed Settings</h2>
    <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
    <?php settings_errors(); ?>

    <br>

    <!-- made the link with an anchor bc query strings didnt save when i tried button -->
    <a
        href="https://api.instagram.com/oauth/authorize?client_id=192229297054716&redirect_uri=https://localhost/wp-admin/index.php&scope=user_profile,user_media&response_type=code">Add
        Account</a>

    <div>
        <h3>Create Group</h3>
        <form method="post">
            <?php $keys = $accounts->get_key_list() ?>
            <?php foreach ($keys as $user_id): ?>
                <input type="checkbox" id="select_account[]" name="select_account[]" value="<?= $user_id ?>">
                <label for="select_account[]">
                    <?= $accounts->get_account($user_id)['username'] ?>
                </label>
                <br>
            <?php endforeach ?>
            <br>

            <button type="submit"> Create Group with Selected Accounts </button>
        </form>

        <?php
        if (isset($_POST['select_account'])): ?>
            <p> Group Created. </p>
            <?php $groups->create_group(); ?>
        <?php endif ?>
    </div>


</div>