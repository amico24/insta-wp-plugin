## Details:
Generates shortcode to display the 5 most recent posts of a group of connected instagram accounts.

The plugin uses the wordpress plugin boilerplate generator https://wppb.me/ for the structure. (i should probably mention that i really only used the admin folder of the template, everything else is leftover from the boilerplate)

Admin pages were made mostly using the guide from this tutorial: https://blog.wplauncher.com/create-wordpress-plugin-settings-page/

The instagram app permissions I used for this only use the instagram basic display api instead of the graph api

First version `1.0.0` is the first working version of the plugin. It connects to the instagram api and is able to display posts, but is very bare bones and may need a lot of reworking.
 
## Instructions for Use:
1. Download files and store in plugin directory in the wordpress folder: `...\wordpress\wp-content\plugins`

2. Change constants in files according to Instagram API app:

    go to `...\multi-insta-feeds\admin\class-multi-insta-feeds-admin-api-connect.php` and change the ff in the $args variable
       
    ```
    client_id: client id of the instagram app from the meta/instagram dev page
    client_secret: also from meta/instagram dev page
    redirect_uri: webpage to redirect to after instagram api is accessed
    ```

    go to `...\multi-insta-feeds\admin\partials\multi-insta-feeds-admin-settings-display` and edit the url on line 32 according to the format: 
    
    ```
    https://api.instagram.com/oauth/authorize?client_id={app-id}&redirect_uri={redirect-uri}&scope=user_profile,user_media&response_type=code
    ```
    
    swap out the client_id and redirect_uri to the same one you used in the $args variable
    
    *regarding redirect_uri: it has to be a page in the wordpress admin dashboard (the plugin is programmed to detect auth codes from there). The page has to be one without an already existing query string. I used the main page of the admin dashboard `wp-admin/index.php` but any page in the admin menu should work (`wp-admin/admin.php?page=multi-insta-feeds` wont work since the redirect removes the original query strings).*
    
    Also make sure to add the redirect uri to the `Valid OAuth Redirect URIs` list on the meta/instagram dev page (https://developers.facebook.com/docs/instagram-basic-display-api/getting-started)
    
    optional: can also change the name of the database entries that accounts and groups are stored in: `$db_accounts` in `class-multi-insta-feeds-admin-accounts.php` and `$db_groups` in `class-multi-insta-feeds-admin-groups.php`
    these are saved in the sql database in the wp_options table

3. Connect instagram accounts

    If Instagram dev app is in developer mode, make sure to add the accounts as testers first (https://developers.facebook.com/docs/instagram-basic-display-api/getting-started)
    
    The instagram account should be logged in the browser that you`re accessing the wordpress website from
    
    To add an account, go to the settings tab under the `Multi Insta Feed` option in the admin page and click `Add Account`
    
    This will redirect you to a confirmation panel for your insta account to grant permission for api access
    
    After granting permission, it will redirect back to the redirect_uri and the plugin should do the rest automatically (detecting the auth code in the query string, exchanging auth code for shortlived access token, then exchanging the short lived token for a long lived token and storing it with other account info in the sql database via the wp_options entry under $db_accounts)
    
    After this, the account should appear in the dashboard of `Multi Insta Feed` along with other details
    
    Do this for all needed accounts (you need to log in for every account you add)

4. Create groups
   
    Go back to the settings page for `Multi Insta Feed` and select the accounts you want to add in a group, then click the `Create Group with Selected Accounts` button. The group with the selected users should show up in the plugin dashboard

5. Use shortcode
   
    Copy the shortcode under the group and paste into the page where you want to display the recent posts of the users in that group

## Known Issues:
- Literally zero css on this. Everything is default html styling.
- Doing certain tasks like deleting accounts doesnt immediately update the rest of the plugin on the changes until the page is refreshed. Sometimes this leads to warnings from wordpress on the page or certain things being displayed incorrectly, which fixes itself once refreshed.
- None of the functions or variables have proper summaries commented in (idk what theyre called its the thing thats commented before the function or variable that describes what it does and what params it needs)
- Actually all the comments in general should probably be redone
- `multi-insta-feeds-admin-functions.php` doesnt actually do anything lol i made it to try and have a general functions file to put constants and other stuff but i couldnt figure out how to get it to work with the boilerplate

