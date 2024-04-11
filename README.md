# SQL Management Scripts

## Overview

This project includes PHP scripts designed for managing SQL databases, particularly useful for environments where only FTP access is available and direct database access is restricted. These scripts facilitate the dumping and restoring of databases, as well as converting the encoding of SQL dump files.

## Scripts

### 1. `settings.php`
Contains all configuration settings, including database connections, file paths, and encoding specifications.

### 2. `dump.php`
Dumps the entire database into SQL files, with each file containing commands to recreate tables and insert data.

### 3. `import.php`
Reads SQL files generated by `dump.php` and executes them to restore tables and data.

### 4. `encode.php`
Converts the encoding of SQL files from one character set to another, useful for database portability and data integrity across different systems.

### 5. `download.php`
Allows downloading of a zipped SQL dump files directly from the browser, useful for manual backups or sharing with others.

### 6. `upload.php`
Uploads a zipped SQL dump file and extracts it to the specified directory for further processing.

### 7. `check_health.php`
Check the web and MySQL server health and settings, outputting the results in a readable format.

### 8. `check_integrity.php`
Checks the integrity of the database, reporting on available tables, their row counts, encoding and collation settings. Also performs a database health check.

### 9. `optimize.php`
Repairs and optimizes all tables in the database, improving performance and reducing disk space usage.



## Setup Instructions

### Configurations

Edit `settings.php` to set up the following configurations:

- **DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME:** Database connection details.
- **DUMP_DIR:** Path where SQL dump files will be stored.
- **ENCODED_DIR:** Path for storing encoded SQL files.
- **ORIGINAL_ENCODING:** Current encoding of SQL files (e.g., 'Windows-1251').
- **TARGET_ENCODING:** Desired encoding for SQL files (e.g., 'UTF-8').

Ensure that the specified directories (`DUMP_DIR` and `ENCODED_DIR`) exist and are writable.

### Running the Scripts

- **Dumping the Database:**
  Navigate to `dump.php` through your web browser. This script will save each table's SQL dump in the specified folder.

- **Restoring the Database:**  
  Open `upload.php` in your browser to execute the SQL files found in `DUMP_DIR` and restore the database.

- **Converting File Encoding:**  
  Access `encode.php` to convert SQL files from `ORIGINAL_ENCODING` to `TARGET_ENCODING`, saving the new files in `ENCODED_DIR`.

- **Downloading SQL Dumps:**
  Open `download.php` in your browser to download a zipped SQL dump file.

- **Checking Database Health:**
  Access `check_health.php` to check the health and settings of the web and MySQL servers.

- **Checking Database & Table Integrity:**
  Open `check_integrity.php` to check the integrity of the database, including table row counts and encoding settings.

- **Optimizing Database Tables:**
  Open `optimize.php` to repair and optimize all tables in the database, improving performance and reducing disk space usage.

## Usage Notes

- **Security:** These scripts can alter your database. Ensure they are adequately protected against unauthorized access (e.g., via .htaccess restrictions or IP whitelisting).

- **Testing:** Test the scripts in a development environment before use in production to avoid data loss.

- **Permissions:** The script files must have appropriate permissions to write to the necessary directories.

## Troubleshooting

- **Permission Errors:** If the scripts cannot read or write files, verify that the correct permissions are set on `DUMP_DIR` and `ENCODED_DIR`.

- **Encoding Errors:** Check the `ORIGINAL_ENCODING` and `TARGET_ENCODING` settings if the data does not appear as expected after conversion.

- **Execution Timeouts:** Adjust `MAX_EXECUTION_TIME` in `settings.php` if scripts timeout, especially when dealing with large databases.
