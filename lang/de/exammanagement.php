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
$string['overview'] = 'Überblick';

//exammanagement_overview.mustache phases
$string['phase_one'] = 'Vor der Prüfung';
$string['phase_two'] = 'Für die Prüfung';
$string['phase_three'] = 'Nach der Korrektur';
$string['phase_four'] = 'Nach der Prüfung';
$string['phase_five'] = 'Klausureinsicht (optional)';
$string['exam_appointment'] = 'Prüfungstermin';
$string['minimize_phase'] = 'Phase minimieren';
$string['maximize_phase'] = 'Phase öffnen';

//exammanagement_overview.mustache states
$string['state_optional'] = 'Optional';
$string['state_required'] = 'Zwingend';
$string['state_success'] = 'Erfolgreich';
$string['state_notset'] = 'Nicht gesetzt';
$string['state_notpossible_participants_missing'] = 'Teilnehmer fehlen';
$string['state_notpossible_rooms_missing'] = 'Räume fehlen';
$string['state_notpossible_examtime_missing'] = 'Datum fehlt';
$string['state_notpossible_assignedplaces_missing'] = 'Sitzplatzzuweisung fehlt';
$string['state_notpossible_tasks_missing'] = 'Aufgaben fehlen';
$string['state_notpossible_results_missing'] = 'Ergebnisse fehlen';
$string['state_notpossible_correctioncompleted_missing'] = 'Korrekturabschluss fehlt';
$string['state_notpossible_examreviewtime_missing'] = 'Zeit der Klausureinsicht fehlt';
$string['state_notpossible_examreviewroom_missing'] = 'Raum der Klausureinsicht fehlt';
$string['state_loading'] = 'Lädt ...';

//exammanagement_overview.mustache workstages buttons
$string['choose_rooms'] = 'Räume auswählen';
$string['set_date'] = 'Datum festlegen';
$string['add_participants'] = 'Teilnehmer hinzufügen';
$string['configure_tasks'] = 'Aufgaben konfigurieren';
$string['edit_textfield'] = 'Freitextfeld bearbeiten';
$string['send_groupmessage'] = 'Nachricht schreiben';
$string['assign_places'] = 'Sitzplätze zuweisen';
$string['export_seatingplan'] = 'Sitzplan exportieren';
$string['export_barcodes'] = 'Prüfungsetiketten exportieren';
$string['export_participantslist_names'] = 'Nach Namen geordnet';
$string['export_participantslist_places'] = 'Nach Sitzplätzen geordnet';
$string['import_bonuspoints'] = 'Bonuspunkte importieren';
$string['configure_gradingscale'] = 'Notenschlüssel konfigurieren';
$string['add_examresults'] = 'Ergebnisse eintragen';
$string['check_results'] = 'Ergebnisse prüfen';
$string['export_as_pdf'] = 'PDF exportieren';
$string['export_as_excel'] = 'Excel-Dokument exportieren';
$string['export_as_paultext'] = 'PAUL-Textdokument exportieren';
$string['delete_examdata'] = 'Prüfungsdaten löschen';
$string['examreview_dateroom'] = 'Datum und Räume festlegen';
$string['change_examresults'] = 'Prüfungsergebnisse ändern';

//chooseRoomsForm.php
$string['import_default_rooms'] = 'Standardräume importieren';
$string['add_custom_room'] = 'Eigenen Prüfungsraum anlegen';

// addDefaultRoomsForm.php
$string['import_default_rooms_from_file'] = 'Standardräume aus Datei importieren';
$string['import_default_rooms_str'] = 'Hier können die allen Dozenten als Standardprüfungsräume zur Auswahl stehenden Räume von Administratoren aus einer Text-Datei importiert werden.';
$string['default_rooms_already_exists'] = 'Es wurden bereits Standardräume importiert. Diese werden durch den Import aus einer neuen Datei überschrieben.';

// addCustomRoomForm.php
$string['add_room'] = 'Raum anlegen';
$string['change_room'] = 'Raum ändern';
$string['delete_room'] = 'Raum löschen';
$string['customroom_name'] = 'Name des Raums';
$string['customroom_placescount'] = 'Anzahl der Sitzplätze';
$string['customroom_description'] = 'Optionale Beschreibung für die Anzeige des Raums bei der Raumauswahl';

//dateTimeForm.php
$string['set_date_time'] = 'Prüfungstermin festlegen';

