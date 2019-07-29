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
 * @copyright   coactum Gmbh 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//exammanagement_participantsview.mustache - can be seen on /view.php as participant
$string['examinformation'] = 'Informationen zur Prüfung';
$string['state'] = 'Status';
$string['no_participant'] = 'Sie sind nicht zur Prüfung angemeldet.';
$string['added_to_exam'] = 'Sie sind zur Prüfung angemeldet.';
$string['date'] = 'Datum';
$string['no_date_entered'] = 'Es wurde noch kein Datum für die Prüfung festgelegt.';
$string['time'] = 'Uhrzeit';
$string['no_time_entered'] = 'Es wurde noch keine Uhrzeit für die Prüfung festgelegt.';
$string['room'] = 'Raum';
$string['no_room_assigned'] = 'Es wurde noch kein Prüfungsraum zugewiesen.';
$string['seat'] = 'Platz';
$string['no_seat_assigned'] = 'Es wurde noch kein Sitzplatz zugewiesen.';
$string['hint'] = 'Hinweis';
$string['no_exam_hint'] = 'Es ist kein Prüfungshinweis verfügbar.';
$string['bonus_for_exam'] = 'Bonusnotenschritte für die Klausur';
$string['bonus_for_exam_added_one'] = 'Für die Klausur haben Sie ';
$string['bonus_for_exam_added_two'] = 'Bonusnotenschritte erreicht.';
$string['bonus_for_exam_not_added'] = 'Es wurden bisher keine Bonusnotenschritte für Sie eingetragen.';
$string['exam_review'] = 'Klausureinsicht';
$string['exam_review_one'] = 'Die Klausurkorrektur ist nun abgeschlossen. Am ';
$string['exam_review_two'] = 'findet die Klausureinsicht statt in Raum ';
$string['examdata_deleted_one'] = 'Die am ';
$string['examdata_deleted_two'] = 'durchgeführte Prüfung ist nun abgeschlossen.';

//exammanagement_overview.mustache - can be seen on /view.php as lecturer
$string['maintitle'] = 'Prüfungsorganisation';
$string['view'] = 'Überblick';
$string['js_confirm_correction_completion'] = 'Diese Aktion schließt die Korrekturphase ab. Danach haben Sie 3 Monate Zeit, alle Prüfungsergebnisse zu exportieren, bevor diese aus Datenschutzgründen unwiederbringlich gelöscht werden.';
$string['data_deleted'] = 'Die Prüfungsdaten aller Teilnehmenden dieser Prüfungsorganisation wurden aus Datenschutzgründen drei Monate nach dem Abschluss der Korrekturphase gelöscht. Diese Prüfungsorganisation kann somit nicht mehr weiter genutzt werden, es können lediglich noch die Basisdaten der Prüfung eingesehen werden.';

//exammanagement_overview.mustache phases - can be seen on /view.php as lecturer
$string['phase_one'] = 'Vor der Prüfung';
$string['phase_two'] = 'Für die Prüfung';
$string['phase_three'] = 'Nach der Korrektur';
$string['phase_four'] = 'Nach der Prüfung';
$string['phase_five'] = 'Klausureinsicht (optional)';
$string['exam_appointment'] = 'Prüfungstermin';
$string['minimize_phase'] = 'Phase minimieren';
$string['maximize_phase'] = 'Phase öffnen';
$string['participants_and_results_overview'] = 'Teilnehmer & Ergebnisübersicht';
$string['exam_rooms'] = 'Prüfungsräume';
$string['exam_date'] = 'Prüfungstermin';
$string['exam_participants'] = 'Prüfungsteilnehmer';
$string['exam_tasks'] = 'Prüfungsaufgaben';
$string['freetext_field'] = 'Freitextfeld';
$string['message_to_participants'] = 'Nachricht an Teilnehmer';
$string['assigning_places'] = 'Sitzplatzzuweisung';
$string['seatingplan'] = 'Sitzplan';
$string['set_visibility_of_examdate'] = 'Prüfungsdatum sichtbar schalten';
$string['exam_labels'] = 'Prüfungsetiketten';
$string['set_visibility_of_examrooms_and_places'] = 'Prüfungsräume und Sitzplätze sichtbar schalten';
$string['places'] = 'Sitzplätze';
$string['participants_lists'] = 'Teilnehmerlisten';
$string['bonus_gradesteps'] = 'Bonusnotenschritte';
$string['gradingscale'] = 'Notenschlüssel';
$string['exam_results'] = 'Prüfungsergebnisse';
$string['exam_results_overview'] = 'Ergebnisübersicht';
$string['complete_correction'] = 'Korrektur abschließen';
$string['points_for_exam_review'] = 'Punkte für Klausureinsicht';
$string['results_with_percentages'] = 'Ergebnisse mit Prozentangaben';
$string['results_and_statistics'] = 'Ergebnisse und Statistik';
$string['results_for_exam_office'] = 'Ergebnisse für das Prüfungsamt';
$string['delete_exam_data'] = 'Prüfungsdaten löschen';
$string['date_and_room_exam_review'] = 'Termin und Raum für die Klausureinsicht';
$string['set_visibility_of_exam_review_information'] = 'Informationen zur Klausureinsicht sichtbar schalten';
$string['altering_exam_results'] = 'Änderung der Prüfungsergebnisse';
$string['export_altered_exam_results'] = 'Export der geänderten Ergebnisse';

//exammanagement_overview.mustache states - can be seen on /view.php as lecturer
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

//exammanagement_overview.mustache work step texts - can be seen on /view.php as lecturer
$string['important_note'] = 'Wichtiger Hinweis:';
$string['note'] = 'Hinweis:';
$string['exam_rooms_set_one'] = 'Es wurden bereits die folgenden ';
$string['exam_rooms_set_two'] = '<strong>Räume</strong> mit insgesamt';
$string['exam_rooms_set_three'] = ' <strong>Sitzplätzen</strong> für die Prüfung ausgewählt';
$string['exam_rooms_not_set'] = 'Es wurden noch keine Räume für die Prüfung ausgewählt.';
$string['at'] = 'um';
$string['deleted_room'] = 'Gelöschter Raum';
$string['exam_date_set_one'] = 'Die Prüfung findet am ';
$string['exam_date_set_two'] = 'statt';
$string['exam_date_not_set'] = 'Es wurden noch kein Datum und keine Uhrzeit für die Prüfung festgelegt.';
$string['exam_participants_set_one'] = 'Teilnehmerinnen und Teilnehmer sind zur Prüfung angemeldet.';
$string['exam_participants_not_set'] = 'Es wurden noch keine Teilnehmerinnen und Teilnehmer zur Prüfung hinzugefügt.';
$string['exam_tasks_set_one'] = 'Es wurden bereits ';
$string['exam_tasks_set_two'] = '<strong>Prüfungsaufgaben</strong> mit insgesamt ';
$string['exam_tasks_set_three'] = '<strong>Punkten</strong> angelegt';
$string['exam_tasks_not_set'] = 'Es wurden noch keine Aufgaben konfiguriert.';
$string['textfield_set'] = 'Im Textfeld steht folgender Inhalt: ';
$string['textfield_not_set'] = 'Es wurde noch kein Inhalt für das Textfeld eingetragen.';
$string['message_to_participants_str'] = 'Hier können Nachrichten (PANDA Mitteilungen) an alle zur Prüfung hinzugefügten Teilnehmerinnen und Teilnehmer versendet werden.';
$string['places_assigned_one'] = 'Teilnehmenden wurden bereits Sitzplätze zugewiesen.';
$string['places_assigned_two'] = 'Die Sitzplatzzuweisung ist damit erfolgreich abgeschlossen.';
$string['places_assigned_three'] = 'Einigen Teilnehmenden müssen somit noch Sitzplätze zugewiesen werden, bevor mit den weiteren Arbeitsschritten fortgefahren werden kann.';
$string['places_assigned_note'] = 'Bei einer (erneuten) Durchführung der automatischen Sitzplatzzuweisung werden alle bereits bestehenden Zuweisungen überschrieben.';
$string['export_seatingplan_str'] = 'Hier kann der Sitzplan nach Sitzplatz oder nach Matrikelnummern sortiert als PDF-Dokument exportiert werden.';
$string['information_visible'] = 'Die Informationen wurden für die Teilnehmer sichtbar geschaltet.';
$string['information_not_visible'] = 'Die Informationen wurden noch nicht für die Teilnehmer sichtbar geschaltet.';
$string['export_examlabels_str'] = 'Hier können Prüfungsetiketten als Barcodes exportiert werden.';
$string['export_examlabels_note'] = 'Erst wenn allen Teilnehmenden Sitzplätze zugewiesen wurden erscheinen diese auf den Prüfungsetiketten.';
$string['export_participantslists_str'] = 'Hier können Teilnehmerlisten nach Nachname oder nach Sitzplatz sortiert als PDF-Dokument exportiert werden.';
$string['export_participantslists_note'] = 'Diese Listen sind nur für den internen Gebrauch durch die Lehrenden bestimmt und dürfen aus Datenschutzgründen nicht veröffentlicht werden!';
$string['no_exam_date_set_str'] = 'Noch wurden kein Prüfungstermin und keine Prüfungsräume festgelegt.';
$string['bonussteps_set_one'] = 'Es wurden bisher Bonusnotenschritte für ';
$string['bonussteps_set_two'] = 'Teilnehmende importiert.';
$string['bonussteps_not_set'] = 'Es wurden noch keine Bonusnotenschritte eingetragen.';
$string['gradingscale_set_one'] = 'Es wurde bereits ein Notenschlüssel konfiguriert.';
$string['gradingscale_not_set'] = 'Es wurde noch kein Notenschlüssel konfiguriert.';
$string['results_set_one'] = 'Es wurden bisher ';
$string['results_set_two'] = 'Prüfungsergebnisse eingetragen.';
$string['results_not_set'] = 'Es wurden noch keine Prüfungsergebnisse eingetragen.';
$string['exam_results_overview_str'] = 'Hier können alle bereits eingegebenen Prüfungsergebnisse angesehen und manuell geändert werden.';
$string['complete_correction_str'] = 'Die in dieser Prüfungsorganisation eingetragenen Daten sind sehr sensibel und müssen deshalb aus Datenschutzgründen gelöscht werden, sobald sie nicht mehr benötigt werden. Nachdem Sie durch Umlegen des Schalters den Abschluss der Korrektur bestätigt haben haben Sie deshalb drei Monate Zeit, die Prüfungsergebnisse für eine weitere Verwendung zu exportieren, bevor diese automatisch gelöscht werden.';
$string['export_points_examreview_str'] = 'Hier können die erreichten Punkte als PDF-Dokument exportiert werden.';
$string['export_results_lists_note'] = 'Diese Punkteliste ist nur für den internen Gebrauch durch die Lehrenden bestimmt und darf aus Datenschutzgründen nicht veröffentlicht werden!';
$string['export_results_percentages_str'] = 'Hier können die Ergebnisse mit Prozentangaben als PDF-Dokument exportiert werden.';
$string['export_results_statistics_str'] = 'Hier können die Ergebnisse und Statistiken als Excel-Dokument exportiert werden.';
$string['export_results_paul_str'] = 'Hier können die Ergebnisse für das Prüfungsamt als PAUL-kompatibles Text-Dokument exportiert werden.';
$string['delete_data_one'] = 'Am';
$string['delete_data_two'] = 'werden alle in dieser Instanz gespeicherten Daten wie etwa Teilnehmerinnen, Prüfungsdetails und Prüfungsergebnisse automatisch gelöscht. Stellen Sie deshalb sicher, dass Sie bis dahin alle wichtigen Daten wie etwa Prüfungsergebnisse für eine weitere Verwendung über die Dokumentenexportfunktionen exportiert haben.';
$string['date_room_examreview_set_one'] = 'Die Klausureinsicht findet am';
$string['date_room_examreview_set_two'] = 'in Raum ';
$string['date_room_examreview_set_three'] = 'statt.';
$string['date_room_examreview_set_four'] = 'Die Klausureinsicht findet in Raum ';
$string['date_room_examreview_set_five'] = 'Die Klausureinsicht findet am ';
$string['date_room_examreview_not_set'] = 'Es wurden noch kein Termin und kein Raum für die Klausureinsicht festgelegt.';
$string['exam_results_altered_one'] = 'Es wurden bisher ';
$string['exam_results_altered_two'] = 'Prüfungsergebnisse nach der Klausureinsicht geändert.';
$string['no_examresults_altered'] = 'Bisher wurden noch keine Prüfungsergebnisse nach der Klausureinsicht geändert.';
$string['exam_results_altered_note'] = 'Hier erscheint nur die Zahl der Prüfungsergebnisse, die ab dem für die Klausureinsicht eingetragenen Zeitpunkt geändert wurden. Das Ändern eines Prüfungsergebnisses nach dem Klick auf die unten stehende Schaltfläche überschreibt dabei sämtliche vorher gespeicherten Ergebnisse für den oder die Teilnehmende. Stellen Sie deshalb sicher, dass Sie vor dem Ändern der Ergebnisse die alten Prüfungsergebnisse zu Sicherungszwecken einmal über die Dokumentenexport-Funktionen in der Phase "Nach der Klausur" exportiert haben.';
$string['export_altered_examresults_str'] = 'Hier können die nach der Klausureinsicht geänderten Ergebnisse als PAUL-kompatibles Text-Dokument exportiert werden.';
$string['export_altered_examresults_note'] = 'Diese Schaltfläche ermöglicht den einfachen Export aller seit dem Datum der Klausureinsicht geänderter Prüfungsergebnisse in einer Datei für das PAUL-Prüfungsamt. Möchten Sie stattdessen einen nach den eingelesenen PAUL-Listen getrennten Export der geänderten Ergebnisse stehen Ihnen dafür wieder die Möglichkeiten des Ergebnis-Exportes aus der Phase "Nach der Klausur" zur Verfügung.';

