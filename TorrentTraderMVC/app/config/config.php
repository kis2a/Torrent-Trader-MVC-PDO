<?php
// DB Details
define("DB_HOST", "localhost");
define("DB_USER", "dbusername");
define("DB_PASS", "dbpassword");
define("DB_NAME", "dbname");
define('DB_CHAR', 'utf8');
// URL Root
define('URLROOT', 'http://localhost/torrenttraderpdo');
// App Root
define('APPROOT', dirname(dirname(__FILE__)));
// Paths
define('BACUP', '../data/backups');
define('CACHE', '../data/cache'); // Cache dir (only used if type is "disk"). Must be CHMOD 777
define('LANG', '../data/languages');
define('LOGGER', '../data/logs');
define('UPLOADDIR', '../data/uploads');
// Quick Time
define('TT_TIME', time());
define('TT_DATE', date("Y-m-d H:i:s"));
// Version
define('VERSION', 'PDO');
// File Charset
define('CHARSET', 'utf-8');
// Announcelist //seperate via comma
define('ANNOUNCELIST', URLROOT . '/announce.php');
// Passkey Url
define('PASSKEYURL', URLROOT . '/announce.php?passkey=%s');
// Can edit Settings
define('_OWNERS', array('M-jay')); // Example & with more define('_OWNERS', array('mjay', 'mjay', 'mjay'));
// Image upload settings
define('IMAGEMAXFILESIZE', 524288); // Max uploaded image size in bytes (Default: 512 kB)
define('ALLOWEDIMAGETYPES', array(
    "image/gif" => ".gif",
    "image/pjpeg" => ".jpg",
    "image/jpeg" => ".jpg",
    "image/jpg" => ".jpg",
    "image/png" => ".png",
));
// Hide Blocks On Pages
define('ISURL', array('login', 'logout', 'signup', 'contactstaff', 'recover'));
//WAIT TIME VARS
define('CLASS_WAIT', 1); //Classes wait time applies to, comma seperated
define('GIGSA', 1); //Minimum gigs
define('RATIOA', 0.50); //Minimum ratio
define('A_WAIT', 24); //If neither are met, wait time in hours
define('GIGSB', 3); //Minimum gigs
define('RATIOB', 0.65); //Minimum ratio
define('B_WAIT', 12); //If neither are met, wait time in hours
define('GIGSC', 5); //Minimum gigs
define('RATIOC', 0.80); //Minimum ratio
define('C_WAIT', 6); //If neither are met, wait time in hours
define('GIGSD', 7); //Minimum gigs
define('RATIOD', 0.95); //Minimum ratio
define('D_WAIT', 2); //If neither are met, wait time in hours
// Log Clean
define('LOGCLEAN', 28 * 86400); // (Default: 28 days)

// category,name,dl,magnet,uploader,tube,imdb,comments,nfo,size,completed,seeders,leechers,health,external,added,speed,wait,rating
define('TORRENTTABLE_COLUMNS', 'category,name,dl,magnet,uploader,tube,imdb,comments,nfo,size,completed,seeders,leechers,health,external,added,speed,wait,rating');

// Set User Group
define('_USER', 1);
define('_POWERUSER', 2);
define('_VIP', 3);
define('_UPLOADER', 4);
define('_MODERATOR', 5);
define('_SUPERMODERATOR', 6);
define('_ADMINISTRATOR', 7);
// Hit & Run mod
define('HNR_ON', false); // Not Finished only for testing
define('HNR_DEADLINE', 7 * 86400); // 7 days to hit the seed target
define('HNR_SEEDTIME', 172800); // target is to seed for 48 hours
define('HNR_WARN', 5); // 5 hit & runs then warned
define('HNR_STOP_DL', 5); // After 5 H & R stop downloading
define('HNR_BAN', 50); // After 50 H&R they are banned
define('HNR_DISABLED', TRUE);

define('_ANNOUNCEINTERVAL', 600);