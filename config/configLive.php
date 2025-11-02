<?php
declare(strict_types=1);

// (Valgfritt, men nyttig under oppsett/feilsøking)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

define('DB_HOST', 'hostmaster.onnet.no');
define('DB_NAME', 'gerhard_dposten');
define('DB_USER', 'gerhard_dpbruker');
define('DB_PASSWORD', 'Use!Web?');

// Opprett tilkobling
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Sette korrekt tegnsett (utf8mb4 anbefales for æ/ø/å og emoji)
$conn->set_charset('utf8mb4');

// Hvis du vil bekrefte i tester at charset er satt:
if ($conn->character_set_name() !== 'utf8mb4') {
     die('Kunne ikke sette charset til utf8mb4.');
}