//exammanagement_overview.mustache work steps buttons - can be seen on /view.php as lecturer
$string['configure_password'] = 'Passwort konfigurieren';
$string['choose_rooms'] = 'Räume auswählen';
$string['set_date'] = 'Termin festlegen';
$string['add_participants'] = 'Teilnehmer hinzufügen';
$string['configure_tasks'] = 'Aufgaben konfigurieren';
$string['edit_textfield'] = 'Freitextfeld bearbeiten';
$string['send_groupmessage'] = 'Nachricht schreiben';
$string['assign_places'] = 'Sitzplätze automatisch zuweisen';
$string['assign_places_manually'] = 'Sitzplätze manuell zuweisen';
$string['export_seatingplan_place'] = 'Nach Sitzplatz sortiert';
$string['export_seatingplan_matrnr'] = 'Nach Matrikelnummer sortiert';
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
$string['examreview_dateroom'] = 'Termin und Raum für Klausureinsicht festlegen';
$string['change_examresults'] = 'Prüfungsergebnisse ändern';

//configurePasswordForm.php
$string['configurePassword'] = 'Passwort konfigurieren';
$string['configure_password'] = 'Hier kann ein Passwort für die Prüfungsorganisation gesetzt und geändert werden.';
$string['password'] = 'Passwort';
$string['reset_password'] = 'Passwort zurücksetzen';

//checkPasswordForm.php
$string['checkPassword'] = 'Passwort eingeben';
$string['check_password'] = 'Der oder die Dozentin hat für diese Prüfungsorganisation ein Passwort festgelegt. Geben Sie es ein, um Zugriff auf die Inhalte der Prüfungsorganisation zu erhalten.';
$string['confirm_password'] = 'Passwort bestätigen';
$string['reset_password_admin'] = 'Passwort zurücksetzen und alle Lehrenden benachrichtigen';
$string['request_password_reset'] = 'Zurücksetzen des Passwortes beim Support beantragen';

//checkPassword.php
$string['wrong_password'] = 'Passwort falsch. Bitte erneut versuchen.';
$string['password_reset_successfull'] = 'Das Passwort der Prüfungsorganisation wurde erfolgreich zurückgesetzt und alle Lehrenden des PANDA-Kurses wurden darüber via PANDA Benachrichtigung informiert.';
$string['password_reset_failed'] = 'Zurücksetzen des Passwortes aufgrund fehlender Berechtigungen fehlgeschlagen.';
$string['password_reset_request_successfull'] = 'Das Zurücksetzen des Passwortes der Prüfungsorganisation wurde erfolgreich beim Support beantragt. Sobald das Passwort zurückgesetzt wurde werden Sie und alle anderen Lehrenden des PANDA-Kurses darüber via PANDA-Nachricht informiert.';
$string['password_reset_request_failed'] = 'Beantragung des Passwort-Zurücksetzens fehlgeschlagen. Bitte kontaktieren Sie den Support auf üblichem Weg via E-Mail.';

//chooseRoomsForm.php
$string['chooseRooms'] = 'Prüfungsräume auswählen';
$string['choose_rooms_str'] = 'Die unten stehenden Räume können als Prüfungsräume gewählt werden.';
$string['import_default_rooms'] = 'Standardräume importieren';
$string['add_custom_room'] = 'Eigenen Prüfungsraum anlegen';
$string['add_default_room'] = 'Neuen Standardraum anlegen';
$string['roomid'] = 'Raum ID';
$string['exam_room'] = 'Raum';
$string['description'] = 'Beschreibung';
$string['room_type'] = 'Raumart';
$string['options'] = 'Optionen';
$string['no_seatingplan_available'] = 'Kein Sitzplan verfügbar';
$string['default_room'] = 'Standardraum';
$string['custom_room'] = 'Eigener Raum';
$string['change_room'] = 'Raum ändern';
$string['delete_room'] = 'Raum löschen';
$string['delete_defaultroom_confirm'] = 'Durch diese Aktion wird der gewählte Standardraum gelöscht. Falls dieser bereits von Lehrenden als Prüfungsraum ausgewählt wurde bleiben seine Informationen in den entsprechenden Prüfungsorganisationen zunächst erhalten, er kann jedoch nicht mehr als neuer Prüfungsraum ausgewählt oder für die (Neu-)Zuweisung von Sitzplätzen genutzt werden.';
$string['delete_room_confirm'] = 'Durch diese Aktion wird dieser selbst erstellte Raum gelöscht. Stellen Sie sicher, dass der Raum aktuell nicht als Prüfungsraum ausgewählt ist.';
$string['hint_room_modelling'] = '<strong>Hinweis:</strong> Einige Räume sind hier mehrfach aufgeführt. Dabei handelt es sich um unterschiedliche Modellierungen desselben Raumes. "1 Platz frei" bedeutet, dass jeder 2. Platz besetzt wird. "2 Plätze frei" bedeutet, dass jeder 3. Platz besetzt wird.';
$string['places_already_assigned_rooms'] = '<strong>Achtung:</strong> Es wurden bereits einigen Teilnehmenden Sitzplätze in diesem Raum zugewiesen. Falls dieser Raum jetzt als Prüfungsraum abgewählt wird wird die gesamte Sitzplatzzuweisung gelöscht und muss dann neu durchgeführt werden.';
$string['no_rooms_found'] = 'Keine Räume gefunden';

//chooseRooms.php
$string['room_deselected_as_examroom'] = 'Der Raum muss zunächst als Prüfungsraum abgewählt werden.';

// addDefaultRoomsForm.php
$string['addDefaultRooms'] = 'Standardräume importieren';
$string['import_default_rooms_str'] = 'Hier können die allen Dozenten als mögliche Prüfungsräume zur Auswahl stehenden Standardräume von Administratoren aus einer Text-Datei importiert werden.';
$string['default_rooms_already_exists'] = '<strong>Achtung:</strong> Es wurden bereits Standardräume importiert. Diese werden durch den Import aus einer neuen Datei überschrieben.';
$string['default_rooms_file_structure'] = 'Import der Standardräume aus Text-Datei (.txt). <br><strong>Aufbau</strong>: Pro Zeile ein Prüfungsraum. Erste Spalte systeminterne Raumid (Raumname_Variante; also z. B. Audimax_2), zweite Spalte benutzersichtbarer Raumname (z. B. Audimax), dritte Spalte benutzersichtbare Beschreibung inklusive Anzahl freigelassener sowie gesamter Plätze (z. B. 2 Plätze frei, 56 Plätze insgesamt), vierte Spalte zur Berechnung der Sitzplatzzahl benötigtes Array mit der Bezeichnung jedes einzelnen Platzes in json-Syntax (z. B. ["R/R01/P07","R/R01/P04","R/R01/P01"] ), fünfte Spalte Quellcode einer SVG-Datei mit dem Raumplan um diesen den Benutzern anzuzeigen (falls vorhanden, ansonsten leer lassen)';

// addCustomRoomForm.php
$string['addCustomRoom'] = 'Eigenen Prüfungsraum anlegen oder bearbeiten';
$string['change_custom_room_name'] = '<strong>Hinweis:</strong> Falls Sie den Namen eines bestehenden Raumes ändern wird stattdessen ein neuer Raum angelegt. In diesem Fall muss der alte Raum noch manuell gelöscht werden.';
$string['custom_room_places'] = '<strong>Hinweis:</strong> Der von Ihnen hier erstellte eigene Prüfungsraum erhält im System so viele Plätze wie von Ihnen unten angegeben werden, wobei die Nummerierung (unabhängig von der im Raum tatsächlich vorhandenen Sitzplatzanzahl oder deren Nummerieung) bei 1 startet und dann hoch zählt. Sie müssen also selbst sicherstellen, dass die von Ihnen angegebene Platzzahl mit den tatsächlich vorhandenen Sitzplätzen übereinstimmt und müssen zudem mögliche Abweichungen bei der Sitzplatznummerierung selbst anpassen.';
$string['customroom_name'] = 'Name des Raums';
$string['customroom_placescount'] = 'Anzahl der Sitzplätze';
$string['customroom_description'] = 'Optionale Beschreibung für die Anzeige des Raums bei der Raumauswahl';
$string['add_room'] = 'Raum speichern';
$string['no_description_new_room'] = 'Keine Beschreibung vorhanden.';

//editDefaultRoomForm.php
$string['editDefaultRoom'] = 'Standardraum bearbeiten';
$string['edit_defaultroom_str'] = 'Hier können Administratorinnen und Administratoren die vorhandenen Standardprüfungungsräume bearbeiten oder Neue erstellen.';
$string['general'] = 'Basisinformationen';
$string['roomid_internal'] = 'Raum ID (systemintern; Raumname_Variante, also z. B. L1.202_1, Audimax_2; erlaubte Zeichen: Buchstaben, Zahlen, Punkt und Unterstrich)';
$string['defaultroom_name'] = 'Name des Raums (benutzersichtbar, erlaubte Zeichen: Buchstaben, Zahlen, Punkt und Leerzeichen)';
$string['defaultroom_description'] = 'Beschreibung (benutzersichtbar, z. B. Informationen zur Modellierung  wie die Zahl freier Plätze zwischen zwei Sitzplätzen, erlaubte Zeichen: Buchstaben, Zahlen, Punkt, Minus und Leerzeichen)';
$string['defaultroom_placescount'] = 'Anzahl der besetzbaren Sitzplätze';
$string['placespreview'] = 'Benennung aller besetzbaren Sitzplätze';
$string['roomplan_available'] = 'Vorhandener Sitzplan';
$string['new_places'] = 'Neue Sitzplätze';
$string['edit_places'] = 'Sitzplätze bearbeiten';
$string['places_mode'] = 'Modus der Sitzplätze';
$string['placesmode_default'] = 'Standard';
$string['placesmode_rows'] = 'Reihenweise';
$string['placesmode_all_individual'] = 'Vollständig individuell';
$string['placesroom'] = 'Anzahl der Gesamtsitzplätze des Raumes';
$string['rowscount'] = 'Anzahl an Reihen im Raum';
$string['placesrow'] = 'Sitzplätze pro Reihe';
$string['placesfree'] = 'Freie Plätze zwischen zwei besetzbaren Sitzplätzen';
$string['one_place_free'] = '1 Platz frei (z. B. 1, 3, 5 ...)';
$string['two_places_free'] = '2 Plätze frei (z. B. 1, 4, 7 ...)';
$string['rowsfree'] = 'Freie Reihen';
$string['no_row_free'] = 'Keine Reihe frei';
$string['one_row_free'] = 'Eine Reihe frei';
$string['placesarray'] = 'Alle Sitzplätze (Bezeichnung aller Sitzplätze durch Komma getrennt, erlaubt Zeichen: Buchstaben, Zahlen, Punkt, Minus, Slash und Leerzeichen)';
$string['new_seatingplan'] = 'Neuer Sitzplan';
$string['defaultroom_svg'] = 'Sitzplan (Textdatei (.txt) mit dem Quellcode einer SVG des Raumplanes)';