//addParticipantsForm.php
$string['view_participants'] = 'Teilnehmer ansehen';
$string['import_participants'] = 'Teilnehmer hinzufügen';
$string['import_new_participants'] = 'Andere Teilnehmer hinzufügen';
$string['import_participants_from_file'] = 'Teilnehmer aus Datei hinzufügen';
$string['import_course_participants'] = 'Kursteilnehmer hinzufügen';
$string['view_added_partipicants'] = 'Liste aller zur Prüfung hinzugefügten Teilnehmer.';
$string['view_added_and_course_partipicants'] = 'Liste aller bisher zur Prüfung hinzugefügten Teilnehmer sowie aller Kursteilnehmer.';
$string['participants'] = 'Teilnehmer';
$string['matriculation_number'] = 'Matrikelnummer';
$string['course_groups'] = 'Kursgruppen';
$string['import_state'] = 'Status';
$string['newmatrnr'] = 'Benutzer werden zur Prüfung hinzugefügt.';
$string['badmatrnr'] = 'Zeilen mit ungültigen Matrikelnummern (Benutzer können nicht zur Prüfung hinzugefügt werden).';
$string['oddmatrnr'] = 'Benutzer mit Warnungen (können trotzdem hinzugefügt werden).';
$string['existingmatrnr'] = 'Benutzer sind bereits Prüfungsteilnehmer (keine Änderungen).';
$string['deletedmatrnr'] = 'Benutzer werden gelöscht.';
$string['add_to_exam'] = 'Zur Prüfung hinzufügen';
$string['import_from_excel_file'] = 'Externe Teilnehmer aus Excel-Datei importieren (Matrikelnummern in beliebiger Spalte) und zur Prüfung hinzufügen.';
$string['paul_file'] = 'PAUL-Datei';
$string['import_from_paul_file'] = 'Externe Teilnehmer von aus PAUL exportierter Datei importieren (Einträge mit Tabulator getrennt; die ersten zwei Zeilen enthalten Prüfungsinformationen) und zur Prüfung hinzufügen.';
$string['no_participants_added'] = 'Bisher wurden keine Teilnehmer zur Prüfung hinzugefügt.';
$string['read_file'] = 'Datei einlesen';
$string['state_added_to_exam'] = 'Prüfungsteilnehmer';
$string['state_added_to_exam_no_moodle'] = 'Prüfungsteilnehmer (ohne PANDA Benutzerkonto)';
$string['state_courseparticipant'] = 'Kursteilnehmer';
$string['state_newmatrnr'] = 'Neu eingelesen';
$string['state_newmatrnr_no_moodle'] = 'Neu eingelesen (ohne PANDA Benutzerkonto)';
$string['state_badmatrnr'] = 'Ungültige Matrikelnummer';
$string['state_doubled'] = 'Doppelte Matrikelnummer';
$string['state_oddmatrnr_nocourseparticipant'] = 'Neu eingelesen (kein Kursteilnehmer)';
$string['state_existingmatrnr'] = 'Bereits Prüfungsteilnehmer';
$string['state_deletedmatrnr'] = 'Gelöscht';

//showParticipants.php
$string['delete_all_participants'] = 'Alle Teilnehmer löschen';

//configureTasksForm.php
$string['configure_tasks_str'] = 'Aufgaben konfigurieren';
$string['configure_tasks_text'] = 'Hier kann die Maximalpunktzahl für jede Aufgabe der Prüfung eingestellt werden.';
$string['add_remove_tasks'] = 'Aufgaben hinzufügen oder entfernen:';
$string['task'] = 'Aufgabe';
$string['points'] = 'Punkte';
$string['total'] = 'Summe';

//textfield.php
$string['add_text_str'] = 'Freitext hinzufügen';
$string['add_text_text'] = 'Der unten eingegebene Text wird den Teilnehmern in der Teilnehmeransicht der Aktivität Prüfungsorganisation angezeigt.';

//groupmessagesForm.php
$string['groupmessages_str'] = 'Nachrichtentext hinzufügen';
$string['groupmessages_text_1'] = 'Der unten eingegebene Text wird ';
$string['groupmessages_text_2'] = ' zur Prüfung hinzugefügten Teilnehmern als Email zugeschickt.';
$string['groupmessages_text_3'] = ' Prüfungsteilnehmer besitzen kein PANDA-Benutzerkonto und werden diese Nachricht deshalb nicht automatisch erhalten. Kontaktieren Sie diese Teilnehmer deshalb unter deren unten angegebenen Universitäts-E-Mail-Adressen:';

