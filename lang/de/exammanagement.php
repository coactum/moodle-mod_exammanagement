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
 * @package     mod_exammanagement
 * @category    string
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Strings for exammanagement_participantsview.mustache used on view.php as student.
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
$string['bonussteps_for_exam'] = 'Bonusnotenschritte für die Prüfung';
$string['bonussteps_for_exam_added'] = 'Für die Prüfung haben Sie <strong>{$a}</strong> Bonusnotenschritt(e) erreicht.';
$string['bonuspoints_for_exam'] = 'Bonuspunkte für die Prüfung';
$string['bonuspoints_for_exam_added'] = 'Für die Prüfung haben Sie <strong>{$a}</strong> Bonuspunkte erreicht.';
$string['no_bonus_earned'] = 'keine';
$string['totalpoints_achieved'] = 'Sie haben an der Prüfung teilgenommen und dort <strong>{$a}</strong> Punkte erzielt.';
$string['totalpoints_achieved_with_bonus'] = 'Zusammen mit den oben genannten Bonuspunkten ergibt sich ein Prüfungsergebnis von <strong>{$a}</strong> Punkten.';
$string['legal_hint_totalpoints'] = '<strong>Wichtiger Hinweis:</strong> Dies ist lediglich eine vorläufige Punkteangabe welche sich zum Beispiel im Rahmen der Klausureinsicht noch ändern kann. Ein Rechtsanspruch auf das hier angezeigte Ergebnis existiert nicht.';
$string['exam_review'] = 'Klausureinsicht';
$string['exam_review_added'] = 'Die Klausurkorrektur ist nun abgeschlossen. Am <strong>{$a->examreviewtime}</strong> findet die Klausureinsicht in Raum <strong>{$a->examreviewroom}</strong> statt.';
$string['examdata_deleted'] = 'Die am <strong>{$a}</strong> durchgeführte Prüfung ist nun abgeschlossen.';

// Strings for exammanagement_overview.mustache used on view.php as teacher.
$string['view'] = 'Überblick';
$string['js_confirm_correction_completion'] = 'Diese Aktion schließt die Korrekturphase ab. Danach haben Sie 3 Monate Zeit, alle Prüfungsergebnisse zu exportieren, bevor diese aus Datenschutzgründen unwiederbringlich gelöscht werden.';
$string['data_deleted'] = 'Die Prüfungsdaten aller Teilnehmenden dieser Prüfungsorganisation wurden aus Datenschutzgründen drei Monate nach dem Abschluss der Korrekturphase gelöscht. Diese Prüfungsorganisation kann somit nicht mehr weiter genutzt werden, es können lediglich noch die Basisdaten der Prüfung eingesehen werden.';
$string['phase_one'] = 'Vor der Prüfung';
$string['phase_two'] = 'Für die Prüfung';
$string['phase_three'] = 'Nach der Korrektur';
$string['phase_four'] = 'Nach der Prüfung';
$string['phase_five'] = 'Klausureinsicht (optional)';
$string['exam_appointment'] = 'Prüfungstermin';
$string['minimize_phase'] = 'Minimieren';
$string['maximize_phase'] = 'Öffnen';
$string['participants_and_results_overview'] = 'Teilnehmer/innen & Ergebnisübersicht';
$string['exam_rooms'] = 'Prüfungsräume';
$string['exam_date'] = 'Prüfungstermin';
$string['exam_participants'] = 'Prüfungsteilnehmer/innen';
$string['exam_tasks'] = 'Prüfungsaufgaben';
$string['freetext_field'] = 'Freitextfeld';
$string['mediacontent'] = 'Medieninhalte';
$string['message_to_participants'] = 'Nachricht an Teilnehmer/innen';
$string['assigning_places'] = 'Sitzplatzzuweisung';
$string['seatingplan'] = 'Sitzplan';
$string['set_visibility_of_examdate'] = 'Prüfungsdatum sichtbar schalten';
$string['exam_labels'] = 'Prüfungsetiketten';
$string['set_visibility_of_examrooms_and_places'] = 'Prüfungsräume und Sitzplätze sichtbar schalten';
$string['places'] = 'Sitzplätze';
$string['participants_lists'] = 'Teilnehmerlisten';
$string['bonus'] = 'Bonus';
$string['gradingscale'] = 'Notenschlüssel';
$string['exam_results'] = 'Prüfungsergebnisse';
$string['exam_results_overview'] = 'Ergebnisübersicht';
$string['set_visibility_of_bonus_and_results'] = 'Bonus und Ergebnisse sichtbar schalten';
$string['complete_correction'] = 'Korrektur abschließen';
$string['toggle_grading_completion'] = 'Korrekturabschluss umschalten';
$string['points_for_exam_review'] = 'Punkte für Klausureinsicht';
$string['results_with_percentages'] = 'Ergebnisse mit Prozentangaben';
$string['results_and_statistics'] = 'Ergebnisse und Statistik';
$string['results_for_exam_office'] = 'Ergebnisse für das Prüfungsamt';
$string['delete_exam_data'] = 'Prüfungsdaten löschen';
$string['date_and_room_exam_review'] = 'Termin und Raum für die Klausureinsicht';
$string['set_visibility_of_exam_review_information'] = 'Informationen zur Klausureinsicht sichtbar schalten';
$string['altering_exam_results'] = 'Änderung der Prüfungsergebnisse';
$string['export_altered_exam_results'] = 'Export der geänderten Ergebnisse';
$string['toggle_information'] = 'Sichtbarkeit umschalten';
$string['state_optional'] = 'Optional';
$string['state_required'] = 'Zwingend';
$string['state_success'] = 'Erfolgreich';
$string['state_notset'] = 'Nicht gesetzt';
$string['state_notpossible_participants_missing'] = 'Teilnehmer/innen fehlen';
$string['state_notpossible_rooms_missing'] = 'Räume fehlen';
$string['state_notpossible_examtime_missing'] = 'Datum fehlt';
$string['state_notpossible_assignedplaces_missing'] = 'Sitzplatzzuweisung fehlt';
$string['state_notpossible_tasks_missing'] = 'Aufgaben fehlen';
$string['state_notpossible_bonus_missing'] = 'Bonus fehlt';
$string['state_notpossible_results_missing'] = 'Ergebnisse fehlen';
$string['state_notpossible_correctioncompleted_missing'] = 'Korrekturabschluss fehlt';
$string['state_notpossible_examreviewtime_missing'] = 'Zeit der Klausureinsicht fehlt';
$string['state_notpossible_examreviewroom_missing'] = 'Raum der Klausureinsicht fehlt';
$string['state_notpossible_gradingscale_missing'] = 'Notenschlüssel fehlt';
$string['state_loading'] = 'Lädt ...';
$string['important_note'] = 'Wichtiger Hinweis:';
$string['note'] = 'Hinweis:';
$string['exam_rooms_set'] = 'Es wurden bereits die folgenden <strong> {$a->roomscount} Räume</strong> mit insgesamt <strong>{$a->totalseatscount} Sitzplätzen</strong> für die Prüfung ausgewählt:';
$string['exam_rooms_not_set'] = 'Es wurden noch keine Räume für die Prüfung ausgewählt.';
$string['deleted_room'] = 'Gelöschter Raum';
$string['exam_date_set'] = 'Die Prüfung findet am <strong>{$a}</strong> statt.';
$string['exam_date_not_set'] = 'Es wurden noch kein Datum und keine Uhrzeit für die Prüfung festgelegt.';
$string['exam_participants_set'] = '<strong>{$a}</strong> Teilnehmer/innen sind zur Prüfung angemeldet.';
$string['exam_participants_not_set'] = 'Es wurden noch keine Teilnehmer/innen zur Prüfung hinzugefügt.';
$string['exam_tasks_set'] = 'Es wurden bereits <strong>{$a->taskcount} Prüfungsaufgaben</strong> mit insgesamt <strong>{$a->totalpoints} Punkten</strong> angelegt.';
$string['exam_tasks_not_set'] = 'Es wurden noch keine Aufgaben konfiguriert.';
$string['textfield_set'] = 'Im Textfeld steht folgender Inhalt:';
$string['textfield_not_set'] = 'Es wurde noch kein Inhalt für das Textfeld eingetragen.';
$string['message_to_participants_str'] = 'Hier können Nachrichten als interne Mitteilungen sowie Emails an alle zur Prüfung hinzugefügten Teilnehmer/innen versendet werden.';
$string['places_assigned'] = '<strong>{$a->assignedplacescount} / {$a->participantscount}</strong> Teilnehmenden wurden bereits Sitzplätze zugewiesen.';
$string['all_places_assigned'] = 'Die Sitzplatzzuweisung ist damit erfolgreich abgeschlossen.';
$string['not_all_places_assigned_yet'] = 'Einigen Teilnehmenden wurden noch keine Sitzplätze zugewiesen. Diese sind in später exportierten Sitzplänen, Teilnehmerlisten und Prüfungsetiketten nicht enthalten.';
$string['export_seatingplan_str'] = 'Hier kann der Sitzplan nach Sitzplatz oder nach Matrikelnummern sortiert als PDF-Dokument exportiert werden.';
$string['information_visible'] = 'Die Informationen wurden für die Teilnehmer/innen sichtbar geschaltet.';
$string['information_not_visible'] = 'Die Informationen wurden noch nicht für die Teilnehmer/innen sichtbar geschaltet.';
$string['export_examlabels_str'] = 'Hier können Prüfungsetiketten mit Barcodes oder QR-Codes exportiert werden.';
$string['export_examlabels_note'] = 'Erst wenn allen Teilnehmenden Sitzplätze zugewiesen wurden erscheinen diese auf den Prüfungsetiketten.';
$string['export_participantslists_str'] = 'Hier können Teilnehmerlisten nach Nachname oder nach Sitzplatz sortiert als PDF-Dokument exportiert werden.';
$string['export_participantslists_note'] = 'Diese Listen sind nur für den internen Gebrauch durch die Lehrenden bestimmt und dürfen aus Datenschutzgründen nicht veröffentlicht werden!';
$string['no_exam_date_set_str'] = 'Noch wurden kein Prüfungstermin und keine Prüfungsräume festgelegt.';
$string['bonus_set'] = 'Es wurden bisher Bonuspunkte oder Bonusnotenschritte für <strong>{$a->bonuscount} / {$a->participantscount}</strong> Teilnehmende importiert.';
$string['bonus_not_set'] = 'Es wurden noch keine Bonuspunkte oder Bonusnotenschritte eingetragen.';
$string['gradingscale_set'] = 'Es wurde bereits ein Notenschlüssel konfiguriert.';
$string['gradingscale_not_set'] = 'Es wurde noch kein Notenschlüssel konfiguriert.';
$string['results_set'] = 'Es wurden bisher <strong>{$a->resultscount} / {$a->participantscount}</strong> Prüfungsergebnisse eingetragen.';
$string['results_not_set'] = 'Es wurden noch keine Prüfungsergebnisse eingetragen.';
$string['exam_results_overview_str'] = 'Hier können alle bereits eingegebenen Prüfungsergebnisse angesehen und manuell geändert werden.';
$string['complete_correction_str'] = 'Die in dieser Prüfungsorganisation eingetragenen Daten sind sehr sensibel und müssen deshalb aus Datenschutzgründen gelöscht werden, sobald sie nicht mehr benötigt werden. Nachdem Sie durch Umlegen des Schalters den Abschluss der Korrektur bestätigt haben haben Sie deshalb <strong> drei Monate </strong> Zeit, die Prüfungsergebnisse für eine weitere Verwendung zu exportieren, bevor diese automatisch gelöscht werden.';
$string['export_points_examreview_str'] = 'Hier können die erreichten Punkte als PDF-Dokument exportiert werden.';
$string['export_points_examreview_str_points'] = 'Hier können die erreichten Klausurpunkte <strong>(ohne ggf. vorher errungene Bonuspunkte)</strong> als PDF-Dokument exportiert werden.';
$string['export_results_lists_note'] = 'Diese Punkteliste ist nur für den internen Gebrauch durch die Lehrenden bestimmt und darf aus Datenschutzgründen nicht veröffentlicht werden!';
$string['export_results_statistics_note'] = 'Diese Datei enthält alle Prüfungsdaten und - ergebnisse, ist nur für den internen Gebrauch durch die Lehrenden bestimmt und darf aus Datenschutzgründen nicht veröffentlicht werden!';
$string['export_results_percentages_str'] = 'Hier können die Ergebnisse mit Prozentangaben als PDF-Dokument exportiert werden.';
$string['export_results_percentages_str_points'] = 'Hier können die Klausurergebnisse <strong>(ohne ggf. vorher errungene Bonuspunkte)</strong> mit Prozentangaben als PDF-Dokument exportiert werden.';
$string['export_results_statistics_str'] = 'Hier können die Ergebnisse und Statistiken als Excel-Dokument exportiert werden.';
$string['export_results_text_str'] = 'Hier können die Ergebnisse für das Prüfungsamt als Text-Dokument exportiert werden.';
$string['data_deletion_date_set'] = 'Am <strong>{$a}</strong> werden alle in dieser Instanz gespeicherten Daten wie etwa Teilnehmer/innen, Prüfungsdetails und Prüfungsergebnisse automatisch gelöscht. Stellen Sie deshalb sicher, dass Sie bis dahin alle wichtigen Daten wie etwa Prüfungsergebnisse für eine weitere Verwendung über die Dokumentenexportfunktionen exportiert haben.';
$string['date_room_examreview_set'] = 'Die Klausureinsicht findet am <strong>{$a->examreviewdate}</strong> in Raum <strong>{$a->examreviewroom}</strong> statt.';
$string['room_examreview_set'] = 'Die Klausureinsicht findet in Raum <strong>{$a}</strong> statt.';
$string['date_examreview_set'] = 'Die Klausureinsicht findet am <strong>{$a}</strong> statt.';
$string['date_room_examreview_not_set'] = 'Es wurden noch kein Termin und kein Raum für die Klausureinsicht festgelegt.';
$string['exam_results_altered'] = 'Es wurden bisher <strong>{$a}</strong> Prüfungsergebnisse nach der Klausureinsicht geändert.';
$string['no_examresults_altered'] = 'Bisher wurden noch keine Prüfungsergebnisse nach der Klausureinsicht geändert.';
$string['exam_results_altered_note'] = 'Hier erscheint nur die Zahl der Prüfungsergebnisse, die ab dem für die Klausureinsicht eingetragenen Zeitpunkt geändert wurden. Das Ändern eines Prüfungsergebnisses nach dem Klick auf die unten stehende Schaltfläche überschreibt dabei sämtliche vorher gespeicherten Ergebnisse für den oder die Teilnehmende. Stellen Sie deshalb sicher, dass Sie vor dem Ändern der Ergebnisse die alten Prüfungsergebnisse zu Sicherungszwecken einmal über die Dokumentenexport-Funktionen in der Phase "Nach der Klausur" exportiert haben.';
$string['export_altered_examresults_str'] = 'Hier können die nach der Klausureinsicht geänderten Ergebnisse als Text-Dokument exportiert werden.';
$string['export_altered_examresults_note'] = 'Diese Schaltfläche ermöglicht den einfachen Export aller seit dem Datum der Klausureinsicht geänderter Prüfungsergebnisse in einer Datei für das Prüfungsamt. Möchten Sie stattdessen einen nach den eingelesenen Teilnehmer-Listen getrennten Export der geänderten Ergebnisse stehen Ihnen dafür wieder die Möglichkeiten des Ergebnis-Exportes aus der Phase "Nach der Klausur" zur Verfügung.';
$string['configure_password'] = 'Passwort konfigurieren';
$string['choose_rooms'] = 'Räume auswählen';
$string['set_date'] = 'Termin festlegen';
$string['add_participants'] = 'Teilnehmer/innen hinzufügen';
$string['configure_tasks'] = 'Aufgaben konfigurieren';
$string['edit_textfield'] = 'Freitextfeld bearbeiten';
$string['send_groupmessage'] = 'Nachricht schreiben';
$string['assign_places'] = 'Sitzplätze automatisch zuweisen';
$string['assign_places_manually'] = 'Sitzplätze manuell zuweisen';
$string['export_seatingplan_place'] = 'Nach Sitzplatz sortiert';
$string['export_seatingplan_matrnr'] = 'Nach Matrikelnummer sortiert';
$string['export_barcodes'] = 'Mit Barcodes';
$string['export_qrcodes'] = 'Mit QR-Codes';
$string['export_participantslist_names'] = 'Nach Namen geordnet';
$string['export_participantslist_places'] = 'Nach Sitzplätzen geordnet';
$string['import_bonus'] = 'Bonus importieren';
$string['configure_gradingscale'] = 'Notenschlüssel konfigurieren';
$string['add_examresults_manually'] = 'Ergebnisse manuell eintragen';
$string['add_examresults_barcode'] = 'Ergebnisse eintragen (QR- oder Barcode)';
$string['check_results'] = 'Ergebnisse prüfen';
$string['export_as_pdf'] = 'PDF exportieren';
$string['export_as_excel'] = 'Excel-Dokument exportieren';
$string['export_as_text'] = 'Textdokument exportieren';
$string['examreview_dateroom'] = 'Termin und Raum für Klausureinsicht festlegen';
$string['change_examresults'] = 'Prüfungsergebnisse ändern';
$string['available_places'] = 'Verfügbare Plätze';