//editDefaultRoom.php
$string['no_editable_default_room'] = 'Kein bearbeitbarer Standardraum da durch Dozent angelegt';

//setDateTimeForm.php
$string['setDateTime'] = 'Prüfungstermin festlegen';
$string['set_date_time_str'] = 'Hier können das Datum und die Uhrzeit der Prüfung ausgewählt werden.';

//viewParticipants.php
$string['viewParticipants'] = 'Teilnehmer ansehen';
$string['import_participants_from_file_recommended'] = 'Teilnehmer aus Datei hinzufügen (empfohlen)';
$string['import_course_participants_optional'] = 'Kursteilnehmer importieren (optional)';
$string['view_added_partipicants'] = 'Liste aller zur Prüfung hinzugefügten Teilnehmer.';
$string['participants'] = 'Teilnehmer';
$string['matriculation_number'] = 'Matrikelnummer';
$string['course_groups'] = 'Kursgruppen';
$string['import_state'] = 'Status';
$string['state_added_to_exam'] = 'Prüfungsteilnehmer';
$string['participants_without_panda_account'] = 'Prüfungsteilnehmer ohne PANDA Benutzerkonto';
$string['state_added_to_exam_no_moodle'] = 'Prüfungsteilnehmer (ohne PANDA Benutzerkonto)';
$string['delete_participant'] = 'Teilnehmer löschen';
$string['participant_deletion_warning'] = 'Durch diese Aktion werden der gewählte Prüfungsteilnehmende sowie alle für diesen eingetragenen Ergebnisse gelöscht.';
$string['delete_all_participants'] = 'Alle Teilnehmer löschen';
$string['all_participants_deletion_warning'] = 'Durch diese Aktion werden sämtliche Prüfungsteilnehmenden sowie alle für diese eingetragenen Ergebnisse gelöscht.';

//addParticipantsForm.php
$string['import_participants_from_file'] = 'Teilnehmer aus Datei hinzufügen';
$string['import_from_paul_file'] = 'Externe Teilnehmer von aus PAUL exportierter Datei importieren (Einträge mit Tabulator getrennt; die ersten zwei Zeilen enthalten Prüfungsinformationen) und zur Prüfung hinzufügen.';
$string['read_file'] = 'Datei einlesen';
$string['addParticipants'] = 'Teilnehmer hinzufügen';
$string['import_new_participants'] = 'Andere Teilnehmer hinzufügen';
$string['places_already_assigned_participants'] = '<strong>Achtung:</strong> Es wurden bereits Sitzplätze zugewiesen. Falls nun neue Prüfungsteilnehmende hinzugefügt werden müssen diesen noch Sitzplätze zugewiesen werden.';
$string['newmatrnr'] = 'Benutzer werden zur Prüfung hinzugefügt.';
$string['badmatrnr'] = 'Zeilen mit ungültigen Matrikelnummern (Benutzer können nicht zur Prüfung hinzugefügt werden).';
$string['oddmatrnr'] = 'Benutzer mit Warnungen (können trotzdem hinzugefügt werden).';
$string['existingmatrnr'] = 'Benutzer sind bereits Prüfungsteilnehmer (keine Änderungen).';
$string['deletedmatrnr'] = 'Prüfungsteilnehmer werden entfernt.';
$string['select_deselect_all'] = 'Alle aus-/abwählen';
$string['add_to_exam'] = 'Zur Prüfung hinzufügen';
$string['no_participants_added_page'] = 'Bisher wurden keine Teilnehmer zur Prüfung hinzugefügt.';
$string['state_newmatrnr'] = 'Neu eingelesen';
$string['state_newmatrnr_no_moodle'] = 'Neu eingelesen (ohne PANDA Benutzerkonto)';
$string['state_badmatrnr'] = 'Ungültige Matrikelnummer';
$string['state_doubled'] = 'Doppelte Matrikelnummer';
$string['state_oddmatrnr_nocourseparticipant'] = 'Neu eingelesen (kein Kursteilnehmer)';
$string['state_existingmatrnr'] = 'Bereits Prüfungsteilnehmer';
$string['state_deletedmatrnr'] = 'Werden gelöscht';

//addCourseParticipantsForm.php
$string['addCourseParticipants'] = 'Kursteilnehmer hinzufügen';
$string['state_courseparticipant'] = 'Kursteilnehmer';
$string['view_added_and_course_partipicants'] = 'Liste aller bisher zur Prüfung hinzugefügten Teilnehmer sowie aller Kursteilnehmer.';
$string['deletedmatrnr_no_course'] = 'Prüfungsteilnehmer werden entfernt (da sie keine Kursteilnehmer sind).';
$string['existingmatrnr_course'] = 'Kursteilnehmer sind bereits Prüfungsteilnehmer (keine Änderungen).';
$string['course_participant_import_preventing_paul_export'] = '<strong>Achtung:</strong> Der Import der Kursteilnehmer als Prüfungsteilnehmer ist zwar möglich, allerdings werden diese Teilnehmer später beim Ergebnis-Export für das Prüfungsamt in einer eigenen Liste exportiert. Ihre Ergebnisse können somit gegebenenfalls nicht vernünftig in PAUL eingetragen werden. Wenn Sie vorhaben, die Prüfungsergebnisse in PAUL eintragen zu lassen, sollten Sie die Teilnehmer lieber mithilfe der aus PAUL exportierten Teilnehmerlisten der Prüfung importieren.';

//configureTasksForm.php
$string['configureTasks'] = 'Aufgaben konfigurieren';
$string['configure_tasks_text'] = 'Hier können die Anzahl und die Maximalpunktzahlen aller Prüfungsaufgaben festgelegt werden.';
$string['add_remove_tasks'] = 'Aufgaben hinzufügen oder entfernen:';
$string['task'] = 'Aufgabe';
$string['points'] = 'Punkte';
$string['total'] = 'Summe';
$string['results_already_entered'] = '<strong>Achtung:</strong> Es wurden bereits Prüfungsergebnisse eingetragen. Prüfen Sie bitte nach dem Ändern der Aufgaben, ob diese eventuell aktualisiert werden müssen.';
$string['gradingscale_already_entered'] = '<strong>Achtung:</strong> Es wurde bereits ein Notenschlüssel eingetragen. Prüfen Sie bitte nach dem Ändern der Aufgaben, ob dieser eventuell angepasst werden muss.';

//setTextfieldForm.php
$string['setTextfield'] = 'Freitext hinzufügen';
$string['content_of_textfield'] = 'Inhalt des Textfeldes';
$string['add_text_text'] = 'Hier kann ein beliebiger prüfungsbezogener Inhalt eingetragen werden, welcher den PrüfungsteilnehmerInnen nach dem Speichern sofort in deren Teilnehmeransicht angezeigt wird. ';

//sendGroupmessageForm.php
$string['sendGroupmessage'] = 'Gruppennachricht schreiben';
$string['groupmessages_text_1'] = 'Der unten eingegebene Text wird ';
$string['groupmessages_text_2'] = ' zur Prüfung hinzugefügten Teilnehmern als PANDA-Benachrichtigung sowie als Email zugeschickt.';
$string['groupmessages_warning_1'] = 'Achtung: ';
$string['groupmessages_warning_2'] = ' Prüfungsteilnehmer besitzen kein PANDA-Benutzerkonto und werden diese Nachricht deshalb nicht automatisch erhalten. Kontaktieren Sie diese Teilnehmerinnen deshalb am besten manuell per E-Mail durch einen Klick auf den folgenden Button:';
$string['send_manual_message'] = 'E-Mail schreiben';
$string['subject'] = 'Betreff';
$string['content'] = 'Inhalt';
$string['send_message'] = 'Nachricht abschicken';

// assignPlaces.php
$string['participants_missing_places'] = 'Einigen Teilnehmerinnen und Teilnehmern konnte noch kein Sitzplatz zugewiesen werden. Fügen Sie ausreichend Räume zur Prüfung hinzu und wiederholen Sie die Zuweisung oder weisen Sie die noch fehlenden Sitzplätze manuell zu.';

//importBonusForm.php
$string['importBonus'] = 'Bonuspunkte importieren';
$string['import_bonus_text'] = 'Hier können von den Teilnehmenden errungene Bonuspunkte importiert und in Bonusnotenschritte für die Prüfung umgerechnet werden.';
$string['set_bonussteps'] = 'Bonusnotenschritte festlegen';
$string['add_remove_bonusstep'] = 'Bonusschritt hinzufügen oder entfernen:';
$string['bonusstep'] = 'Bonusnotenschritt (maximal 3)';
$string['required_points'] = 'Für Notenschritt erforderliche Punkte';
$string['configure_fileimport'] = 'Dateiimport konfigurieren';
$string['import_mode'] = 'Art des Dateiimports';
$string['moodle_export'] = 'Bewertungsexport aus PANDA';
$string['individual'] = 'Individuell';
$string['idfield'] = 'Spalte in der die Benutzeridentifikatoren stehen (z. B. A, B, C ... ; Beim Import von aus PANDA exportierten Bewertungen automatisch gesetzt)';
$string['pointsfield'] = 'Spalte welche die zu wertenden Bonuspunkte enthält (z. B. A, B, C ...)';
$string['import_bonus_from_file'] = 'Bonuspunkte aus Excel-Datei importieren; Benutzeridentifikator (in PANDA hinterlegte E-Mailadresse beim Bewertungsimport oder Matrikelnummer beim individuellen Import und Bonuspunkte müssen in den oben ausgewählten Spalten stehen).';
$string['bonus_already_entered'] = '<strong>Achtung:</strong> Es wurden bereits Bonusnotenschritte für Teilnehmende importiert. Diese werden durch den erneuten Import gelöscht und ersetzt.';

//importBonus.php
$string['points_bonussteps_invalid'] = 'Punkte für Bonusschritte ungültig';

//configureGradingscaleForm.php
$string['configureGradingscale'] = 'Notenschlüssel konfigurieren';
$string['configure_gradingscale_totalpoints'] = 'Die maximale Anzahl von Punkten ist';

//inputResultsForm.php
$string['inputResults'] = 'Prüfungsergebnisse eintragen';
$string['input_results_text'] = 'Hier können nach der Eingabe der Matrikelnummer eines Prüflings die von diesem in der Prüfung errungenen Punkte eingetragen werden.';
$string['confirm_matrnr'] = 'Die Bestätigung der Matrikelnummer ist auch durch das Drücken der Enter- bzw. der Return-Taste sowie der Tabulator-Taste möglich.';
$string['exam_participant'] = 'Prüfungsteilnehmer/in';
$string['matrnr_barcode'] = 'Matrikelnummer oder Barcode';
$string['matrnr'] = 'Matrikelnummer';
$string['participant'] = 'Teilnehmer/in';
$string['exam_state'] = 'Prüfungsstatus';
$string['exam_points'] = 'Prüfungspunkte';
$string['not_participated'] = 'NT';
$string['fraud_attempt'] = 'Betrugsversuch';
$string['ill'] = 'Krank';
$string['max_points'] = 'Maximale Punkte';
$string['save_and_next'] = 'Speichern und zum Nächsten';
$string['validate_matrnr'] = 'Matrikelnummer validieren';
$string['input_other_matrnr'] = 'Matrikelnummer ändern';
$string['noparticipant'] = 'Kein gültiger Teilnehmer';
$string['invalid_matrnr'] = 'Ungültige Matrikelnummer';
$string['invalid_barcode'] = 'Ungültiger Barcode';

