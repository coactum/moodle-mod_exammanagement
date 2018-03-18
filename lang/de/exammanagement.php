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

//participant list
$string['participantslist'] = 'Teilnehmerliste';
$string['internal_use'] = 'Nur für die Prüfungsorganisation!';
$string['lastname'] = 'Name';
$string['firstname'] = 'Vorname';
$string['matrno'] = 'Matr.-Nr.';
$string['room'] = 'Raum';
$string['place'] = 'Platz';

//helptexts
$string['helptext_str'] = 'Hilfetext';
$string['helptext_link'] = 'Eine ausführliche Erläuterung der genannten Elemente und Funktionen findet sich im "IMT HilfeWiki" unter dem folgenden Link:';
$string['helptext_overview']= 'Dies ist die <strong>Startseite der Prüfungsorganisation</strong>. DozentInnen und/oder deren MitarbeiterInnen sehen hier alle für das Durchführen einer Prüfung nötigen und hilfreichen Arbeitsschritte. <br /><br />
Diese sind übersichtlich in vier verschiedene Phasen unterteilt, welche entlang eines Zeitstrangs angeordnet sind. Für jeden einzelnen Arbeitsschritt ist der Bearbeitungsstatus durch entsprechende Symbole, Texte und Farben zu erkennen. Es gibt verpflichtende Arbeitsschritte und optionale, welche zwar hilfreich sind, aber auch weggelassen werden können.
Sobald alle verpflichtenden Schritte einer Phase erledigt sind klappt diese automatisch zu und es öffnet sich die Nächste. Phasen können jedoch auch jederzeit manuell geöffnet und zugeklappt werden. <br /><br />
Jeder Arbeitsschritt kann durch einen Klick auf den entsprechenden Link geöffnet werden. Manche Arbeitsschritte können erst nach der Bearbeitung eines anderen Schrittes geöffnet werden. In diesem Fall erscheint nach dem Klick eine Fehlermeldung. <br /><br />
Durch den Button "Bearbeitungsrechte entziehen" bzw. "Bearbeitungsrechte zuweisen“ können Sie Ihren studentischen MitarbeiterInnen die Zugriffserlaubnis auf die Inhalte der Prüfungsorganisation in PANDA entziehen bzw. erneut zuweisen. <br /><br />
<strong>Hinweis:</strong> Studierende haben keinen Zugriff auf diese Ansicht. Sie sehen stattdessen in einer eigenen Ansicht die für sie freigeschalteten Informationen zum Prüfungstermin.';
$string['helptext_addRooms']= 'Auf dieser Seite kann eine Liste aller im System verfügbaren möglichen <strong>Prüfungsräume</strong> angesehen und einer oder mehrere davon als Raum für die aktuelle Prüfung ausgewählt werden. <br /> <br />
Zudem können nach einem Klick auf den Button „Neue Räume hinzufügen“ auch eigene potenzielle Prüfungsräume zur Liste hinzugefügt werden. <br /> <br />
Um einen Raum als Prüfungsraum auszuwählen muss zunächst die Box links neben dessen Namen angeklickt werden. Es besteht auch die Möglichkeit, durch einen Klick auf die Box neben dem Schriftzug „Alle aus-/abwählen“ alle in der Liste stehenden Räume auszuwählen. Ein Klick auf den Button „Räume für Prüfung auswählen“ speichert die gewählten Räume als Prüfungsräume. Ist ein Raum bereits markiert wurde er bereits als Raum für die Prüfung gespeichert.<br /> <br />
Die gewählten Prüfungsräume werden später verwendet, um den zur Prüfung hinzugefügten TeilnehmerInnen Sitzplätze zuzuweisen. Ihre Sitzplätze werden den PrüfungsteilnehmerInnen später (sobald Sie diese Information auf der Übersichtsseite für die Studierenden sichtbar geschaltet haben) in ihrer Ansicht angezeigt. Außerdem wird die Sitzplatzzuweisung in Dokumenten wie der Teilnehmerliste oder den Prüfungsetiketten benötigt. <br /> <br />
Informationen über die in einem Raum vorhandenen Sitzplätze gibt die Tabelle. Ist für einen Raum ein Sitzplan vorhanden kann dieser durch das Bewegen des Mauszeigers auf das "Ja" in der Spalte "Sitzplan“ angesehen werden. <br /> <br />
<strong>Wichtig:</strong>
<ul><li>Um die weiteren Funktionen der PANDA Prüfungsorganisation nutzen zu können muss hier mindestens ein Raum als Prüfungsraum ausgewählt werden. Zudem müssen die gewählten Räume mindestens so viele Sitzplätze bieten, wie TeilnehmerInnen an der Prüfung teilnehmen werden.</li>
<li>Werden neue Räume zur Prüfung hinzugefügt oder Bestehende entfernt, nachdem den TeilnehmerInnen bereits Sitzplätze zugewiesen wurden, muss diese Zuordnung wiederholt werden.</li></ul>
<strong>Achtung:</strong> Das System berücksichtigt nicht die Verfügbarkeit der gewählten Räume. Als DozentIn müssen Sie die Räume, in welchen die Prüfung stattfinden soll, bei der zentralen Raumverwaltung der Universität Paderborn buchen und so abklären, dass die entsprechenden Räume auch tatsächlich zum Zeitpunkt der Prüfung verfügbar sind.';
$string['helptext_setDateTime']= 'Hier können das <strong>Datum</strong> und die <strong>Uhrzeit</strong> der Prüfung ausgewählt werden. <br /> <br />
Der hier gewählte Prüfungstermin wird auf der Übersichtsseite der Prüfungsorganisation angezeigt und später in den erzeugten Dokumenten wie etwa der Teilnehmerliste oder den Klausuretiketten verwendet. <br /> <br />
Zudem wird er den PrüfungsteilnehmerInnen in deren Ansicht angezeigt, sobald Sie diese Informationen auf der Übersichtsseite für die Studierenden sichtbar geschaltet haben. <br /> <br />
<strong>Wichtig:</strong> Das Datum und die Uhrzeit der Prüfung müssen hier gesetzt werden, um die Prüfungsorganisation in PANDA sinnvoll nutzen zu können.';
$string['helptext_addParticipants']= 'Auf dieser Seite können Informationen zu den <strong>TeilnehmerInnen</strong> angesehen und diese zur Prüfung hinzugefügt werden. <br /> <br />
Dabei gibt es einerseits die Möglichkeit, Studierende aus den in der unten befindlichen Liste aufgeführten TeilnehmerInnen des PANDA Kurses auszuwählen. Diese müssen durch Anklicken der Box neben ihrem Namen zunächst ausgewählt und anschließend durch Betätigen des Buttons „Zur Prüfung hinzufügen“ zur Prüfung hinzugefügt werden. Durch einen Klick auf die Box neben dem Schriftzug „Alle aus-/abwählen“ können auch alle in der Liste stehenden TeilnehmerInnen aus- bzw. abgewählt werden. Ist ein Teilnehmender bereits durch einen Haken markiert wurde er bereits zur Prüfung hinzugefügt.<br /> <br />
Andererseits können nach einem Klick auf den Button „Import aus Datei“ auch Studierende, die bisher nicht im PANDA Kurs eingeschrieben sind, als TeilnehmerIn zur Prüfung hinzugefügt werden. Nach dem Einlesen der TeilnehmerInnen müssen diese dann noch auf der vorherigen Seite als TeilnehmerInnen ausgewählt werden. Als Angabe zur Quelle ist bei derart hinzugefügten TeilnehmerInnen „Eigener Import“ aufgeführt.<br /> <br />
Weitere in der Teilnehmertabelle stehende Informationen wie die Matrikelnummer oder die in PANDA zugewiesene Gruppe einer TeilnehmerIn sollen helfen, wenn nur bestimmte TeilnehmerInnen zu einer Prüfung hinzugefügt werden sollen (z. B., weil die anderen TeilnehmerInnen einem anderen Prüfungstermin zugewiesen werden sollen). <br /> <br />
Das Hinzufügen von TeilnehmerInnen ist der wichtigste Arbeitsschritt in der Prüfungsorganisation. Nur den TeilnehmerInnen, die hier als PrüfungsteilnehmerInnen hinzugefügt werden, können später Sitzplätze zugewiesen, Dokumente erstellt oder Prüfungspunkte eingetragen werden. Nicht als PrüfungsteilnehmerInnen hinzugefügte Studierende haben (selbst wenn sie bereits im PANDA Kurs eingeschrieben sind) keinen Zugriff auf die Teilnehmeransicht mit den Prüfungsinformationen und erhalten zudem keine Benachrichtigung über die Nachrichtenfunktion auf der Übersichtsseite. <br /> <br />
<strong>Wichtig:</strong>
<ul><li>Um die weiteren Funktionen der PANDA Prüfungsorganisation sinnvoll nutzen zu können müssen hier alle an der Prüfung teilnehmende Studierende als Prüfungsteilnehmer hinzugefügt werden.
<li>Werden neue TeilnehmerInnen zur Prüfung hinzugefügt oder Bestehende entfernt, nachdem den TeilnehmerInnen bereits Sitzplätze zugewiesen wurden, muss diese Zuordnung wiederholt werden.</li></ul>
<strong>Achtung:</strong> Wenn es bei der Erstellung der Instanz nicht explizit ausgewählt wurde sind nicht automatisch alle KursteilnehmerInnen in PANDA auch Prüfungsteilnehmer. Diese müssen hier manuell hinzugefügt werden damit die Prüfungsorganisation funktioniert.';
$string['helptext_setTextfield']= 'Hier kann ein beliebiger <strong>Freitext</strong> für die Prüfung eingetragen werden, welcher den PrüfungsteilnehmerInnen nach dem Speichern sofort in deren Ansicht angezeigt wird. <br /> <br />
Auf diese Weise können den PrüfungsteilnehmerInnen etwa unkompliziert Hinweise zu den in der Prüfung erlaubten Hilfsmitteln mitgeteilt werden. Neben einfachen Texten können dabei auch komplexere Elemente wie etwa Bilder oder (soweit von Moodle unterstützt) sogar Formeln verwendet werden. <br /> <br />
Diese Funktion ist rein optional. Wenn Sie also z.B. keine Hinweise für die PrüfungsteilnehmerInnen haben können Sie das unten stehende Feld auch einfach leer lassen und auf den Button „Abbrechen“ klicken. <br /> <br />
<strong>Hinweis:</strong>  Diese Funktion ist vorwiegend für Mitteilungen gedacht, die nicht zeitkritisch sind. Möchten Sie die PrüfungsteilnehmerInnen etwa am Tag vor der Prüfung über einen kurzfristigen Wechsel der Prüfungsräume informieren empfehlen wir dafür die Nutzung der Funktion „Nachricht an Teilnehmer schreiben“ auf der Übersichtsseite.
Dadurch erhalten die PrüfungsteilnehmerInnen sofort eine E-Mail und können so die eingetragenen Informationen selbst dann mitbekommen, wenn sie nicht aktiv in PANDA nachsehen.';
$string['helptext_sendGroupmessages']= 'Auf dieser Seite kann der Betreff und der Inhalt einer <strong>Nachricht</strong> eingegeben werden, welche nach der Betätigung des Buttons „Mail abschicken“ <strong>an alle</strong> zur Prüfung als <strong>Teilnehmer</strong> hinzugefügten Studierenden gesendet wird. <br /> <br />
Diese bekommen die Nachricht direkt nach dem Abschicken als E-Mail an ihren universitären E-Mail-Account geschickt und können so z. B. einfach auf kurzfristige Änderungen (etwa der Prüfungszeiten oder -Räume) aufmerksam gemacht werden. <br /> <br />
Diese Funktion ist rein optional, Sie müssen auf diesem Wege keine Nachricht an die PrüfungsteilnehmerInnen senden. <br /> <br />
<strong>Hinweis:</strong>  Um den PrüfungsteilnehmerInnen ausführlichere Hinweise etwa zu den in der Klausur erlaubten Hilfsmittel zu geben kann auch die Funktion „Freitextfeld bearbeiten“ auf der Übersichtsseite genutzt werden.';

