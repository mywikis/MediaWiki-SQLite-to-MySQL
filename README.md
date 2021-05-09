# MediaWiki-SQLite-to-MySQL
Public release of MyWikis SQLite to MySQL conversion for MediaWiki.

## Overview

This script is written in PHP and intends to convert a MediaWiki database on SQLite into one that runs on MySQL.

It converts by using a CSV file as an intermediate data representation format. First, the SQLite database contents are dumped into CSV files, with each CSV file representing a table. Then, the CSV files are loaded into a MySQL database initialized with the MediaWiki database schema. Afterwards, ad hoc fixes are applied to correct issues with data representation, since CSV being unable to represent the difference between empty and null cell values.

**This should not be considered stable, and this script does not accurately preserve the exact state of the SQLite database**.

## How to use

### Files

* `sqlite-to-csv.php`: Exports SQLite database into CSV format, one CSV file per table
* `csv-to-mysql.php`: Imports into MySQL database from CSV file.
* `tables-1.xx.txt`: A listing of tables in the MediaWiki SQL database that need to be exported and imported. This should ideally be derived from the SQLite DB's schema. It does not hurt to add additional table names that don't exist in a database; doing so will only cause notices/warnings to appear, emitted by MySQL, but doesn't cause any fatal errors that stop execution.
* `tables-1.31.txt`: The default provided tables listing file. This works for MediaWiki 1.31 and includes ConfirmAccount tables.

### Steps

1. Set which tables file you are using in the `sqlite-to-csv.php` script and your SQLite database's extension.
2. Add your MySQL connection info in `csv-to-mysql.php`, as well as the paths to the table listing and schema files.
3. Add any missing table names to your `tables-1.xx.txt` file. They would come from extensions you've installed. The default tables file provided comes with all of the MediaWiki tables plus ConfirmAccount tables. Anything else that's necessary would need to be added to this list.
4. Generate a MySQL schema file by setting up a dummy MySQL database using the MediaWiki installation script, then using a line like this: `mysqldump -h yourhostnameorIP -u root -p --no-data dummydbname > schema.sql`. Delete the dummy database; it can't be used for the import. Afterwards, create a new MySQL database and use `schema.sql` to populate its schema like so: `mysql -h mysql.hostname.com -u mysqladmin -pPassword1234567890 realdbname < schema.sql`.
5. Create a temp directory where the CSV files can be stored. It's recommended to use `/tmp` for this.
6. Run `sqlite-to-csv.php`, then `csv-to-mysql.php`, passing in the appropriate arguments as explained in the comments of these two files.

## Known issues

- The following errors are normal and expected:
```
Error: no such table: external_user
Error: near ";": syntax error
ERROR 1064 (42000) at line 1: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"' ESCAPED BY '"' LINES TERMINATED ...' at line 1
```
- If MySQL says that "this command isn't supported in this version" or "this command was disabled", enable `LOAD DATA LOCAL INFILE` in `my.cnf` by adding the line `local_infile = 1`.
- When regenerating the recent changes table manually after completion of the import, recent changes will not display recent changes accurately.
