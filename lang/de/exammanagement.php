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
$string['partricipants_and_results_overview'] = 'Teilnehmer & Ergebnisübersicht';
$string['exam_rooms'] = 'Prüfungsräume';
$string['exam_date'] = 'Prüfungsdatum';
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
$string['date_and_room_exam_review'] = 'Datum und Raum für die Klausureinsicht';
$string['set_visibility_of_exam_review_information'] = 'Informationen zur Klausureinsicht sichtbar schalten';
$string['altering_exam_results'] = 'Änderung der Prüfungsergebnisse';
$string['export_altered_exam_results'] = 'Export der geänderten Ergebnisse';

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
$string['configure_password'] = 'Passwort konfigurieren';
$string['choose_rooms'] = 'Räume auswählen';
$string['set_date'] = 'Datum festlegen';
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
$string['delete_examdata'] = 'Prüfungsdaten löschen';
$string['examreview_dateroom'] = 'Datum und Räume festlegen';
$string['change_examresults'] = 'Prüfungsergebnisse ändern';

//configurePasswordForm.php
$string['configure_password_str'] = 'Passwort konfigurieren';
$string['configure_password'] = 'Hier kann ein Passwort für die Prüfungsorganisation gesetzt und geändert werden.';
$string['password'] = 'Passwort';
$string['reset_password'] = 'Passwort zurücksetzen';

//checkPasswordForm.php
$string['check_password_str'] = 'Passwort eingeben';
$string['check_password'] = 'Der oder die Dozentin hat für diese Prüfungsorganisation ein Passwort festgelegt. Geben Sie es ein, um Zugriff auf die Inhalte der Prüfungsorganisation zu erhalten.';
$string['confirm_password'] = 'Passwort bestätigen';
$string['reset_password_admin'] = 'Passwort zurücksetzen (nur für Administratoren)';

//checkPassword.php
$string['wrong_password'] = 'Passwort falsch. Bitte erneut versuchen.';
$string['password_reset_successful'] = 'Zurücksetzen des Passwortes erfolgreich.';

//chooseRoomsForm.php
$string['choose_exam_rooms'] = 'Prüfungsräume auswählen';
$string['choose_rooms_str'] = 'Die unten stehenden Räume können als Prüfungsräume gewählt werden.';
$string['import_default_rooms'] = 'Standardräume importieren';
$string['add_custom_room'] = 'Eigenen Prüfungsraum anlegen';
$string['room'] = 'Raum';
$string['description'] = 'Beschreibung';
$string['room_type'] = 'Raumart';
$string['options'] = 'Optionen';
$string['no_seatingplan_available'] = 'Kein Sitzplan verfügbar';
$string['default_room'] = 'Standardraum';
$string['custom_room'] = 'Eigener Raum';
$string['delete_room_confirm'] = 'Durch diese Aktion wird dieser selbst erstellte Raum gelöscht. Stellen Sie sicher, dass der Raum aktuell nicht als Prüfungsraum ausgewählt ist.';
$string['hint_room_modelling'] = '<strong>Hinweis:</strong> Einige Räume sind hier mehrfach aufgeführt. Dabei handelt es sich um unterschiedliche Modellierungen desselben Raumes. "1 Platz frei" bedeutet, dass jeder 2. Platz besetzt wird. "2 Plätze frei" bedeutet, dass jeder 3. Platz besetzt wird.';
$string['places_already_assigned_rooms'] = '<strong>Achtung:</strong> Es wurden bereits einigen Teilnehmenden Sitzplätze in diesem Raum zugewiesen. Falls dieser Raum jetzt als Prüfungsraum abgewählt wird wird die gesamte Sitzplatzzuweisung gelöscht und muss dann neu durchgeführt werden.';
$string['no_rooms_found'] = 'Keine Räume gefunden';

//chooseRooms.php
$string['room_deselected_as_examroom'] = 'Der Raum muss zunächst als Prüfungsraum abgewählt werden.';

// addDefaultRoomsForm.php
$string['import_default_rooms_from_file'] = 'Standardräume aus Datei importieren';
$string['import_default_rooms_str'] = 'Hier können die allen Dozenten als Standardprüfungsräume zur Auswahl stehenden Räume von Administratoren aus einer Text-Datei importiert werden.';
$string['default_rooms_already_exists'] = '<strong>Achtung:</strong> Es wurden bereits Standardräume importiert. Diese werden durch den Import aus einer neuen Datei überschrieben.';