//importBonusForm.php
$string['import_bonus_str'] = 'Bonuspunkte importieren';
$string['import_bonus_text'] = 'Hier können von den Teilnehmenden errungene Bonuspunkte importiert und in Bonusnotenschritte für die Prüfung umgerechnet werden.';
$string['set_bonussteps'] = 'Bonusnotenschritte festlegen';
$string['add_remove_bonusstep'] = 'Bonusschritt hinzufügen oder entfernen:';
$string['bonusstep'] = 'Bonusnotenschritt (maximal 3)';
$string['required_points'] = 'Für Notenschritt erforderliche Punkte';
$string['configure_fileimport'] = 'Dateiimport konfigurieren';
$string['import_mode'] = 'Art des Dateiimports';
$string['moodle_export'] = 'Bewertungsexport aus Moodle';
$string['individual'] = 'Individuell';
$string['idfield'] = 'Spalte in der die Benutzeridentifikatoren stehen (z. B. A, B, C ... ; Beim Import von aus PANDA exportierten Bewertungen automatisch gesetzt)';
$string['pointsfield'] = 'Spalte welche die zu wertenden Bonuspunkte enthält (z. B. A, B, C ...)';
$string['import_bonus_from_file'] = 'Bonuspunkte aus Excel-Datei importieren; Benutzeridentifikator (in PANDA hinterlegte E-Mailadresse beim Bewertungsimport oder Matrikelnummer beim individuellen Import und Bonuspunkte müssen in den oben ausgewählten Spalten stehen).';

//configureGradingscaleForm.php
$string['configure_gradingscale_str'] = 'Notenschlüssel konfigurieren';
$string['configure_gradingscale_totalpoints'] = 'Die maximale Anzahl von Punkten ist';

//inputResultsForm.php
$string['input_results_str'] = 'Prüfungsergebnisse eintragen';
$string['matrnr_barcode'] = 'Barcode / Matrikelnummer';
$string['participant'] = 'Teilnehmer';
$string['exam_state'] = 'Prüfungsstatus';
$string['exam_points'] = 'Prüfungspunkte';
$string['not_participated'] = 'NT';
$string['fraud_attempt'] = 'Betrugsversuch';
$string['ill'] = 'Krank';
$string['max_points'] = 'Maximale Punkte';
$string['save_and_next'] = 'Speichern und zum Nächsten';
$string['validate_matrnr'] = 'Matrikelnummer validieren';
$string['confirm_matrnr'] = 'Die Bestätigung der Matrikelnummer ist auch über Drücken der Enter- bzw. Return-Taste möglich.';
$string['input_other_matrnr'] = 'Matrikelnummer ändern';

//showResultsForm.php
$string['show_results_str'] = 'Teilnehmer- und Ergebnisübersicht';
$string['matriculation_number_short'] = 'Matr. Nr.';
$string['result'] = 'Ergebnis';
$string['resultwithbonus'] = 'Ergebnis inkl. Bonus';
$string['bonussteps'] = 'Bonusschritte';
$string['change_bonus'] = 'Bonus ändern';
$string['cancel'] = 'Zurück zur Prüfungsorganisation';
$string['gradingscale_not_set'] = 'Um automatisch eine Note berechnen zu lassen muss zunächst der Notenschlüßel eingetragen werden.';
$string['nt'] = 'NT';
$string['fa'] = 'Betrugsversuch';
$string['ill'] = 'Krank';

//participant list
$string['participantslist'] = 'Teilnehmerliste';
$string['participantslist_names'] = 'Teilnehmerliste_Namen';
$string['participantslist_places'] = 'Teilnehmerliste_Plätze';
$string['internal_use'] = 'Nur für die Prüfungsorganisation!';
$string['lastname'] = 'Name';
$string['firstname'] = 'Vorname';
$string['matrno'] = 'Matr.-Nr.';
$string['room'] = 'Raum';
$string['place'] = 'Platz';

// seatingplan
$string['seatingplan'] = 'Sitzplan';
$string['total_seats'] = 'Plätze';
$string['lecture_room'] = 'Hörsaal';
$string['places_differ'] = 'Dieser Plan kann von der tatsächlichen Platznummerierung abweichen.';
$string['places_alternative'] = 'In diesem Fall nutzen Sie bitte die Nummerierung dieses Plans.';
$string['numbered_seats_usable_seats'] = 'nummerierte Sitze = benutzbare Sitze';