//participantsOverviewForm.php
$string['participants_overview_text'] = 'Alle bereits zur Prüfung hinzugefügten Teilnehmerinnen können in dieser Liste angesehen und bearbeitet werden.';
$string['edit'] = 'Bearbeiten';
$string['participantsOverview'] = 'Teilnehmer- und Ergebnisübersicht';
$string['matriculation_number_short'] = 'Matr. Nr.';
$string['totalpoints'] = 'Gesamtpunkte';
$string['result'] = 'Ergebnis';
$string['bonussteps'] = 'Bonusschritte';
$string['resultwithbonus'] = 'Ergebnis inklusive Bonus';
$string['edit_user'] = 'Benutzer bearbeiten';
$string['save_changes'] = 'Änderungen speichern';
$string['cancel'] = 'Zurück zur Prüfungsorganisation';
$string['normal'] = 'Normal';
$string['nt'] = 'NT';
$string['fa'] = 'Betrugsversuch';
$string['ill'] = 'Krank';
$string['available'] = 'Verfügbar';

//participant list
$string['participantslist'] = 'Teilnehmerliste';
$string['participantslist_names'] = 'Teilnehmerliste_Namen';
$string['participantslist_places'] = 'Teilnehmerliste_Plätze';
$string['internal_use'] = 'Nur fuer den internen Gebrauch durch die Lehrenden!';
$string['lastname'] = 'Name';
$string['firstname'] = 'Vorname';
$string['matrno'] = 'Matr.-Nr.';
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
$string['notrated'] = 'Nicht bewertet';
$string['tasks_and_boni'] = 'Aufgaben und Bonuspunkte';
$string['mean'] = 'Mittelwert';
$string['count'] = 'Anzahl';
$string['details'] = 'Details';

// exportResultsPAULFile.php
$string['results'] = 'Prüfungsergebnisse';
$string['cannot_create_zip_archive'] = 'Fehler beim Erzeugen des zip-Archives';

// examReviewDateRoomForm.php
$string['examReviewDateRoom'] = 'Termin und Raum für Klausureinsicht festlegen';
$string['examreview_dateroom_str'] = 'Falls für die Prüfung eine Klausureinsicht stattfinden soll können hier der Termin und der Raum dafür ausgewählt werden.';
$string['examreview_date'] = 'Termin';
$string['examreview_room'] = 'Raum (als Freitext eintragen)';

// forms (common)
$string['operation_canceled'] = 'Vorgang abgebrochen';
$string['operation_successfull'] = 'Vorgang erfolgreich';
$string['alteration_failed'] = 'Änderung fehlgeschlagen';
$string['no_rooms_added'] = 'Noch keine Prüfungsräume ausgewählt. Arbeitsschritt nicht möglich';
$string['no_participants_added'] = 'Noch keine Prüfungsteilnehmer hinzugefügt. Arbeitsschritt nicht möglich';
$string['not_all_places_assigned'] = 'Noch nicht alle Sitzplätze zugewiesen. Arbeitsschritt nicht möglich';
$string['no_tasks_configured'] = 'Noch keine Aufgaben konfiguriert. Arbeitsschritt nicht möglich';
$string['no_results_entered'] = 'Noch keine Prüfungsergebnisse eingetragen. Arbeitsschritt nicht möglich';
$string['correction_not_completed'] = 'Korrektur noch nicht abgeschlossen. Arbeitsschritt nicht möglich';