// Strings for exammanagement_overview.mustache if mode export_grades is active.
$string['export_grades'] = 'Bewertungen exportieren';
$string['export_grades_help'] = 'Hier können die von den Kursteilnehmer/innen errungenen Bewertungen (als Punkte oder Noten) zuerst importiert, bei Bedarf in Noten umgerechnet und dann für das Prüfungsamt (zu den jeweiligen Matrikelnummern zugeordnet) als Textdatei exportiert werden. <br><br>
Dazu müssen ... <br>
1. alle betroffenen Kursteilnehmer/innen hinzugefügt werden <br>
2. falls eine Umrechnung von Punkten in eine Note gewünscht ist ein Notenschlüssel konfiguriert werden <br>
3. die Bewertungen zunächst aus dem Kurs exportiert und dann in die Prüfungsorganisation importiert werden. <br><br>
Nachdem dann die Ergebnisse überprüft und gegebenenfalls angepasst sowie die Vorbereitungen abgeschlossen wurden können die den Matrikelnummern der Teilnehmenden zugeordneten Bewertungspunkte oder Noten in einer Textdatei exportiert werden.';
$string['import_grades'] = 'Bewertungen importieren';
$string['grades'] = 'Bewertungen';
$string['grades_set'] = 'Es wurden bisher für <strong>{$a->gradescount} / {$a->participantscount}</strong> Teilnehmende Bewertungen importiert.';
$string['grades_not_set'] = 'Es wurden noch keine Bewertungen importiert.';
$string['grading_points'] = 'Bewertungen (Punkte oder Noten)';
$string['result_based_of_grades'] = 'Note (errechnet aus Bewertungspunkten)';
$string['revert_grades'] = 'Alle Bewertungen zurücksetzen';
$string['import_grades_text'] = 'Hier können von den Teilnehmer/innen errungene Punkte der Kursbewertungen importiert werden.';
$string['exam_results_overview_grades'] = 'Hier können die bereits importierten Bewertungen und die daraus gegebenenfalls errechneten Noten angesehen werden.';
$string['complete_preparations'] = 'Vorbereitungen abschließen';
$string['participantsoverview_grades_help'] = 'In dieser <strong>Bewertungsübersicht</strong> können für sämtliche importierten Teilnehmer/innen deren Bewertungspunkte und die daraus gegebenenfalls berechneten Noten angesehen und bearbeitet werden.<br><br>
Nach einem Klick auf den Button "Bewertungen bearbeiten" können für jeden Teilnehmenden manuell Bewertungen (sowohl als Punkt oder Notenwert) eingetragen oder aber bereits eingetragene Bewertungen bearbeitet werden. Mithilfe der Tabulator-Taste kann dabei zwischen den einzelnen Teilnehmer/innen gewechselt werden, während ein Klick auf den entsprechenden Button genau wie das Drücken der Enter-Taste alle vorgenommenen Änderungen speichert.<br><br>
Falls bereits ein Notenschlüssel eingetragen wurde werden die importierten Bewertungspunkte automatisch in eine Note umgerechnet, die dann später exportiert werden kann. Wurde kein Notenschlüssel eingetragen (zum Beispiel weil die Bewertungen schon als Noten importiert wurden) werden später stattdessen diese exportiert.';
$string['participantsoverview_grades'] = 'Bewertungsübersicht';
$string['importbonus_grades_help'] = 'Hier können (beispielsweise bei der Bearbeitung von Übungsaufgaben errungene) <strong>Bewertungspunkte</strong> der Teilnehmer/innen importiert werden. <br><br>
Bewertungsexport aus '. get_config('mod_exammanagement', 'moodlesystemname').': Dazu müssen die Bewertungen aus dem '. get_config('mod_exammanagement', 'moodlesystemname').' Kurs zunächst wie <a href="https://docs.moodle.org/de/Bewertungen_exportieren" class="alert-link" target="_blank">hier</a> beschrieben exportiert werden. Danach müssen Sie die exportierte Datei einmal öffnen und nachsehen, in welcher Spalte die Punkte eingetragen sind. Die Bezeichnung der Spalte muss dann im dafür vorgesehenen Feld im unteren Abschnitt eingetragen werden.<br><br>
Zum Abschluss muss nun noch die einzulesende Datei mit den Bewertungspunkten ausgewählt und dann durch einen Klick auf den Button "Datei einlesen" eingelesen werden, um den Bewertungspunkteimport durchzuführen.';
$string['importbonus_grades'] = 'Bewertungen importieren';
$string['edit_grades'] = 'Bewertungen bearbeiten';

// Strings for configurepassword.php form.
$string['configurepassword'] = 'Passwort konfigurieren';
$string['configure_password'] = 'Hier kann ein Passwort für die Prüfungsorganisation gesetzt und geändert werden.';
$string['password'] = 'Passwort';
$string['reset_password'] = 'Passwort zurücksetzen';

// Strings for checkpassword.php and form.
$string['checkpassword'] = 'Passwort eingeben';
$string['checkpasswordadmin'] = 'Passwort eingeben';
$string['checkpasswordstr'] = 'Der oder die Dozentin hat für diese Prüfungsorganisation ein Passwort festgelegt. Geben Sie es ein, um Zugriff auf die Inhalte der Prüfungsorganisation zu erhalten.';
$string['confirmpassword'] = 'Passwort bestätigen';
$string['resetpasswordadmin'] = 'Passwort zurücksetzen und alle Lehrenden benachrichtigen';
$string['requestpasswordreset'] = 'Zurücksetzen des Passwortes beim Support beantragen';
$string['wrong_password'] = 'Passwort falsch. Bitte erneut versuchen.';
$string['password_reset_successfull'] = 'Das Passwort der Prüfungsorganisation wurde erfolgreich zurückgesetzt und alle Lehrenden des {$a->systemname}-Kurses wurden darüber via {$a->systemname} Benachrichtigung informiert.';
$string['password_reset_failed'] = 'Zurücksetzen des Passwortes aufgrund fehlender Berechtigungen fehlgeschlagen.';
$string['password_reset_request_successfull'] = 'Das Zurücksetzen des Passwortes der Prüfungsorganisation wurde erfolgreich beim Support beantragt. Sobald das Passwort zurückgesetzt wurde werden Sie und alle anderen Lehrenden des {$a->systemname}-Kurses darüber via {$a->systemname}-Nachricht informiert.';
$string['password_reset_request_failed'] = 'Beantragung des Passwort-Zurücksetzens fehlgeschlagen. Bitte kontaktieren Sie den Support auf üblichem Weg via E-Mail.';
$string['password_reset_mailsubject'] = '[{$a->systemname}-Support] Zurücksetzen des Passwortes der Prüfungsorganisation "{$a->name}" im Kurs "{$a->coursename}" erfolgreich';
$string['password_reset_mailtext'] = 'Der {$a->systemname} Support hat wie angefordert das Passwort der Prüfungsorganisation "{$a->name}" im Kurs "{$a->coursename}" zurückgesetzt. Sie können nun ohne Eingabe eines Passwortes auf die Inhalte der Prüfungsorganisation zugreifen und falls gewünscht ein neues Passwort für die Prüfungsorganisation festlegen. <br>Viele Grüße, <br>Ihr {$a->systemname}-Team <br><br> <b>English version:</b> The {$a->systemname} support has resetted the password of the exam management "{$a->name}" in course "{$a->coursename}". You can now access the contents of the exam management without entering a password and, if required, define a new password for the exam management. <br>Greetings, <br>Your {$a->systemname} team';
$string['password_reset_request_mailsubject'] = '{$a->systemname} Prüfungsorganisation: Anforderung eines Passwort-Resets für die Prüfungsorganisation "{$a->name}" im Kurs "{$a->coursename}"';
$string['password_reset_request_mailtext'] = 'Der bzw. die {$a->systemname} Benutzerin {$a->user} hat das Zurücksetzen des Passwortes für die Prüfungsorganisation im Kurs "{$a->coursename}" beantragt. <br> Durch einen Klick auf diesen <b><a href="{$a->url}">Link</a></b> können Sie als in {$a->systemname} angemeldeter Benutzer mit der Rolle Admin, Manager oder Kursverwalter das Passwort der Prüfungsorganisation zurücksetzen. Dadurch können sämtliche Lehrenden des Kurses wieder ohne Eingabe eines Passwortes auf die Inhalt der Prüfungsorganisation zugreifen und werden darüber automatisch per Mail informiert.';

// Strings for chooserooms.php and form.
$string['chooserooms'] = 'Prüfungsräume auswählen';
$string['choose_rooms_str'] = 'Die unten stehenden Räume können als Prüfungsräume gewählt werden.';
$string['export_default_rooms'] = 'Standardräume exportieren';
$string['import_default_rooms'] = 'Standardräume importieren';
$string['add_custom_room'] = 'Eigenen Prüfungsraum anlegen';
$string['add_default_room'] = 'Standardraum anlegen';
$string['roomid'] = 'Raum ID';
$string['exam_room'] = 'Raum';
$string['description'] = 'Beschreibung';
$string['room_type'] = 'Raumart';
$string['no_seatingplan_available'] = 'Kein Sitzplan verfügbar';
$string['default_room'] = 'Standardraum';
$string['custom_room'] = 'Eigener Raum';
$string['change_room'] = 'Raum ändern';
$string['delete_room'] = 'Raum löschen';
$string['delete_defaultroom_confirm'] = 'Durch diese Aktion wird der gewählte Standardraum gelöscht. Falls dieser bereits von Lehrenden als Prüfungsraum ausgewählt wurde bleiben seine Informationen in den entsprechenden Prüfungsorganisationen zunächst erhalten, er kann jedoch nicht mehr als neuer Prüfungsraum ausgewählt oder für die (Neu-)Zuweisung von Sitzplätzen genutzt werden.';
$string['delete_room_confirm'] = 'Durch diese Aktion wird dieser selbst erstellte Raum gelöscht. Stellen Sie sicher, dass der Raum aktuell nicht als Prüfungsraum ausgewählt ist.';
$string['places_already_assigned_rooms'] = '<strong>Achtung:</strong> Es wurden bereits einigen Teilnehmenden Sitzplätze in diesem Raum zugewiesen. Falls dieser Raum jetzt als Prüfungsraum abgewählt wird wird die gesamte Sitzplatzzuweisung gelöscht und muss dann neu durchgeführt werden.';
$string['no_rooms_found'] = 'Keine Räume gefunden';
$string['room_deselected_as_examroom'] = 'Der Raum muss zunächst als Prüfungsraum abgewählt werden.';

// String for exportdefaultrooms.php.
$string['default_exam_rooms'] = 'Standardprüfungsräume';
$string['no_default_rooms'] = 'Keine Standardprüfungsräume vorhanden';

// Strings for importdefaultrooms.pgp and form.
$string['importdefaultrooms'] = 'Standardräume importieren';
$string['import_default_rooms_str'] = 'Hier können die allen Dozenten als mögliche Prüfungsräume zur Auswahl stehenden Standardräume von Administratoren aus einer Text-Datei importiert werden.';
$string['default_rooms_already_exists'] = '<strong>Achtung:</strong> Es wurden bereits Standardräume importiert. Diese werden durch den Import aus einer neuen Datei überschrieben.';
$string['default_rooms_file_structure'] = 'Import der Standardräume aus Text-Datei (.txt). <br><strong>Aufbau</strong>: Pro Zeile ein Prüfungsraum. Erste Spalte systeminterne Raumid (Raumname_Variante; also z. B. Audimax_2), zweite Spalte benutzersichtbarer Raumname (z. B. Audimax), dritte Spalte benutzersichtbare Beschreibung inklusive Anzahl freigelassener sowie gesamter Plätze (z. B. 2 Plätze frei, 56 Plätze insgesamt), vierte Spalte zur Berechnung der Sitzplatzzahl benötigtes Array mit der Bezeichnung jedes einzelnen Platzes in json-Syntax (z. B. ["R/R01/P07","R/R01/P04","R/R01/P01"] ), fünfte Spalte Quellcode einer SVG-Datei mit dem Raumplan um diesen den Benutzern anzuzeigen (falls vorhanden, ansonsten leer lassen). Raumeigenschaften getrennt durch das Zeichen * . <i>Beispiel:</i> AudiMax_1*AudiMax*1 Platz frei, 3 Plätze*["R/R01/P01","R/R02/P01","R/R02/P03"]*< svg>...< /svg>';

// Strings for addcustomroom.php and form.
$string['addcustomroom'] = 'Eigenen Prüfungsraum anlegen oder bearbeiten';
$string['change_custom_room_name'] = '<strong>Hinweis:</strong> Falls Sie den Namen eines bestehenden Raumes ändern wird stattdessen ein neuer Raum angelegt. In diesem Fall muss der alte Raum noch manuell gelöscht werden.';
$string['custom_room_places'] = '<strong>Hinweis:</strong> Der von Ihnen hier erstellte eigene Prüfungsraum erhält im System so viele Plätze wie von Ihnen unten angegeben werden, wobei die Nummerierung (unabhängig von der im Raum tatsächlich vorhandenen Sitzplatzanzahl oder deren Nummerieung) bei 1 startet und dann hoch zählt. Sie müssen also selbst sicherstellen, dass die von Ihnen angegebene Platzzahl mit den tatsächlich vorhandenen Sitzplätzen übereinstimmt und müssen zudem mögliche Abweichungen bei der Sitzplatznummerierung selbst anpassen.';
$string['customroom_name'] = 'Name des Raums';
$string['customroom_placescount'] = 'Anzahl der Sitzplätze';
$string['customroom_description'] = 'Optionale Beschreibung für die Anzeige des Raums bei der Raumauswahl';
$string['add_room'] = 'Raum speichern';
$string['no_description_new_room'] = 'Keine Beschreibung vorhanden.';

// Strings for editdefaultroom.php and form.
$string['editdefaultroom'] = 'Standardraum bearbeiten';
$string['edit_defaultroom_str'] = 'Hier können Administratorinnen und Administratoren die vorhandenen Standardprüfungungsräume bearbeiten oder Neue erstellen.';
$string['general'] = 'Basisinformationen';
$string['roomid_internal'] = 'Raum ID (systemintern; Raumname_Variante, also z. B. L1.202_1, Audimax_2; erlaubte Zeichen: Buchstaben, Zahlen, Punkt und Unterstrich)';
$string['defaultroom_name'] = 'Name des Raums (benutzersichtbar, erlaubte Zeichen: Buchstaben, Zahlen, Punkt und Leerzeichen)';
$string['defaultroom_description'] = 'Beschreibung (benutzersichtbar, z. B. Informationen zur Modellierung wie die Zahl freier Plätze zwischen zwei Sitzplätzen, erlaubte Zeichen: Buchstaben, Zahlen, Punkt, Minus und Leerzeichen)';
$string['defaultroom_placescount'] = 'Anzahl der besetzbaren Sitzplätze';
$string['placespreview'] = 'Benennungen aller besetzbaren Sitzplätze';
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
$string['no_editable_default_room'] = 'Kein bearbeitbarer Standardraum da durch Dozent angelegt';

// Strings for setexamdate.php and form.
$string['setexamdate'] = 'Prüfungstermin festlegen';
$string['setexamdatestr'] = 'Hier können das Datum und die Uhrzeit der Prüfung ausgewählt werden.';

// Strings for viewparticipants.php.
$string['viewparticipants'] = 'Teilnehmer/innen ansehen';
$string['import_participants_from_file_recommended'] = 'Teilnehmer/innen aus Datei hinzufügen';
$string['import_course_participants_optional'] = 'Kursteilnehmer/innen importieren';
$string['view_added_partipicants'] = 'Liste aller zur Prüfung hinzugefügten Teilnehmer/innen.';
$string['participants'] = 'Teilnehmer/innen';
$string['matriculation_number'] = 'Matrikelnummer';
$string['course_groups'] = 'Kursgruppen';
$string['import_state'] = 'Status';
$string['state_added_to_exam'] = 'Prüfungsteilnehmer/in';
$string['state_added_to_exam_no_moodle'] = 'Prüfungsteilnehmer/in (ohne {$a->systemname} Benutzerkonto)';
$string['state_added_to_exam_no_moodle_help'] = 'Diese/r Teilnehmer/innen muss sich mindestens einmal im System anmelden um die Prüfungsinformationen in der Teilnehmeransicht einsehen zu können. Er oder sie kann zudem über die Prüfungsorganisation versendete Gruppennachrichten nicht empfangen und kann auch nicht in Kursgruppen hinzugefügt werden.';
$string['state_added_to_exam_no_course'] = 'Prüfungsteilnehmer/in (kein/e Kursteilnehmer/in)';
$string['state_added_to_exam_no_course_help'] = 'Diese/r Teilnehmer/in muss erst zum Kurs hinzugefügt werden bevor er oder sie die Prüfungsinformationen in der Teilnehmeransicht einsehen oder in Kursgruppen hinzugefügt werden kann.';
$string['delete_participant'] = 'Teilnehmer/in löschen';
$string['participant_deletion_warning'] = 'Durch diese Aktion werden der gewählte Prüfungsteilnehmende sowie alle für diesen eingetragenen Ergebnisse gelöscht.';
$string['delete_all_participants'] = 'Alle Teilnehmer/innen löschen';
$string['all_participants_deletion_warning'] = 'Durch diese Aktion werden sämtliche Prüfungsteilnehmenden sowie alle für diese eingetragenen Ergebnisse gelöscht.';
$string['deleted_user'] = 'Aus {$a->systemname} gelöschte/r Teilnehmer/in';

