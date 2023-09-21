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

    <br>

    <form method="post">
        <input type="hidden" name="refresh_accounts" id="refresh_accounts" value="1" />
        <button type="submit"> Refresh Accounts </button>
    </form>

    <?php
    if (isset($_POST['refresh_accounts'])) {
        $graph_accounts->refresh_accounts();
        unset($_POST['refresh_accounts']);
        new Multi_Insta_Feeds_Errors('Accounts Refreshed.', 'notice-success');
    }
    ?>

    <h4>Display Posts from All Accounts:</h4>
    <input type="text" value="[feed_display type=posts]" readonly>

    <h4>Display Profiles from All Accounts:</h4>
    <input type="text" value="[feed_display type=accounts]" readonly>

    <hr>

    <h1>Groups:</h1>
    <table>
        <tr>
            <th>Group Index</th>
            <th>Users</th>
            <th>Recent Posts</th>
            <th>Profiles</th>
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
                <td><input type="text" value="[feed_display group=<?= $grp ?> type=posts]" readonly></td>
                <td><input type="text" value="[feed_display group=<?= $grp ?> type=accounts]" readonly></td>
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