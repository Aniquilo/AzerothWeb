# Warcry-Web

## Getting Started ##

1. Create a `website` database and import the the SQL file from `sql/warcry.sql`.
2. Create a `data` database and import the the SQL file from `sql/wow_data.sql`.
3. Go to the directory `configuration` and fill in and rename the following files:

From | To
------------- | -------------
authentication.blank.php | authentication.php
basic.blank.php | basic.php
database.blank.php | database.php
logon.blank.php | logon.php
realms.blank.php | realms.php

4. Setup a cronjob for the top voters of the month rewards. Here's an example:

`0 0 1 * * /usr/bin/php /var/www/warcry/cronjobs/top_voters_of_the_month_rewards/run.php`

5. Set your account the owner role by running the following query in the website database, make sure to replace `<account id>` with your account's id:

`INSERT INTO rbac_user_role (user_id, role_id) VALUES (<account id>, 7);`