// addCustomRoomForm.php
$string['add_room'] = 'Raum anlegen';
$string['change_room'] = 'Raum ändern';
$string['delete_room'] = 'Raum löschen';
$string['customroom_name'] = 'Name des Raums';
$string['customroom_placescount'] = 'Anzahl der Sitzplätze';
$string['customroom_description'] = 'Optionale Beschreibung für die Anzeige des Raums bei der Raumauswahl';
$string['change_custom_room_name'] = '<strong>Hinweis:</strong> Falls Sie den Namen eines bestehenden Raumes ändern wird stattdessen ein neuer Raum angelegt. In diesem Fall muss der alte Raum noch manuell gelöscht werden.';
$string['custom_room_places'] = '<strong>Hinweis:</strong> Der von Ihnen hier erstellte eigene Prüfungsraum erhält im System so viele Plätze wie von Ihnen unten angegeben werden, wobei die Nummerierung (unabhängig von der im Raum tatsächlich vorhandenen Sitzplatzanzahl oder deren Nummerieung) bei 1 startet. Sie müssen also selbst sicherstellen, dass die von Ihnen angegebene Platzzahl mit den tatsächlich vorhandenen Sitzplätzen übereinstimmt und müssen zudem mögliche Abweichungen bei der Sitzplatznummerierung selbst anpassen.';

//dateTimeForm.php
$string['set_date_time'] = 'Prüfungstermin festlegen';

//showParticipants.php
$string['view_participants'] = 'Teilnehmer ansehen';
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
$string['import_participants'] = 'Teilnehmer hinzufügen';
$string['import_new_participants'] = 'Andere Teilnehmer hinzufügen';
$string['places_already_assigned_participants'] = '<strong>Achtung:</strong> Es wurden bereits Sitzplätze zugewiesen. Falls nun neue Prüfungsteilnehmende hinzugefügt werden müssen diesen noch Sitzplätze zugewiesen werden.';
$string['newmatrnr'] = 'Benutzer werden zur Prüfung hinzugefügt.';
$string['badmatrnr'] = 'Zeilen mit ungültigen Matrikelnummern (Benutzer können nicht zur Prüfung hinzugefügt werden).';
$string['oddmatrnr'] = 'Benutzer mit Warnungen (können trotzdem hinzugefügt werden).';
$string['existingmatrnr'] = 'Benutzer sind bereits Prüfungsteilnehmer (keine Änderungen).';
$string['deletedmatrnr'] = 'Benutzer werden gelöscht.';
$string['add_to_exam'] = 'Zur Prüfung hinzufügen';
$string['no_participants_added_page'] = 'Bisher wurden keine Teilnehmer zur Prüfung hinzugefügt.';
$string['state_newmatrnr'] = 'Neu eingelesen';
$string['state_newmatrnr_no_moodle'] = 'Neu eingelesen (ohne PANDA Benutzerkonto)';
$string['state_badmatrnr'] = 'Ungültige Matrikelnummer';
$string['state_doubled'] = 'Doppelte Matrikelnummer';
$string['state_oddmatrnr_nocourseparticipant'] = 'Neu eingelesen (kein Kursteilnehmer)';
$string['state_existingmatrnr'] = 'Bereits Prüfungsteilnehmer';
$string['state_deletedmatrnr'] = 'Gelöscht';

//addCourseParticipantsForm.php
$string['import_course_participants'] = 'Kursteilnehmer hinzufügen';
$string['state_courseparticipant'] = 'Kursteilnehmer';
$string['view_added_and_course_partipicants'] = 'Liste aller bisher zur Prüfung hinzugefügten Teilnehmer sowie aller Kursteilnehmer.';
$string['course_participant_import_preventing_paul_export'] = '<strong>Achtung:</strong> Der Import der Kursteilnehmer als Prüfungsteilnehmer ist zwar möglich, allerdings werden diese Teilnehmer später beim Ergebnis-Export für das Prüfungsamt in einer eigenen Liste exportiert (und können somit gegebenenfalls nicht vernünftig in PAUL eingetragen werden). Wenn Sie vorhaben die Prüfungsergebnisse in PAUL eintragen zu lassen müssen Sie die Teilnehmer mithilfe der entsprechenden PAUL-Teilnehmerlisten der Prüfung importieren.';

//configureTasksForm.php
$string['configure_tasks_str'] = 'Aufgaben konfigurieren';
$string['configure_tasks_text'] = 'Hier kann die Maximalpunktzahl für jede Aufgabe der Prüfung eingestellt werden.';
$string['add_remove_tasks'] = 'Aufgaben hinzufügen oder entfernen:';
$string['task'] = 'Aufgabe';
$string['points'] = 'Punkte';
$string['total'] = 'Summe';
$string['results_already_entered'] = '<strong>Achtung:</strong> Es wurden bereits Prüfungsergebnisse eingetragen. Prüfen Sie bitte nach dem Ändern der Aufgaben, ob diese eventuell aktualisiert werden müssen.';
$string['gradingscale_already_entered'] = '<strong>Achtung:</strong> Es wurde bereits ein Notenschlüssel eingetragen. Prüfen Sie bitte nach dem Ändern der Aufgaben, ob dieser eventuell angepasst werden muss.';

