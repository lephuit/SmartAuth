# SmartAuth
SmartAuth is the smart way to implement authentication and authorization for your FuelPHP projects.
We are trying to create the most secure and efficient way to deal with your users.
You define what your users are, the package provide basic functionality.

## How to install
1. Copy files to your packages folder.
2. Copy the `smartauth/config/smartauth.php` file to your `app/config` folder and edit values according to your needs.
3. Run the following command:
`$ php oil r migrate --packages=smartauth`

## Current features:
* Basic authentication
* Cookie login (remember me)
* Group functionality (with group nesting)
* Authorization (via user/group roles)
* Password salting (global salt or unique user salt)
* Minimum password complexity

## TODO features
* Email activation
* Password reset
* Forced periodical password change
* Brute-force prevention measurements

## Change log
v0.2 - March 25, 2012
- Password complexity checks implemented
- Salting mechanisms implemented
- Cookie login implemented

v0.1 - March 24, 2012
- First commit, basic functionality implemented

