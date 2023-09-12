<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://sample.com
 * @since      1.0.0
 *
 * @package    Multi_Insta_Feeds
 * @subpackage Multi_Insta_Feeds/admin/partials
 */

namespace MIF\Admin;

$accounts = new Multi_Insta_Feeds_Accounts;
$api = new Multi_Insta_Feeds_API_Connect;
$groups = new Multi_Insta_Feeds_Groups;

?>
<!-- 
    Makes a table of connected accounts + other info and links
    It might be good to swap this out with WP_List_Table 
-->
<div class="wrap">
    <h1>User IDs:</h1>

    <table>
        <tr>
            <th>Username</th>
            <th>User ID</th>
            <th>Access Token</th>
            <th>Delete User</th>
            <th>Token Expiry</th>
            <th>Refresh Token</th>
        </tr>
        <?php $keys = $accounts->get_key_list() ?>
        <?php foreach ($keys as $user_id): ?>

            <tr>

                <td>
                    <?= $accounts->get_account($user_id)['username'] ?>
                </td>
                <td>
                    <?= $accounts->get_account($user_id)['user_id'] ?>
                </td>
                <td><input type="text" value="<?= $accounts->get_account($user_id)['access_token'] ?>" class="field left"
                        readonly></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="delete_user" id="delete_user" value="<?= $user_id ?>" />
                        <button type="submit"> Delete </button>
                    </form>
                </td>
                <td>
                    <?= floor((($accounts->get_account($user_id)['creation_time'] + 5183944) - time()) / 86400) ?> days
                </td>
                <td>
                    <form method="post">
                        <input type="hidden" name="refresh_token" id="refresh_token" value="<?= $user_id ?>" />
                        <button type="submit"> Refresh </button>
                    </form>
                </td>
            </tr>

            <?php
            if (isset($_POST['delete_user'])) {
                //might wanna add a confirmation alert for this
                $accounts->delete_account($_POST['delete_user']);
                unset($_POST['delete_user']);
            }
            if (isset($_POST['refresh_token'])) {
                $api->refresh_access_token($_POST['refresh_token']);
                unset($_POST['refresh_token']);
            }
            ?>
        <?php endforeach ?>
    </table>

    <h1>Groups:</h1>
    <table>
        <tr>
            <th>Group Index</th>
            <th>Users</th>
            <th>Shortcode</th>
            <th>Delete Group</th>
        </tr>
        <?php for ($grp = 0; $grp < $groups->get_total_groups(); $grp++): ?>
            <tr>
                <td>
                    <?= $grp ?>
                </td>
                <td>
                    <?php foreach ($groups->get_user_list($grp, 'username') as $user) {
                        echo $user . "<br>";
                    } ?>
                </td>
                <td><input type="text" value="[feed_display group=<?= $grp ?>]" readonly></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="delete_group" id="delete_group" value="<?= $grp ?>" />
                        <button type="submit"> Delete </button>
                    </form>
                </td>
            </tr>
            <?php
            if (isset($_POST['delete_group'])) {
                //might wanna add a confirmation alert for this
                $groups->delete_group($_POST['delete_group']);
                unset($_POST['delete_group']);
            }
            ?>

        <?php endfor ?>

    </table>

</div>