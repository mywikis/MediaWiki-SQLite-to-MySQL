# MediaWiki-SQLite-to-MySQL
Public release of MyWikis SQLite to MySQL conversion for MediaWiki.

## Overview

This script is written in PHP and intends to convert a MediaWiki database on SQLite into one that runs on MySQL.

It converts by using a CSV file as an intermediate data representation format. First, the SQLite database contents are dumped into CSV files, with each CSV file representing a table. Then, the CSV files are loaded into a MySQL database initialized with the MediaWiki database schema. Afterwards, ad hoc fixes are applied to correct issues with data representation, since CSV being unable to represent the difference between empty and null cell values.

**This should not be considered stable, and this script does not accurately preserve the exact state of the SQLite database**.

## Known issues

- When regenerating the recent changes table manually after completion of the import, recent changes will not display recent changes accurately.