//textfieldForm.php
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
$string['moodle_export'] = 'Bewertungsexport aus PANDA';
$string['individual'] = 'Individuell';
$string['idfield'] = 'Spalte in der die Benutzeridentifikatoren stehen (z. B. A, B, C ... ; Beim Import von aus PANDA exportierten Bewertungen automatisch gesetzt)';
$string['pointsfield'] = 'Spalte welche die zu wertenden Bonuspunkte enthält (z. B. A, B, C ...)';
$string['import_bonus_from_file'] = 'Bonuspunkte aus Excel-Datei importieren; Benutzeridentifikator (in PANDA hinterlegte E-Mailadresse beim Bewertungsimport oder Matrikelnummer beim individuellen Import und Bonuspunkte müssen in den oben ausgewählten Spalten stehen).';
$string['bonus_already_entered'] = '<strong>Achtung:</strong> Es wurden bereits Bonusnotenschritte für Teilnehmende importiert. Diese werden durch den erneuten Import gelöscht und ersetzt.';

//importBonus.php
$string['points_bonussteps_invalid'] = 'Punkte für Bonusschritte ungültig';

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

//participantsOverviewForm.php
$string['edit'] = 'Bearbeiten';
$string['show_results_str'] = 'Teilnehmer- und Ergebnisübersicht';
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

// examReviewDateRoomForm.php
$string['examreview_dateroom'] = 'Datum und Räume für Klausureinsicht';
$string['examreview_date'] = 'Datum';
$string['examreview_room'] = 'Räume (als Freitext eintragen)';

// forms (common)
$string['operation_canceled'] = 'Vorgang abgebrochen';
$string['operation_successfull'] = 'Vorgang erfolgreich';
$string['alteration_failed'] = 'Änderung fehlgeschlagen';
$string['no_participants_added'] = 'Noch keine Prüfungsteilnehmer hinzugefügt. Arbeitsschritt nicht möglich';
$string['not_all_places_assigned'] = 'Noch nicht alle Sitzplätze zugewiesen. Arbeitsschritt nicht möglich';
$string['correction_not_completed'] = 'Korrektur noch nicht abgeschlossen.';

