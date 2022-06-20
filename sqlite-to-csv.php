
<?php
/**
 * SQLite to CSV
 * 
 * Usage:
 * php sqlite-to-csv.php database_name /path/to/sqlite/db/folder /path/to/csv/storage
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

$sqlite_ext = 'sqlite';
$path_to_tables_file = '/path/to/tables-1.xx.txt'; # 1.xx: MediaWiki version of input DB

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
