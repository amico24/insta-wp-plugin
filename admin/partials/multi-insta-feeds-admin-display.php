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

$graph_accounts = new Multi_Insta_Feeds_Graph_Accounts;
$graph_groups = new Multi_Insta_Feeds_Graph_Groups;

?>

<div class="wrap">
    <h1>Accounts</h1>
    <table>
        <tr>
            <th>Username</th>
            <th>Delete Account</th>
        </tr>
        <?php foreach ($graph_accounts->get_accounts() as $user): ?>
            <tr>
                <td>
                    <?= $user ?>
                </td>
                <td>
                    <form method="post">
                        <input type="hidden" name="delete_account" id="delete_account" value="<?= $user ?>" />
                        <button type="submit"> Delete </button>
                    </form>
                </td>
            </tr>
            <?php
            if (isset($_POST['delete_account'])) {
                //might wanna add a confirmation alert for this
                $graph_accounts->delete_account($_POST['delete_account']);
                unset($_POST['delete_account']);
            }
            ?>
        <?php endforeach ?>
    </table>

    <hr>

    <h1>Groups:</h1>
    <table>
        <tr>
            <th>Group Index</th>
            <th>Users</th>
            <th>Shortcode</th>
            <th>Delete Group</th>
        </tr>
        <?php for ($grp = 0; $grp < $graph_groups->get_total_groups(); $grp++): ?>
            <tr>
                <td>
                    <?= $grp ?>
                </td>
                <td>
                    <?php foreach ($graph_groups->get_user_list($grp) as $user) {
                        echo $user . "<br>";
                    } ?>
                </td>
                <td><input type="text" value="[feed_display group=<?= $grp ?>]" readonly></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="delete_group_2" id="delete_group_2" value="<?= $grp ?>" />
                        <button type="submit"> Delete </button>
                    </form>
                </td>
            </tr>
            <?php
            if (isset($_POST['delete_group_2'])) {
                //might wanna add a confirmation alert for this
                $graph_groups->delete_group($_POST['delete_group_2']);
                unset($_POST['delete_group_2']);
            }
            ?>

        <?php endfor ?>

    </table>

</div>