//helptexts
$string['help'] = 'Hilfe';
$string['helptext_str'] = 'Hilfetext';
$string['helptext_link'] = 'Eine ausführliche Erläuterung aller Elemente und Funktionen der Prüfungsorganisation findet sich im "IMT HilfeWiki" unter dem folgenden Link:';
$string['helptext_open'] = 'Hilfetext öffnen/schließen';
$string['helptext_overview']= 'Dies ist die <strong>Startseite der Prüfungsorganisation</strong>. Lehrende und/oder deren Mitarbeiterinnen & Mitarbeiter können hier alle für das Durchführen einer Prüfung sinnvollen Arbeitsschritte ausführen. <br /><br />
Diese sind übersichtlich in verschiedene Phasen unterteilt, welche entlang eines Zeitstrangs angeordnet sind. Für jeden einzelnen Arbeitsschritt ist der Bearbeitungsstatus durch entsprechende Symbole, Texte und Farben zu erkennen. Es gibt verpflichtende Arbeitsschritte und Optionale, die zwar hilfreich sind, aber auch weggelassen werden können.
Sobald alle verpflichtenden Schritte einer Phase erledigt sind klappt diese automatisch zu und es öffnet sich die Nächste. Phasen können jedoch auch jederzeit manuell geöffnet und zugeklappt werden. <br /><br />
Jeder Arbeitsschritt kann nach einem Klick auf den entsprechenden Button durchgeführt werden. Dieser erscheint, sobald alle für einen Arbeitsschritte nötigen anderen Schritte erfüllt sind. <br /><br />
Durch den Button "Passwort konfigurieren“ können Sie zudem ein Passwort festlegen (oder ändern), welches ab diesem Zeitpunkt für einen Zugriff auf die Prüfungsorganisation eingegeben werden muss. Auf diese Weise können Sie zum Beispiel Ihren studentischen MitarbeiterInnen, die Ihre PANDA Kurse betreuen, den Zugriff auf die sensiblen Inhalte der Prüfungsorganisation entziehen. <br /><br />
<strong>Hinweis:</strong> Studierende haben keinen Zugriff auf diese Ansicht. Sie sehen stattdessen in einer eigenen Ansicht die für sie freigeschalteten Informationen zum Prüfungstermin.';
$string['helptext_checkPassword'] = 'Der oder die Dozentin hat für diese Prüfungsorganisation ein Passwort festgelegt. Geben Sie es ein, um die Inhalte der Prüfungsorganisation ansehen zu können.';
$string['helptext_checkPasswordAdmin'] = 'Der oder die Dozentin hat für diese Prüfungsorganisation ein Passwort festgelegt. Geben Sie es ein, um die Inhalte der Prüfungsorganisation ansehen zu können. <br> <br> Als Administrator können Sie hier auf Wunsch der oder des Lehrenden das Passwort der Prüfungsorganisation zurücksetzen. Alle Lehrenden des PANDA-Kurses werden darüber per PANDA-Nachricht benachrichtigt.';
$string['helptext_configurePassword'] = 'Auf dieser Seite kann ein Passwort für die Prüfungsorganisation gesetzt oder geändert werden. Dieses muss ab dann von jeder oder jedem Lehrenden des PANDA-Kurses eingegeben werden, um auf die Inhalte der Prüfungsorganisation zugreifen zu können. <br><br>
Um ein Passwort zu setzen muss dieses zunächst in das erste Feld eingegeben und dann im zweiten Feld bestätigt werden.<br><br>
Denken Sie daran, ihr Passwort hinreichend sicher zu wählen und nehmen Sie vor allem kein Kennwort, dass Sie bereits anderswo als Passwort verwenden (vor allem nicht im Universitätskontext!).<br><br>
Durch einen Klick auf den Button "Passwort zurücksetzen" können Sie den Passwortschutz für die Prüfungsorganisation wieder aufheben.';
$string['helptext_chooseRooms']= 'Auf dieser Seite kann die Liste aller im System verfügbaren möglichen <strong>Prüfungsräume</strong> angesehen und einer oder mehrere davon als Raum für die aktuelle Prüfung ausgewählt werden. <br /> <br />
Zudem können nach einem Klick auf den Button "Eigenen Prüfungsraum anlegen" auch eigene Prüfungsräume zur Liste hinzugefügt (und anschließend als Prüfungsraum ausgewählt) werden. <br /> <br />
Um einen Raum als Prüfungsraum auszuwählen muss zunächst das Kästchen links neben dessen Namen angeklickt werden. Ein Klick auf den Button „Räume auswählen“ speichert die gewählten Räume als Prüfungsräume. Ist ein Raum nach dem Öffnen der Seite bereits markiert wurde er schon als Raum für die Prüfung gespeichert.<br /> <br />
Die gewählten Prüfungsräume werden später verwendet, um den zur Prüfung hinzugefügten TeilnehmerInnen Sitzplätze zuzuweisen. Ihre Sitzplätze werden den PrüfungsteilnehmerInnen später (sobald Sie diese Information auf der Übersichtsseite für die Studierenden sichtbar geschaltet haben) in deren Ansicht angezeigt. Außerdem wird die Sitzplatzzuweisung in Dokumenten wie der Teilnehmerliste oder dem Sitzplan benötigt. <br /> <br />
Eine Beschreibung des Raumes (und damit meistens auch die Zahl der in Diesem vorhandenen Sitzplätze) steht in der Tabelle. Ist für einen Raum ein Sitzplan im System hinterlegt kann dieser durch das Drücken der linken Maustaste über dem Info-Symbol in der Spalte "Sitzplan“ angesehen werden. <br /> <br />
<strong>Wichtige Hinweise:</strong>
<ul><li>Um die weiteren Funktionen der PANDA Prüfungsorganisation nutzen zu können muss hier mindestens ein Raum als Prüfungsraum ausgewählt werden. Zudem müssen die gewählten Räume mindestens so viele Sitzplätze bieten, wie TeilnehmerInnen an der Prüfung teilnehmen sollen.</li>
<li>Werden neue Räume zur Prüfung hinzugefügt oder Bestehende entfernt, nachdem den TeilnehmerInnen bereits Sitzplätze zugewiesen wurden, muss diese Zuordnung wiederholt werden.</li>
<li>Einige Räume sind hier mehrfach aufgeführt. Dabei handelt es sich um unterschiedliche Modellierungen desselben Raumes. "1 Platz frei" bedeutet, dass jeder 2. Platz besetzt wird. "2 Plätze frei" bedeutet, dass jeder 3. Platz besetzt wird.</li></ul>
<strong>Achtung:</strong> Das System berücksichtigt nicht die Verfügbarkeit der gewählten Räume. Als DozentIn müssen Sie die Räume, in welchen die Prüfung stattfinden soll, bei der zentralen Raumverwaltung der Universität Paderborn buchen und so abklären, dass die entsprechenden Räume auch tatsächlich zum Zeitpunkt der Prüfung verfügbar sind.';
$string['helptext_addCustomRoom'] = 'Hier kann ein eigener Prüfungsraum angelegt werden ...';
$string['helptext_addDefaultRooms']= 'Hier können die Standardräume importiert werden ...';
$string['helptext_setDateTime']= 'Hier können das <strong>Datum</strong> und die <strong>Uhrzeit</strong> der Prüfung ausgewählt werden. <br /> <br />
Der hier gewählte Prüfungstermin wird auf der Übersichtsseite der Prüfungsorganisation angezeigt und später in den erzeugten Dokumenten wie etwa der Teilnehmerliste oder den Klausuretiketten verwendet. <br /> <br />
Zudem wird er den PrüfungsteilnehmerInnen in deren Ansicht angezeigt, sobald Sie diese Informationen auf der Übersichtsseite für die Studierenden sichtbar geschaltet haben. <br /> <br />
<strong>Wichtig:</strong> Das Datum und die Uhrzeit der Prüfung müssen hier gesetzt werden, um die Prüfungsorganisation in PANDA sinnvoll nutzen zu können.';
$string['helptext_showParticipants']= 'Auf dieser Seite können alle zur Prüfung hinzugefügten <strong>Prüfungsteilnehmer</strong> und Informationen wie deren Profil, Matrikelnummer sowie die ihnen gegebenenfalls in PANDA zugewiesenen Gruppen angesehen werden. <br /> <br />
Es können hier zudem neue Teilnehmerinnen zur Prüfung hinzugefügt werden. Dazu gibt es zwei Möglichkeiten: <br /> <br />
1. Es können nach einem Klick auf den Button "Teilnehmer aus Datei hinzufügen" Teilnehmer aus einer oder mehreren, aus PAUL exportierten Prüfungslisten importiert werden. Dies ist der empfohlene Weg des Teilnehmerimportes, da nur auf diese Weise später ein Export der Prüfungsergebnisse der Anzahl und dem Aufbau dieser eingelesenen PAUL-Listen entsprechend möglich ist. Diese Variante sollte also gewählt werden, möchte man später die Prüfungsergebnisse direkt in PAUL eintragen (lassen).<br>
2. Es besteht außerdem die Möglichkeit, nach einem Klick auf den Button "Kursteilnehmer importieren" Teilnehmer des PANDA-Kurses als Prüfungsteilnehmer zu importieren. Wird diese Variante gewählt können die Prüfungsergebnisse später allerdings nur in einer einzigen Ergebnisliste exportiert werden, ein listenweiser Export und ein einfaches anschließendes Eintragen der Prüfungsergebnisse in PAUL ist dann somit nicht möglich. Es besteht zudem auch nicht die Möglichkeit, einmal als Kursteilnehmer importierte Teilnehmer später durch nachträgliches Einlesen einer PAUL-Liste "umzuschreiben". Dafür muss der oder die Teilnehmerin zunächst komplett gelöscht werden.<br><br>
Das Hinzufügen von TeilnehmerInnen ist einer der wichtigsten Arbeitsschritte in der Prüfungsorganisation. Nur wenn Sie hier mindestens einen hinzugefügten Teilnehmemenden sehen können Sie später Sitzplätze zuweisen, Prüfungspunkte eintragen oder Ergebnisdokumente exportieren. Nicht als PrüfungsteilnehmerInnen hinzugefügte Studierende haben (selbst wenn sie bereits im PANDA Kurs eingeschrieben sind) außerdem keinen Zugriff auf die Teilnehmeransicht mit den Prüfungsinformationen und erhalten auch keine Benachrichtigung über die Nachrichtenfunktion auf der Übersichtsseite der Prüfungsorganisation. <br /> <br />
Falls Sie einen durch eine Zwischenüberschrift abgetrennten unteren Teil der Tabelle sehen, dann haben Sie Prüfungsteilnehmer importiert, die keinen Benutzeraccount in PANDA haben. Diese können zwar auch aus einer PAUL-Datei importiert werden, einige Arbeitsschritte wie etwa das Schreiben einer Benachrichtigung müssen für diese Teilnehmer jedoch manuell durchgeführt werden und andere (etwa das Ansehen der Studentenansicht für die Teilnehmer selbst) sind gänzlich unmöglich.<br><br>
Es besteht auf dieser Seite außerdem die Möglichkeit, einzelne oder alle bereits importierte Prüfungsteilnehmer wieder zu löschen. Um einzelne Teilnehmer zu löschen genügt ein Klick auf den Mülleimer in der Zeile des jeweiligen Teilnehmenden, um alle Teilnehmer zu löschen muss hingegen der rote Button unter der Tabelle gedrückt werden. Beachten Sie jedoch, dass durch das Löschen eines oder aller Teilnehmer automatisch alle für diese hinterlegten Informationen wie etwa Sitzplätze oder eingetragene Prüfungspunkte gelöscht werden und dass diese Informationen danach nicht wieder hergestellt werden können.';
$string['helptext_addParticipants']= 'Auf dieser Seite können TeilnehmerInnen aus PAUL-Prüfungslisten zur Prüfung hinzugefügt werden. Auf diese Weise können deren Ergebnisse später wieder listenweise exportiert und dann einfach in PAUL eingetragen werden. <br /> <br />
Dazu benötigen Sie zunächst die aus PAUL exportierte Liste ihrer Prüfungsteilnehmer. Diese Datei können Sie dann im Auswahlbereich auswählen und durch einen Klick auf den entsprechenden Button einlesen lassen. <br><br>
Auf der nun folgenden Seite sehen Sie alle aus der Datei eingelesenen Matrikelnummern. Dabei wird in verschiedenen Bereichen genau aufgeschlüsselt, welchen Status eine Matrikelnummer hat und ob der dazugehörige Studierende zur Prüfung hinzugefügt werden kann. <br><br>
Im Folgenden werden die verschiedenen Stati kurz erklärt:<br>
<ul><li><strong>Ungültige Matrikelnummer</strong>: Die eingegebene Matrikelnummer ist ungültig, weil sie zum Beispiel nicht erlaubte Zeichen wie etwa Buchstaben enthält. Sie kann deshalb auch nicht als Teilnehmer eingelesen werden.</li>
<li><strong>Doppelte Matrikelnummer</strong>: Die Matrikelnummer kommt in der Datei mehrfach vor. Als Prüfungsteilnehmer kann Sie jedoch im entsprechenden Abschnitt nur einmal eingelesen werden.</li>
<li><strong>Neu eingelesen (kein Kursteilnehmer)</strong>: Der zu dieser Matrikelnummer gehörende Studierende ist nicht Teil des PANDA-Kurses. Er kann problemlos als Prüfungsteilnehmer importiert werden. Um jedoch zu vermeiden, dass hier ein Fehler vorliegt, müssen Sie ihn dazu durch Setzen des Hakens manuell auswählen.</li>
<li><strong>Neu eingelesen (ohne PANDA Benutzerkonto)</strong>: Der zu dieser Matrikelnummer gehörende Studierende hat noch keinen Account in PANDA. Dies kann etwa geschehen, wenn er sich noch nie in PANDA angemeldet hat. Der oder die Studierende kann zwar als Prüfungsteilnehmer importiert werden, jedoch kann er dann nicht die Teilnehmeransicht der Prüfungsorganisation betrachten und Sie können ihn auch nicht über die Benachrichtigungssfunktion der Prüfungsorganisation erreichen. Deshalb müssen Sie diesen Studierenden hier manuell anhaken.</li>
<li><strong>Gelöscht</strong>: Dieser Teilnehmer wurde in einer früheren Version der verwendeten PAUL-Liste bereits als Prüfungsteilnehmer importiert, ist in der aktuellen jedoch nicht mehr enthalten (weil er sich zum Beispiel in der Zwischenzeit von der Prüfung in PAUL abgemeldet hat). Durch Auswählen können Sie nun bestimmen, dass dieser Teilnehmer von der aktuellen Prüfung entfernt werden soll.</li>
<li><strong>Bereits Prüfungsteilnehmer</strong>: Dieser Teilnehmer wurde bereits als Prüfungsteilnehmer importiert und wird durch den aktuellen Import nicht verändert.</li>
<li><strong>Neu eingelesen</strong>: Dies ist ein gültiger Teilnehmer, der ohne Probleme zur Prüfung hinzugefügt werden kann. Alle Teilnehmer in diesem Abschnitt sind für das Hinzufügen zur Prüfung vorausgewählt.</li>
</ul>
Alle Teilnehmer, die zur Prüfung hinzugefügt (oder von dieser wieder entfernt) werden sollen können nun ausgewählt werden, indem entweder der Haken in die Box neben dem Namen oder aber im Feld "Alle aus-/abwählen" des jeweiligen Bereiches gesetzt wird. Ein anschließendes Drücken des Buttons "Zur Prüfung hinzufügen" fügt die ausgewählten Teilnehmer dann zur Prüfung hinzu.<br><br>
Für den Import von Teilnehmern aus mehreren Listen können Sie diesen Vorgang nun einfach mehrfach durchführen.';
$string['helptext_addCourseParticipants']= 'Hier können alle im PANDA Kurs eingeschriebenen Kursteilnehmer als Prüfungsteilnehmer importiert werden. <br><br>
Dazu müssen im unteren Abschnitt all jene Teilnehmerinnen ausgewählt werden, die zur Prüfung hinzugefügt werden sollen. Einzelne Teilnehmer können dabei durch einen Klick in das Kästchen neben ihrem Namen ausgewählt werden, zum Aus- (oder ab)wählen aller Kursteilnehmer genügt hingegen ein Klick in das entsprechende Kästchen "Alle aus-/abwählen". Es können zudem im entsprechenden Abschnitt gegebenenfalls bereits vorhandene Prüfungsteilnehmer ohne PANDA-Account ausgewählt werden. Diese werden dann bei einem Klick auf den ganz unten befindlichen Button "Zur Prüfung hinzufügen" von der Prüfung entfernt, während die ausgewählten Kursteilnehmer zur Prüfung hinzugefügt werden. Für alle Teilnehmer mit dem Status "Bereits Prüfungsteilnehmer" ändert sich hingegen nichts. <br><br>
Werden Teilnehmer hinzugefügt, nachdem bereits Sitzplätze zugewiesen wurden, müssen diesen noch Plätze zugewiesen werden.<br><br>
<strong>Achtung:</strong> Wird diese Variante des Teilnehmerimportes gewählt werden die Ergebnisse aller so hinzugefügten Teilnehmer später in einer einzelnen separaten Liste für das Prüfungsamt exportiert, wodurch das Eintragen in PAUL gegebenenfalls schwierig wird. Wenn Sie vorhaben, die Prüfungsergebnisse in PAUL eintragen zu lassen, sollten Sie die Teilnehmer lieber mithilfe der entsprechenden PAUL-Teilnehmerlisten zur Prüfung hinzufügen.';
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
$string['helptext_importBonus']= 'Hier können Bonuspunkte der Prüfungsteilnehmer importiert und in Bonusnotenschritte für die Prüfung umgewandelt werden. Auf diese Weise können zum Beispiel durch die Studierenden bei der Bearbeitung von Übungsaufgaben errungene Bonuspunkte direkt in Bonusnotenschritte für die Klausur umgewandelt werden. <br><br>
Dazu muss zunächst im oberen Abschnitt die Zahl der für die Klausur möglichen Bonusnotenschritte festgelegt werden. Es sind maximal drei Bonusnotenschritte (ein Notenschritt wäre etwa die Verbesserung von 1,7 auf 1,3) möglich, insgesamt können Prüfungsteilnehmer sich also um maximal eine Note verbessern. Für jeden Bonusnotenschritt muss danach zudem angegeben werden, wie viele Punkte die Studierenden zum Erhalten dieses Schrittes mindestens erreicht haben müssen. <br><br>
Im unteren Abschnitt kann als Nächstes die Art des Dateiimportes festgelegt werden. Dazu gibt es zwei Möglichkeiten:<br><br>
1. Bewertungsexport aus PANDA: Haben Ihre Studierenden ihre Übungszettel über die PANDA Aufgabenabgabe abgegeben und wurden diese dort korrigiert und bewertet sollte hier der Bewertungsexport aus PANDA ausgewählt werden, da auf diese Weise sämtliche Bonuspunkte für den kompletten PANDA Kurs unkompliziert eingelesen werden können.<br>
Dazu müssen die Bewertungen aus dem PANDA Kurs zunächst wie <a href="https://hilfe.uni-paderborn.de/Dozent:_Bewertungen_Export_von_Gruppen#Setup_f.C3.BCr_Bewertungen" class="alert-link" target="_blank">hier</a> beschrieben exportiert werden. Danach müssen Sie die exportierte Datei einmal öffnen und nachsehen, in welcher Spalte die Punkte eingetragen sind. Die Bezeichnung der Spalte muss dann im dafür vorgesehenen Feld im unteren Abschnitt eingetragen werden.<br><br>
2. Individuell: Falls Sie ihre Bonuspunkte nicht über die PANDA Aufgabenabgabe verwaltet haben können Sie alternativ den Modus des individuellen Importes auswählen. Für diesen brauchen Sie eine Excel-Datei, bei der für jeden betroffenen Teilnehmenden in einer eigenen Zeile entweder die in PANDA hinterlegte Email-Adresse oder aber die Matrikelnummer in einer und die erreichte Punktzahl in einer anderen Spalte steht. Die Bezeichnung sowohl der Spalte, in der die Benutzerindentfikatoren aller Studierenden steht als auch die der Spalte, die alle Bonuspunktzahlen enthält müssen dann in den entsprechenden Feldern im unteren Abschnitt angegeben werden. <br><br>
Zum Abschluss muss nun noch die einzulesende Datei mit den Bonuspunkten ausgewählt und dann durch einen Klick auf den Button "Datei einlesen" eingelesen werden, um den Bonuspunkteimport durchzuführen.';
$string['helptext_configureGradingscale']= 'Hier kann ein <strong>Notenschlüssel</strong> für die Prüfung konfiguriert werden. <br><br>
Sobald die Prüfungsergebnisse eingetragen wurden wird dieser dazu benutzt, automatisch die Prüfungsnoten aller Teilnehmenden zu errechnen. Wurde kein Notenschlüssel konfiguriert ist die automatische Berechnung einer Note hingegen nicht möglich.<br><br>
Es muss für jeden Notenschritt einzeln angegeben werden, wie viele Punkte für dessen Erreichen mindestens notwendig sind. Eine 70 im Feld unter 1,0 würden demnach bedeuten, dass eine Teilnehmerin mindestens 70 Punkte erreichen muss, um die Note 1,0 zu bekommen).<br><br>
Die zu erreichende Punktzahl für einen Notenschritt kann zwischen 0 und der angegebenen Gesamtpunktezahl aller Prüfungsaufgaben liegen, sie muss allerdings höher sein als die für den Notenschritt davor benötigte Punktzahl. So müssen für das Erreichen einer 1,0 etwa mehr Punkte gefordert sein als für das Erreichen einer 1,3. Dazu ist auch die Nutzung von Kommazahlen als Punkte möglich. Erreicht ein Teilnehmender weniger Punkte als für die 4,0 notwendig sind bekommt er stattdessen die Note 5.<br><br>
Der Notenschlüssel kann jederzeit (auch nach dem Eintragen der Prüfungsergebnisse) geändert werden, die Noten der Prüfungsteilnehmer werden in diesem Fall sofort automatisch an den neuen Notenschlüssel angepasst.';
$string['helptext_inputResults']= 'Hier können die Prüfungsergebnisse eingetragen werden ...';
$string['helptext_participantsOverview']= 'Hier können die eingetragenen Ergebnisse geprüft werden ...';
$string['helptext_examReviewDateRoom']= 'Falls für die Prüfung eine Klausureinsicht stattfinden soll können hier das Datum und der Raum dafür ausgewählt werden. <br><br>
Die Bezeichnung des Raumes kann dabei frei als normaler Text in das untere Formularfeld eingegeben werden. Auf diese Weise können Sie auch nicht im System als Prüfungsraum hinterlegte Räume wie etwa ihr Büro als Klausureinsichtsraum auswählen. <br><br>
Wenn Sie nach dem Zeitpunkt der Klausureinsicht Prüfungsergebnisse für die Teilnehmenden ändern können Sie diese danach einfach auf der Übersichtsseite gesondert für das Prüfungsamt exportieren. <br><br>
Die hier festgelegten Informationen zum Datum und Raum der Klausureinsicht können später auf der Übersichtsseite wieder für die Studierenden sichtbar geschaltet werden.';

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