// Strings for converttogroup.php and form.
$string['convert_to_group'] = 'Zu Gruppe konvertieren';
$string['converttogroup'] = 'Zu Gruppe konvertieren';
$string['convert_to_group_str'] = 'Hier können ausgewählte oder alle importierten Prüfungsteilnehmer/innen in eine {$a->systemname} Gruppe umgewandelt werden.';
$string['participants_convertable'] = 'Prüfungsteilnehmer/innen werden zur Gruppe hinzugefügt.';
$string['participants_not_convertable'] = 'Prüfungsteilnehmer/innen können nicht zur Gruppe hinzugefügt werden.';
$string['groupname'] = 'Gruppenname';
$string['groupdescription'] = 'Beschreibung der Gruppe';
$string['group'] = 'Gruppe';
$string['new_group'] = 'Neue Gruppe';

// Strings for addparticipants.php and form.
$string['import_participants_from_file'] = 'Teilnehmer/innen aus Dateien hinzufügen';
$string['import_from_text_file'] = 'Prüfungsteilnehmer aus einer oder mehreren Dateien importieren (Einträge mit Tabulator getrennt; die ersten zwei Zeilen sind für Informationen wie den Prüfungsnamen vorgesehen und werden nicht ausgewertet).';
$string['read_file'] = 'Dateien einlesen';
$string['addparticipants'] = 'Teilnehmer/innen hinzufügen';
$string['import_new_participants'] = 'Andere Teilnehmer/innen hinzufügen';
$string['places_already_assigned_participants'] = '<strong>Achtung:</strong> Es wurden bereits Sitzplätze zugewiesen. Falls nun neue Prüfungsteilnehmende hinzugefügt werden müssen diesen noch Sitzplätze zugewiesen werden.';
$string['newmatrnr'] = 'Benutzer werden zur Prüfung hinzugefügt.';
$string['badmatrnr'] = 'Zeilen mit ungültigen Matrikelnummern (Benutzer können nicht zur Prüfung hinzugefügt werden).';
$string['oddmatrnr'] = 'Benutzer mit Warnungen werden zur Prüfung hinzugefügt.';
$string['existingmatrnr'] = 'Benutzer sind bereits Prüfungsteilnehmer/innen (keine Änderungen).';
$string['deletedmatrnr'] = 'Prüfungsteilnehmer/innen werden entfernt.';
$string['select_deselect_all'] = 'Alle aus-/abwählen';
$string['add_to_exam'] = 'Zur Prüfung hinzufügen';
$string['no_participants_added_page'] = 'Bisher wurden keine Teilnehmer/innen zur Prüfung hinzugefügt.';
$string['state_newmatrnr'] = 'Neu eingelesen';
$string['state_nonmoodle'] = 'Ohne {$a->systemname} Benutzerkonto';
$string['state_nonmoodle_help'] = 'Diese/r Teilnehmer/in muss sich nach dem Import erst noch mindestens einmal im System anmelden, um die Prüfungsinformationen in der Teilnehmeransicht einsehen zu können. Er oder sie kann sonst außerdem keine über die Prüfungsorganisation versendete Gruppennachrichten empfangen und auch nicht in Kursgruppen hinzugefügt werden.';
$string['state_badmatrnr'] = 'Ungültige Matrikelnummer';
$string['state_doubled'] = 'Doppelte Matrikelnummer';
$string['state_no_courseparticipant'] = 'Kein Kursteilnehmer/in';
$string['state_no_courseparticipant_help'] = 'Diese/r Teilnehmer/in muss nach dem Import erst noch zum Kurs hinzugefügt werden, um die Prüfungsinformationen in der Teilnehmeransicht einsehen oder in Kursgruppen hinzugefügt werden zu können.';
$string['state_existingmatrnr'] = 'Bereits Prüfungsteilnehmer/in';
$string['state_existingmatrnrnocourse'] = 'Bereits Prüfungsteilnehmer/in (kein Kursteilnehmer/in)';
$string['state_existingmatrnrnomoodle'] = 'Bereits Prüfungsteilnehmer/in (ohne {$a->systemname} Benutzerkonto)';
$string['state_to_be_deleted'] = 'Wird gelöscht';
$string['state_not_in_file_anymore'] = 'Nicht mehr in Datei';
$string['state_convertable_group'] = 'Zu Gruppe hinzufügbar';
$string['state_not_convertable_group_moodle'] = 'Nicht zuweisbar (ohne {$a->systemname} Benutzerkonto)';
$string['state_not_convertable_group_course'] = 'Nicht zuweisbar (kein Kursteilnehmer/in)';

// Strings for addcourseparticipants.php and form.
$string['addcourseparticipants'] = 'KursTeilnehmer/innen hinzufügen';
$string['state_courseparticipant'] = 'Kursteilnehmer/in';
$string['view_added_and_course_partipicants'] = 'Liste aller bisher zur Prüfung hinzugefügten Teilnehmer/innen sowie aller Kursteilnehmer/innen.';
$string['deletedmatrnr_no_course'] = 'Prüfungsteilnehmer/innen werden entfernt (da sie keine Kursteilnehmer/innen sind).';
$string['existingmatrnr_course'] = 'Kursteilnehmer/innen sind bereits Prüfungsteilnehmer/innen (keine Änderungen).';
$string['course_participant_import_preventing_text_export'] = '<strong>Achtung:</strong> Der Import der Kursteilnehmer/innen als Prüfungsteilnehmer/innen ist zwar möglich, allerdings werden diese Teilnehmer/innen später beim Ergebnis-Export für das Prüfungsamt in einer eigenen Liste exportiert. Ihre Ergebnisse können somit gegebenenfalls nicht vernünftig reimportiert werden. Wenn Sie vorhaben, die Prüfungsergebnisse zu reimportieren, sollten Sie die Teilnehmer/innen lieber mithilfe der Teilnehmerlisten der Prüfung importieren.';

// Strings for configuretasks.php and form.
$string['configuretasks'] = 'Aufgaben konfigurieren';
$string['configure_tasks_text'] = 'Hier können die Anzahl und die Maximalpunktzahlen aller Prüfungsaufgaben festgelegt werden.';
$string['add_remove_tasks'] = 'Aufgaben hinzufügen oder entfernen:';
$string['task'] = 'Aufgabe';
$string['points'] = 'Punkte';
$string['total'] = 'Gesamtpunktzahl';
$string['results_already_entered'] = '<strong>Achtung:</strong> Es wurden bereits Prüfungsergebnisse eingetragen. Prüfen Sie bitte nach dem Ändern der Aufgaben, ob diese eventuell aktualisiert werden müssen.';
$string['gradingscale_already_entered'] = '<strong>Achtung:</strong> Es wurde bereits ein Notenschlüssel eingetragen. Prüfen Sie bitte nach dem Ändern der Aufgaben, ob dieser eventuell angepasst werden muss.';

// Strings for textfield.php and form.
$string['settextfield'] = 'Freitext hinzufügen';
$string['contentoftextfield'] = 'Inhalt des Textfeldes';
$string['settextfieldstr'] = 'Hier kann ein beliebiger prüfungsbezogener Inhalt eingetragen werden, welcher den Prüfungsteilnehmer/innen nach dem Speichern sofort in deren Teilnehmeransicht angezeigt wird.';

// Strings for the sendgroupmessage.php and form.
$string['sendgroupmessage'] = 'Gruppennachricht schreiben';
$string['groupmessages_text'] = 'Der unten eingegebene Text wird <strong>{$a->participantscount}</strong> zur Prüfung hinzugefügten Teilnehmer/innen als {$a->systemname}-Benachrichtigung sowie als Email zugeschickt.';
$string['groupmessages_warning'] = '<strong>Achtung: {$a->participantscount}</strong> Prüfungsteilnehmer/innen besitzen kein {$a->systemname}-Benutzerkonto und werden diese Nachricht deshalb nicht automatisch erhalten. Kontaktieren Sie diese Teilnehmer/innen deshalb am besten manuell per E-Mail durch einen Klick auf den folgenden Button:';
$string['send_manual_message'] = 'E-Mail schreiben';
$string['subject'] = 'Betreff';
$string['content'] = 'Inhalt';
$string['send_message'] = 'Nachricht abschicken';
$string['mailsubject'] = '[{$a->systemname} - Prüfungsorganisation] {$a->coursename}: {$a->subject}';
$string['mailfooter'] = 'Diese Nachricht wurde über die Prüfungsorganisation in {$a->systemname} verschickt. Unter dem folgenden Link finden Sie alle weiteren Informationen. <br> {$a->categoryname} -> {$a->coursename} -> Prüfungsorganisation -> {$a->name} <br> {$a->url}';

// Stings for assignplaces.php and form.
$string['assignplaces'] = 'Sitzplätze zuweisen';
$string['assign_places_text'] = 'Hier können allen Prüfungsteilnehmer/innen automatisch oder manuell Sitzplätze zugewiesen werden.';
$string['revert_places_assignment'] = 'Sitzplatzzuweisung zurücksetzen';
$string['choose_assignment_mode'] = 'Zuweisungsmodus für Räume und Sitzplätze wählen';
$string['current_assignment_mode'] = 'Bisheriger Zuweisungsmodus:';
$string['assignment_mode_places'] = 'Modus Platzzuweisung';
$string['assignment_mode_rooms'] = 'Modus Raumzuweisung';
$string['mode_places_lastname'] = 'Nachname (alphabetisch)';
$string['mode_places_matrnr'] = 'Matrikelnummer (aufsteigend)';
$string['mode_places_random'] = 'Zufällig';
$string['mode_places_manual'] = 'Manuelle Zuweisung';
$string['mode_room_ascending'] = 'Aufsteigend (vom kleinsten zum größten Raum)';
$string['mode_room_descending'] = 'Absteigend (vom größten zum kleinsten Raum)';
$string['all_places_already_assigned'] = '<strong>Achtung:</strong> Es wurden bereits alle Sitzplätze zugewiesen. Diese werden bei einer Neuzuweisung komplett überschrieben.';
$string['keep_seat_assignment_left'] = 'Bestehende Zuweisung behalten';
$string['keep_seat_assignment_right'] = 'Bereits bestehende Zuweisungen bei der Neuzuweisung behalten. Wenn nicht angehakt werden diese entfernt.';
$string['participants_missing_places'] = 'Einigen Teilnehmer/innen konnte noch kein Sitzplatz zugewiesen werden. Fügen Sie ausreichend Räume zur Prüfung hinzu und wiederholen Sie die Zuweisung oder weisen Sie die noch fehlenden Sitzplätze manuell zu.';
$string['edited_manually'] = 'Manuell bearbeitet';

// Strings for importbonus.php and form.
$string['importbonus'] = 'Bonuspunkte importieren';
$string['import_bonus_text'] = 'Hier können von den Teilnehmenden errungene Bonuspunkte entweder direkt importiert oder in Bonusnotenschritte für die Prüfung umgerechnet werden.';
$string['revert_bonus'] = 'Bonus für alle zurücksetzen';
$string['choose_bonus_import_mode'] = 'Importmodus auswählen';
$string['bonus_import_mode'] = 'Importmodus';
$string['mode_bonussteps'] = 'Bonusnotenschritte';
$string['mode_bonuspoints'] = 'Bonuspunkte';
$string['import_bonuspoints_text'] = 'In diesem Importmodus werden die eingelesenen Bonuspunkte direkt auf die von den Teilnehmenden in der Prüfung errungenen Punkte addiert.';
$string['set_bonussteps'] = 'Bonusnotenschritte festlegen';
$string['add_remove_bonusstep'] = 'Bonusschritt hinzufügen oder entfernen:';
$string['bonusstep'] = 'Bonusnotenschritt (maximal 3)';
$string['required_points'] = 'Für Notenschritt erforderliche Punkte';
$string['configure_fileimport'] = 'Dateiimport konfigurieren';
$string['import_mode'] = 'Art des Dateiimports';
$string['moodle_export'] = 'Bewertungsexport aus {$a->systemname}';
$string['individual'] = 'Individuell';
$string['idfield'] = 'Spalte in der die Benutzeridentifikatoren stehen (z. B. A, B, C ... ; Beim Import von aus {$a->systemname} exportierten Bewertungen automatisch gesetzt)';
$string['pointsfield'] = 'Spalte welche die zu wertenden Bonuspunkte enthält (z. B. A, B, C ...)';
$string['import_bonus_from_file'] = 'Bonuspunkte aus Excel-Datei importieren; Benutzeridentifikator (in {$a->systemname} hinterlegte E-Mailadresse beim Bewertungsimport oder Matrikelnummer beim individuellen Import und Bonuspunkte müssen in den oben ausgewählten Spalten stehen)';
$string['bonus_already_entered'] = '<strong>Achtung:</strong> Es wurden bereits Bonuspunkte oder Bonusnotenschritte für {$a->bonuscount} Teilnehmende importiert. Diese werden, falls bei dem neuen Import für die betroffenen Teilnehmenden erneut Bonuspunkte eingelesen werden, durch die neuen Werte überschrieben.';
$string['points_bonussteps_invalid'] = 'Punkte für Bonusschritte ungültig';

// Strings for configuregradingscale.php and form.
$string['configuregradingscale'] = 'Notenschlüssel konfigurieren';
$string['configure_gradingscale_text'] = 'Hier kann ein Notenschlüssel für die Prüfung konfiguriert werden.';
$string['configure_gradingscale_totalpoints'] = 'Die maximal erreichbare Anzahl an Punkten ist:';

// Strings for inputresults.php and form.
$string['inputresults'] = 'Prüfungsergebnisse eintragen';
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
$string['input_other_matrnr'] = 'Ändern';
$string['noparticipant'] = 'Kein/e gültige/r Teilnehmer/in';
$string['invalid_matrnr'] = 'Ungültige Matrikelnummer';
$string['invalid_matrnr_format'] = 'Ungültiges Matrikelnummerformat';
$string['invalid_barcode'] = 'Ungültiger Barcode';

// Strings for participantsoverview.php and form.
$string['participants_overview_text'] = 'Alle bereits zur Prüfung hinzugefügten Teilnehmer/innen können in dieser Liste angesehen und bearbeitet werden.';
$string['edit'] = 'Bearbeiten';
$string['participantsoverview'] = 'Teilnehmer- und Ergebnisübersicht';
$string['matriculation_number_short'] = 'Matr. Nr.';
$string['bonuspoints'] = 'Bonuspunkte';
$string['totalpoints_with_bonuspoints'] = 'Gesamtpunkte inkl. Bonuspunkte';
$string['totalpoints'] = 'Gesamtpunkte';
$string['result'] = 'Ergebnis';
$string['bonussteps'] = 'Bonusschritte';
$string['resultwithbonus'] = 'Ergebnis inkl. Bonusnotenschritte';
$string['edit_user'] = 'Benutzer bearbeiten';
$string['save_changes'] = 'Änderungen speichern';
$string['cancel'] = 'Zurück zur Prüfungsorganisation';
$string['normal'] = 'Normal';
$string['nt'] = 'NT';
$string['fa'] = 'Betrugsversuch';
$string['ill'] = 'Krank';
$string['available'] = 'Verfügbar';
$string['edit_results_and_boni'] = 'Prüfungsergebnisse und Boni bearbeiten';
$string['nr'] = 'Nr.';
$string['max'] = 'Max:';

// Strings for the participants list.
$string['participantslist'] = 'Teilnehmerliste';
$string['participantslist_names'] = 'Teilnehmerliste_Namen';
$string['participantslist_places'] = 'Teilnehmerliste_Plätze';
$string['internal_use'] = 'Nur fuer den internen Gebrauch durch die Lehrenden!';
$string['lastname'] = 'Name';
$string['firstname'] = 'Vorname';
$string['matrno'] = 'Matr.-Nr.';
$string['place'] = 'Platz';

// Strings for the seating plan.
$string['seatingplan'] = 'Sitzplan';
$string['total_seats'] = 'Plätze';
$string['lecture_room'] = 'Hörsaal';
$string['places_differ'] = 'Dieser Plan kann von der tatsächlichen Platznummerierung abweichen.';
$string['places_alternative'] = 'In diesem Fall nutzen Sie bitte die Nummerierung dieses Plans.';
$string['numbered_seats_usable_seats'] = 'nummerierte Sitze = benutzbare Sitze';

// Strings for the exam labels.
$string['examlabels'] = 'Prüfungsetiketten';
$string['required_label_type'] = 'Benoetigter Etikettentyp:';