//helptexts
$string['help'] = 'Hilfe';
$string['helptext_str'] = 'Hilfetext';
$string['helptext_link'] = 'Eine ausführliche Erläuterung aller Elemente und Funktionen der Prüfungsorganisation findet sich im "IMT HilfeWiki" unter dem folgenden Link:';
$string['helptext_open'] = 'Hilfetext öffnen/schließen';
$string['helptext_overview']= 'Dies ist die <strong>Startseite der Prüfungsorganisation</strong>. Lehrende und/oder deren Mitarbeiterinnen & Mitarbeiter können hier alle für das Durchführen einer Prüfung sinnvollen Arbeitsschritte ausführen. <br /><br />
Diese sind übersichtlich in verschiedene Phasen unterteilt, welche entlang eines Zeitstrangs angeordnet sind. Für jeden einzelnen Arbeitsschritt ist der Bearbeitungsstatus durch entsprechende Symbole, Texte und Farben zu erkennen. Es gibt verpflichtende Arbeitsschritte und Optionale, die zwar hilfreich sind, aber auch weggelassen werden können. Sobald alle verpflichtenden Schritte einer Phase erledigt sind klappt diese automatisch zu und es öffnet sich die Nächste. Phasen können jedoch auch jederzeit manuell geöffnet und zugeklappt werden. <br /><br />
Jeder Arbeitsschritt kann nach einem Klick auf den entsprechenden Button durchgeführt werden. Dieser erscheint, sobald alle für einen Arbeitsschritte nötigen anderen Schritte erfüllt sind. <br /><br />
Durch den Button "Passwort konfigurieren“ können Sie zudem ein Passwort festlegen (oder ändern), welches ab diesem Zeitpunkt für einen Zugriff auf die Prüfungsorganisation eingegeben werden muss. Auf diese Weise können Sie zum Beispiel Ihren studentischen MitarbeiterInnen, die Ihre PANDA Kurse betreuen, den Zugriff auf die sensiblen Inhalte der Prüfungsorganisation entziehen. <br /><br />
<strong>Hinweis:</strong> Studierende haben keinen Zugriff auf diese Ansicht. Sie sehen stattdessen in einer eigenen Ansicht die für sie freigeschalteten Informationen zum Prüfungstermin.';
$string['helptext_checkPassword'] = 'Der oder die Dozentin hat für diese Prüfungsorganisation ein Passwort festgelegt. Geben Sie es ein, um die Inhalte der Prüfungsorganisation ansehen zu können. <br><br> Durch einen Klick auf den entsprechenden Button können Sie beim Support ein Zurücksetzen des Passwortes beantragen. Wurde das Passwort zurückgesetzt werden Sie und alle anderen Lehrenden des PANDA-Kurses darüber via PANDA-Benachrichtigung informiert.';
$string['helptext_checkPasswordAdmin'] = 'Der oder die Dozentin hat für diese Prüfungsorganisation ein Passwort festgelegt. Geben Sie es ein, um die Inhalte der Prüfungsorganisation ansehen zu können. <br> <br>
Als Administrator können Sie hier auf Wunsch der oder des Lehrenden das Passwort der Prüfungsorganisation zurücksetzen. Alle Lehrenden des PANDA-Kurses werden darüber per PANDA-Nachricht benachrichtigt.';
$string['helptext_configurePassword'] = 'Auf dieser Seite kann ein <strong>Passwort</strong> für die Prüfungsorganisation gesetzt oder geändert werden. Dieses muss ab dann von jeder oder jedem Lehrenden des PANDA-Kurses eingegeben werden, um auf die Inhalte der Prüfungsorganisation zugreifen zu können. <br><br>
Um ein Passwort zu setzen muss dieses zunächst in das erste Feld eingegeben und dann im zweiten Feld bestätigt werden.<br><br>
Denken Sie daran, ihr Passwort hinreichend sicher zu wählen und nehmen Sie vor allem kein Kennwort, dass Sie bereits anderswo als Passwort verwenden (vor allem nicht im Universitätskontext!).<br><br>
Durch einen Klick auf den Button "Passwort zurücksetzen" können Sie den Passwortschutz für die Prüfungsorganisation wieder aufheben.';
$string['helptext_chooseRooms']= 'Auf dieser Seite kann die Liste aller im System verfügbaren möglichen <strong>Prüfungsräume</strong> angesehen und einer oder mehrere davon als Raum für die aktuelle Prüfung ausgewählt werden. <br /> <br />
Zudem können nach einem Klick auf den Button "Eigenen Prüfungsraum anlegen" auch eigene Prüfungsräume zur Liste hinzugefügt (und anschließend als Prüfungsraum ausgewählt) werden. <br /> <br />
Um einen Raum als Prüfungsraum auszuwählen muss zunächst das Kästchen links neben dessen Namen angeklickt werden. Ein Klick auf den Button „Räume auswählen“ speichert die gewählten Räume als Prüfungsräume. Ist ein Raum nach dem Öffnen der Seite bereits markiert wurde er schon als Raum für die Prüfung gespeichert.<br /> <br />
Die gewählten Prüfungsräume werden später verwendet, um den zur Prüfung hinzugefügten TeilnehmerInnen Sitzplätze zuzuweisen. Ihre Sitzplätze werden den PrüfungsteilnehmerInnen später (sobald Sie diese Information auf der Übersichtsseite für die Studierenden sichtbar geschaltet haben) in deren Ansicht angezeigt. Außerdem wird die Sitzplatzzuweisung in Dokumenten wie der Teilnehmerliste oder dem Sitzplan benötigt. <br /> <br />
Eine Beschreibung jedes Raumes und die Zahl der in ihm vorhandenen Sitzplätze befinden sich in der Tabelle. Ist für einen Raum ein Sitzplan im System hinterlegt kann dieser durch das Drücken der linken Maustaste über dem Info-Symbol in der Spalte "Sitzplan“ angesehen werden. Ist ein Raum ein selbst erstellter Prüfungsraum kann er durch einen Klick auf das Stift-Symbol am rechten Ende der Zeile bearbeitet werden, während ein Klick des Mülleimersymbols daneben und eine anschließende Bestätigung ihn löscht (wozu er allerdings nicht als Prüfungsraum ausgewählt sein darf). <br /> <br />
<strong>Wichtige Hinweise:</strong>
<ul><li>Um die weiteren Funktionen der PANDA Prüfungsorganisation nutzen zu können muss hier mindestens ein Raum als Prüfungsraum ausgewählt werden. Zudem müssen die gewählten Räume mindestens so viele Sitzplätze bieten, wie TeilnehmerInnen an der Prüfung teilnehmen sollen.</li>
<li>Wird ein Prüfungsraum abgewählt, nachdem TeilnehmerInnen Sitzplätze in diesem zugewiesen wurden, wird die gesamte Sitzplatzzuweisung gelöscht und muss wiederholt werden. Davon betroffene Räume sind mit einem Warnhinweis versehen.</li>
<li>Einige Räume sind hier mehrfach aufgeführt. Dabei handelt es sich um unterschiedliche Modellierungen desselben Raumes. "1 Platz frei" bedeutet, dass jeder 2. Platz besetzt wird. "2 Plätze frei" bedeutet, dass jeder 3. Platz besetzt wird.</li></ul>
<strong>Achtung:</strong> Das System berücksichtigt nicht die Verfügbarkeit der gewählten Räume. Als DozentIn müssen Sie die Räume, in welchen die Prüfung stattfinden soll, bei der zentralen Raumverwaltung der Universität Paderborn buchen und so abklären, dass die entsprechenden Räume auch tatsächlich zum Zeitpunkt der Prüfung verfügbar sind.';
$string['helptext_addCustomRoom'] = 'Auf dieser Seite können Sie als Dozentin oder Dozent einen <strong>eigenen Prüfungsraum</strong> erstellen, falls der Raum, in welchem Sie Ihre Prüfung halten wollen, nicht als Prüfungsraum im System aufgeführt ist. Alternativ können Sie hier auch einen bereits vorhandenen selbst erstellten Prüfungsraum bearbeiten. <br><br>
Um einen neuen Raum zu erstellen muss zunächst dessen Name angegeben werden. Als Nächstes müssen Sie die Zahl der Sitzplätze angeben, die der Raum haben soll. Beachten Sie dabei, dass Sie selbst nachsehen müssen, wie viele Plätze tatsächlich im Raum vorhanden sind und dass die Nummerierung der Sitzplätze des hier erstellten Raums im System unabhängig von der tatsächlich im Raum vorhandenen Nummerierung immer bei 1 beginnt. Das bedeutet, dass Sie möglicherweise auftretende Unstimmigkeiten mit der tatsächlichen Sitzplatznummerierung manuell anpassen müssen. Abschließend kann noch eine optionale Beschreibung des Raumes angegeben werden. Diese sollte alle für Sie wichtigen Informationen über den Raum enthalten, damit Sie den Raum später zum Beispiel im nächsten Semester bei Bedarf einfach erneut benutzen können. Ein Klick auf den Button "Raum speichern" legt schließlich den neuen Prüfungsraum an.<br><br>
Ein auf diese Weise angelegter Raum kann anschließend aus der Liste der verfügbaren Prüfungsräume als Raum ausgewählt und danach wie jeder andere Prüfungsraum regulär genutzt werden.<br><br>
Falls auf der Seite der Raumauswahl hingegen ein bestehender eigener Prüfungsraum zur Bearbeitung ausgewählt wurde kann dieser nun verändert werden. In diesem Fall können hier die Sitzplatzanzahl und die Beschreibung des gewählten Raums geändert und diese Änderung anschließend durch einen Klick auf "Raum speichern" gesichert werden. Wenn dabei die Sitzplatzanzahl verringert wird behalten alle an der Prüfung Teilnehmenden trotzdem zunächst ihre bisher zugewiesenen Sitzplätze, bis Sie die automatische Sitzplatzzuweisung erneut durchführen.';
$string['helptext_addDefaultRooms']= 'Hier können Sie als PANDA-Administrator eine Reihe an <strong>Standardräumen</strong> importieren, die nach dem Einlesen allen Dozenten bei der Auswahl der Prüfungsräume als mögliche Räume zur Verfügung stehen. <br><br>
Um die Standardräume zu importieren muss zunächst eine korrekt aufgebaute Textdatei im unteren Bereich ausgewählt und dann durch einen Klick auf den entsprechenden Button eingelesen werden. <br><br>
Die einzulesende Textdatei muss dabei die folgenden Informationen zu jedem Prüfungsraum enthalten, wobei jede Zeile für einen Prüfungsraum steht: 
<ul><li>Erste Spalte: Die systeminterne Raumid nach dem Muster <i>Raumname_Variante</i>, also zum Beispiel <i>Audimax_2</i></li>
<li>Zweite Spalte: Der benutzersichtbare Raumname, also zum Beispiel <i>Audimax</i></li>
<li>Dritte Spalte: Die benutzersichtbare Raumbeschreibung inklusive der Anzahl der freigelassenen sowie der gesamten Plätze, also zum Beispiel <i>2 Plätze frei, 56 Plätze insgesamt</i></li>
<li>Vierte Spalte: Ein zur Berechnung der Sitzplatzzahl des Raumes benötigtes Array, welches die Bezeichnung jedes einzelnen im Raum vorhandenen Sitzplatzes enthält. Das Array muss dabei in json-Syntax verfasst sein, also zum Beispiel folgendermaßen aussehen: <i>["R/R01/P07","R/R01/P04","R/R01/P01", ...] </i></li>
<li>Fünfte Spalte: Wenn ein Sitzplan für den Raum als .svg-Datei vorhanden ist und dieser den Benutzern angezeigt werden soll muss in dieser Spalte der Quellcode der SVG-Datei stehen, ansonsten kann diese Spalte leer gelassen werden.</li></ul>
Wurden bereits Standardräume eingelesen werden diese durch einen Neuimport überschrieben. Die Informationen zu allen dabei gegebenenfalls gelöschten Räumen bleiben anschließend in allen Prüfungsorganisations-Instanzen, in denen Sie aktuell genutzt werden, zunächst erhalten. Gelöschte Räume können jedoch von den Dozenten nicht mehr als neue Prüfungsräume ausgewählt oder aber für die (Neu-)Zuweisung von Sitzplätzen genutzt werden.';
$string['helptext_editDefaultRoom']= 'Hier können Administratorinnen und Administratoren einen bestehenden <strong>Standardraum bearbeiten</strong> oder einen Neuen anlegen.<br><br>
Dazu werden zuerst die Basisinformationen des Raumes angezeigt, die zugleich eingetragen beziehungsweise bearbeitet werden können. Dies ist zunächst die systeminterne ID des Raumes, die pluginintern für die Identifikation des Raumes verwendet wird und dem folgenden Schema entsprechend aufgebaut sein sollte: Der Raumname gefolgt von einem Unterstrich gefolgt von der Variante des Raumes, die besonders bei mehreren Modellierungen desselben Raumes mit einer unterschiedlichen Anzahl an freien Plätzen zwischen den belegbaren Sitzplätzen relevant ist. Für den Teil des Raumnamens sind dabei alle Buchstaben, Zahlen und auch der Punkt erlaubt, Raumvarianten kann es maximal zehn gleichzeitig geben. Wird ein vorhandener Raum bearbeitet kann die Raum ID nicht verändert werden. Es folgt der Name des Raumes, der für alle Dozierenden sichtbar ist und aus Buchstaben, Zahlen, Punkten und Leerzeichen bestehen darf. Die Raumbeschreibung ist ebenfalls für die Benutzer sichtbar, sollte zum Beispiel Informationen über die gewählte Modellierung (einen oder zwei Plätze frei zwischen zwei besetzbaren Sitzplätzen) enthalten und darf dieselben Zeichen enthalten wie der Raumname. Unter diesen Informationen werden, falls ein bereits existierender Raum zur Bearbeitung ausgewählt wurde, noch weitere Informationen zum Raum angezeigt, etwa die bisherige Anzahl an besetzbaren Sitzplätzen und eine Übersicht über deren Benennung sowie (falls dieser vorhanden ist) der für den Raum hinterlegten Sitzplan. <br><br>
Falls bei einem bestehenden Raum Sitzplätze bearbeitet werden sollen ist dies möglich, sobald im nächsten Abschnitt bei "Sitzplätze bearbeiten" die Option "Ja" ausgewählt wurde. Bei der Erstellung eines neuen Raumes ist dies nicht nötig, in diesem Fall kann direkt im Abschnitt "Neue Sitzplätze" mit dem Eintragen derselben fortgefahren werden. Für die Befüllung des Raumes mit Sitzplätzen gibt es drei verschiedene Modi, welche die einfache Nachbildung aller wichtigen Modellierungsarten von Prüfungsräumen ermöglichen sollen: Im Modus "Standard" werden für einen Raum automatisch so viele zuweisbare Sitzplätze angelegt, bis die angegebene Gesamtplatzzahl des Raumes erreicht ist, wobei die angegebene Anzahl freier Plätze zwischen zwei zuweisbaren Sitzplätzen berücksichtigt wird. Die Benennung der Plätze beginnt dabei bei 1 und zählt dann aufwärts. Soll also ein Raum mit 100 Gesamtplätzen befüllt werden, zwischen denen jeweils ein Platz unbenutzt bleibt, würde dieser insgesamt 50 in der Prüfungsorganisation belegbare Plätze mit den Benennungen 1, 3, 5, ..., 100 bekommen. Bei zwei Plätzen frei wären es 34 Plätze mit den Benennungen 1, 4, 7, ..., 100. Der Sitzplatz-Modus "Reihenweise" funktioniert ähnlich, nur müssen hier die Anzahl der in einem Raum vorhandenen zu befüllenden Reihen sowie die pro Reihe vorhandenen Plätze angegeben werden. Jede Reihe wird dann mit entsprechend vielen Plätzen befüllt, wobei wieder die angegebene Anzahl freier Plätze und die ebenfalls anzugebene Zahl an gegebenenfalls freizulassenden Reihen berücksichtigt wird. Die Plätze jeder Reihe werden dabei identisch benannt, also jeweils etwa 1,3,5,7 ... . Für alle Raummodellierungen, die mithilfe dieser beiden Modi nicht nachgebildet werden können gibt es den dritten Modus mit dem Namen "Vollständig individuell". In diesem können die Bezeichnungen aller Plätze komplett frei eingetragen werden, wobei zwischen zwei Platzbezeichnungen stets ein Komma stehen muss. In den Platzbezeichnungen sind alle Buchstaben, Zahlen, Punkte, Minuszeichen, Slashs sowie Leerzeichen erlaubt. Dieser Modus eignet sich sehr gut dazu, komplexere Sitzplatzmodellierungen vorzunehmen oder aber mit den ersten beiden Modi erstellte Modellierungen etwas anzupassen. Dies ist zum Beispiel hilfreich, wenn die erste oder die letzte Reihe eines Raumes aufgrund baulicher Begebenheiten weniger Plätze hat als die anderen oder wenn bei durchgehender Platznummerierung Sitzplätze trotzdem in Reihen angeordnet sind und dabei etwa jede zweite Reihe freigelassen werden soll. Bei der Bearbeitung eines bereits existierenden Raumes ist dieser Modus deswegen bereits vorausgewählt, kann aber natürlich jederzeit durch einen anderen Modus ersetzt werden.<br><br>
Als Letztes kann für einen Raum ein neuer Raumplan hinzufügt werden. Dieser muss außerhalb der Prüfungsorganisation erstellt werden und sollte sämtliche für den Standardraum angelegte Sitzplätze und deren Bezeichnungen enthalten. Der Raumplan muss dazu als SVG in einer Textdatei (.txt) gespeichert sein, die dann im letzten Abschnitt dieser Seite hochgeladen werden muss. Dabei ist zu beachten, dass der Inhalt der Datei mit der SVG des Raumplans vor dem Upload sorgfältig geprüft werden muss, da das Plugin an dieser Stelle böswillige oder fehlerhafte Inhalte in der Datei nicht erkennen kann. Wurde eine Datei mit einem Raumplan ausgewählt wird dieser nach einem Klick auf "Raum speichern" zusammen mit den restlichen angegebenen Informationen gespeichert. <br><br>
Der auf diese Weise angelegte oder geänderte Raum kann sofort von allen Lehrenden in ihren Prüfungsorganisationen als Prüfungsraum ausgewählt werden. Bei der Änderung des Namens oder der Anpassung von Sitzplätzen in einem bestehenden und bereits in Prüfungsorganisationen verwendeten Prüfungsraum bleiben der Name und die bisherigen Sitzplatzzuweisungen dort zunächst gespeichert. Die Lehrenden müssen somit die Sitzplätze einmal erneut zuweisen, bevor die Änderungen am Raum dort wirksam werden.';
$string['helptext_setDateTime']= 'Hier können das <strong>Datum und die Uhrzeit der Prüfung</strong> ausgewählt werden. <br /> <br />
Der hier gewählte Prüfungstermin wird auf der Übersichtsseite der Prüfungsorganisation angezeigt und später in den erzeugten Dokumenten wie etwa der Teilnehmerliste oder den Klausuretiketten verwendet. Zudem wird er den PrüfungsteilnehmerInnen in deren Ansicht angezeigt, sobald Sie diese Informationen auf der Übersichtsseite für die Studierenden sichtbar geschaltet haben. <br /> <br />
Das Datum und die Uhrzeit der Prüfung sollten hier also gesetzt werden, um die Prüfungsorganisation in PANDA sinnvoll nutzen zu können.';
$string['helptext_viewParticipants']= 'Auf dieser Seite können alle zur Prüfung hinzugefügten <strong>Prüfungsteilnehmer</strong> und Informationen wie deren Profil, Matrikelnummer sowie die ihnen gegebenenfalls in PANDA zugewiesenen Gruppen angesehen werden. <br /> <br />
Es können hier zudem neue Teilnehmerinnen zur Prüfung hinzugefügt werden. Dazu gibt es zwei Möglichkeiten: <br /> <br />
1. Es können nach einem Klick auf den Button "Teilnehmer aus Datei hinzufügen" Teilnehmer aus einer oder mehreren, aus PAUL exportierten Prüfungslisten importiert werden. Dies ist der empfohlene Weg des Teilnehmerimportes, da nur auf diese Weise später ein Export der Prüfungsergebnisse der Anzahl und dem Aufbau dieser eingelesenen PAUL-Listen entsprechend möglich ist. Diese Variante sollte also gewählt werden, möchte man später die Prüfungsergebnisse direkt in PAUL eintragen (lassen).<br>
2. Es besteht außerdem die Möglichkeit, nach einem Klick auf den Button "Kursteilnehmer importieren" Teilnehmer des PANDA-Kurses als Prüfungsteilnehmer zu importieren. Wird diese Variante gewählt können die Prüfungsergebnisse später allerdings nur in einer einzigen Ergebnisliste exportiert werden, ein listenweiser Export und ein einfaches anschließendes Eintragen der Prüfungsergebnisse in PAUL ist dann somit nicht möglich. Es besteht zudem auch nicht die Möglichkeit, einmal als Kursteilnehmer importierte Teilnehmer später durch nachträgliches Einlesen einer PAUL-Liste "umzuschreiben". Dafür muss der oder die Teilnehmerin zunächst komplett gelöscht werden.<br><br>
Das Hinzufügen von TeilnehmerInnen ist einer der wichtigsten Arbeitsschritte in der Prüfungsorganisation. Nur wenn Sie hier mindestens einen hinzugefügten Teilnehmemenden sehen können Sie später Sitzplätze zuweisen, Prüfungspunkte eintragen oder Ergebnisdokumente exportieren. Nicht als PrüfungsteilnehmerInnen hinzugefügte Studierende haben (selbst wenn sie bereits im PANDA Kurs eingeschrieben sind) außerdem keinen Zugriff auf die Teilnehmeransicht mit den Prüfungsinformationen und erhalten auch keine Benachrichtigungen über die Nachrichtenfunktion auf der Übersichtsseite der Prüfungsorganisation. <br /> <br />
Falls Sie einen durch eine Zwischenüberschrift abgetrennten unteren Teil der Tabelle sehen, dann haben Sie Prüfungsteilnehmer importiert, die keinen Benutzeraccount in PANDA haben. Diese können zwar auch aus einer PAUL-Datei importiert werden, einige Arbeitsschritte wie etwa das Schreiben einer Benachrichtigung müssen für diese Teilnehmer jedoch manuell durchgeführt werden und andere (etwa das Ansehen der Studentenansicht für die Teilnehmer selbst) sind gänzlich unmöglich.<br><br>
Es besteht auf dieser Seite außerdem die Möglichkeit, einzelne oder alle bereits importierte Prüfungsteilnehmer wieder zu löschen. Um einzelne Teilnehmer zu löschen genügt ein Klick auf den Mülleimer in der Zeile des jeweiligen Teilnehmenden, um alle Teilnehmer zu löschen muss hingegen der rote Button unter der Tabelle gedrückt werden. Beachten Sie jedoch, dass durch das Löschen eines oder aller Teilnehmer automatisch alle für diese hinterlegten Informationen wie etwa Sitzplätze oder eingetragene Prüfungspunkte gelöscht werden und dass diese Informationen danach nicht wieder hergestellt werden können.';
$string['helptext_addParticipants']= 'Auf dieser Seite können <strong>TeilnehmerInnen</strong> aus PAUL-Prüfungslisten zur Prüfung hinzugefügt werden. Auf diese Weise können deren Ergebnisse später wieder listenweise exportiert und dann einfach in PAUL eingetragen werden. <br /> <br />
Dazu benötigen Sie zunächst die aus PAUL exportierte Liste ihrer Prüfungsteilnehmer. Diese Datei können Sie dann im Auswahlbereich auswählen und durch einen Klick auf den entsprechenden Button einlesen lassen. <br><br>
Auf der nun folgenden Seite sehen Sie alle aus der Datei eingelesenen Matrikelnummern. Dabei wird in verschiedenen Bereichen genau aufgeschlüsselt, welchen Status eine Matrikelnummer hat und ob der dazugehörige Studierende zur Prüfung hinzugefügt werden kann. <br><br>
Im Folgenden werden die verschiedenen Stati kurz erklärt:<br>
<ul><li><strong>Ungültige Matrikelnummer</strong>: Die eingegebene Matrikelnummer ist ungültig, weil sie zum Beispiel nicht erlaubte Zeichen wie etwa Buchstaben enthält. Sie kann deshalb auch nicht als Teilnehmer eingelesen werden. Die ganz links in der Zeile stehende Zahl gibt die Nummer der Zeile an, in der die defekte Matrikelnummer in der eingelesenen PAUL-Datei steht und wo sie gegebenenfalls kontrolliert werden kann. </li>
<li><strong>Doppelte Matrikelnummer</strong>: Die Matrikelnummer kommt in der Datei mehrfach vor. Als Prüfungsteilnehmer kann Sie jedoch im entsprechenden Abschnitt nur einmal eingelesen werden.</li>
<li><strong>Neu eingelesen (kein Kursteilnehmer)</strong>: Der zu dieser Matrikelnummer gehörende Studierende ist nicht Teil des PANDA-Kurses. Er kann problemlos als Prüfungsteilnehmer importiert werden. Da er jedoch nicht die Teilnehmeransicht des Plugins ansehen kann muss er, um auszuschliessen dass hier ein Fehler vorliegt, durch Setzen des Hakens manuell ausgewählt werden.</li>
<li><strong>Neu eingelesen (ohne PANDA Benutzerkonto)</strong>: Der zu dieser Matrikelnummer gehörende Studierende hat noch keinen Account in PANDA. Dies kann etwa geschehen, wenn er sich noch nie in PANDA angemeldet hat. Der oder die Studierende kann zwar als Prüfungsteilnehmer importiert werden, jedoch kann er dann nicht die Teilnehmeransicht der Prüfungsorganisation betrachten und Sie können ihn auch nicht über die Benachrichtigungssfunktion der Prüfungsorganisation erreichen. Deshalb müssen Sie diesen Studierenden hier manuell anhaken.</li>
<li><strong>Werden gelöscht</strong>: Dieser Teilnehmer wurde in einer früheren Version der verwendeten PAUL-Liste bereits als Prüfungsteilnehmer importiert, ist in der aktuellen jedoch nicht mehr enthalten (weil er sich zum Beispiel in der Zwischenzeit von der Prüfung in PAUL abgemeldet hat). Durch Auswählen können Sie nun bestimmen, dass dieser Teilnehmer von der aktuellen Prüfung entfernt werden soll.</li>
<li><strong>Bereits Prüfungsteilnehmer</strong>: Dieser Teilnehmer wurde bereits als Prüfungsteilnehmer importiert und wird durch den aktuellen Import nicht verändert.</li>
<li><strong>Neu eingelesen</strong>: Dies ist ein gültiger Teilnehmer, der ohne Probleme zur Prüfung hinzugefügt werden kann. Alle Teilnehmer in diesem Abschnitt sind für das Hinzufügen zur Prüfung vorausgewählt.</li>
</ul>
Alle Teilnehmer, die zur Prüfung hinzugefügt (oder von dieser wieder entfernt) werden sollen können nun ausgewählt werden, indem entweder der Haken in die Box neben dem Namen oder aber im Feld "Alle aus-/abwählen" des jeweiligen Bereiches gesetzt wird. Ein anschließendes Drücken des Buttons "Zur Prüfung hinzufügen" fügt die ausgewählten Teilnehmer dann zur Prüfung hinzu.<br><br>
Falls Sie die falsche Datei eingelesen haben können Sie mit einem Klick auf den Button "Andere Teilnehmer hinzufügen" sofort eine neue Datei einlesen. Die aktuell eingelesenen Teilnehmer werden dabei nicht importiert sondern wieder verworfen.<br><br>
Für den Import von Teilnehmern aus mehreren Listen können Sie diesen Vorgang mehrfach durchführen.';
$string['helptext_addCourseParticipants']= 'Hier können alle im PANDA Kurs eingeschriebenen <strong>Kursteilnehmerinnen</strong> als Prüfungsteilnehmer importiert werden. <br><br>
Dazu müssen im unteren Abschnitt all jene Teilnehmerinnen ausgewählt werden, die zur Prüfung hinzugefügt werden sollen. Einzelne Teilnehmer können dabei durch einen Klick in das Kästchen neben ihrem Namen ausgewählt werden, zum Aus- (oder ab)wählen aller Kursteilnehmer genügt hingegen ein Klick in das entsprechende Kästchen "Alle aus-/abwählen". Es können zudem im entsprechenden Abschnitt gegebenenfalls bereits vorhandene Prüfungsteilnehmer die keine Kursteilnehmer sind ausgewählt werden. Diese werden dann bei einem Klick auf den ganz unten befindlichen Button "Zur Prüfung hinzufügen" von der Prüfung entfernt, während die ausgewählten Kursteilnehmer zur Prüfung hinzugefügt werden. Für alle Teilnehmer mit dem Status "Bereits Prüfungsteilnehmer" ändert sich hingegen nichts. <br><br>
Werden Teilnehmer hinzugefügt, nachdem bereits Sitzplätze zugewiesen wurden, müssen diesen noch Plätze zugewiesen werden.<br><br>
<strong>Achtung:</strong> Wird diese Variante des Teilnehmerimportes gewählt werden die Ergebnisse aller so hinzugefügten Teilnehmer später in einer einzelnen separaten Liste für das Prüfungsamt exportiert, wodurch das Eintragen in PAUL gegebenenfalls schwierig wird. Wenn Sie vorhaben, die Prüfungsergebnisse in PAUL eintragen zu lassen, sollten Sie die Teilnehmer lieber mithilfe der entsprechenden PAUL-Teilnehmerlisten zur Prüfung hinzufügen.';
$string['helptext_configureTasks']= 'Hier können die Anzahl und die Maximalpunktzahlen aller <strong>Prüfungsaufgaben</strong> festgelegt werden. <br><br>
Durch Anklicken des "+" Button können neue Aufgaben zur Prüfung hinzugefügt werden. Im Feld unter der jeweiligen Aufgabennummer muss die Maximalpunktzahl eingegeben werden, die später in der jeweiligen Aufgabe erreicht werden kann. Diese Punktzahl muss positiv sein, kann aber eine Kommazahl sein. Durch einen Klick auf den "-" Button können Prüfungsaufgaben wieder entfernt werden, wobei jedoch mindestens eine Aufgabe immer bestehen bleibt. <br><br>
Die Aufgaben sind ein zentrales Element der Prüfungsorganisation. Sie entsprechen den Aufgaben, die nachher in der tatsächlichen Prüfung vorhanden sind und werden benötigt, um später die Prüfungsergebnisse für die Teilnehmerinnen eintragen zu können. Für jede Aufgabe können dann separat die von den Prüfungsteilnehmerinnen errungenen Punkte eingetragen werden, maximal jedoch die hier angegebene Höchstpunktzahl der jeweiligen Aufgabe. Die hier festgelegten Aufgaben und deren Maximalpunktzahlen werden außerdem für das Setzen des Notenschlüssels und für den Export der Prüfungsergebnisse benötigt.<br><br>
Werden die Aufgaben nachträglich verändert, nachdem bereits Prüfungsergebnisse eingetragen oder der Notenschlüssel gesetzt wurde, müssen diese gegebenenfalls an die neue Anzahl beziehungsweise Maximalpunktzahl der Aufgaben angepasst werden.';
$string['helptext_setTextfield']= 'Hier kann ein beliebiger Inhalt als <strong>Freitext</strong> für die Prüfung eingetragen werden, welcher den PrüfungsteilnehmerInnen nach dem Speichern sofort in deren Teilnehmeransicht angezeigt wird. <br /> <br />
Auf diese Weise können den PrüfungsteilnehmerInnen etwa unkompliziert Hinweise zu den in der Prüfung erlaubten Hilfsmitteln mitgeteilt werden. Neben einfachen Texten können dabei auch komplexere Elemente wie etwa Bilder oder gegebenenfalls sogar Formeln verwendet werden. <br /> <br />
Diese Funktion ist rein optional. Wenn Sie also z.B. keine Hinweise für die PrüfungsteilnehmerInnen haben können Sie das unten stehende Feld auch einfach leer lassen und auf den Button „Abbrechen“ klicken. <br /> <br />
<strong>Hinweis:</strong>  Diese Funktion ist vorwiegend für Mitteilungen gedacht, die nicht zeitkritisch sind. Möchten Sie die PrüfungsteilnehmerInnen jedoch etwa am Tag vor der Prüfung über einen kurzfristigen Wechsel der Prüfungsräume informieren, empfiehlt sich dafür stattdessen die Nutzung der Funktion „Nachricht an Teilnehmer schreiben“ auf der Übersichtsseite. Dadurch erhalten die PrüfungsteilnehmerInnen sofort eine E-Mail und können so die eingetragenen Informationen selbst dann mitbekommen, wenn sie nicht aktiv in PANDA nachsehen.';
$string['helptext_sendGroupmessage']= 'Auf dieser Seite kann der Betreff und der Inhalt einer <strong>Nachricht</strong> eingegeben werden, die nach einem Klick auf den Button „Mail abschicken“ <strong>an alle</strong> zur Prüfung als <strong>Teilnehmerinnen</strong> hinzugefügte Studierende gesendet wird. <br /> <br />
Diese bekommen die Nachricht direkt nach dem Abschicken sowohl als PANDA-Benachrichtigung als auch als E-Mail an ihren universitären E-Mail-Account weitergeleitet und können so zum Beispiel einfach auf kurzfristige Änderungen (etwa der Prüfungszeiten oder -Räume) aufmerksam gemacht werden. <br /> <br />
Falls Sie Teilnehmerinnen zur Prüfung hinzugefügt haben, die noch kein PANDA-Benutzerkonto haben, wird dies im Folgenden angezeigt. Da diese Teilnehmerinnen die hier geschriebene Nachricht nicht automatisch erhalten werden müssen Sie sie stattdessen manuell per E-Mail anschreiben. Dies können Sie zum Beispiel nach einem Klick auf den Button "Email schreiben", der ihren E-Mail-Client öffnet und die Mailadressen der entsprechenden Teilnehmer einträgt, tun. <br /> <br />
Die gesamte Benachrichtigungsfunktion ist rein optional, Sie müssen sie nicht nutzen, um eine Nachricht an die PrüfungsteilnehmerInnen zu senden. <br /> <br />
<strong>Hinweis:</strong>  Um den PrüfungsteilnehmerInnen ausführlichere Hinweise etwa zu den in der Klausur erlaubten Hilfsmitteln zu geben kann auch das über die Übersichtsseite erreichbare Freitextfeld genutzt werden.';
$string['helptext_importBonus']= 'Hier können Bonuspunkte der Prüfungsteilnehmer importiert und in <strong>Bonusnotenschritte</strong> für die Prüfung umgewandelt werden. Auf diese Weise können zum Beispiel durch die Studierenden bei der Bearbeitung von Übungsaufgaben errungene Bonuspunkte direkt in Bonusnotenschritte für die Klausur umgewandelt werden. <br><br>
Dazu muss zunächst im oberen Abschnitt die Zahl der für die Klausur möglichen Bonusnotenschritte festgelegt werden. Es sind maximal drei Bonusnotenschritte (ein Notenschritt wäre etwa die Verbesserung von 1,7 auf 1,3) möglich, insgesamt können Prüfungsteilnehmer sich also um maximal eine Note verbessern. Für jeden Bonusnotenschritt muss danach zudem angegeben werden, wie viele Punkte die Studierenden zum Erhalten dieses Schrittes mindestens erreicht haben müssen. <br><br>
Im unteren Abschnitt kann als Nächstes die Art des Dateiimportes festgelegt werden. Dazu gibt es zwei Möglichkeiten:<br><br>
1. Bewertungsexport aus PANDA: Haben Ihre Studierenden ihre Übungszettel über die PANDA Aufgabenabgabe abgegeben und wurden diese dort korrigiert und bewertet sollte hier der Bewertungsexport aus PANDA ausgewählt werden, da auf diese Weise sämtliche Bonuspunkte für den kompletten PANDA Kurs unkompliziert eingelesen werden können.<br>
Dazu müssen die Bewertungen aus dem PANDA Kurs zunächst wie <a href="https://hilfe.uni-paderborn.de/Dozent:_Bewertungen_Export_von_Gruppen#Setup_f.C3.BCr_Bewertungen" class="alert-link" target="_blank">hier</a> beschrieben exportiert werden. Danach müssen Sie die exportierte Datei einmal öffnen und nachsehen, in welcher Spalte die Punkte eingetragen sind. Die Bezeichnung der Spalte muss dann im dafür vorgesehenen Feld im unteren Abschnitt eingetragen werden.<br><br>
2. Individuell: Falls Sie ihre Bonuspunkte nicht über die PANDA Aufgabenabgabe verwaltet haben können Sie alternativ den Modus des individuellen Importes auswählen. Für diesen brauchen Sie eine Excel-Datei, bei der für jeden betroffenen Teilnehmenden in einer eigenen Zeile entweder die in PANDA hinterlegte Email-Adresse oder aber die Matrikelnummer in einer und die erreichte Punktzahl in einer anderen Spalte steht. Die Bezeichnung sowohl der Spalte, in der die Benutzerindentfikatoren aller Studierenden steht als auch die der Spalte, die alle Bonuspunktzahlen enthält müssen dann in den entsprechenden Feldern im unteren Abschnitt angegeben werden. <br><br>
Zum Abschluss muss nun noch die einzulesende Datei mit den Bonuspunkten ausgewählt und dann durch einen Klick auf den Button "Datei einlesen" eingelesen werden, um den Bonuspunkteimport durchzuführen. Die importierten Bonusnotenschritte werden den TeilnehmerInnen sofort in deren Ansicht angezeigt.';
$string['helptext_configureGradingscale']= 'Hier kann ein <strong>Notenschlüssel</strong> für die Prüfung konfiguriert werden. <br><br>
Sobald die Prüfungsergebnisse eingetragen wurden wird dieser dazu benutzt, automatisch die Prüfungsnoten aller Teilnehmenden zu errechnen. Wurde kein Notenschlüssel konfiguriert ist die automatische Berechnung einer Note hingegen nicht möglich.<br><br>
Es muss für jeden Notenschritt einzeln angegeben werden, wie viele Punkte für dessen Erreichen mindestens notwendig sind. Eine 70 im Feld unter 1,0 würden demnach bedeuten, dass eine Teilnehmerin mindestens 70 Punkte erreichen muss, um die Note 1,0 zu bekommen).<br><br>
Die zu erreichende Punktzahl für einen Notenschritt kann zwischen 0 und der angegebenen Gesamtpunktezahl aller Prüfungsaufgaben liegen, sie muss allerdings höher sein als die für den Notenschritt davor benötigte Punktzahl. So müssen für das Erreichen einer 1,0 etwa mehr Punkte gefordert sein als für das Erreichen einer 1,3. Dazu ist auch die Nutzung von Kommazahlen als Punkte möglich. Erreicht ein Teilnehmender weniger Punkte als für die 4,0 notwendig sind bekommt er stattdessen die Note 5.<br><br>
Der Notenschlüssel kann jederzeit (auch nach dem Eintragen der Prüfungsergebnisse) geändert werden, die Noten der Prüfungsteilnehmer werden in diesem Fall sofort automatisch an den neuen Notenschlüssel angepasst.';
$string['helptext_inputResults']= 'Auf dieser Seite können die <strong>Prüfungsergebnisse</strong> der TeilnehmerInnen <strong>eingetragen</strong> werden. <br><br>
Dazu muss zunächst die Matrikelnummer des oder der Teilnehmenden, deren Ergebnisse eingetragen werden sollen, eingegeben werden. Dazu gibt es zwei Möglichkeiten:<br>
1. Sie können die Matrikelnummer des oder der Teilnehmenden manuell eingeben. Klicken Sie dazu in das Feld "Matrikelnummer oder Barcode", tippen Sie die Matrikelnummer ein und bestätigen Sie diese durch ein Drücken der Enter- (bzw. Return-) oder der Tabulator-Taste oder des Buttons "Matrikelnummer validieren". <br> 
2. Alternativ können Sie, falls Sie in Ihrer Prüfung Prüfungsetiketten benutzt haben, auch einen Barcode-Scanner zum schnelleren Eintragen der Prüfungsergebnisse nutzen. Dazu brauchen Sie einen Barcode-Scanner oder alternativ ein Smartphone mit einer entsprechenden App. Mit diesem müssen Sie dann den Barcode auf dem Prüfungsetikett eines Prüflings einscannen, wodurch dessen Matrikelnummer automatisch in das Feld "Matrikelnummer oder Barcode" eingetragen und sofort bestätigt wird. Klappt das automatische Eintragen nicht sofort müssen Sie gegebebenfalls einmal manuell in das Feld "Matrikelnummer oder Barcode" klicken und den Scan dann wiederholen.<br><br>
Sobald eine Matrikelnummer eingetragen und bestätigt wurde wird sie vom System geprüft. Ist sie die gültige Matrikelnummer eines zur Prüfung hinzugefügten Teilnehmers wird nun die Seite zum Eintragen der Prüfungspunkte geöffnet, andernfalls gibt es eine entsprechende Fehlermeldung und es wird wieder die vorige Seite geöffnet, wo eine neue Matrikelnummer eingetragen oder das Eintragen der fehlerhaften Matrikelnummer wiederholt werden kann.<br><br>
Im Fall einer gültigen Matrikelnummer können auf der daraufhin geöffneten Seite nun die Prüfungsergebnisse eingetragen werden. Im Abschnitt "Prüfungsteilnehmer/in" sieht man zunächst die Matrikelnummer und den Namen des oder der gewählten Prüfungsteilnehmerin. Durch einen Klick auf den darunter befindlichen Button "Matrikelnummer ändern" kann man an dieser Stelle wieder auf die vorherige Seite gelangen, um (zum Beispiel im Falle eines Fehlers) dort eine andere Matrikelnummer einzugeben. Im darunter befindlichen Abschnitt "Prüfungspunkte" können für den gewählten Prüfling die in jeder Prüfungsaufgabe errungenen Punkte eingetragen werden. Dazu können direkt im Punkte-Feld der ersten Aufgabe die entsprechenden Punkte eingetragen werden und dann nach einem Drücken der Tabulator-Taste im Feld der nächsten Aufgabe weiter gemacht werden. Als Punktzahl kann dabei eine Zahl zwischen Null und der angezeigten Maximalpunktzahl der jeweiligen Aufgabe eingetragen werden, wobei auch Kommazahlen mit bis zu zwei Nachkommastellen erlaubt sind. Unterliegt der Prüfling einem besonderen Prüfungsstatus (hat er etwa "Nicht Teilgenommen" ("NT"), einen "Betrugsversuch" begangen oder war er "Krank") kann dieser Status im letzten Abschnitt "Prüfungsstatus" durch das Setzen des Hakens in der entsprechenden Checkbox ausgewählt werden. Dadurch werden die Punkte der Aufgaben auf Null gesetzt, das Eintragen der Punkte wird deaktiviert und der gewählte Status wird in allen späteren Dokumenten (zum Beispiel für den PAUL-Export) statt des Ergebnisses angezeigt. Das Entfernen des Hakens beim jeweiligen Prüfungsstatus aktiviert die Möglichkeit zum Punkteeintragen wieder. Wurden für den Prüfling bereits früher Ergebnisse eingetragen sind sowohl der Abschnitt zu den Prüfungspunkten als auch dem Prüfungsstatus möglicherweise bereits vorausgefüllt. In diesem Fall können diese Angaben nun geändert und die Änderungen dann gespeichert werden.<br><br>
Nach einem Klick auf den Button "Speichern und zum Nächsten" oder nach dem Drücken der Enter- bzw. Return-Taste werden die eingetragenen Ergebnisse dann gespeichert und es wird automatisch die Ausgangsseite aufgerufen, auf der dann die Matrikelnummer des nächsten Prüflings (entweder manuell oder per Barcodescanner) eingelesen werden kann.';
$string['helptext_participantsOverview']= 'In dieser <strong>Teilnehmer- und Ergebnisübersicht</strong> können, sobald Prüfungsteilnehmerinnen importiert wurden, alle Informationen zu diesen und alle für diese eingetragenen Ergebnisse angesehen und bearbeitet werden. <br><br>
Für jede der alphabetisch sortiert angezeigten Prüfungsteilnehmerinnen werden standardmäßig sowohl der Vor- und der Nachname als auch die Matrikelnummer angezeigt. Wurden einem oder einer Teilnehmenden bereits ein Sitzplatz zugewiesen werden dieser sowie der zugehörige Raum in den entsprechend benannten Spalten ebenfalls angezeigt. Wurden bereits Prüfungsaufgaben angelegt und wurden für einen Teilnehmer bereits Prüfungsergebnisse eingetragen werden auch diese angezeigt. In der Spalte "Punkte" ist dabei zu sehen, wie viele Punkte der Teilnehmer in jeder einzelnen Aufgabe erreicht hat, während in der Spalte "Gesamtpunkte" die aufsummierte Gesamtpunktezahl angezeigt wird. Wurden noch keine Prüfungsaufgaben angelegt gibt ein Klick auf das stattdessen in der Spalte "Punkte" angezeigte Symbol die Möglichkeit, dies direkt zu erledigen. Wurde noch kein Notenschlüssel eingetragen kann dies nach einem Klick auf das entsprechende Symbol in der Spalte "Ergebnis" getan werden, andernfalls wird in dieser Spalte (falls für den Teilnehmer bereits Ergebnisse eingetragem wurden) dessen aus dem Notenschlüssel berechnete Prüfungsnote angezeigt. Hat der Teilnehmer einen besonderen Status (war er etwa bei der Prüfung krank oder liegt bei ihm ein Betrugsversuch vor) wird dies statt des Prüfungsergebnisses angezeigt. Außerdem stehen in der Spalte "Bonusschritte" die bereits vom Teilnehmenden errungenen Bonusnotenschritte für die Klausur, während in der Spalte "Ergebnis mit Bonus" die Endnote unter Berücksichtigung der Bonusnotenschritte angesehen werden kann. <br><br>
Um die Informationen zu einem Prüfungsteilnehmer zu bearbeiten reicht ein Klick auf das Icon rechts in der Zeile des jeweiligen Prüfungsteilnehmenden. Danach können alle Angaben für den oder die Studierende bearbeitet werden. So können dem oder der Teilnehmenden zum Beispiel einer der bereits für die Prüfung als Prüfungsraum ausgewählten Räume und ein beliebiger Sitzplatz in diesem zugewiesen werden. Unter dem Feld zum Eintragen des Sitzplatzes wird dabei angezeigt, welche Sitzplätze im gewählten Raum verfügbar sind. In der Spalte "Punkte" können dagegen aufgabenweise die vom Prüfling erreichten Punkte eingetragen werden. Alternativ kann falls nötig zudem aus einem Dropdown-Menü ein besonderer Prüfungsstatus wie etwa "Krank", "Nicht Teilgenommen" ("NT") oder aber "Betrugsversuch" ausgewählt werden, wodurch die Punkte automatisch auf Null gesetzt werden und die Möglichkeit zum Punkteeintragen deaktiviert wird. Das Zurücksetzen des Status auf "Normal" erlaubt das Punkteeintragen wieder. Es können außerdem manuell die von einem Prüfling erreichten Bonusschritte ausgewählt werden. Nach dem Speichern der Änderungen durch einen Klick auf den entsprechenden Button werden dann (falls bereits ein Notenschlüssel eingetragen wurde) das Ergebniss sowie das Ergebnis unter Berücksichtigung aller errungenen Bonusnotenschritte berechnet. <br><br>
Für einen Studierenden können dabei alle diese Angaben gleichzeitig oder aber jede Angabe einzeln eingetragen beziehungsweise bearbeitet werden. Auf diese Weise kann diese Seite nicht nur genutzt werden, um fehlerhaft eingetragene Angaben zu korrigieren sondern auch, um für Prüfungsteilnehmende insgesamt manuell Ergebnisse einzutragen oder diesen händisch die gewünschten Sitzplätze zuzuweisen. Auch für TeilnehmerInnen ohne Matrikelnummer können auf diese Weise einfach Prüfungsergebnisse eingetragen werden.';
$string['helptext_examReviewDateRoom']= 'Falls für die Prüfung eine <strong>Klausureinsicht</strong> stattfinden soll können hier der Termin und der Raum dafür ausgewählt werden. <br><br>
Die Bezeichnung des Raumes kann dabei frei als normaler Text in das untere Formularfeld eingegeben werden. Auf diese Weise können Sie auch nicht im System als Prüfungsraum hinterlegte Räume wie etwa ihr Büro als Klausureinsichtsraum auswählen. <br><br>
Wenn Sie nach dem Zeitpunkt der Klausureinsicht Prüfungsergebnisse für die Teilnehmenden ändern können Sie diese danach einfach auf der Übersichtsseite gesondert für das Prüfungsamt exportieren. <br><br>
Die hier festgelegten Informationen zum Termin und Raum der Klausureinsicht können später auf der Übersichtsseite wieder für die Studierenden sichtbar geschaltet werden.';

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
$string['err_js_internal_error'] = 'Interner Fehler. Bitte erneut versuchen.';
$string['err_password_incorrect'] = 'Passwort nicht identisch. Bitte erneut eingeben.';
$string['err_novalidpassword'] = 'Kein gültiges Passwort.';
$string['err_examdata_deleted'] = 'Die Prüfungsdaten wurden bereits gelöscht. Eine Nutzung der Prüfungsorganisation ist nicht mehr möglich.';
$string['no_param_given'] = 'Matrikelnummernabgleich nicht möglich.';