//universal
$string['modulename'] = 'Prüfungsorganisation';
$string['modulenameplural'] = 'Prüfungsorganisationen';
$string['pluginname'] = 'Prüfungsorganisation';
$string['coursecategory_name_no_semester'] = 'SEMESTERLOS';

//add new module instance and mod_form.php
$string['modulename_help'] = 'Mithilfe der PANDA-Prüfungsorganisation können Sie Prüfungen für Ihren Kurs einfach online organisieren und somit auch Prüfungen mit vielen Teilnehmerinnen und Teilnehmern bequem verwalten.';
$string['exammanagementname'] = 'Prüfungsorganisation';
$string['exammanagement:enable exam management'] = 'Prüfungsorganisation aktivieren';
$string['messageprovider:exam management messages'] = 'Nachrichten zur Prüfungsorganisation';
$string['pluginadministration'] = 'Administration der Prüfungsverwaltung';
$string['security_password'] = 'Passwortschutz';
$string['new_password'] = 'Neues Passwort';
$string['security_password_help'] = 'Durch das Festlegen eines Sicherheitspasswortes können Sie den Zugang zu dieser Prüfungsorganisation gegenüber anderen PANDA-Benutzern (z. B. Ihren studentischen Tutoren) begrenzen. Diese müssen dann zunächst das Passwort eingeben, bevor sie Zugang zu den Inhalten der Prüfungsorganisation erhalten.';
$string['old_password'] = 'Altes Passwort (nur benötigt falls ein bereits gesetztes Passwort geändert werden soll)';
$string['old_password_help'] = 'Falls ein bereits gesetztes Passwort geändert werden soll muss dieses hier eintragen werden.';
$string['incorrect_password_change'] = 'Das alte Passwort ist falsch. Passwortänderung abgebrochen';