// examlabels
$string['examlabels'] = 'Prüfungsetiketten';
$string['required_label_type'] = 'Benoetigter Etikettentyp:';

// exoprtResultsExamReview.php
$string['pointslist_examreview'] = 'Punkteliste Klausureinsicht';

// exportResultsPercentages.php
$string['percentages'] = 'Prozent';
$string['pointslist_percentages'] = 'Punkteliste Prozente';

// exportResultsStatistics.php
$string['examresults_statistics'] = 'Statistik der Prüfungsergebnisse';
$string['examresults_statistics_description'] = 'Statistik der Prüfungsergebnisse als MS Excel Datei';
$string['examresults_statistics_category'] = 'Statistik der Prüfungsergebnisse';
$string['overview'] = 'Übersicht';
$string['examname'] = 'Name der Prüfung';
$string['examterm'] = 'Semester';
$string['examdate'] = 'Prüfungsdatum';
$string['examtime'] = 'Prüfungsbeginn';
$string['examrooms'] = 'Prüfungsräume';
$string['grade'] = 'Note';
$string['nobonus'] = 'Ohne Bonus';
$string['withbonus'] = 'Mit Bonus';
$string['inpercent'] = 'in %';
$string['registered'] = 'Angemeldet';
$string['passed'] = 'Bestanden';
$string['notpassed'] = 'Nicht bestanden';
$string['notrated'] = 'Nicht bewerted';
$string['tasks_and_boni'] = 'Aufgaben und Bonuspunkte';
$string['mean'] = 'Mittelwert';
$string['count'] = 'Anzahl';
$string['details'] = 'Details';

// exportResultsPAULFile.php
$string['results'] = 'Prüfungsergebnisse';

// examReviewDateRoomForm.php
$string['examreview_dateroom'] = 'Datum und Räume für Klausureinsicht';
$string['examreview_date'] = 'Datum';
$string['examreview_room'] = 'Räume (als Freitext eintragen)';

//helptexts
$string['help'] = 'Hilfe';

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
$string['helptext_addDefaultRooms']= 'Hier können die Standardräume importiert werden ...';
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
$string['helptext_addCourseParticipants']= 'Hier können die Kursteilnehmer importiert werden ...';
$string['helptext_configureTasks']= 'Hier können Aufgaben gesetzt werden ...';
$string['helptext_setTextfield']= 'Hier kann ein beliebiger <strong>Freitext</strong> für die Prüfung eingetragen werden, welcher den PrüfungsteilnehmerInnen nach dem Speichern sofort in deren Ansicht angezeigt wird. <br /> <br />
Auf diese Weise können den PrüfungsteilnehmerInnen etwa unkompliziert Hinweise zu den in der Prüfung erlaubten Hilfsmitteln mitgeteilt werden. Neben einfachen Texten können dabei auch komplexere Elemente wie etwa Bilder oder (soweit von Moodle unterstützt) sogar Formeln verwendet werden. <br /> <br />
Diese Funktion ist rein optional. Wenn Sie also z.B. keine Hinweise für die PrüfungsteilnehmerInnen haben können Sie das unten stehende Feld auch einfach leer lassen und auf den Button „Abbrechen“ klicken. <br /> <br />
<strong>Hinweis:</strong>  Diese Funktion ist vorwiegend für Mitteilungen gedacht, die nicht zeitkritisch sind. Möchten Sie die PrüfungsteilnehmerInnen etwa am Tag vor der Prüfung über einen kurzfristigen Wechsel der Prüfungsräume informieren empfehlen wir dafür die Nutzung der Funktion „Nachricht an Teilnehmer schreiben“ auf der Übersichtsseite.
Dadurch erhalten die PrüfungsteilnehmerInnen sofort eine E-Mail und können so die eingetragenen Informationen selbst dann mitbekommen, wenn sie nicht aktiv in PANDA nachsehen.';
$string['helptext_sendGroupmessages']= 'Auf dieser Seite kann der Betreff und der Inhalt einer <strong>Nachricht</strong> eingegeben werden, welche nach der Betätigung des Buttons „Mail abschicken“ <strong>an alle</strong> zur Prüfung als <strong>Teilnehmer</strong> hinzugefügten Studierenden gesendet wird. <br /> <br />
Diese bekommen die Nachricht direkt nach dem Abschicken als E-Mail an ihren universitären E-Mail-Account geschickt und können so z. B. einfach auf kurzfristige Änderungen (etwa der Prüfungszeiten oder -Räume) aufmerksam gemacht werden. <br /> <br />
Diese Funktion ist rein optional, Sie müssen auf diesem Wege keine Nachricht an die PrüfungsteilnehmerInnen senden. <br /> <br />
<strong>Hinweis:</strong>  Um den PrüfungsteilnehmerInnen ausführlichere Hinweise etwa zu den in der Klausur erlaubten Hilfsmittel zu geben kann auch die Funktion „Freitextfeld bearbeiten“ auf der Übersichtsseite genutzt werden.';
$string['helptext_importBonus']= 'Hier können Bonuspunkte importiert und in Bonusnotenschritte für dier Prüfung umgewandelt werden ...';
$string['helptext_configureGradingscale']= 'Hier kann der Notenschlüssel konfiguriert werden ...';
$string['helptext_inputResults']= 'Hier können die Prüfungsergebnisse eingetragen werden ...';
$string['helptext_showResults']= 'Hier können die eingetragenen Ergebnisse geprüft werden ...';
$string['helptext_examReviewDateRoom']= 'Hier können ein Datum und Räume für die Klausureinsicht eingetragen werden ...';
$string['helptext_addCustomRoom'] = 'Hier kann ein eigener Prüfungsraum angelegt werden ...';

