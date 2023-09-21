## Details:
Generates shortcode to display the 5 most recent posts of a group of connected instagram accounts.

The plugin uses the wordpress plugin boilerplate generator https://wppb.me/ for the structure. (i should probably mention that i really only used the admin folder of the template, everything else is leftover from the boilerplate)

New version uses Instagram Graph API instead of Basic Display API

This version is able to connect to business and creator accounts and display their posts. It wont work with personal accounts.

Posts that have copyrighted material can't be displayed.

Also `multi-insta-feeds-admin-functions.php` doesnt actually do anything lol i made it to try and have a general functions file to put constants and other stuff but i couldnt figure out how to get it to work with the boilerplate


**About the access token:** right now, the plugin works by getting an access token directly from the graph api explorer that facebook provides. I wanted to do a direct facebook login (like here: `https://developers.facebook.com/docs/instagram-api/getting-started` steps 1 & 2) so that the access token can be taken directly from the plugin, but using the facebook login API needs a verified business to use. On top of that, I'm pretty sure the app needs to go through review to get access to most of the permissions needed to access Instagram (The only permissions you can request without review are `public_profile` and `email`). So for now, getting an access token from graph api explorer works.

 
## Instructions for Use:
1. Download files and store in plugin directory in the wordpress folder: `...\wordpress\wp-content\plugins`

2. Set up a business facebook app with Instagram Graph API product

   I'm not sure if Facebook Login is needed here but if you can't get the permissiosn needed in step 4 then you should probably add it

4. Change values of $client_id and $app_secret in `class-multi-insta-feeds-admin-graph-api.php` according to facebook app

3. Make sure you have the following accounts:
    
    - Instagram Business/Creator Account
    - Facebook Page Connected to the Instagram Account
    - Make sure that your developer facebook account has task access on the facebook page (note: you cant get task access if you're the creator of the page for some reason)

    The way graph api works is that it connects to facebook, then uses the facebook connection to grab data from instagram, which is why the fb page is needed.

4. Get User Access Token from Graph API Explorer (`https://developers.facebook.com/tools/explorer/`)

   Choose the facebook app and request the following permissions:
    - pages_show_list
    - instagram_basic
    - instagram_manage_insights
    - pages_read_engagement
    - ads_management or business_management (i used both to be safe)

    When generating the token, make sure to only select to give access to the facebook page connected to the instagram account.

6. Input the Access token into the settings page of the plugin. If successful, it should convert the access token into a long-lived token and display the instagram page user id.

   **About the long-lived token:** Apparently on graph API the tokens refresh themselves when theyre used to access the api so no need to refresh. They still expire after being unused for like 60 days if im not mistaken.

8. Add the usernames of the accounts that you want to display one at a time (ex: `mcdonalds` `zuck`, no @ or spaces) (Business/Creator accounts only)

9. Create a group with your chosen accounts

10. Copy/paste the shortcode of the group onto website.
