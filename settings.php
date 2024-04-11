<?php

// Database settings
const DB_HOST = 'localhost'; // Database host
const DB_USERNAME = 'your_username'; // Database username
const DB_PASSWORD = 'your_password'; // Leave empty if no password
const DB_NAME = 'your_database'; // Database name

// Folder to dump SQL files
const DUMP_DIR = 'data'; // Make sure the folder exists and is writable

// Application settings
const DEBUG = true; // Set to false in production
const MAX_EXECUTION_TIME = 0;  // No time limit
const MEMORY_LIMIT = '512M';   // Increase memory limit to 512MB

// Encoding settings for encode script
const ENCODED_DIR = DUMP_DIR . '/encoded';  // Directory for encoded files
const ORIGINAL_ENCODING = 'Windows-1251';  // Example: 'Windows-1251' for Cyrillic
const TARGET_ENCODING = 'UTF-8';           // Target encoding

// Init debug mode
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Apply server execution settings
ini_set('max_execution_time', MAX_EXECUTION_TIME);
ini_set('memory_limit', MEMORY_LIMIT);