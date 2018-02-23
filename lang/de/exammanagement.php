<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     tool_exammanagement
 * @category    string
 * @copyright   coactum Gmbh 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//exammanagement_overview.mustache
$string['maintitle'] = 'Prüfungsorganisation';
$string['overview']= 'Überblick';

//dateTimeForm.php
$string['resetDateTime'] = 'Datum und Zeit zurücksetzen';

//helptexts
$string['helptext_str'] = 'Hilfetext';
$string['helptext_link'] = 'Eine ausführliche Erläuterung der genannten Elemente und Funktionen findet sich im "IMT HilfeWiki" unter dem folgenden Link:';
$string['helptext_overview']= 'Dies ist die <strong>Startseite der Prüfungsorganisation</strong>. DozentInnen und/oder deren MitarbeiterInnen sehen hier alle für das Durchführen einer Prüfung nötigen und hilfreichen Arbeitsschritte. <br /><br />
Diese sind übersichtlich in vier verschiedene Phasen unterteilt, welche entlang eines Zeitstrangs angeordnet sind. Für jeden einzelnen Arbeitsschritt ist der Bearbeitungsstatus durch entsprechende Symbole, Texte und Farben zu erkennen. Es gibt verpflichtende Arbeitsschritte und optionale, welche zwar hilfreich sind, aber auch weggelassen werden können.
Sobald alle verpflichtenden Schritte einer Phase erledigt sind klappt diese automatisch zu und es öffnet sich die Nächste. Phasen können jedoch auch jederzeit manuell geöffnet und zugeklappt werden. <br /><br />
Jeder Arbeitsschritt kann durch einen Klick auf den entsprechenden Link geöffnet werden. Manche Arbeitsschritte können erst nach der Bearbeitung eines anderen Schrittes geöffnet werden. In diesem Fall erscheint nach dem Klick eine Fehlermeldung. <br /><br />
Durch den Button "Bearbeitungsrechte entziehen" bzw. "Bearbeitungsrechte zuweisen“ können Sie Ihren studentischen MitarbeiterInnen die Zugriffserlaubnis auf die Inhalte der Prüfungsorganisation in PANDA entziehen bzw. erneut zuweisen. <br /><br />
Hinweis: Studierende haben keinen Zugriff auf diese Ansicht. Sie sehen stattdessen in einer eigenen Ansicht die für sie freigeschalteten Informationen zum Prüfungstermin.';
$string['helptext_setDateTime']= 'Hier können das <strong>Datum</strong> und die <strong>Uhrzeit</strong> der Prüfung ausgewählt werden. <br /> <br />
Der hier gewählte Prüfungstermin wird auf der Übersichtsseite der Prüfungsorganisation angezeigt und später in den erzeugten Dokumenten verwendet. <br /> <br />
Zudem wird er den Studierenden in deren Ansicht angezeigt, sobald Sie diese Informationen auf der Übersichtsseite für die Studierenden sichtbar geschaltet haben.';

//errors
$string['missingidandcmid'] = 'Kurs ID fehlt (missingcmid)';

//universal
$string['modulename'] = 'Prüfungsorganisation';
$string['modulenameplural'] = 'Prüfungsorganisationen';
$string['pluginname'] = 'Prüfungsorganisation';

//addnewinstance
$string['modulename_help'] = 'Dies ist der Hilfetext, welcher bei der Auswahl einer neuen Aktivität angezeigt wird ...';
$string['exammanagementname'] = 'Prüfungsorganisation';
$string['exammanagementname_help'] = 'Bitte keinen anderen Namen verwenden als den Standardnamen ...';
$string['exammanagementdescription'] = 'Beschreibung:';
$string['exammanagementdescriptiontext'] = 'Beschreibungstext ....';
$string['exammanagementfieldset'] = 'Weitere Einstellungen';
$string['exammanagement:enable exam management'] = 'Prüfungsorganisation aktivieren';
$string['messageprovider:exam management messages'] = 'Nachrichten zur Prüfungsorganisation';
$string['pluginadministration'] = 'exam management administration';

//capabilities
$string['exammanagement:addinstance'] = 'Neue Prüfungsorganisation hinzufügen';
$string['exammanagement:viewinstance'] = 'Prüfungsorganisation ansehen';
$string['exammanagement:takeexams'] = 'Prüfung ablegen';
$string['exammanagement:sendgroupmessage'] = 'Nachricht an Teilnehmer senden';
