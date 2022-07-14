<?php

// Website-Einstellungen & DB Einstellungen

if ($_SERVER['SERVER_NAME'] == '/')
{
	$document_root = '/';
	define("HOST",'localhost');
	define("DB_USER",'');
	define("DB_PASS",'');
	define("DB_NAME",'');
}

// localhost

else
{
	$document_root = 'http://localhost/kundenmanagement/';
	define("HOST",'localhost');
	define("DB_USER",'root');
	define("DB_PASS",'');
	define("DB_NAME",'kundenmanagement');
}

define("DOCUMENT_ROOT",$document_root);
define("ROOT",$_SERVER['DOCUMENT_ROOT']);

// DB connection

$con = mysql_connect(HOST,DB_USER,DB_PASS) or die ("MySQL connect error. ".mysql_error());
$select_db = mysql_select_db(DB_NAME) or die ("MySQL select db error. ".mysql_error());

// Wartungsmodus an/aus

define("MAINTENANCE",false);

// Authentifizierung

define("AUTH_USER","test");
define("AUTH_PASS","test");

// Admin Daten für Installation

define("ADMIN_NAME",'admin');
define("ADMIN_PASS",'admin');
define("ADMIN_REAL_NAME",'Administrator');

// Mail settings

define("POP3",'');
define("PORT_1",'');
define("PORT_2",'');
define("SEND_ADRESS",'');
define("PASS",'');
define("MAIL_HOST",'');
define("FROM_MAIL",'');
define("FROM_NAME",'');

// Daten Contao-Schnittstelle

define("CONTAO_DB_HOST",'');
define("CONTAO_DB_USER",'');
define("CONTAO_DB_PASS",'');
define("CONTAO_DB_NAME",'');

// Sprach-Konfiguration

date_default_timezone_set("Europe/Berlin");
setlocale(LC_ALL,"de_DE@euro","de_DE","deu_deu");

// Allgemeine Stammdaten

define("DEFAULT_MWST",19);

// PDF allgemein

define("UN_NAME",utf8_decode("FormerCompany"));
define("UN_STRASSE",utf8_decode("Street"));
define("UN_PLZ",utf8_decode("PLZ"));
define("UN_ORT",utf8_decode("Location"));
define("UN_TELEFON",utf8_decode("Phone"));
define("UN_FAX",utf8_decode("Fax"));
define("UN_MOBIL",utf8_decode("Mobile"));
define("UN_EMAIL",utf8_decode("mail"));
define("UN_STEUERNR",utf8_decode("RandomNumber"));

// Rechnung (spezifisch)

define("PAYING_INFO",utf8_decode("Zahlbar innerhalb von 20 Tagen ohne Abzug"));
define("GRUSSTEXT",utf8_decode("Vielen Dank für Ihren Auftrag! Mit sonnigen Grüßen aus Dietfurt!"));
define("INFO_TXT",utf8_decode("(Bitte stets Rechnungsnummer angeben)"));
define("FOOTER_1",utf8_decode("Die Ware bleibt bis zur vollen Bezahlung unser Eigentum "."-"." Zahlungen sind nur an uns zu leisten "."-"." Gerichtsstand für beide Seiten ist Weißenburg in Bay."));
define("FOOTER_2",utf8_decode("Bank1"));
define("FOOTER_3",utf8_decode("Bank2"));
define("FOOTER_4",utf8_decode("Der Rechnungsempfänger ist verpflichtet, die Rechnung zu Steuerzwecken 2 Jahre lang aufzubewahren."));
define("FOOTER_5",utf8_decode("Das Leistungsdatum entspricht dem Rechnungsdatum."));

// Angebot (spezifisch)

define("ANGEBOTSTEXT",utf8_decode("Ein persönliches, exklusives Angebot für Sie von uns."));
define("FOOTER_AN_1",utf8_decode("Kupferpreise beziehen sich stets auf die Tagesdotierung vom jeweiligen Angebotsdatum und können stark variieren."));
define("FOOTER_AN_2",utf8_decode("Nur solange der Vorrat reicht. Änderungen vorbehalten."));
define("FOOTER_AN_3",utf8_decode("Wir würden uns freuen, bald von Ihnen zu hören!"));

// Mahnung (spezifisch)

define("MAHNTEXT",utf8_decode("Überweisen Sie bitte den fälligen Betrag spätestens bis zum"));
define("INFO_MA_TXT",utf8_decode("(Bitte stets Rechnungsnummer bzw. Mahnungsnummer angeben)"));
define("FOOTER_MA_1",utf8_decode("Die Ware bleibt bis zur vollen Bezahlung unser Eigentum "."-"." Zahlungen sind nur an uns zu leisten "."-"." Gerichtsstand für beide Seiten ist Weißenburg in Bay."));
define("FOOTER_MA_2",utf8_decode("Bank1"));
define("FOOTER_MA_3",utf8_decode("Bank2"));

// Lieferschein (spezifisch)

define("DELIVERYTEXT",utf8_decode("Vielen Dank für Ihren Auftrag! Mit sonnigen Grüßen aus Dietfurt!"));
define("DELIVERY_INFO_TXT",utf8_decode("(Bei Rückfragen bitte stets Lieferscheinnummer angeben)"));
define("FOOTER_DEL_1",utf8_decode("Die Ware bleibt bis zur vollen Bezahlung unser Eigentum "."-"." Zahlungen sind nur an uns zu leisten "."-"." Gerichtsstand für beide Seiten ist Weißenburg in Bay."));
define("FOOTER_DEL_2",utf8_decode("Bank1"));
define("FOOTER_DEL_3",utf8_decode("Bank2"));

// PDF creation

$password = "";
$passwordlength = 32;
$charset = "abcdefghijklmnopqrstuvwxyz1234567890";

for ($x = 1; $x <= $passwordlength; $x++)
{
	$rand = rand() % strlen($charset);
	$temp = substr($charset, $rand, 1);
	$password .= $temp;
}

define("PDF_KEY",$password);

// Kalender

define("L_LANG","de_DE");

// Excel

define("LOCALE","De");
define("EXCEL_VERSION","Excel_2007"); // e.g. (Excel_2003,Excel_2007,Excel_2010) Momentan ausschließlich 2007 verfügbar!!!

// Datei Uploads

define("TARGET_PATH","uploads");

?>