//universal
$string['modulename'] = 'Prüfungsorganisation';
$string['modulenameplural'] = 'Prüfungsorganisationen';
$string['pluginname'] = 'Prüfungsorganisation';
$string['coursecategory_name_no_semester'] = 'SEMESTERLOS';

//add new module instance and mod_form.php
$string['modulename_help'] = 'Mithilfe der Prüfungsorganisation können Sie Prüfungen für Ihren Kurs einfach online organisieren und somit auch Prüfungen mit vielen Teilnehmerinnen und Teilnehmern bequem verwalten.

Als Dozent oder Dozentin können Sie dabei

* die Basisdaten der Prüfung einstellen
* für die Prüfungsdurchführung hilfreiche Dokumente wie etwa Sitzpläne und Teilnehmerlisten exportieren
* die Prüfungsergebnisse für die Teilnehmerinnen händisch oder mithilfe eines Barcodescanners eintragen
* alle Ergebnisse in verschiedenen Dokumenten für die weitere Verwendung (z. B. durch das Prüfungsamt) exportieren.

Die Teilnehmerinnen und Teilnehmer der Prüfung sehen hingegen in ihrer eigenen Ansicht alle relevanten Informationen der Prüfung wie etwa den Termin, den eigenen Sitzplatz oder für die Prüfung errungene Bonusnotenschritte. Außerdem kann mithilfe der Benachrichtigungsfunktion einfach und zuverlässig mit diesen kommuniziert werden.';
$string['modulename_link'] = 'https://hilfe.uni-paderborn.de/PANDA';
$string['exammanagementname'] = 'Prüfungsorganisation';
$string['exammanagement:enable exam management'] = 'Prüfungsorganisation aktivieren';
$string['messageprovider:exam management messages'] = 'Nachrichten zur Prüfungsorganisation';
$string['pluginadministration'] = 'Administration der Prüfungsorganisation';
$string['security_password'] = 'Passwortschutz';
$string['new_password'] = 'Neues Passwort';
$string['security_password_help'] = 'Durch das Festlegen eines Sicherheitspasswortes können Sie den Zugang zu dieser Prüfungsorganisation gegenüber anderen PANDA-Benutzern (z. B. Ihren studentischen Tutoren) begrenzen. Diese müssen dann zunächst das Passwort eingeben, bevor sie Zugang zu den Inhalten der Prüfungsorganisation erhalten.';
$string['confirm_new_password'] = 'Neues Passwort wiederholen';
$string['confirm_new_password_help'] = 'Für das Setzen des neuen Passwortes muss dieses hier erneut eingegeben werden.';
$string['old_password'] = 'Altes Passwort (nur benötigt falls ein bereits gesetztes Passwort geändert werden soll)';
$string['old_password_help'] = 'Falls ein bereits gesetztes Passwort geändert werden soll muss dieses hier eintragen werden.';
$string['incorrect_password_change'] = 'Das alte Passwort ist falsch. Passwortänderung abgebrochen';