// Strings for the exported results.
$string['pointslist_examreview'] = 'Punkteliste Klausureinsicht';

// Strings for exportresultspercentages.php.
$string['percentages'] = 'Prozent';
$string['pointslist_percentages'] = 'Punkteliste Prozente';

// Strings for exportresultsstatistics.php.
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
$string['withbonus'] = 'Mit Bonusnotenschritte';
$string['inpercent'] = 'in %';
$string['registered'] = 'Angemeldet';
$string['passed'] = 'Bestanden';
$string['notpassed'] = 'Nicht bestanden';
$string['notrated'] = 'Nicht bewertet';
$string['tasks_and_boni'] = 'Aufgaben und Bonuspunkte';
$string['mean'] = 'Mittelwert';
$string['count'] = 'Anzahl';
$string['details'] = 'Details';
$string['points_with_bonus'] = 'Punkte inkl. Bonus';

// Strings for exportresultstextfile.php.
$string['results'] = 'Prüfungsergebnisse';
$string['cannot_create_zip_archive'] = 'Fehler beim Erzeugen des zip-Archives';

// Strings for examreview.php and form.
$string['examreview'] = 'Termin und Raum für Klausureinsicht festlegen';
$string['examreviewstr'] = 'Falls für die Prüfung eine Klausureinsicht stattfinden soll können hier der Termin und der Raum dafür ausgewählt werden.';
$string['examreviewdate'] = 'Termin';
$string['examreviewroom'] = 'Raum (als Freitext eintragen)';

// Common strings for notifications.
$string['operation_canceled'] = 'Vorgang abgebrochen';
$string['operation_successfull'] = 'Vorgang erfolgreich';
$string['alteration_failed'] = 'Änderung fehlgeschlagen';
$string['no_rooms_added'] = 'Noch keine Prüfungsräume ausgewählt. Arbeitsschritt nicht möglich';
$string['no_participants_added'] = 'Noch keine Prüfungsteilnehmer/innen hinzugefügt. Arbeitsschritt nicht möglich';
$string['no_places_assigned'] = 'Noch keine Sitzplätze zugewiesen. Arbeitsschritt nicht möglich';
$string['no_tasks_configured'] = 'Noch keine Aufgaben konfiguriert. Arbeitsschritt nicht möglich';
$string['no_results_entered'] = 'Noch keine Prüfungsergebnisse eingetragen. Arbeitsschritt nicht möglich';
$string['correction_not_completed'] = 'Korrektur noch nicht abgeschlossen. Arbeitsschritt nicht möglich';
$string['itemscount'] = 'Objekte pro Seite';
$string['chooseitemsperpage'] = 'Anzahl an Objekten pro Seite wählen';

// Strings for the course reset on lib.php.
$string['deleteallexamdata'] = 'Alle Daten der Prüfung (z. B. Prüfungsräume und -Datum, Aufgabenpunkte, Notenschlüßel) und alle Prüfungsteilnehmenden löschen';
$string['allexamdatadeleted'] = 'Prüfungsdaten gelöscht';
$string['deleteexamparticipantsdata'] = 'Alle Prüfungsteilnehmenden löschen aber Prüfungsdaten behalten';
$string['examparticipantsdatadeleted'] = 'Prüfungsteilnehmende gelöscht';

// Strings for the calendar.
$string['examtime_calendarevent'] = 'Prüfung "{$a}"';
$string['examreviewtime_calendarevent'] = 'Klausureinsicht für die Prüfung "{$a}"';

