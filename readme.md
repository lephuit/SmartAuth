# SmartAuth
SmartAuth is the smart way to implement authentication and authorization for your FuelPHP projects.
We are trying to create the most secure and efficient way to deal with your users.
You define what your users are, the package provide basic functionality.

## How to install
1. Copy files to your packages folder.
2. Copy the `smartauth/config/smartauth.php` file to your `app/config` folder and edit value according to your needs.
3. Run the following command:
`$ php oil r migrate --packages=smartauth`
or if you are not using oil, import the `smartauth/schema.sql` (for MySQL) file to your database.

## Current features:
* Basic authentication
* Group functionality (with group nesting)
* Authorization (via user/group roles)
* Different password hash/user
* I18n support (using `Lang` class)

## TODO
* Cookie login (remember me)
* Email activation
* Password reset
* Full documentation
* Brute-force prevention measurements

## Change log
v0.1 - March 24, 2012
First commit, basic functionality implemented.