//capabilities
$string['exammanagement:addinstance'] = 'Neue Prüfungsorganisation hinzufügen';
$string['exammanagement:viewinstance'] = 'Prüfungsorganisation ansehen';
$string['exammanagement:viewparticipantspage'] = 'Teilnehmeransicht ansehen';
$string['exammanagement:takeexams'] = 'Prüfung ablegen';
$string['exammanagement:sendgroupmessage'] = 'Nachricht an Teilnehmer senden';
$string['exammanagement:importdefaultrooms'] = 'Standardräume importieren';
$string['exammanagement:resetpassword'] = 'Password zurücksetzen';
$string['exammanagement:requestpasswordreset'] = 'Zurücksetzen des Passwortes beantragen';

//settings.php - admin settings
$string['moodleid_supportuser'] = 'Moodle-ID des Supportbenutzers';
$string['moodleid_supportuser_help'] = 'Der oder die Benutzerin, deren systeminterne ID hier eingetragen wurde, erhält sämtliche bei der Beantragung der Zurücksetzung des Passwortes einer Prüfungsorganisation durch NutzerInnen automatisch generierten Nachrichten. Der Benutzer (am besten ein ausschließlich für den Support angelegter Benutzer mit einer der Rollen Admin, Manager oder Kursersteller) erhält die Nachricht sowohl als PANDA-Benachrichtigung als auch an die in seinem Profil hinterlegte E-Mail-Adresse weitergeleitet. Wurde hier noch keine ID eingetragen können NutzerInnen nicht das Zurücksetzen des Passwortes in ihrer Prüfungsorganisation beantragen.';

