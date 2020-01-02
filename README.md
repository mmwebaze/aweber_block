# aweber_block
## About Aweber Block
This module integrates Drupal 8 with Aweber an email marketing platform.
## Installation
* Install as usual, see
     https://www.drupal.org/docs/8/extending-drupal-8/installing-contributed-modules-find-import-enable-configure-drupal-8 for further
     information.
* Alternatively git clone this git repository
## Configuration and setup
To achieve successful configuration and usage, an Aweber account and Aweber developer account (https://labs.aweber.com) will be necessary. 
Set the module's configuration settings (/admin/config/aweber_block/config) to enable Drupal 8 to Aweber integration as follows:
1. Aweber base url: https://api.aweber.com/1.0
2. Aweber <b>Client ID</b> and <b>Secret</b> can be got from the Aweber developer account you intend to associate your Drupal site with.
3. The OAuth Redirect URL should be your <b>siteaddress/aweber_block/getCode</b>
4. Auth request url: https://auth.aweber.com/oauth2/authorize
5. Save the configurations
6. Authorize the app (/admin/config/aweber_block/get_authorization) then click the provided link. If steps 1 - 5 were successful,
you should be redirected to Aweber's Authorize App login.
7. Add the block to your site

## Support & Licensing
* Any bugs or required support must be made in the issue page of the module's git repository.
* Aweber api documentation can be found <a href='https://api.aweber.com/'>here</a>
* This software is licensed under GNU General Public License, version 2 or later. That means you are free to download, 
reuse, modify, and distribute any files in the module's git repository. More information on GNU General Public 
License, version 2 or later can be found <a href='http://www.gnu.org/licenses/old-licenses/gpl-2.0.html'> here</a>
