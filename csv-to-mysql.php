<?php
/**
 * CSV to MySQL
 * 
 * Usage:
 * php csv-to-mysql.php database_name /path/to/csv/storage
 * Note: If LOAD DATA LOCAL INFILE disabled, enable it in my.cnf using local_infile = 1
 */

$host = '';
$username = '';
$password = '';

$path_to_tables_file = '/path/to/tables-1.xx.txt';
$path_to_schema_file = '/path/to/mediawiki-1.31.0-schema-with-confirmacct.sql';

$database = $argv[1];
$csv_path = $argv[2];

`mysql -h ${host} -u ${username} -p${password} -e "CREATE DATABASE IF NOT EXISTS ${database};"`;
`mysql -h ${host} -u ${username} -p${password} ${database} < ${path_to_schema_file}`;

$handle = fopen($path_to_tables_file, "r");
while (!feof($handle)) {
    $table = rtrim(fgets($handle));
    $handle2 = fopen("$csv_path/$database/$table.csv", "r");
    $l1 = rtrim(fgets($handle2));
    `mysql -h ${host} -u ${username} -p${password} --local-infile=1 -e "SET foreign_key_checks = 0; LOAD DATA LOCAL INFILE '${csv_path}/${database}/${table}.csv' IGNORE INTO TABLE ${table} FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES (${l1});" ${database}`;
}
fclose($handle);
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE revision SET rev_content_format = NULL, rev_content_model = NULL WHERE rev_content_model = '';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE archive SET ar_content_format = NULL, ar_content_model = NULL WHERE ar_content_model = '';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE user SET user_password_expires = NULL WHERE user_password_expires REGEXP '[^A-Za-z0-9]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE change_tag SET ct_params = NULL WHERE ct_params REGEXP '[^A-Za-z0-9]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE user_groups SET ug_expiry = NULL WHERE ug_expiry REGEXP '[^A-Za-z0-9]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE page SET page_lang = NULL WHERE page_lang REGEXP '[^A-Za-z]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE updatelog SET ul_value = NULL WHERE ul_value REGEXP '[^A-Za-z]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE watchlist SET wl_notificationtimestamp = NULL WHERE wl_notificationtimestamp REGEXP '[^A-Za-z0-9]';" ${database}`;
//`mysql -h ${host} -u ${username} -p${password} -e "UPDATE  SET  = NULL WHERE  REGEXP '[^A-Za-z0-9]';" ${database}`;
//`mysql -h ${host} -u ${username} -p${password} -e "UPDATE  SET  = NULL WHERE  REGEXP '[^A-Za-z0-9]';" ${database}`;
//`mysql -h ${host} -u ${username} -p${password} -e "UPDATE  SET  = NULL WHERE  REGEXP '[^A-Za-z]';" ${database}`;