//delete_temp_participants.php - task
$string['delete_temp_participants'] = 'Temporär gespeicherte Teilnehmer löschen';

//check_participants_without_moodle_account.php - task
$string['check_participants_without_moodle_account'] = 'Teilnehmer ohne Moodle Account überprüfen';

//delete_old_exam_data.php - task
$string['delete_old_exam_data'] = 'Alte Prüfungsdaten löschen';
$string['warningmailsubjectone'] = '[Prüfungsorganisation] Erinnerung: Zukünftige Löschung der Prüfungsdaten';
$string['warningmailsubjecttwo'] = '[Prüfungsorganisation] Warnung: Baldige Löschung der Prüfungsdaten';
$string['warningmailsubjectthree'] = '[Prüfungsorganisation] Letzte Warnung: Die Prüfungsdaten werden morgen gelöscht';
$string['warningmailcontentpartone'] = 'Alle Prüfungsinformationen der Prüfung ';
$string['warningmailcontentparttwo'] = ' im Kurs ';
$string['warningmailcontentpartthree'] = ' werden am ';
$string['warningmailcontentpartfour'] = ' gelöscht. Bitte stellen Sie sicher, dass Sie alle relevanten Prüfungsdaten zur weiteren Verwendung exportiert haben. Sie können dafür die Exportfunktionen der PANDA Prüfungsorganisation nutzen. Am angegebenen Datum werden sämtliche Prüfungsdaten endgültig gelöscht, eine nachrägliche Wiederherstellung der Daten ist ab diesem Zeitpunkt nicht mehr möglich!';
$string['warningmailcontentpartoneenglish'] = '<strong>English version</strong>: All information on the exam ';
$string['warningmailcontentparttwoenglish'] = ' in course ';
$string['warningmailcontentpartthreeenglish'] = ' will be deleted on ';
$string['warningmailcontentpartfourenglish'] = ' . Please make sure that you have exported all relevant exam data for further use. To do this, you can use the export functions of the PANDA exam organization. On the specified date, all exam data will be finally deleted, a later recovery of the data is then no longer possible!';