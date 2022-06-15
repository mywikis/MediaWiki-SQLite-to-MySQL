<?php
/**
 * CSV to MySQL
 * 
 * Usage:
 * php csv-to-mysql.php database_name /path/to/csv/storage
 * Note: If LOAD DATA LOCAL INFILE disabled, enable it in my.cnf using local_infile = 1
 */

/*
    MediaWiki SQLite to MySQL Conversion
    Copyright (C) 2021 MyWikis LLC

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

$host = '';
$username = '';
$password = '';

$path_to_tables_file = '/path/to/tables-1.xx.txt';
$path_to_schema_file = '/path/to/mediawiki-1.xx-schema-with-confirmacct.sql';

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
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE user SET user_password_expires = NULL WHERE user_password_expires REGEXP '[^A-Za-z0-9]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE change_tag SET ct_params = NULL WHERE ct_params REGEXP '[^A-Za-z0-9]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE user_groups SET ug_expiry = NULL WHERE ug_expiry REGEXP '[^A-Za-z0-9]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE page SET page_lang = NULL WHERE page_lang REGEXP '[^A-Za-z]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE updatelog SET ul_value = NULL WHERE ul_value REGEXP '[^A-Za-z]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE watchlist SET wl_notificationtimestamp = NULL WHERE wl_notificationtimestamp REGEXP '[^A-Za-z0-9]';" ${database}`;
`mysql -h ${host} -u ${username} -p${password} -e "UPDATE comment SET comment_data = NULL WHERE LENGTH(comment_data) = 0;" ${database}`;