//capabilities
$string['exammanagement:addinstance'] = 'Neue Prüfungsorganisation hinzufügen';
$string['exammanagement:viewinstance'] = 'Prüfungsorganisation ansehen';
$string['exammanagement:viewparticipantspage'] = 'Teilnehmeransicht ansehen';
$string['exammanagement:takeexams'] = 'Prüfung ablegen';
$string['exammanagement:sendgroupmessage'] = 'Nachricht an Teilnehmer senden';
$string['exammanagement:addDefaultRooms'] = 'Standardräume importieren';

//delete_old_exam_data.php
$string['delete_old_exam_data'] = 'Alte Prüfungsdaten löschen';
$string['warningmailsubjectone'] = 'Erinnerung: Zukünftige Löschung der Prüfungsdaten';
$string['warningmailsubjecttwo'] = 'Warnung: Baldige Löschung der Prüfungsdaten';
$string['warningmailsubjectthree'] = 'Letzte Warnung: Die Prüfungsdaten werden morgen gelöscht';
$string['warningmailcontentpartone'] = 'Alle Prüfungsinformationen der Prüfung ';
$string['warningmailcontentparttwo'] = 'im Kurs ';
$string['warningmailcontentpartthree'] = 'werden am ';
$string['warningmailcontentpartfour'] = ' gelöscht. Bitte stellen Sie sicher, dass sie alle relevanten Prüfungsdaten zur weiteren Verwendung exportiert haben. Sie können dafür die Exportfunktionen der PANDA Prüfungsorganisation nutzen. Am angegebenen Datum werden sämtliche Prüfungsdaten endgültig gelöscht, eine nachrägliche Wiederherstellung der Daten ist ab diesem Zeitpunkt nicht mehr möglich!';