//errors and permissions
$string['missingidandcmid'] = 'Kurs ID fehlt (missingcmid)';
$string['nopermissions'] = 'Sie haben keine Berechtigung dies zu tun.';

//universal
$string['modulename'] = 'Prüfungsorganisation';
$string['modulenameplural'] = 'Prüfungsorganisationen';
$string['pluginname'] = 'Prüfungsorganisation';

//addnewinstance
$string['modulename_help'] = 'Mithilfe der PANDA-Prüfungsorganisation können Sie Prüfungen für Ihren Kurs einfache online organisieren und somit auch Prüfungen mit vielen Teilnehmern bequem verwalten.';
$string['exammanagementname'] = 'Prüfungsorganisation';
$string['exammanagement:enable exam management'] = 'Prüfungsorganisation aktivieren';
$string['messageprovider:exam management messages'] = 'Nachrichten zur Prüfungsorganisation';
$string['pluginadministration'] = 'exam management administration';

//capabilities
$string['exammanagement:addinstance'] = 'Neue Prüfungsorganisation hinzufügen';
$string['exammanagement:viewinstance'] = 'Prüfungsorganisation ansehen';
$string['exammanagement:viewparticipantspage'] = 'Teilnehmeransicht ansehen';
$string['exammanagement:takeexams'] = 'Prüfung ablegen';
$string['exammanagement:sendgroupmessage'] = 'Nachricht an Teilnehmer senden';
$string['exammanagement:addDefaultRooms'] = 'Standardräume importieren';
