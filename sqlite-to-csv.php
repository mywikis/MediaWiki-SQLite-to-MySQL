
<?php
/**
 * SQLite to CSV
 * 
 * Usage:
 * php sqlite-to-csv.php database_name /path/to/sqlite/db/folder /path/to/csv/storage
 */

$sqlite_ext = '.sqlite';
$path_to_tables_file = '/path/to/tables-1.35.txt';

$database = $argv[1];
$sqlite_path = $argv[2];
$csv_path = $argv[3];

`mkdir -p ${csv_path}/${database}`;

$handle = fopen($path_to_tables_file, "r");
while (!feof($handle)) {
    $table = rtrim(fgets($handle));
    `sqlite3 -header -csv "${sqlite_path}/${database}.${sqlite_ext}" "SELECT * FROM ${table};" > "${csv_path}/${database}/${table}.csv"`;
}
fclose($handle);