//errors and permissions
$string['missingidandcmid'] = 'Ungültige Kursmodul-ID';
$string['nopermissions'] = 'Sie haben keine Berechtigung dies zu tun.';
$string['err_underzero'] = 'Die eingegebene Zahl darf nicht kleiner als Null sein.';
$string['err_novalidinteger'] = 'Der eingegebene Wert ist keine gültige Zahl.';
$string['err_overmaxpoints'] = 'Die eingegebene Zahl überschreitet die Maximalpunktzahl.';
$string['err_bonusstepsnotcorrect'] = 'Mindestens einer der Bonusnotenschritte passt nicht zu den anderen.';
$string['err_gradingstepsnotcorrect'] = 'Mindestens einer der Notenschritte passt nicht zu den anderen.';
$string['err_taskmaxpoints'] = 'Die eingetragene Punktzahl überschreitet die Maximalpunktzahl der Aufgabe.';
$string['err_roomsdoubleselected'] = 'Derselbe Raum wurde mehrfach in unterschiedlichen Belegungen als Prüfungsraum gewählt.';
$string['err_invalidcheckboxid_rooms'] = 'Ungültige Raumid.';
$string['err_invalidcheckboxid_participants'] = 'Ungültige Teilnehmerid.';
$string['err_nonvalidmatrnr'] = 'Ungültige Matrikelnummer.';
$string['err_customroomname_taken'] = 'Raumname bereits vergeben';
$string['err_filloutfield'] = 'Bitte Feld ausfüllen';
$string['err_nofile'] = 'Bitte Datei auswählen';
$string['err_noalphanumeric'] = 'Enthält ungültige Zeichen';

//universal
$string['modulename'] = 'Prüfungsorganisation';
$string['modulenameplural'] = 'Prüfungsorganisationen';
$string['pluginname'] = 'Prüfungsorganisation';

//addnewinstance
$string['modulename_help'] = 'Mithilfe der PANDA-Prüfungsorganisation können Sie Prüfungen für Ihren Kurs einfach online organisieren und somit auch Prüfungen mit vielen Teilnehmern bequem verwalten.';
$string['exammanagementname'] = 'Prüfungsorganisation';
$string['exammanagement:enable exam management'] = 'Prüfungsorganisation aktivieren';
$string['messageprovider:exam management messages'] = 'Nachrichten zur Prüfungsorganisation';
$string['pluginadministration'] = 'Administration der Prüfungsverwaltung';

//capabilities
$string['exammanagement:addinstance'] = 'Neue Prüfungsorganisation hinzufügen';
$string['exammanagement:viewinstance'] = 'Prüfungsorganisation ansehen';
$string['exammanagement:viewparticipantspage'] = 'Teilnehmeransicht ansehen';
$string['exammanagement:takeexams'] = 'Prüfung ablegen';
$string['exammanagement:sendgroupmessage'] = 'Nachricht an Teilnehmer senden';
$string['exammanagement:addDefaultRooms'] = 'Standardräume importieren';