// Strings for the helptexts.
$string['overview_help'] = 'Dies ist die <strong>Startseite der Prüfungsorganisation</strong>. Lehrende und/oder deren Mitarbeiterinnen und Mitarbeiter können hier alle für das Durchführen einer Prüfung sinnvollen Arbeitsschritte ausführen. <br /><br />
Diese sind übersichtlich in verschiedene Phasen unterteilt, welche entlang eines Zeitstrangs angeordnet sind. Für jeden einzelnen Arbeitsschritt ist der Bearbeitungsstatus durch entsprechende Symbole, Texte und Farben zu erkennen. Es gibt verpflichtende Arbeitsschritte und Optionale, die zwar hilfreich sind, aber auch weggelassen werden können. Sobald alle verpflichtenden Schritte einer Phase erledigt sind klappt diese automatisch zu und es öffnet sich die Nächste. Phasen können jedoch auch jederzeit manuell geöffnet und zugeklappt werden. <br /><br />
Jeder Arbeitsschritt kann nach einem Klick auf den entsprechenden Button durchgeführt werden. Dieser erscheint, sobald alle für einen Arbeitsschritte nötigen anderen Schritte erfüllt sind. <br /><br />
Durch den Button "Passwort konfigurieren“ können Sie zudem ein Passwort festlegen (oder ändern), welches ab diesem Zeitpunkt für einen Zugriff auf die Prüfungsorganisation eingegeben werden muss. Auf diese Weise können Sie zum Beispiel Ihren studentischen MitarbeiterInnen, die Ihre Kurse betreuen, den Zugriff auf die sensiblen Inhalte der Prüfungsorganisation entziehen. <br /><br />
<strong>Hinweis:</strong> Studierende haben keinen Zugriff auf diese Ansicht. Sie sehen stattdessen in einer eigenen Ansicht die für sie freigeschalteten Informationen zur Prüfung.';
$string['overview_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['checkpassword_help'] = 'Der oder die Dozentin hat für diese Prüfungsorganisation ein Passwort festgelegt. Geben Sie es ein, um die Inhalte der Prüfungsorganisation ansehen zu können. <br><br> Durch einen Klick auf den entsprechenden Button können Sie beim Support ein Zurücksetzen des Passwortes beantragen. Wurde das Passwort zurückgesetzt werden Sie und alle anderen Lehrenden des '. get_config('mod_exammanagement', 'moodlesystemname').'-Kurses darüber via '. get_config('mod_exammanagement', 'moodlesystemname').'-Benachrichtigung informiert.';
$string['checkpassword_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['checkpasswordadmin_help'] = 'Der oder die Dozentin hat für diese Prüfungsorganisation ein Passwort festgelegt. Geben Sie es ein, um die Inhalte der Prüfungsorganisation ansehen zu können. <br> <br>
Als Administrator können Sie hier auf Wunsch der oder des Lehrenden das Passwort der Prüfungsorganisation zurücksetzen. Alle Lehrenden des '. get_config('mod_exammanagement', 'moodlesystemname').'-Kurses werden darüber per '. get_config('mod_exammanagement', 'moodlesystemname').'-Nachricht benachrichtigt.';
$string['checkpasswordadmin_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['configurepassword_help'] = 'Auf dieser Seite kann ein <strong>Passwort</strong> für die Prüfungsorganisation gesetzt oder geändert werden. Dieses muss ab dann von jeder oder jedem Lehrenden des '. get_config('mod_exammanagement', 'moodlesystemname').'-Kurses eingegeben werden, um auf die Inhalte der Prüfungsorganisation zugreifen zu können. <br><br>
Um ein Passwort zu setzen muss dieses zunächst in das erste Feld eingegeben und dann im zweiten Feld bestätigt werden.<br><br>
Denken Sie daran, ihr Passwort hinreichend sicher zu wählen und nehmen Sie vor allem kein Kennwort, dass Sie bereits anderswo als Passwort verwenden (vor allem nicht im Universitätskontext!).<br><br>
Durch einen Klick auf den Button "Passwort zurücksetzen" können Sie den Passwortschutz für die Prüfungsorganisation wieder aufheben.';
$string['configurepassword_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['chooserooms_help'] = 'Auf dieser Seite kann die Liste aller im System verfügbaren möglichen <strong>Prüfungsräume</strong> angesehen und einer oder mehrere davon als Raum für die aktuelle Prüfung ausgewählt werden. <br /> <br />
Zudem können nach einem Klick auf den Button "Eigenen Prüfungsraum anlegen" auch eigene Prüfungsräume zur Liste hinzugefügt (und anschließend als Prüfungsraum ausgewählt) werden. <br /> <br />
Um einen Raum als Prüfungsraum auszuwählen muss zunächst das Kästchen links neben dessen Namen angeklickt werden. Ein Klick auf den Button „Räume auswählen“ speichert die gewählten Räume als Prüfungsräume. Ist ein Raum nach dem Öffnen der Seite bereits markiert wurde er schon als Raum für die Prüfung gespeichert.<br /> <br />
Die gewählten Prüfungsräume werden später verwendet, um den zur Prüfung hinzugefügten Teilnehmer/innen Sitzplätze zuzuweisen. Ihre Sitzplätze werden den Prüfungsteilnehmer/innen später (sobald Sie diese Information auf der Übersichtsseite für die Studierenden sichtbar geschaltet haben) in deren Ansicht angezeigt. Außerdem wird die Sitzplatzzuweisung in Dokumenten wie der Teilnehmerliste oder dem Sitzplan benötigt. <br /> <br />
Eine Beschreibung jedes Raumes und die Zahl der in ihm vorhandenen Sitzplätze befinden sich in der Tabelle. Ist für einen Raum ein Sitzplan im System hinterlegt kann dieser durch das Drücken der linken Maustaste über dem Info-Symbol in der Spalte "Sitzplan“ angesehen werden. Ist ein Raum ein selbst erstellter Prüfungsraum kann er durch einen Klick auf das Stift-Symbol am rechten Ende der Zeile bearbeitet werden, während ein Klick des Mülleimersymbols daneben und eine anschließende Bestätigung ihn löscht (wozu er allerdings nicht als Prüfungsraum ausgewählt sein darf). <br /> <br />
<strong>Wichtige Hinweise:</strong>
<ul><li>Um die weiteren Funktionen der '. get_config('mod_exammanagement', 'moodlesystemname').' Prüfungsorganisation nutzen zu können muss hier mindestens ein Raum als Prüfungsraum ausgewählt werden. Zudem müssen die gewählten Räume mindestens so viele Sitzplätze bieten, wie Teilnehmer/innen an der Prüfung teilnehmen sollen.</li>
<li>Wird ein Prüfungsraum abgewählt, nachdem Teilnehmer/innen Sitzplätze in diesem zugewiesen wurden, wird die gesamte Sitzplatzzuweisung gelöscht und muss wiederholt werden. Davon betroffene Räume sind mit einem Warnhinweis versehen.</li>
<li>Einige Räume sind hier mehrfach aufgeführt. Dabei handelt es sich um unterschiedliche Modellierungen desselben Raumes. "1 Platz frei" bedeutet, dass jeder 2. Platz besetzt wird. "2 Plätze frei" bedeutet, dass jeder 3. Platz besetzt wird.</li></ul>
<strong>Achtung:</strong> Das System berücksichtigt nicht die Verfügbarkeit der gewählten Räume. Als DozentIn müssen Sie die Räume, in welchen die Prüfung stattfinden soll, bei der zentralen Raumverwaltung buchen und so abklären, dass die entsprechenden Räume auch tatsächlich zum Zeitpunkt der Prüfung verfügbar sind.';
$string['chooserooms_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['addcustomroom_help'] = 'Auf dieser Seite können Sie als Dozentin oder Dozent einen <strong>eigenen Prüfungsraum</strong> erstellen, falls der Raum, in welchem Sie Ihre Prüfung halten wollen, nicht als Prüfungsraum im System aufgeführt ist. Alternativ können Sie hier auch einen bereits vorhandenen selbst erstellten Prüfungsraum bearbeiten. <br><br>
Um einen neuen Raum zu erstellen muss zunächst dessen Name angegeben werden. Als Nächstes müssen Sie die Zahl der Sitzplätze angeben, die der Raum haben soll. Beachten Sie dabei, dass Sie selbst nachsehen müssen, wie viele Plätze tatsächlich im Raum vorhanden sind und dass die Nummerierung der Sitzplätze des hier erstellten Raums im System unabhängig von der tatsächlich im Raum vorhandenen Nummerierung immer bei 1 beginnt. Das bedeutet, dass Sie möglicherweise auftretende Unstimmigkeiten mit der tatsächlichen Sitzplatznummerierung manuell anpassen müssen. Abschließend kann noch eine optionale Beschreibung des Raumes angegeben werden. Diese sollte alle für Sie wichtigen Informationen über den Raum enthalten, damit Sie den Raum später zum Beispiel im nächsten Semester bei Bedarf einfach erneut benutzen können. Ein Klick auf den Button "Raum speichern" legt schließlich den neuen Prüfungsraum an.<br><br>
Ein auf diese Weise angelegter Raum kann anschließend aus der Liste der verfügbaren Prüfungsräume als Raum ausgewählt und danach wie jeder andere Prüfungsraum regulär genutzt werden.<br><br>
Falls auf der Seite der Raumauswahl hingegen ein bestehender eigener Prüfungsraum zur Bearbeitung ausgewählt wurde kann dieser nun verändert werden. In diesem Fall können hier die Sitzplatzanzahl und die Beschreibung des gewählten Raums geändert und diese Änderung anschließend durch einen Klick auf "Raum speichern" gesichert werden. Wenn dabei die Sitzplatzanzahl verringert wird behalten alle an der Prüfung Teilnehmenden trotzdem zunächst ihre bisher zugewiesenen Sitzplätze, bis Sie die automatische Sitzplatzzuweisung erneut durchführen.';
$string['addcustomroom_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['importdefaultrooms_help'] = 'Hier können Sie als '. get_config('mod_exammanagement', 'moodlesystemname').'-Administrator eine Reihe an <strong>Standardräumen</strong> importieren, die nach dem Einlesen allen Dozenten bei der Auswahl der Prüfungsräume als mögliche Räume zur Verfügung stehen. <br><br>
Um die Standardräume zu importieren muss zunächst eine korrekt aufgebaute Textdatei im unteren Bereich ausgewählt und dann durch einen Klick auf den entsprechenden Button eingelesen werden. <br><br>
Die einzulesende Textdatei muss dabei die folgenden (durch das Zeichen * getrennte) Informationen zu jedem Prüfungsraum enthalten, wobei jede Zeile für einen Prüfungsraum steht:
<ul><li>Erste Spalte: Die systeminterne Raumid nach dem Muster <i>Raumname_Variante</i>, also zum Beispiel <i>Audimax_2</i></li>
<li>Zweite Spalte: Der benutzersichtbare Raumname, also zum Beispiel <i>Audimax</i></li>
<li>Dritte Spalte: Die benutzersichtbare Raumbeschreibung inklusive der Anzahl der freigelassenen sowie der gesamten Plätze, also zum Beispiel <i>2 Plätze frei, 56 Plätze insgesamt</i></li>
<li>Vierte Spalte: Ein zur Berechnung der Sitzplatzzahl des Raumes benötigtes Array, welches die Bezeichnung jedes einzelnen im Raum vorhandenen Sitzplatzes enthält. Das Array muss dabei in json-Syntax verfasst sein, also zum Beispiel folgendermaßen aussehen: <i>["R/R01/P07","R/R01/P04","R/R01/P01", ...] </i></li>
<li>Fünfte Spalte: Wenn ein Sitzplan für den Raum als .svg-Datei vorhanden ist und dieser den Benutzern angezeigt werden soll muss in dieser Spalte der Quellcode der SVG stehen, ansonsten kann diese Spalte leer gelassen werden.</li></ul>
<strong>Beispiel: </strong>AudiMax_1 * AudiMax * 1 Platz frei, 3 Plätze * ["R/R01/P01","R/R02/P01","R/R02/P03"] * < svg>...< /svg><br><br>
Alle bereits existierenden Standardräume werden durch einen Neuimport überschrieben. Die Informationen zu allen dabei gegebenenfalls gelöschten oder geänderten Räumen bleiben anschließend in allen Prüfungsorganisationen, in denen sie aktuell genutzt werden, zunächst erhalten. Gelöschte Räume können jedoch von den Dozenten nicht mehr als neue Prüfungsräume ausgewählt oder aber für die (Neu-)Zuweisung von Sitzplätzen genutzt werden. Änderungen bei Namen oder Sitzplätzen von Standardräumen werden in den einzelnen Prüfungsorganisationen ebenfalls erst nach einer erneuten Sitzplatzzuweisung wirksam.';
$string['importdefaultrooms_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['editdefaultroom_help'] = 'Hier können Administratorinnen und Administratoren einen bestehenden <strong>Standardraum bearbeiten</strong> oder einen Neuen anlegen.<br><br>
Dazu werden zuerst die Basisinformationen des Raumes angezeigt, die zugleich eingetragen beziehungsweise bearbeitet werden können. Dies ist zunächst die systeminterne ID des Raumes, die pluginintern für die Identifikation des Raumes verwendet wird und dem folgenden Schema entsprechend aufgebaut sein sollte: Der Raumname gefolgt von einem Unterstrich gefolgt von der Variante des Raumes, die besonders bei mehreren Modellierungen desselben Raumes mit einer unterschiedlichen Anzahl an freien Plätzen zwischen den belegbaren Sitzplätzen relevant ist. Für den Teil des Raumnamens sind dabei alle Buchstaben, Zahlen und auch der Punkt erlaubt, die Raumvariante hinter dem Unterstrich darf nur aus Zahlen bestehen. Wird ein vorhandener Raum bearbeitet kann die Raum ID nicht verändert werden. Es folgt der Name des Raumes, der für alle Dozierenden sichtbar ist und aus Buchstaben, Zahlen, Punkten und Leerzeichen bestehen darf. Die Raumbeschreibung ist ebenfalls für die Benutzer sichtbar, sollte zum Beispiel Informationen über die gewählte Modellierung (ein oder zwei Plätze frei zwischen zwei besetzbaren Sitzplätzen) enthalten und darf dieselben Zeichen enthalten wie der Raumname. Unter diesen Informationen werden, falls ein bereits existierender Raum zur Bearbeitung ausgewählt wurde, noch weitere Informationen zum Raum angezeigt, etwa die bisherige Anzahl an besetzbaren Sitzplätzen und eine Übersicht über deren Benennung sowie (falls dieser vorhanden ist) der für den Raum hinterlegte Sitzplan. <br><br>
Falls bei einem bestehenden Raum Sitzplätze bearbeitet werden sollen ist dies möglich, sobald im nächsten Abschnitt bei "Sitzplätze bearbeiten" die Option "Ja" ausgewählt wurde. Bei der Erstellung eines neuen Raumes ist dies nicht nötig, in diesem Fall kann direkt im Abschnitt "Neue Sitzplätze" mit dem Eintragen derselben fortgefahren werden. Für die Befüllung des Raumes mit Sitzplätzen gibt es drei verschiedene Modi, welche die einfache Nachbildung aller wichtigen Modellierungsarten von Prüfungsräumen ermöglichen sollen: Im Modus "Standard" werden für einen Raum automatisch so viele zuweisbare Sitzplätze angelegt, bis die angegebene Gesamtplatzzahl des Raumes erreicht ist, wobei die angegebene Anzahl freier Plätze zwischen zwei zuweisbaren Sitzplätzen berücksichtigt wird. Die Benennung der Plätze beginnt dabei bei 1 und zählt dann aufwärts. Soll also ein Raum mit 100 Gesamtplätzen befüllt werden, zwischen denen jeweils ein Platz unbenutzt bleiben soll, würde dieser insgesamt 50 in der Prüfungsorganisation belegbare Plätze mit den Benennungen 1, 3, 5, ..., 100 bekommen. Bei zwei Plätzen frei wären es 34 Plätze mit den Benennungen 1, 4, 7, ..., 100. Der Sitzplatz-Modus "Reihenweise" funktioniert ähnlich, nur müssen hier die Anzahl der in einem Raum vorhandenen Reihen sowie die pro Reihe vorhandenen Plätze angegeben werden. Jede Reihe wird dann mit entsprechend vielen Plätzen befüllt, wobei wieder die angegebene Anzahl freier Plätze und die ebenfalls anzugebene Zahl an freizulassenden Reihen berücksichtigt wird. Die Plätze werden dabei mit einer Kombination aus Reihe und Platznummer benannt, also etwa R01/P01, R01/P03, R01/P05, R02/P01 ... . Für alle Raummodellierungen, die mithilfe dieser beiden Modi nicht nachgebildet werden können gibt es den dritten Modus mit dem Namen "Vollständig individuell". In diesem können die Bezeichnungen aller Plätze komplett frei eingetragen werden, wobei zwischen zwei Platzbezeichnungen stets ein Komma stehen muss. In den Platzbezeichnungen sind alle Buchstaben, Zahlen, Punkte, Minuszeichen, Slashs sowie Leerzeichen erlaubt. Dieser Modus eignet sich sehr gut dazu, komplexere Sitzplatzmodellierungen vorzunehmen oder aber mit den ersten beiden Modi erstellte Modellierungen etwas anzupassen. Dies ist zum Beispiel hilfreich, wenn die erste oder die letzte Reihe eines Raumes aufgrund baulicher Begebenheiten weniger Plätze hat als die anderen oder wenn bei durchgehender Platznummerierung Sitzplätze trotzdem in Reihen angeordnet sind und dabei etwa jede zweite Reihe freigelassen werden soll. Bei der Bearbeitung eines bereits existierenden Raumes ist dieser Modus deswegen bereits vorausgewählt, kann aber natürlich jederzeit durch einen anderen Modus ersetzt werden.<br><br>
Als Letztes kann für einen Raum ein neuer Raumplan hinzufügt werden. Dieser muss außerhalb der Prüfungsorganisation erstellt werden und sollte sämtliche für den Standardraum angelegte Sitzplätze enthalten. Der Raumplan muss dazu als SVG in einer Textdatei (.txt) gespeichert sein, die dann im letzten Abschnitt dieser Seite hochgeladen werden kann. Dabei ist zu beachten, dass der Inhalt der Datei mit der SVG des Raumplans vor dem Upload sorgfältig geprüft werden muss, da das Plugin an dieser Stelle böswillige oder fehlerhafte Inhalte in der Datei nicht zuverlässig erkennen kann. Wurde eine Datei mit einem Raumplan ausgewählt wird dieser nach einem Klick auf "Raum speichern" zusammen mit den restlichen angegebenen Informationen gespeichert. <br><br>
Der auf diese Weise angelegte oder geänderte Raum kann sofort von allen Lehrenden in ihren Prüfungsorganisationen als Prüfungsraum ausgewählt werden. Bei der Änderung des Namens oder der Anpassung von Sitzplätzen in einem bestehenden und bereits in Prüfungsorganisationen verwendeten Prüfungsraum bleiben der Name und die bisherigen Sitzplatzzuweisungen dort zunächst gespeichert. Die Lehrenden müssen somit die Sitzplätze einmal erneut zuweisen, bevor die Änderungen am Raum dort wirksam werden.';
$string['editdefaultroom_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['setexamdate_help'] = 'Hier können das <strong>Datum und die Uhrzeit der Prüfung</strong> ausgewählt werden. <br /> <br />
Der hier gewählte Prüfungstermin wird auf der Übersichtsseite der Prüfungsorganisation angezeigt und später in den erzeugten Dokumenten wie etwa der Teilnehmerliste oder den Klausuretiketten verwendet. Zudem wird er den Prüfungsteilnehmer/innen in deren Ansicht angezeigt, sobald Sie diese Informationen auf der Übersichtsseite für die Studierenden sichtbar geschaltet haben. <br /> <br />
Das Datum und die Uhrzeit der Prüfung sollten hier also gesetzt werden, um die Prüfungsorganisation in '. get_config('mod_exammanagement', 'moodlesystemname').' sinnvoll nutzen zu können.';
$string['setexamdate_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['viewparticipants_help'] = 'Auf dieser Seite können alle zur Prüfung hinzugefügten <strong>Prüfungsteilnehmer/innen</strong> und Informationen wie deren Profil, Matrikelnummer sowie die ihnen gegebenenfalls in '. get_config('mod_exammanagement', 'moodlesystemname').' zugewiesenen Gruppen angesehen werden. <br /> <br />
Es können hier zudem neue Teilnehmer/innen zur Prüfung hinzugefügt werden. Dazu gibt es zwei Möglichkeiten: <br /> <br />
1. Es können nach einem Klick auf den Button "Teilnehmer/innen aus Datei hinzufügen" Teilnehmer/innen aus einer oder mehreren Prüfungslisten importiert werden. Dies ist der empfohlene Weg des Importes, da nur auf diese Weise später ein Export der Prüfungsergebnisse der Anzahl und dem Aufbau dieser eingelesenen Listen entsprechend möglich ist. Diese Variante sollte also gewählt werden, möchte man später die Prüfungsergebnisse direkt importieren (lassen).<br>
2. Es besteht außerdem die Möglichkeit, nach einem Klick auf den Button "Kursteilnehmer/innen importieren" Teilnehmer/innen des '. get_config('mod_exammanagement', 'moodlesystemname').'-Kurses als Prüfungsteilnehmer/innen zu importieren. Wird diese Variante gewählt können die Prüfungsergebnisse später allerdings nur in einer einzigen Ergebnisliste exportiert werden, ein listenweiser Export und deren einfacher anschließender Reimport ist dann gegebenenfalls nicht möglich. Es besteht zudem auch nicht die Möglichkeit, einmal als Kursteilnehmer/innen importierte Teilnehmer/innen später durch nachträgliches Einlesen einer Liste "umzuschreiben". Dafür muss der oder die Teilnehmer/in zunächst komplett gelöscht werden.<br><br>
Das Hinzufügen von Teilnehmer/innen ist einer der wichtigsten Arbeitsschritte in der Prüfungsorganisation. Nur wenn Sie hier mindestens einen hinzugefügten Teilnehmemenden sehen können Sie später Sitzplätze zuweisen, Prüfungspunkte eintragen oder Ergebnisdokumente exportieren. Nicht als PrüfungsTeilnehmer/innen hinzugefügte Studierende haben (selbst wenn sie bereits im '. get_config('mod_exammanagement', 'moodlesystemname').' Kurs eingeschrieben sind) außerdem keinen Zugriff auf die Teilnehmeransicht mit den Prüfungsinformationen und erhalten auch keine Benachrichtigungen über die Nachrichtenfunktion auf der Übersichtsseite der Prüfungsorganisation. <br /> <br />
Falls Sie in der Tabelle Teilnehmer/innen mit einem entsprechenden Status sehen dann haben Sie Prüfungsteilnehmer/innen importiert, die keinen Benutzeraccount in '. get_config('mod_exammanagement', 'moodlesystemname').' haben. Diese können zwar auch aus einer Datei importiert werden, einige Arbeitsschritte wie etwa das Schreiben einer Benachrichtigung müssen für diese Teilnehmer/innen jedoch manuell durchgeführt werden und andere (etwa das Ansehen der Studentenansicht für die Teilnehmer/innen selbst) sind gänzlich unmöglich.<br><br>
Es besteht auf dieser Seite außerdem die Möglichkeit, einzelne oder alle bereits importierten Prüfungsteilnehmer/innen wieder zu löschen. Um einzelne Teilnehmer/innen zu löschen genügt ein Klick auf den Mülleimer in der Zeile des jeweiligen Teilnehmenden, um alle Teilnehmer/innen zu löschen muss hingegen der rote Button unter der Tabelle gedrückt werden. Beachten Sie jedoch, dass durch das Löschen eines oder aller Teilnehmer/innen automatisch alle für diese hinterlegten Informationen wie etwa Sitzplätze oder eingetragene Prüfungspunkte gelöscht werden und dass diese Informationen danach nicht wieder hergestellt werden können.<br><br>
Durch den Button "Zu Gruppe konvertieren" können schließlich einzelne oder alle hier aufgeführten Teilnehmer/innen in eine '. get_config('mod_exammanagement', 'moodlesystemname').' Gruppe umgewandelt werden.';
$string['viewparticipants_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['converttogroup_help'] = 'Hier können ausgewählte oder alle importierten Prüfungsteilnehmer/innen in eine '.get_config('mod_exammanagement', 'moodlesystemname').' Gruppe umgewandelt werden. <br /> <br />
Dazu kann entweder für die Erstellung einer neuen '.get_config('mod_exammanagement', 'moodlesystemname').' Gruppe deren Name und optional auch Beschreibung in die entsprechenden Formularfelder eingetragen oder aber eine bereits bestehende Gruppe ausgewählt werden. Dann können im unteren Abschnitt alle gewünschten Teilnehmer/innen ausgewählt und danach durch einen Klick auf den Button "Zu Gruppe konvertieren" in eine Gruppe umgewandelt werden.<br /> <br />
Prüfungsteilnehmer/innen, die nicht zum Kurs gehören oder kein '.get_config('mod_exammanagement', 'moodlesystemname').' Benutzerkonto haben werden (falls vorhanden) in einem separaten Abschnitt angezeigt und können nicht zu einer Gruppe hinzugefügt werden. <br /> <br />
Die hier erstellten Gruppen können im gesamten Kurs und allen dortigen Aktivitäten genau wie normale Gruppen verwendet werden. Nach dem Anklicken einer Gruppe etwa in der Teilnehmerübersicht kann diese bearbeitet oder verändert werden.';
$string['converttogroup_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['addparticipants_help'] = 'Auf dieser Seite können <strong>Teilnehmer/innen</strong> aus Prüfungslisten zur Prüfung hinzugefügt werden. Auf diese Weise können deren Ergebnisse später wieder listenweise exportiert und dann einfach reimportiert werden. <br /> <br />
Dazu benötigen Sie zunächst eine oder mehrere Listen mit den Matrikelnummern der Prüfungsteilnehmer/innen. Diese Dateien können in den Auswahlbereich gezogen und dann durch einen Klick auf den entsprechenden Button eingelesen werden. <br><br>
Auf der nun folgenden Seite sehen Sie alle aus der oder den Dateien eingelesenen Matrikelnummern. Dabei wird in verschiedenen Bereichen genau aufgeschlüsselt, welchen Status eine Matrikelnummer hat und ob der dazugehörige Studierende zur Prüfung hinzugefügt werden kann. <br><br>
Im Folgenden werden die verschiedenen Stati kurz erklärt:<br>
<ul><li><strong>Ungültige Matrikelnummer</strong>: Die eingegebene Matrikelnummer ist ungültig, weil sie zum Beispiel nicht erlaubte Zeichen wie etwa Buchstaben enthält. Sie kann deshalb auch nicht als Teilnehmer/in eingelesen werden. Die ganz links in der Zeile stehende Zahl gibt die Nummer der Zeile und der Datei an, in der die defekte Matrikelnummer gefunden werden kann. </li>
<li><strong>Doppelte Matrikelnummer</strong>: Die Matrikelnummer kommt in der oder den Dateien mehrfach vor. Als Prüfungsteilnehmer/in kann Sie jedoch im entsprechenden Abschnitt nur einmal eingelesen werden.</li>
<li><strong>Neu eingelesen (kein/e Kursteilnehmer/in)</strong>: Der zu dieser Matrikelnummer gehörende Studierende ist nicht Teil des '. get_config('mod_exammanagement', 'moodlesystemname').'-Kurses. Er oder sie kann problemlos als Prüfungsteilnehmer/in importiert werden. Da er jedoch nicht die Teilnehmeransicht des Plugins ansehen kann muss er, um auszuschliessen dass hier ein Fehler vorliegt, durch Setzen des Hakens manuell ausgewählt werden.</li>
<li><strong>Neu eingelesen (ohne '. get_config('mod_exammanagement', 'moodlesystemname').' Benutzerkonto)</strong>: Der zu dieser Matrikelnummer gehörende Studierende hat noch keinen Account in '. get_config('mod_exammanagement', 'moodlesystemname').'. Dies kann etwa geschehen, wenn er sich noch nie in '. get_config('mod_exammanagement', 'moodlesystemname').' angemeldet hat. Der oder die Studierende kann zwar als Prüfungsteilnehmer/innen importiert werden, jedoch kann er dann nicht die Teilnehmeransicht der Prüfungsorganisation betrachten und Sie können ihn auch nicht über die Benachrichtigungssfunktion der Prüfungsorganisation erreichen. Deshalb müssen Sie diesen Studierenden hier manuell anhaken.</li>
<li><strong>Werden gelöscht</strong>: Diese/r Teilnehmer/in wurde bereits als Prüfungsteilnehmer/in importiert, ist in den aktuell eingelesenen Dateien jedoch nicht mehr enthalten (weil er sich zum Beispiel in der Zwischenzeit von der Prüfung abgemeldet hat). Durch Auswählen können Sie nun bestimmen, dass diese/r Teilnehmer/in von der aktuellen Prüfung entfernt werden soll.</li>
<li><strong>Bereits Prüfungsteilnehmer/in</strong>: Dieser Teilnehmende wurde bereits als Prüfungsteilnehmer/in importiert und wird durch den aktuellen Import nicht verändert.</li>
<li><strong>Neu eingelesen</strong>: Dies ist ein/e gültige/r Teilnehmer/in, der oder die ohne Probleme zur Prüfung hinzugefügt werden kann. Alle Teilnehmer/innen in diesem Abschnitt sind für das Hinzufügen zur Prüfung vorausgewählt.</li>
</ul>
Alle Teilnehmer/innen, die zur Prüfung hinzugefügt (oder von dieser wieder entfernt) werden sollen können nun ausgewählt werden, indem entweder der Haken in die Box neben dem Namen oder aber im Feld "Alle aus-/abwählen" des jeweiligen Bereiches gesetzt wird. Ein anschließendes Drücken des Buttons "Zur Prüfung hinzufügen" fügt die ausgewählten Teilnehmer/innen dann zur Prüfung hinzu.<br><br>
Falls Sie eine falsche Datei eingelesen haben können Sie mit einem Klick auf den Button "Andere Teilnehmer/innen hinzufügen" das Einlesen wiederholen. Die aktuell eingelesenen Teilnehmer/innen werden dabei nicht importiert sondern wieder verworfen.';
$string['addparticipants_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['addcourseparticipants_help'] = 'Hier können alle im '. get_config('mod_exammanagement', 'moodlesystemname').' Kurs eingeschriebenen <strong>Kursteilnehmer/innen</strong> als Prüfungsteilnehmer/innen importiert werden. <br><br>
Dazu müssen im unteren Abschnitt all jene Teilnehmer/innen ausgewählt werden, die zur Prüfung hinzugefügt werden sollen. Einzelne Teilnehmer/innen können dabei durch einen Klick in das Kästchen neben ihrem Namen ausgewählt werden, zum Aus- (oder ab)wählen aller Kursteilnehmer/innen genügt hingegen ein Klick in das entsprechende Kästchen "Alle aus-/abwählen". Es können zudem im entsprechenden Abschnitt gegebenenfalls bereits vorhandene Prüfungsteilnehmer/innen die keine Kursteilnehmer/innen sind ausgewählt werden. Diese werden dann bei einem Klick auf den ganz unten befindlichen Button "Zur Prüfung hinzufügen" von der Prüfung entfernt, während die ausgewählten Kursteilnehmer/innen zur Prüfung hinzugefügt werden. Für alle Teilnehmer/innen mit dem Status "Bereits Prüfungsteilnehmer/in" ändert sich hingegen nichts. <br><br>
Werden Teilnehmer/innen hinzugefügt, nachdem bereits Sitzplätze zugewiesen wurden, müssen diesen noch Plätze zugewiesen werden.<br><br>
<strong>Achtung:</strong> Wird diese Variante des Teilnehmerimportes gewählt werden die Ergebnisse aller so hinzugefügten Teilnehmer/innen später in einer einzelnen separaten Liste für das Prüfungsamt exportiert, wodurch der Reimport gegebenenfalls schwierig wird. Wenn Sie vorhaben, die Prüfungsergebnisse reimportieren zu lassen, sollten Sie die Teilnehmer/innen lieber mithilfe der entsprechenden Teilnehmerlisten zur Prüfung hinzufügen.';
$string['addcourseparticipants_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['configuretasks_help'] = 'Hier können die Anzahl und die Maximalpunktzahlen aller <strong>Prüfungsaufgaben</strong> festgelegt werden. <br><br>
Durch Anklicken des "+" Button können neue Aufgaben zur Prüfung hinzugefügt werden. Im Feld unter der jeweiligen Aufgabennummer muss die Maximalpunktzahl eingegeben werden, die später in der jeweiligen Aufgabe erreicht werden kann. Diese Punktzahl muss positiv sein, kann aber eine Kommazahl sein. Durch einen Klick auf den "-" Button können Prüfungsaufgaben wieder entfernt werden, wobei jedoch mindestens eine Aufgabe immer bestehen bleibt. <br><br>
Die Aufgaben sind ein zentrales Element der Prüfungsorganisation. Sie entsprechen den Aufgaben, die nachher in der tatsächlichen Prüfung vorhanden sind und werden benötigt, um später die Prüfungsergebnisse für die Teilnehmer/innen eintragen zu können. Für jede Aufgabe können dann separat die von den Prüfungsteilnehmer/innen errungenen Punkte eingetragen werden, maximal jedoch die hier angegebene Höchstpunktzahl der jeweiligen Aufgabe. Die hier festgelegten Aufgaben und deren Maximalpunktzahlen werden außerdem für das Setzen des Notenschlüssels und für den Export der Prüfungsergebnisse benötigt.<br><br>
Werden die Aufgaben nachträglich verändert, nachdem bereits Prüfungsergebnisse eingetragen oder der Notenschlüssel gesetzt wurde, müssen diese gegebenenfalls an die neue Anzahl beziehungsweise Maximalpunktzahl der Aufgaben angepasst werden.';
$string['configuretasks_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['settextfield_help'] = 'Hier kann ein beliebiger Inhalt als <strong>Freitext</strong> für die Prüfung eingetragen werden, welcher den Prüfungsteilnehmer/innen nach dem Speichern sofort in deren Teilnehmeransicht angezeigt wird. <br /> <br />
Auf diese Weise können den Prüfungsteilnehmer/innen etwa unkompliziert Hinweise zu den in der Prüfung erlaubten Hilfsmitteln mitgeteilt werden. Neben einfachen Texten können dabei auch komplexere Elemente wie etwa Bilder oder gegebenenfalls sogar Formeln verwendet werden. <br /> <br />
Diese Funktion ist rein optional. Wenn Sie also z.B. keine Hinweise für die Prüfungsteilnehmer/innen haben können Sie das unten stehende Feld auch einfach leer lassen und auf den Button „Abbrechen“ klicken. <br /> <br />
<strong>Hinweis:</strong> Diese Funktion ist vorwiegend für Mitteilungen gedacht, die nicht zeitkritisch sind. Möchten Sie die Prüfungsteilnehmer/innen jedoch etwa am Tag vor der Prüfung über einen kurzfristigen Wechsel der Prüfungsräume informieren, empfiehlt sich dafür stattdessen die Nutzung der Funktion „Nachricht an Teilnehmer/innen schreiben“ auf der Übersichtsseite. Dadurch erhalten die Prüfungsteilnehmer/innen sofort eine E-Mail und können so die eingetragenen Informationen selbst dann mitbekommen, wenn sie nicht aktiv in '. get_config('mod_exammanagement', 'moodlesystemname').' nachsehen.';
$string['settextfield_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['sendgroupmessage_help'] = 'Auf dieser Seite kann der Betreff und der Inhalt einer <strong>Nachricht</strong> eingegeben werden, die nach einem Klick auf den Button „Mail abschicken“ <strong>an alle</strong> zur Prüfung als <strong>Teilnehmer/innen</strong> hinzugefügte Studierende gesendet wird. <br /> <br />
Diese bekommen die Nachricht direkt nach dem Abschicken sowohl als '. get_config('mod_exammanagement', 'moodlesystemname').'-Benachrichtigung als auch als E-Mail an ihren universitären E-Mail-Account weitergeleitet und können so zum Beispiel einfach auf kurzfristige Änderungen (etwa der Prüfungszeiten oder -Räume) aufmerksam gemacht werden. <br /> <br />
Falls Sie Teilnehmer/innen zur Prüfung hinzugefügt haben, die noch kein '. get_config('mod_exammanagement', 'moodlesystemname').'-Benutzerkonto haben, wird dies im Folgenden angezeigt. Da diese Teilnehmer/innen die hier geschriebene Nachricht nicht automatisch erhalten werden müssen Sie sie stattdessen manuell per E-Mail anschreiben. Dies können Sie zum Beispiel nach einem Klick auf den Button "Email schreiben", der ihren E-Mail-Client öffnet und die Mailadressen der entsprechenden Teilnehmer/innen einträgt, tun. <br /> <br />
Die gesamte Benachrichtigungsfunktion ist rein optional, Sie müssen sie nicht nutzen, um eine Nachricht an die Prüfungsteilnehmer/innen zu senden. <br /> <br />
<strong>Hinweis:</strong> Um den Prüfungsteilnehmer/innen ausführlichere Hinweise etwa zu den in der Klausur erlaubten Hilfsmitteln zu geben kann auch das über die Übersichtsseite erreichbare Freitextfeld genutzt werden.';
$string['sendgroupmessage_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['assignplaces_help'] = 'Auf dieser Seite können allen Prüfungsteilnehmer/innen <strong>Sitzplätze</strong> für die Klausur zugewiesen werden. Diese können später in entsprechenden Dokumenten wie etwa den Sitzplänen oder Prüfungsetiketten exportiert werden.<br><br>
Sitzplätze können dabei entweder manuell oder aber automatisiert zugewiesen werden.<br /> <br />
Für die <strong>manuelle Zuweisung</strong> muss zunächst der entsprechende Button angeklickt werden. Danach kann in der Tabelle jedem Teilnehmenden manuell ein Sitzplatz in einem Raum zugewiesen werden. Die für jeden Raum verfügbaren Sitzplätze werden dabei angezeigt. Nach einem Klick auf den Button "Sitzplätze manuell zuweisen" werden sämtliche Änderungen bei Teilnehmer/innen, denen sowohl ein Raum als auch ein Sitzplatz zugewiesen wurde gespeichert.<br /> <br />
Für die <strong>automatisierte Zuweisung</strong> muss zunächst der Zuweisungsmodus für die Sitzplätze gewählt werden. Folgende drei Alternativen sind möglich:<br />
1. Zuweisung anhand des (alphabetisch sortierten) Nachnamens. Dies entspricht der bisherigen automatischen Zuweisung.<br />
2. Zuweisung anhand der aufsteigend sortierten Matrikelnummern.<br />
3. Zufällige Zuweisung. Mehrfaches wiederholen dieser Zuweisung führt stets zu einer anderen Sitzplatzreihenfolge.<br /><br />
Falls mehrere Räume als Prüfungsräume ausgewählt wurden kann zudem noch der Zuweisungsmodus der Räume verändert werden. Möglich dabei sind:<br />
1. Aufsteigende Zuweisung - Zuerst wird der kleinste Raum komplett befüllt und dann jeweils der Nächstgrößere.<br />
2. Absteigende Zuweisung - Hier wird zuerst der größte Raum komplett befüllt und dann jeweils der Nächstkleinere. Dies entspricht der bisherigen automatischen Zuweisung.<br /><br />
Nach einem Klick auf den Button "Sitzplätze automatisch zuweisen" werden dann die Sitzplätze gemäß den gewählten Einstellungen zugewiesen. Durch Setzen des entsprechenden Hakens kann dabei festgelegt werden, dass bestehende Zuweisungen behalten und lediglich noch nicht zugewiesene Sitzplätze an Teilnehmer/innen ohne Platz zugewiesen werden. Andernfalls werden bestehende Zuweisungen komplett überschrieben. Bereits bestehende Zuweisungen lassen sich zudem über den entsprechenden Button komplett zurücksetzen.<br> <br />
<strong>Hinweis:</strong> Es ist so auch möglich, zunächst ausgewählten Teilnehmer/innen bestimmte Plätze (etwa in den ersten Reihen) zuzuweisen und danach alle restlichen Plätze automatisch zuweisen zu lassen.';
$string['assignplaces_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['importbonus_help'] = 'Hier können (beispielsweise bei der Bearbeitung von Übungsaufgaben errungene) <strong>Bonuspunkte</strong> der Prüfungsteilnehmer/innen entweder direkt importiert oder aber in <strong>Bonusnotenschritte</strong> für die Prüfung umgewandelt werden. Die direkt importierten Bonuspunkte werden später auf die in der Prüfung errungene Punktzahl addiert während Bonusnotenschritte am Ende mit der Prüfungsnote verrechnet werden. <br><br>
Dazu muss zunächst im oberen Abschnitt der Importmodus gewählt werden. Wird hier der Modus "Bonusnotenschritte" gewählt kann zudem die Zahl der für die Klausur möglichen Bonusnotenschritte festgelegt werden. Es sind maximal drei Bonusnotenschritte (ein Notenschritt wäre etwa die Verbesserung von 1,7 auf 1,3) möglich, insgesamt können Prüfungsteilnehmende sich also um maximal eine Note verbessern. Für jeden Bonusnotenschritt muss danach zudem angegeben werden, wie viele Punkte die Prüflinge zum Erhalten dieses Schrittes mindestens erreicht haben müssen. <br><br>
Im unteren Abschnitt muss als Nächstes die Art des Dateiimportes festgelegt werden. Dazu gibt es zwei Möglichkeiten:<br><br>
1. Bewertungsexport aus '. get_config('mod_exammanagement', 'moodlesystemname').': Haben Ihre Studierenden ihre Übungszettel über die '. get_config('mod_exammanagement', 'moodlesystemname').' Aufgabenabgabe abgegeben und wurden diese dort korrigiert und bewertet sollte hier der Bewertungsexport aus '. get_config('mod_exammanagement', 'moodlesystemname').' ausgewählt werden, da auf diese Weise sämtliche Bonuspunkte für den kompletten '. get_config('mod_exammanagement', 'moodlesystemname').' Kurs unkompliziert eingelesen werden können.<br>
Dazu müssen die Bewertungen aus dem '. get_config('mod_exammanagement', 'moodlesystemname').' Kurs zunächst wie <a href="https://docs.moodle.org/de/Bewertungen_exportieren" class="alert-link" target="_blank">hier</a> beschrieben exportiert werden. Danach müssen Sie die exportierte Datei einmal öffnen und nachsehen, in welcher Spalte die Punkte eingetragen sind. Die Bezeichnung der Spalte muss dann im dafür vorgesehenen Feld im unteren Abschnitt eingetragen werden.<br><br>
2. Individuell: Falls Sie ihre Bonuspunkte nicht über die '. get_config('mod_exammanagement', 'moodlesystemname').' Aufgabenabgabe verwaltet haben können Sie alternativ den Modus des individuellen Importes auswählen. Für diesen brauchen Sie eine Excel-Datei, bei der für jeden betroffenen Teilnehmenden in einer eigenen Zeile entweder die in '. get_config('mod_exammanagement', 'moodlesystemname').' hinterlegte Email-Adresse oder aber die Matrikelnummer in einer und die erreichte Punktzahl in einer anderen Spalte steht. Die Bezeichnung sowohl der Spalte, in der die Benutzerindentfikatoren aller Studierenden steht als auch die der Spalte, die alle Bonuspunktzahlen enthält müssen dann in den entsprechenden Feldern im unteren Abschnitt angegeben werden. <br><br>
Zum Abschluss muss nun noch die einzulesende Datei mit den Bonuspunkten ausgewählt und dann durch einen Klick auf den Button "Datei einlesen" eingelesen werden, um den Bonuspunkteimport durchzuführen. Die importierte Anzahl an Bonusnotenschritten oder Bonuspunkten wird den Teilnehmer/innen in deren Ansicht angezeigt, sobald dies im entsprechenden Arbeitsschritt der Übersichtsseite freigeschaltet wurde.';
$string['importbonus_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['configuregradingscale_help'] = 'Hier kann ein <strong>Notenschlüssel</strong> für die Prüfung konfiguriert werden. <br><br>
Sobald die Prüfungsergebnisse eingetragen wurden wird dieser dazu benutzt, automatisch die Prüfungsnoten aller Teilnehmenden zu errechnen. Wurde kein Notenschlüssel konfiguriert ist die automatische Berechnung einer Note hingegen nicht möglich.<br><br>
Es muss für jeden Notenschritt einzeln angegeben werden, wie viele Punkte für dessen Erreichen mindestens notwendig sind. Eine 70 im Feld unter 1,0 würden demnach bedeuten, dass ein/e Teilnehmer/in mindestens 70 Punkte erreichen muss, um die Note 1,0 zu bekommen).<br><br>
Die zu erreichende Punktzahl für einen Notenschritt kann zwischen 0 und der angegebenen Gesamtpunktezahl aller Prüfungsaufgaben liegen, sie muss allerdings höher sein als die für den Notenschritt davor benötigte Punktzahl. So müssen für das Erreichen einer 1,0 etwa mehr Punkte gefordert sein als für das Erreichen einer 1,3. Dazu ist auch die Nutzung von Kommazahlen als Punkte möglich. Erreicht ein Teilnehmender weniger Punkte als für die 4,0 notwendig sind bekommt er stattdessen die Note 5.<br><br>
Der Notenschlüssel kann jederzeit (auch nach dem Eintragen der Prüfungsergebnisse) geändert werden, die Noten der Prüfungsteilnehmer/innen werden in diesem Fall sofort automatisch an den neuen Notenschlüssel angepasst.';
$string['configuregradingscale_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['inputresults_help'] = 'Auf dieser Seite können die <strong>Prüfungsergebnisse</strong> der Teilnehmer/innen <strong>eingetragen</strong> werden. <br><br>
Dazu muss zunächst die Matrikelnummer des oder der Teilnehmenden, deren Ergebnisse eingetragen werden sollen, eingegeben werden. Dazu gibt es zwei Möglichkeiten:<br>
1. Sie können die Matrikelnummer des oder der Teilnehmenden manuell eingeben. Klicken Sie dazu in das Feld "Matrikelnummer oder Barcode", tippen Sie die Matrikelnummer ein und bestätigen Sie diese durch ein Drücken der Enter- (bzw. Return-) oder der Tabulator-Taste oder des Buttons "Matrikelnummer validieren". <br>
2. Alternativ können Sie, falls Sie in Ihrer Prüfung Prüfungsetiketten benutzt haben, auch einen Barcode-Scanner zum schnelleren Eintragen der Prüfungsergebnisse nutzen. Dazu brauchen Sie einen Barcode-Scanner oder alternativ ein Smartphone mit einer entsprechenden App. Mit diesem müssen Sie dann den Barcode auf dem Prüfungsetikett eines Prüflings einscannen, wodurch dessen Matrikelnummer automatisch in das Feld "Matrikelnummer oder Barcode" eingetragen und sofort bestätigt wird. Klappt das automatische Eintragen nicht sofort müssen Sie gegebebenfalls einmal manuell in das Feld "Matrikelnummer oder Barcode" klicken und den Scan dann wiederholen.<br><br>
Sobald eine Matrikelnummer eingetragen und bestätigt wurde wird sie vom System geprüft. Ist sie die gültige Matrikelnummer eines oder einer zur Prüfung hinzugefügten Teilnehmer/in wird nun die Seite zum Eintragen der Prüfungspunkte geöffnet, andernfalls gibt es eine entsprechende Fehlermeldung und es wird wieder die vorige Seite geöffnet, wo eine neue Matrikelnummer eingetragen oder das Eintragen der fehlerhaften Matrikelnummer wiederholt werden kann.<br><br>
Im Fall einer gültigen Matrikelnummer können auf der daraufhin geöffneten Seite nun die Prüfungsergebnisse eingetragen werden. Im Abschnitt "Prüfungsteilnehmer/in" sieht man zunächst die Matrikelnummer und den Namen des oder der gewählten Prüfungsteilnehmer/in. Durch einen Klick auf den darunter befindlichen Button "Matrikelnummer ändern" kann man an dieser Stelle wieder auf die vorherige Seite gelangen, um (zum Beispiel im Falle eines Fehlers) dort eine andere Matrikelnummer einzugeben. Im darunter befindlichen Abschnitt "Prüfungspunkte" können für den gewählten Prüfling die in jeder Prüfungsaufgabe errungenen Punkte eingetragen werden. Dazu können direkt im Punkte-Feld der ersten Aufgabe die entsprechenden Punkte eingetragen werden und dann nach einem Drücken der Tabulator-Taste im Feld der nächsten Aufgabe weiter gemacht werden. Als Punktzahl kann dabei eine Zahl zwischen Null und der angezeigten Maximalpunktzahl der jeweiligen Aufgabe eingetragen werden, wobei auch Kommazahlen mit bis zu zwei Nachkommastellen erlaubt sind. Unterliegt der Prüfling einem besonderen Prüfungsstatus (hat er etwa "Nicht Teilgenommen" ("NT"), einen "Betrugsversuch" begangen oder war er "Krank") kann dieser Status im letzten Abschnitt "Prüfungsstatus" durch das Setzen des Hakens in der entsprechenden Checkbox ausgewählt werden. Dadurch werden die Punkte der Aufgaben auf Null gesetzt, das Eintragen der Punkte wird deaktiviert und der gewählte Status wird in allen späteren Dokumenten (zum Beispiel für den Export) statt des Ergebnisses angezeigt. Das Entfernen des Hakens beim jeweiligen Prüfungsstatus aktiviert die Möglichkeit zum Punkteeintragen wieder. Wurden für den Prüfling bereits früher Ergebnisse eingetragen sind sowohl der Abschnitt zu den Prüfungspunkten als auch dem Prüfungsstatus möglicherweise bereits vorausgefüllt. In diesem Fall können diese Angaben nun geändert und die Änderungen dann gespeichert werden.<br><br>
Nach einem Klick auf den Button "Speichern und zum Nächsten" oder nach dem Drücken der Enter- bzw. Return-Taste werden die eingetragenen Ergebnisse dann gespeichert und es wird automatisch die Ausgangsseite aufgerufen, auf der dann die Matrikelnummer des nächsten Prüflings (entweder manuell oder per Barcodescanner) eingelesen werden kann.';
$string['inputresults_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['participantsoverview_help'] = 'In dieser <strong>Teilnehmer- und Ergebnisübersicht</strong> können, sobald Prüfungsteilnehmer/innen importiert wurden, alle Informationen zu diesen angesehen und all ihre Prüfungsergebnisse eingetragen sowie bearbeitet werden. <br><br>
Für jede der alphabetisch sortiert angezeigten Prüfungsteilnehmer/innen werden standardmäßig sowohl der Vor- und der Nachname als auch die Matrikelnummer angezeigt. Wurden einem oder einer Teilnehmenden bereits ein Sitzplatz zugewiesen werden dieser sowie der zugehörige Raum in den entsprechend benannten Spalten ebenfalls angezeigt. Wurden bereits Prüfungsaufgaben angelegt und wurden für eine/n Teilnehmer/in bereits Prüfungsergebnisse eingetragen werden auch diese angezeigt. In der Spalte "Punkte" ist dabei zu sehen, wie viele Punkte der oder die Teilnehmer/in in jeder einzelnen Aufgabe erreicht hat, während in der Spalte "Gesamtpunkte" die aufsummierte Gesamtpunktezahl angezeigt wird. Wurden noch keine Prüfungsaufgaben angelegt gibt ein Klick auf das stattdessen in der Spalte "Punkte" angezeigte Symbol die Möglichkeit, dies direkt zu erledigen. Wurde noch kein Notenschlüssel eingetragen kann dies nach einem Klick auf das entsprechende Symbol in der Spalte "Ergebnis" getan werden, andernfalls wird in dieser Spalte (falls für den oder die Teilnehmer/in bereits Ergebnisse eingetragem wurden) dessen aus dem Notenschlüssel berechnete Prüfungsnote angezeigt. Hat der oder die Teilnehmer/in einen besonderen Status (war er etwa bei der Prüfung krank oder liegt bei ihm ein Betrugsversuch vor) wird dies statt des Prüfungsergebnisses angezeigt. Außerdem stehen in den Spalten "Bonusschritte" beziehungsweise "Bonuspunkte" die bereits vom Teilnehmenden errungenen Bonusnotenschritte sowie Bonusunkte für die Klausur, während in den Spalten "Gesamtpunkte inkl. Bonuspunkte" und "Ergebnis inkl. Bonusnotenschritte" die Gesamtpunktzahl sowie die Endnote unter Berücksichtigung der Bonuspunkte und Bonusnotenschritte angesehen werden kann. <br><br>
Um die Informationen zu einem oder einer Prüfungsteilnehmer/in zu bearbeiten reicht ein Klick auf den Button "Prüfungsergebnisse und Boni bearbeiten". Danach können in der Tabelle für jeden Teilnehmenden die Prüfungsergebnisse, Bonuspunkte und -Schritte sowie der Prüfungsstatus eingetragen beziehungsweise bearbeitet werden. In der Spalte "Punkte" können dabei aufgabenweise die vom Prüfling erreichten Punkte eingetragen werden. Falls nötig kann zudem aus einem Dropdown-Menü ein besonderer Prüfungsstatus wie etwa "Krank", "Nicht Teilgenommen" ("NT") oder aber "Betrugsversuch" ausgewählt werden, wodurch die Punkte für den oder die betreffende Teilnehmer/in automatisch auf Null gesetzt und die Möglichkeit zum Punkteeintragen deaktiviert wird. Das Zurücksetzen des Status auf "Normal" erlaubt das Punkteeintragen wieder, während der Status "-" die Punkte auf nicht gesetzt zurücksetzt. Es können außerdem entweder die von den Prüflingen erreichten Bonuspunkte oder aber Bonusnotenschritte eingetragen werden. Durch Drücken der Tabulator-Taste kann dabei zwischen den einzelnen Feldern und Teilnehmer/innen gewechselt werden, während ein Klick auf den entsprechenden Button oder das Drücken der Eingabe-Taste sämtliche eingetragenen Änderungen speichert. <br><br>
Für die Studierenden können dabei alle diese Angaben gleichzeitig oder einzeln eingetragen beziehungsweise bearbeitet werden. Auf diese Weise kann diese Seite nicht nur genutzt werden, um fehlerhaft eingetragene Angaben zu korrigieren sondern auch, um für Prüfungsteilnehmende insgesamt manuell Ergebnisse oder Boni einzutragen. Auch für Teilnehmer/innen ohne Matrikelnummer können auf diese Weise einfach Prüfungsergebnisse eingetragen werden. Die manuelle Zuweisung von Sitzplätzen ist hingegen nicht hier sondern im entsprechenden Arbeitsschritt möglich.';
$string['participantsoverview_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['examreview_help'] = 'Falls für die Prüfung eine <strong>Klausureinsicht</strong> stattfinden soll können hier der Termin und der Raum dafür ausgewählt werden. <br><br>
Die Bezeichnung des Raumes kann dabei frei als normaler Text in das untere Formularfeld eingegeben werden. Auf diese Weise können Sie auch nicht im System als Prüfungsraum hinterlegte Räume wie etwa ihr Büro als Klausureinsichtsraum auswählen. <br><br>
Wenn Sie nach dem Zeitpunkt der Klausureinsicht Prüfungsergebnisse für die Teilnehmenden ändern können Sie diese danach einfach auf der Übersichtsseite gesondert für das Prüfungsamt exportieren. <br><br>
Die hier festgelegten Informationen zum Termin und Raum der Klausureinsicht können später auf der Übersichtsseite wieder für die Studierenden sichtbar geschaltet werden.';
$string['examreview_link'] = get_config('mod_exammanagement', 'additionalressources');

// Errors and permissions.
$string['incorrectcourseid'] = 'Inkorrekte Kurs-ID';
$string['incorrectmodule'] = 'Inkorrekte Kurs-Modul-ID';
$string['nopermissions'] = 'Sie haben keine Berechtigung dies zu tun.';
$string['ldapnotenabled'] = 'LDAP-Nutzung deaktiviert.';
$string['ldapnotconfigured'] = 'Kein gültiges LDAP konfiguriert.';
$string['ldapconfigmissing'] = 'LDAP nicht vollständig konfiguriert. Die folgenden vom Plugin benötigten Elemente müssen noch in den globalen Plugineinstellungen spezifiziert werden:';
$string['ldapconnectionfailed'] = 'Verbindung zum LDAP fehlgeschlagen. Bitte versuchen Sie es erneut oder kontaktieren Sie Ihren System-Administrator.';
$string['nomatrnravailable'] = 'Keine Matrikelnummern verfügbar da';
$string['not_possible_no_matrnr'] = 'Nicht möglich weil keine Matrikelnummern verfügbar sind -';
$string['importmatrnrnotpossible'] = 'Import nach Matrikelnummer nicht möglich -';
$string['enterresultsmatrnr'] = 'Eintragen der Ergebnisse nach Matrikelnummer nicht möglich -';
$string['err_underzero'] = 'Die eingegebene Zahl darf nicht kleiner als Null sein.';
$string['err_toohigh'] = 'Der eingegebene Wert ist zu hoch.';
$string['err_novalidinteger'] = 'Der eingegebene Wert ist keine gültige Zahl.';
$string['err_overmaxpoints'] = 'Die eingegebene Zahl überschreitet die Maximalpunktzahl.';
$string['err_bonusstepsnotcorrect'] = 'Mindestens einer der Bonusnotenschritte passt nicht zu den anderen.';
$string['err_gradingstepsnotcorrect'] = 'Mindestens einer der Notenschritte passt nicht zu den anderen.';
$string['err_taskmaxpoints'] = 'Die eingetragene Punktzahl überschreitet die Maximalpunktzahl der Aufgabe.';
$string['err_roomsdoubleselected'] = 'Derselbe Raum wurde mehrfach in unterschiedlichen Belegungen als Prüfungsraum gewählt.';
$string['err_invalidcheckboxid_rooms'] = 'Ungültige Raumid.';
$string['err_invalidcheckboxid_participants'] = 'Ungültige ID des oder der Teilnehmer/in.';
$string['err_nonvalidmatrnr'] = 'Ungültige Matrikelnummer.';
$string['err_customroomname_taken'] = 'Raumname bereits vergeben';
$string['err_filloutfield'] = 'Bitte Feld ausfüllen';
$string['err_nofile'] = 'Bitte Datei auswählen';
$string['err_noalphanumeric'] = 'Enthält ungültige Zeichen';
$string['err_js_internal_error'] = 'Interner Fehler. Bitte erneut versuchen.';
$string['err_password_incorrect'] = 'Passwort nicht identisch. Bitte erneut eingeben.';
$string['err_novalidpassword'] = 'Kein gültiges Passwort.';
$string['err_examdata_deleted'] = 'Die Prüfungsdaten wurden bereits gelöscht. Eine Nutzung der Prüfungsorganisation ist nicht mehr möglich.';
$string['err_already_defaultroom'] = 'Bereits Standardraum. Probieren Sie stattdessen Raum-ID';
$string['err_novalidplacescount'] = 'Die eingegebene Zahl ist keine gültige Sitzplatzzahl.';
$string['err_nocourseparticipants'] = 'Noch keine Kursteilnehmer/innen vorhanden.';
$string['err_groupname_taken'] = 'Gruppenname bereits vergeben.';
$string['err_too_long'] = 'Der eingegebene Wert ist zu lang.';

// Universal.
$string['modulename'] = 'Prüfungsorganisation';
$string['modulenameplural'] = 'Prüfungsorganisationen';
$string['pluginname'] = 'Prüfungsorganisation';
$string['coursecategory_name_no_semester'] = 'SEMESTERLOS';

// Strings for mod_form.php.
$string['modulename_help'] = 'Mithilfe der Prüfungsorganisation können Prüfungen in Kursen einfach online organisiert und so auch Präsenzprüfungen mit vielen Teilnehmenden bequem verwaltet werden.

Lehrende können dabei in ihrer Ansicht ...

* die Basisdaten der Prüfung einstellen
* für die Prüfungsdurchführung hilfreiche Dokumente wie etwa Sitzpläne und Teilnehmerlisten exportieren
* die Prüfungsergebnisse für die Teilnehmenden händisch oder mithilfe eines Barcodescanners beziehungsweise eines Smartphones mit QR Codes eintragen
* alle Ergebnisse in verschiedenen Dokumenten für die weitere Verwendung (z. B. durch das Prüfungsamt) exportieren

Die Teilnehmerinnen und Teilnehmer der Prüfung sehen hingegen in ihrer eigenen Ansicht alle relevanten Informationen der Prüfung wie etwa den Termin, den eigenen Sitzplatz, für die Prüfung errungene Bonusnotenschritte oder die Prüfungsergebnisse. Außerdem kann mithilfe der Benachrichtigungsfunktion einfach und zuverlässig mit diesen kommuniziert werden.';
$string['modulename_link'] = 'https://docs.moodle.org/de/mod/exammanagement';
$string['exammanagement_name'] = 'Name der Prüfungsorganisation';
$string['exammanagement_name_help'] = 'Der im Kurs angezeigte Name der Aktivität (z. B. "Klausur 1").';
$string['exammanagement:enable exam management'] = 'Prüfungsorganisation aktivieren';
$string['pluginadministration'] = 'Administration der Prüfungsorganisation';
$string['security_password'] = 'Passwortschutz';
$string['new_password'] = 'Neues Passwort';
$string['security_password_help'] = 'Durch das Festlegen eines Sicherheitspasswortes können Sie den Zugang zu dieser Prüfungsorganisation gegenüber anderen '. get_config('mod_exammanagement', 'moodlesystemname').'-Benutzern (z. B. Ihren studentischen Tutoren) begrenzen. Diese müssen dann zunächst das Passwort eingeben, bevor sie Zugang zu den Inhalten der Prüfungsorganisation erhalten.';
$string['confirm_new_password'] = 'Neues Passwort wiederholen';
$string['confirm_new_password_help'] = 'Für das Setzen des neuen Passwortes muss dieses hier erneut eingegeben werden.';
$string['old_password'] = 'Altes Passwort';
$string['old_password_help'] = 'Falls ein bereits gesetztes Passwort geändert werden soll muss dieses hier eintragen werden.';
$string['incorrect_password_change'] = 'Das alte Passwort ist falsch. Passwortänderung abgebrochen';
$string['export_grades_as_exam_results'] = 'Kursbewertungen als Prüfungsergebnisse exportieren';
$string['activate_mode'] = 'Bewertungsexport aktivieren';
$string['export_grades_as_exam_results_help'] = 'Ist diese Option aktiviert können mit der Prüfungsorganisation die Kursbewertungen aller Teilnehmer/innen einfach in einem Textdokument als Prüfungsergebnisse exportiert werden. Jede Bewertung ist dabei (falls diese vorhanden ist) der Matrikelnummer des jeweiligen Teilnehmenden zugeordnet.';
$string['deselectstepsandphases'] = 'Arbeitsschritte und Phasen abschalten';
$string['deselectphaseexamreview'] = 'Phase Klausureinsicht abschalten';

// Strings for the capabilities.
$string['exammanagement:addinstance'] = 'Neue Prüfungsorganisation hinzufügen';
$string['exammanagement:viewinstance'] = 'Prüfungsorganisation ansehen';
$string['exammanagement:viewparticipantspage'] = 'Teilnehmeransicht ansehen';
$string['exammanagement:takeexams'] = 'Prüfung ablegen';
$string['exammanagement:receivegroupmessage'] = 'Gruppennachricht der Prüfungsorganisation empfangen';
$string['exammanagement:importdefaultrooms'] = 'Standardräume importieren';
$string['exammanagement:resetpassword'] = 'Password zurücksetzen';
$string['exammanagement:requestpasswordreset'] = 'Zurücksetzen des Passwortes beantragen';
$string['exammanagement:receivedeletionwarningmessages'] = 'Löschwarnungen erhalten';

// Strings for the message providers.
$string['messageprovider:groupmessage'] = 'Gruppennachrichten der Prüfungsorganisation';
$string['messageprovider:passwordresetrequest'] = 'Anfragen zum Zurücksetzen des Passwortes von Prüfungsorganisationen';
$string['messageprovider:passwordresetmessage'] = 'Bestätigungsnachrichten des Zurücksetzens des Passwortes von Prüfungsorganisation';
$string['messageprovider:deletionwarningmessage'] = 'Warnnachrichten der Prüfungsorganisation zur baldigen Löschung von Prüfungsdaten';

// Strings for the privacy provider.
$string['privacy:metadata:exammanagement'] = 'Enthält keine personenbezogenen Daten. Enthält stattdessen alle mit dem Plugin zur Prüfungsorganisation angelegten Prüfungen und deren allgemeine Prüfungsinformationen.';
$string['privacy:metadata:exammanagement:no_data'] = 'Keine personenbezogenen Daten.';
$string['privacy:metadata:exammanagement_participants'] = 'Enthält alle Prüfungsteilnehmer/innen aus allen Prüfungsorganisationen und deren persönliche Prüfungsinformationen.';
$string['privacy:metadata:exammanagement_temp_part'] = 'Enthält alle temporär gespeicherten potenziellen Prüfungsteilnehmer/innen aller Prüfungsorganisationen und deren persönliche Prüfungsinformationen. Diese potenziellen Prüfungsteilnehmer/innen können nicht immer einem Moodle Benutzer zugeordnet werden und werden einmal täglich durch einen geplanten Vorgang gelöscht. Ein Export dieser Daten ist somit nicht möglich und eine weitergehende Löschung nicht notwendig.';
$string['privacy:metadata:exammanagement_rooms'] = 'Enthält alle standard- und benutzerdefinierten Prüfungsräume die in den einzelnen Prüfungsorganisationen ausgewählt werden können.';
$string['privacy:metadata:exammanagement_participants:exammanagement'] = 'ID der Prüfungsorganisation des Teilnehmers';
$string['privacy:metadata:exammanagement_participants:courseid'] = 'Kurs der Prüfungsorganisation des Teilnehmers';
$string['privacy:metadata:exammanagement_participants:categoryid'] = 'Category-ID des Kurses der Prüfungsorganisation des Teilnehmers';
$string['privacy:metadata:exammanagement_participants:moodleuserid'] = 'Moodleinterne ID des Teilnehmers';
$string['privacy:metadata:exammanagement_participants:login'] = 'Login des Teilnehmers';
$string['privacy:metadata:exammanagement_participants:firstname'] = 'Vorname des Teilnehmers (falls kein Moodle-Account vorhanden)';
$string['privacy:metadata:exammanagement_participants:lastname'] = 'Nachname des Teilnehmers (falls kein Moodle-Account vorhanden)';
$string['privacy:metadata:exammanagement_participants:email'] = 'E-Mailadresse des Teilnehmers (falls kein Moodle-Account vorhanden)';
$string['privacy:metadata:exammanagement_participants:headerid'] = 'ID der Kopfzeile der Datei aus welcher der Teilnehmer importiert ist';
$string['privacy:metadata:exammanagement_participants:roomid'] = 'ID des dem Teilnehmer zugewiesenen Raumes';
$string['privacy:metadata:exammanagement_participants:roomname'] = 'Name des dem Teilnehmer zugewiesenen Raumes';
$string['privacy:metadata:exammanagement_participants:place'] = 'Sitzplatz des Teilnehmers';
$string['privacy:metadata:exammanagement_participants:exampoints'] = 'Prüfungsergebnisse des Teilnehmers als Objekt in JSON-Syntax';
$string['privacy:metadata:exammanagement_participants:examstate'] = 'Prüfungsstatus des Teilnehmers als Objekt in JSON-Syntax';
$string['privacy:metadata:exammanagement_participants:timeresultsentered'] = 'Zeitstempel wann das Ergebnis eingetragen wird';
$string['privacy:metadata:exammanagement_participants:bonussteps'] = 'Bonusnotenschritte des Teilnehmers';
$string['privacy:metadata:exammanagement_participants:bonuspoints'] = 'Bonuspunkte des Teilnehmers für die Prüfung';
$string['privacy:metadata:exammanagement_temp_part:identifier'] = 'Matrikelnummer des potenziellen Teilnehmers';
$string['privacy:metadata:exammanagement_temp_part:line'] = 'Zeilennummer des potenziellen Teilnehmers in der importierten Teilnehmerliste';
$string['privacy:metadata:exammanagement_temp_part:headerid'] = 'ID des Headers der Teilnehmerliste aus dem der potenzielle Teilnehmer eingelesen wurde';
$string['privacy:metadata:exammanagement_rooms:roomid'] = 'Raum-ID (intern)';
$string['privacy:metadata:exammanagement_rooms:name'] = 'Raumname';
$string['privacy:metadata:exammanagement_rooms:description'] = 'Raumbeschreibung';
$string['privacy:metadata:exammanagement_rooms:seatingplan'] = 'Sitzplan als Vektorgrafik';
$string['privacy:metadata:exammanagement_rooms:places'] = 'Sitzplätze des Raumes als Array in JSON-Syntax';
$string['privacy:metadata:exammanagement_rooms:type'] = 'Raumtyp (Standard oder benutzerdefiniert)';
$string['privacy:metadata:exammanagement_rooms:moodleuserid'] = 'Moodleinterne ID des Nutzers der den benutzerdefinierten Raum angelegt hat';
$string['privacy:metadata:exammanagement_rooms:misc'] = 'Weitere Werte in JSON-Syntax (momentan nur Zeitstempel wann Standardraum erstellt wurde)';
$string['privacy:metadata:core_message'] = 'Das Plugin zur Prüfungsorganisation sendet Nachrichten an Benutzer und speichert deren Inhalte in der Datenbank. Dies können Gruppennachrichten mit Prüfungsinformationen an Teilnehmer sein, Nachrichten an den Support wenn ein Lehrender diesen bittet das Passwort einer Prüfungsorganisation zurückzusetzen oder aber Systemnachrichten, die einen Lehrenden über die baldige Löschung der sensiblen Daten einer Prüfungsorganisation informieren.';
$string['privacy:metadata:preference:exammanagement_phase_one'] = 'Ob der Benutzer die erste Phase der Prüfungsorganisation geöffnet oder geschlossen hat';
$string['privacy:metadata:preference:exammanagement_phase_two'] = 'Ob der Benutzer die zweite Phase der Prüfungsorganisation geöffnet oder geschlossen hat';
$string['privacy:metadata:preference:exammanagement_phase_exam'] = 'Ob der Benutzer die Prüfungsphase der Prüfungsorganisation geöffnet oder geschlossen hat';
$string['privacy:metadata:preference:exammanagement_phase_three'] = 'Ob der Benutzer die dritte Phase der Prüfungsorganisation geöffnet oder geschlossen hat';
$string['privacy:metadata:preference:exammanagement_phase_four'] = 'Ob der Benutzer die vierte Phase der Prüfungsorganisation geöffnet oder geschlossen hat';
$string['privacy:metadata:preference:exammanagement_phase_five'] = 'Ob der Benutzer die fünfte Phase der Prüfungsorganisation geöffnet oder geschlossen hat';
$string['privacy:metadata:preference:exammanagement_phaseopenedorclosed'] = 'Ob der Benutzer die Phase der Prüfungsorganisation manuell geöffnet oder geschlossen hat';
$string['privacy:metadata:preference:exammanagement_pagecount'] = 'Wie viele Objekte der Benutzer pro Seite auf den paginierten Tabellen angezeigt bekommen möchte';
$string['opened'] = 'Offen';
$string['closed'] = 'Geschlossen';

// Strings for the admin settings on settings.php.
$string['enablepasswordresetrequest'] = 'Beantragen des Passwort-Zurücksetzens aktivieren';
$string['enablepasswordresetrequest_help'] = 'Sobald diese Funktion aktiviert wurde können alle Lehrenden in ihren Prüfungsorganisationen durch einen Klick auf den entsprechenden Button das Zurücksetzen der dort gesetzten Passwörter beantragen. Hat ein Dozent dies getan bekommen alle BenutzerInnen mit der Rolle "Manager/in" eine automatisch generierte Nachricht sowohl als interne Benachrichtigung als auch an die in ihrem Profil hinterlegte E-Mail-Adresse weitergeleitet und können danach durch einen Klick auf den in dieser Nachricht enthaltenen Link das Passwort zurücksetzen. Dadurch werden alle Lehrenden der betroffenen Prüfungsorganisation automatisch per interner Benachrichtigung sowie E-Mail über das Zurücksetzen des Passwortes informiert und können danach wieder ohne Eingabe eines Passwortes auf die Inhalte der Prüfungsorganisation zugreifen. Ist die Funktion hier deaktiviert können NutzerInnen das Zurücksetzen des Passwortes in ihrer Prüfungsorganisation nicht automatisch beantragen, allerdings können Manager und Administratoren trotzdem das Passwort jeder Prüfungsorganisation zurücksetzen.';
$string['moodlesystemname'] = 'Name des Moodle Systems';
$string['moodlesystemname_help'] = 'Der Name des Moodle Systems. Dieser wird im Plugin zum Beispiel in Hilfetexten angezeigt.';
$string['enableglobalmessage'] = 'Mitteilung beim Erstellen einer Prüfungsorganisation anzeigen';
$string['enableglobalmessage_help'] = 'Wird diese Option aktiviert wird die folgende globale Mitteilung beim Anlegen einer neuen Prüfungsorganisation angezeigt.';
$string['globalmessage'] = 'Globale Mitteilung';
$string['globalmessage_help'] = 'Text der globalen Mitteilung die beim Anlegen einer neuen Prüfungsorganisation angezeigt wird wenn dies aktiviert ist.';
$string['enablehelptexts'] = 'Plugininterne Hilfetexte aktivieren';
$string['enablehelptexts_help'] = 'Wird diese Option aktiviert werden plugininterne Hilfetexte in sämtlichen Prüfungsorganisationen angezeigt.';
$string['additionalressources'] = 'Zusätzliche Hilfe';
$string['additionalressources_help'] = 'Der hier eingetragene Weblink wird in den plugininternen Hilfetexten als Quelle für weiterführende Informationen angezeigt, wenn diese für das Plugin aktiviert wurden.';
$string['enableldap'] = 'LDAP verwenden';
$string['enableldap_help'] = 'Das Setzen des Hakens erlaubt dem Plugin zur Prüfungsorganisation die Verwendung eines im System hinterlegten externen LDAP-Servers zur Ermittlung der Stammdaten der Prüfungsteilnehmer/innen wie etwa deren Matrikelnummern. Damit dies funktioniert muss der externe LDAP-Server zur Verfügung stehen und in Moodle <a href="https://docs.moodle.org/de/LDAP-Server"><u>konfiguriert</u></a> sein. Zudem müssen in den folgenden Einstellungen der "distinguished name" (kurz dn) sowie die Namen all jener Felder des LDAP eingetragen werden, welche die vom Plugin benötigten Informationen enthalten. Ist dies geschehen verwendet das Plugin automatisch die im LDAP hinterlegten Daten. Andernfalls stehen die entsprechenden Funktionalitäten des Plugins (z. B. Teilnehmerimport bzw. Punkteeintragen nach Matrikelnummern oder Export der Prüfungsetiketten) nicht zur Verfügung.';
$string['ldapdn'] = '"Distinguished name" (kurz "dn")';
$string['ldapdn_help'] = 'In diesem Feld muss der "distinguished name" (kurz "dn") eingetragen werden. Dieser beschreibt die Positionierung der Einträge im LDAP-System. Wird dieses Feld leer gelassen nutzt das Plugin bei aktiviertem LDAP den Wert "contexts" aus den globalen Einstellungen des Authentifizierungsplugins LDAP-Server ("auth_ldap"). Ist keines dieser beiden Felder ausgefüllt können die vom LDAP abhängigen Pluginfunktionen nicht genutzt werden.';
$string['ldap_objectclass_student'] = 'LDAP-Klasse des Teilnehmer-Objekts';
$string['ldap_objectclass_student_help'] = 'Hier kann ein Klassenname eingetragen werden der dann als zusätzliches Filterkriterium für das Teilnehmerobjekt im LDAP verwendet wird.';
$string['ldap_field_map_username'] = 'Feld Benutzername';
$string['ldap_field_map_username_help'] = 'Hier muss die Bezeichnung des Feldes im LDAP-System eingetragen werden, in welchem der Benutzername des Teilnehmers steht. Dieser Benutzername muss mit dem Benutzernamen des Teilnehmers in '. get_config('mod_exammanagement', 'moodlesystemname').' übereinstimmen. Wird dieses Feld leer gelassen nutzt das Plugin bei aktiviertem LDAP den Wert "field_map_idnumber" aus den globalen Einstellungen des Authentifizierungsplugins LDAP-Server ("auth_ldap"). Ist keines dieser beiden Felder ausgefüllt können die vom LDAP abhängigen Pluginfunktionen nicht genutzt werden.';
$string['ldap_field_map_matriculationnumber'] = 'Feld Matrikelnummer';
$string['ldap_field_map_matriculationnumber_help'] = 'Hier muss die Bezeichnung des Feldes im LDAP-System eingetragen werden, in welchem die Matrikelnummer des Teilnehmers steht. Wird dieses Feld nicht ausgefüllt können alle Pluginfunktionen, die Matrikelnummern benötigen (etwa Punkteeintragen nach Matrikelnummer oder Anzeige bzw. Export von Matrikelnummern) nicht genutzt werden.';
$string['ldap_field_map_firstname'] = 'Feld Vorname';
$string['ldap_field_map_firstname_help'] = 'Hier muss die Bezeichnung des Feldes im LDAP-System eingetragen werden, in welchem der Vorname des Teilnehmers steht. Wird dieses Feld leer gelassen nutzt das Plugin bei aktiviertem LDAP den Wert "field_map_firstname" aus den globalen Einstellungen des Authentifizierungsplugins LDAP-Server ("auth_ldap"). Ist keines dieser beiden Felder ausgefüllt können Teilnehmer, die keinen gültigen '. get_config('mod_exammanagement', 'moodlesystemname').' Account haben, nicht als Prüfungsteilnehmer importiert werden.';
$string['ldap_field_map_lastname'] = 'Feld Nachname';
$string['ldap_field_map_lastname_help'] = 'Hier muss die Bezeichnung des Feldes im LDAP-System eingetragen werden, in welchem der Nachname des Teilnehmers steht. Wird dieses Feld leer gelassen nutzt das Plugin bei aktiviertem LDAP den Wert "field_map_lastname" aus den globalen Einstellungen des Authentifizierungsplugins LDAP-Server ("auth_ldap"). Ist keines dieser beiden Felder ausgefüllt können Teilnehmer, die keinen gültigen '. get_config('mod_exammanagement', 'moodlesystemname').' Account haben, nicht als Prüfungsteilnehmer importiert werden.';
$string['ldap_field_map_mail'] = 'Feld Emailadresse';
$string['ldap_field_map_mail_help'] = 'Hier muss die Bezeichnung des Feldes im LDAP-System eingetragen werden, in welchem die Emailadresse des Teilnehmers steht. Wird dieses Feld leer gelassen nutzt das Plugin bei aktiviertem LDAP den Wert "field_map_email" aus den globalen Einstellungen des Authentifizierungsplugins LDAP-Server ("auth_ldap"). Ist keines dieser beiden Felder ausgefüllt können Teilnehmer, die keinen gültigen '. get_config('mod_exammanagement', 'moodlesystemname').' Account haben, nicht als Prüfungsteilnehmer importiert werden.';

// Strings for sheduled tasks.
$string['delete_temp_participants'] = 'Temporär gespeicherte Teilnehmer/innen löschen';
$string['check_participants_without_moodle_account'] = 'Teilnehmer/innen ohne Moodle Account überprüfen';
$string['delete_old_exam_data'] = 'Alte Prüfungsdaten löschen';
$string['warningmailsubjectone'] = '[Prüfungsorganisation] Erinnerung: Zukünftige Löschung der Prüfungsdaten';
$string['warningmailsubjecttwo'] = '[Prüfungsorganisation] Warnung: Baldige Löschung der Prüfungsdaten';
$string['warningmailsubjectthree'] = '[Prüfungsorganisation] Letzte Warnung: Die Prüfungsdaten werden morgen gelöscht';
$string['warningmailcontent'] = 'Alle Prüfungsinformationen der Prüfung "{$a->examname}" im Kurs "{$a->coursename}" werden am {$a->datadeletiondate} gelöscht. Bitte stellen Sie sicher, dass Sie alle relevanten Prüfungsdaten zur weiteren Verwendung exportiert haben. Sie können dafür die Exportfunktionen der {$a->systemname} Prüfungsorganisation nutzen. Am angegebenen Datum werden sämtliche Prüfungsdaten endgültig gelöscht, eine nachrägliche Wiederherstellung der Daten ist ab diesem Zeitpunkt nicht mehr möglich!';
$string['warningmailcontentenglish'] = '<strong>English version</strong>: All information on the exam "{$a->examname}" in course "{$a->coursename}" will be deleted on {$a->datadeletiondate}. Please make sure that you have exported all relevant exam data for further use. To do this, you can use the export functions of the {$a->systemname} exam management. On the specified date, all exam data will be finally deleted, a later recovery of the data is then no longer possible!';
$string['delete_unassigned_custom_rooms'] = 'Verwaiste benutzerdefinierte Prüfungsräume löschen';
