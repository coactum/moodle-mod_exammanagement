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
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//exammanagement_participantsview.mustache - can be seen on /view.php as participant
$string['examinformation'] = 'Information on the exam';
$string['state'] = 'State';
$string['no_participant'] = 'You are not registered for the exam.';
$string['added_to_exam'] = 'You are registered for the exam.';
$string['date'] = 'Date';
$string['no_date_entered'] = 'No date has yet been set for the exam.';
$string['time'] = 'Time';
$string['no_time_entered'] = 'No time has yet been set for the exam.';
$string['room'] = 'Room';
$string['no_room_assigned'] = 'No exam room has been assigned yet.';
$string['seat'] = 'Seat';
$string['no_seat_assigned'] = 'No seat has been assigned yet.';
$string['hint'] = 'Notice';
$string['bonussteps_for_exam'] = 'Bonus grade steps for the exam';
$string['bonussteps_for_exam_added'] = 'You have gained <strong>{$a}</strong> bonus grade step(s) for the exam.';
$string['bonuspoints_for_exam'] = 'Bonus points for the exam';
$string['bonuspoints_for_exam_added'] = 'You have gained <strong>{$a}</strong> bonus points for the exam.';
$string['no_bonus_earned'] = 'no';
$string['totalpoints_achieved'] = 'You have taken the exam and scored <strong>{$a}</strong> points.';
$string['totalpoints_achieved_with_bonus'] = 'Together with the bonus points mentioned above, the exam result is <strong>{$a}</strong> points.';
$string['legal_hint_totalpoints'] = '<strong>Please note:</strong> This is only a preliminary indication of points which may change, for example during the exam review. There is no legal claim to the result shown here.';
$string['exam_review'] = 'Exam review';
$string['exam_review_added'] = 'The correction of the exam has now been completed. On <strong>{$a->examreviewtime}</strong> there will be the exam review in room <strong>{$a->examreviewroom}</strong>.';
$string['examdata_deleted'] = 'The exam that has taken place on <strong>{$a}</strong> is now completed.';

//exammanagement_overview.mustache - can be seen on /view.php as lecturer
$string['maintitle'] = 'Exam management';
$string['view'] = 'Overview';
$string['js_confirm_correction_completion'] = 'This action completes the correction phase. You then have 3 months to export all exam results before they are irretrievably deleted for data protection reasons.';
$string['data_deleted'] = 'For data protection reasons, the exam data of all participants in this exam organization were deleted three months after completion of the correction phase. This exam organization can therefore no longer be used; only the basic data of the exam can still be viewed.';

//exammanagement_overview.mustache phases - can be seen on /view.php as lecturer
$string['phase_one'] = 'Pre-exam organization';
$string['phase_two'] = 'Exam organization';
$string['phase_three'] = 'Exam results';
$string['phase_four'] = 'Post exam';
$string['phase_five'] = 'Exam review (optional)';
$string['exam_appointment'] = 'Exam date';
$string['minimize_phase'] = 'Minimize';
$string['maximize_phase'] = 'Open';
$string['participants_and_results_overview'] = 'Participants & results overview';
$string['exam_rooms'] = 'Exam rooms';
$string['exam_date'] = 'Exam date';
$string['exam_participants'] = 'Exam participants';
$string['exam_tasks'] = 'Exam tasks';
$string['freetext_field'] = 'Textfield for free text';
$string['mediacontent'] = 'Media content';
$string['message_to_participants'] = 'Message to participants';
$string['assigning_places'] = 'Assignment of exam seats';
$string['seatingplan'] = 'Seatingplan';
$string['set_visibility_of_examdate'] = 'Show exam date';
$string['exam_labels'] = 'Exam labels';
$string['set_visibility_of_examrooms_and_places'] = 'Show exam rooms and places';
$string['places'] = 'Places';
$string['participants_lists'] = 'Participants lists';
$string['bonus'] = 'Bonus';
$string['gradingscale'] = 'Gradingscale';
$string['exam_results'] = 'Exam results';
$string['exam_results_overview'] = 'Exam results overview';
$string['set_visibility_of_bonus_and_results'] = 'Show bonus and exam results';
$string['complete_correction'] = 'Grading completion';
$string['toggle_grading_completion'] = 'Toggle grading completion';
$string['points_for_exam_review'] = 'Points for exam review';
$string['results_with_percentages'] = 'Results with percentages';
$string['results_and_statistics'] = 'Results and statistics';
$string['results_for_exam_office'] = 'Results for exam office';
$string['delete_exam_data'] = 'Delete exam data';
$string['date_and_room_exam_review'] = 'Date and room for exam review';
$string['set_visibility_of_exam_review_information'] = 'Show exam review information';
$string['altering_exam_results'] = 'Changing exam results';
$string['export_altered_exam_results'] = 'Export of altered exam results';
$string['toggle_information'] = 'Toggle information';

//exammanagement_overview.mustache - mode export_grades
$string['export_grades'] = 'Export grades';
$string['export_grades_help'] = 'Here, the grading points earned by the course participants can first be imported, converted into grades, and then exported as a text file for the exam office (assigned to the respective matriculation numbers). <br><br>
For this purpose ... <br><br>
1. all affected course participants have to be added <br>
2. a gradingscale must be configured <br>
3. the course grades must first be exported and then imported into the exam organization. <br><br>
Then, after the results have been checked and the preparations completed, the grades assigned to the participants can be exported as a text file along with their matriculation numbers.';
$string['import_grades'] = 'Import grades';
$string['grades'] = 'Course grades';
$string['grades_set'] = 'Grades for <strong>{$a->gradescount} / {$a->participantscount}</strong> participants have been imported yet.';
$string['grades_not_set'] = 'No grades have been imported yet.';
$string['grading_points'] = 'Grades (points)';
$string['revert_grades'] = 'Revert all grades';
$string['import_grades_text'] = 'Grading points achieved by the course participants can be imported here.';
$string['exam_results_overview_grades'] = 'Here the already imported grading points and the grades calculated from them can be viewed.';
$string['complete_preparations'] = 'Complete preparations';
$string['participantsOverview_grades_help'] = 'In this <strong>participant and result overview</strong> all imported participants as well as their grading points and the grades calculated from them can be viewed.';
$string['participantsOverview_grades'] = 'Participants and results list';
$string['importBonus_grades_help']= '<strong>Grading points</strong> (for example earned from exercises) can be imported here.
Exported grades from '. get_config('mod_exammanagement', 'moodlesystemname').': To do this, the gradings from the '. get_config('mod_exammanagement', 'moodlesystemname').' course must first be exported (see <a href="https://docs.moodle.org/35/en/Grade_export" class="alert-link" target="_blank">here</a>). Then you have to open the exported file once and check in which column the points are entered. The name of the column must then be entered in the field provided in the lower section. <br><br>
Finally you have to select the file with the grading points you want to import and then click on the "Import file" button to import the points.';
$string['importBonus_grades'] = 'Import grades';

//exammanagement_overview.mustache states - can be seen on /view.php as lecturer
$string['state_optional'] = 'Optional';
$string['state_required'] = 'Required';
$string['state_success'] = 'Success';
$string['state_notset'] = 'Not set';
$string['state_notpossible_participants_missing'] = 'No participants';
$string['state_notpossible_rooms_missing'] = 'No rooms';
$string['state_notpossible_examtime_missing'] = 'No date';
$string['state_notpossible_assignedplaces_missing'] = 'No seats assigned';
$string['state_notpossible_tasks_missing'] = 'No tasks';
$string['state_notpossible_bonus_missing'] = 'No bonus';
$string['state_notpossible_results_missing'] = 'No results';
$string['state_notpossible_correctioncompleted_missing'] = 'Correction not completed';
$string['state_notpossible_examreviewtime_missing'] = 'No time for exam review';
$string['state_notpossible_examreviewroom_missing'] = 'No room for exam review';
$string['state_notpossible_gradingscale_missing'] = 'No gradingscale';
$string['state_loading'] = 'Loading ...';

//exammanagement_overview.mustache work step texts - can be seen on /view.php as lecturer
$string['important_note'] = 'Please note:';
$string['note'] = 'Note:';
$string['exam_rooms_set'] = '<strong> {$a->roomscount} rooms</strong> with <strong> {$a->totalseatscount} places</strong> in total have been selected as exam rooms:';
$string['exam_rooms_not_set'] = 'No rooms have been selected for the exam yet.';
$string['at'] = 'at';
$string['deleted_room'] = 'Deleted room';
$string['exam_date_set'] = 'The exam takes place on <strong>{$a}</strong>.';
$string['exam_date_not_set'] = 'No date and time have been set for the exam yet.';
$string['exam_participants_set'] = '<strong>{$a}</strong> participants have been added to the exam.';
$string['exam_participants_not_set'] = 'No participants have been added to the exam yet.';
$string['exam_tasks_set'] = '<strong>{$a->taskcount} tasks</strong> with <strong> {$a->totalpoints} points</strong> in total have been added to the exam.';
$string['exam_tasks_not_set'] = 'No exam tasks have been set yet.';
$string['textfield_set'] = 'The textfield contains the following content:';
$string['textfield_not_set'] = 'No content has been entered in the textfield yet.';
$string['message_to_participants_str'] = 'Here a message can be sent to all exam participants as internal notification and email.';
$string['places_assigned'] = '<strong>{$a->assignedplacescount} / {$a->participantscount}</strong> places have already been assigned to participants.';
$string['all_places_assigned'] = 'The assignment of places is now successfully completed.';
$string['not_all_places_assigned_yet'] = 'Some participants have not yet been assigned seats. These are not included in later exported seating plans, participant lists and exam labels';
$string['export_seatingplan_str'] = 'Here the seating plan can be exported as a PDF document sorted by place or by matriculation number.';
$string['information_visible'] = 'The information was made visible to the participants.';
$string['information_not_visible'] = 'The information has not been made visible to the participants yet.';
$string['export_examlabels_str'] = 'Here exam labels can be exported as barcodes.';
$string['export_examlabels_note'] = 'Seats only appear on the exam labels if seats have been assigned to all participants of the exam.';
$string['export_participantslists_str'] = 'Here you can export participant lists sorted by last name or seats as a PDF document.';
$string['export_participantslists_note'] = 'These lists are only intended for internal use by the lecturers and should not be published because of data protection reasons!';
$string['no_exam_date_set_str'] = 'No exam date and no exam rooms have been set yet.';
$string['bonus_set'] = 'Bonus points or grade steps for <strong>{$a->bonuscount} / {$a->participantscount}</strong> participants have been imported yet.';
$string['bonus_not_set'] = 'No bonus points or grade steps have been imported yet.';
$string['gradingscale_set'] = 'A gradingscale has already been configured.';
$string['gradingscale_not_set'] = 'No gradingscale has been configured yet.';
$string['results_set'] = '<strong>{$a->resultscount} / {$a->participantscount}</strong> exam results have been entered yet.';
$string['results_not_set'] = 'No exam results have yet been entered.';
$string['exam_results_overview_str'] = 'All exam results that have been already entered can be viewed and manually modified here.';
$string['complete_correction_str'] = 'The data entered in this exam organization tool are very sensitive and must therefore be deleted for data protection reasons as soon as they are no longer needed. After confirming grading completion by moving the switch, you have three months to export the exam results for further use. After that they will be automatically deleted.';
$string['export_points_examreview_str'] = 'Here you can export the achieved exam points as a PDF document.';
$string['export_points_examreview_str_points'] = 'Here you can export the achieved exam points <strong>(without prior achieved bonus points)</strong> as a PDF document.';
$string['export_results_lists_note'] = 'This list of points is only intended for internal use by the lecturers and should not be published because of data protection reasons!';
$string['export_results_statistics_note'] = 'This file contains all exam information and results, is only intended for internal use by the lecturers and should not be published because of data protection reasons!';
$string['export_results_percentages_str'] = 'Export the results with percentages as a PDF document.';
$string['export_results_percentages_str_points'] = 'Export the results with percentages <strong>(without prior achieved bonus points)</strong> as a PDF document.';
$string['export_results_statistics_str'] = 'Export the results and statistics as an Excel document.';
$string['export_results_text_str'] = 'The results for the exam office can be exported here as a text document.';
$string['data_deletion_date_set'] = 'On <strong>{$a}</strong> all data stored in this instance such as participants, exam information and exam results will be automatically deleted. Therefore, make sure you have exported all important data, such as exam results, for further use via the document export functions.';
$string['date_room_examreview_set'] = 'The exam review will take place on <strong>{$a->examreviewdate}</strong> in room <strong>{$a->examreviewroom}</strong>.';
$string['room_examreview_set'] = 'The exam review takes place in room <strong>{$a}</strong>.';
$string['date_examreview_set'] = 'The exam review takes place on <strong>{$a}</strong>.';
$string['date_room_examreview_not_set'] = 'No date or room have been set for the exam review yet.';
$string['exam_results_altered'] = '<strong>{$a}</strong> exam results have been changed after exam.';
$string['no_examresults_altered'] = 'No exam results have yet been altered after the exam review.';
$string['exam_results_altered_note'] = 'Only those exam results that have been changed since the date entered for the exam review appears here. Changing an exam result after clicking on the button below overwrites all previously saved results for the participant. Therefore, before changing the results, make sure that you have exported the old exam results once for backup purposes using the document export functions in the "After exam" phase.';
$string['export_altered_examresults_str'] = 'Export the results changed after the exam as a text document.';
$string['export_altered_examresults_note'] = 'This button allows the easy export of all exam results changed since the date of the exam review to a file for the exam office. Instead, if you want to export the changed results separately according to the imported participants lists, you can again use the options for exporting the results from the "After exam" phase.';

//exammanagement_overview.mustache work steps buttons - can be seen on /view.php as lecturer
$string['configure_password'] = 'Configure password';
$string['choose_rooms'] = 'Select rooms';
$string['set_date'] = 'Set date';
$string['add_participants'] = 'Add participants';
$string['configure_tasks'] = 'Configure tasks';
$string['edit_textfield'] = 'Edit textfield';
$string['send_groupmessage'] = 'Write message';
$string['assign_places'] = 'Assign seats automatically';
$string['assign_places_manually'] = 'Assign seats manually';
$string['export_seatingplan_place'] = 'Sorted by place';
$string['export_seatingplan_matrnr'] = 'Sorted by matriculation number';
$string['export_barcodes'] = 'Export barcodes';
$string['export_participantslist_names'] = 'Sorted by names';
$string['export_participantslist_places'] = 'Sorted by places';
$string['import_bonus'] = 'Import bonus';
$string['configure_gradingscale'] = 'Configure gradingscale';
$string['add_examresults'] = 'Add results';
$string['check_results'] = 'Check results';
$string['export_as_pdf'] = 'Export PDF';
$string['export_as_excel'] = 'Export excel file';
$string['export_as_text'] = 'Export text file';
$string['examreview_dateroom'] = 'Set date and room for exam review';
$string['change_examresults'] = 'Change exam results';
$string['available_places'] = 'Available places';

//configurePasswordForm.php
$string['configurePassword'] = 'Configure password';
$string['configure_password'] = 'This page allows setting and modifying the password for the exam management';
$string['password'] = 'Password';
$string['reset_password'] = 'Reset password';

//checkPasswordForm.php
$string['checkPassword'] = 'Enter password';
$string['checkPasswordAdmin'] = 'Enter password';
$string['check_password'] = 'A password for this exam organization was set by the lecturer. You have to enter it below to gain access.';
$string['confirm_password'] = 'Confirm password';
$string['reset_password_admin'] = 'Reset password and inform all teachers';
$string['request_password_reset'] = 'Request password reset via support';

//checkPassword.php
$string['wrong_password'] = 'Wrong password. Please retry.';
$string['password_reset_successfull'] = 'The password of the exam organization was successfully reset and all lecturers of the {$a->systemname} course were informed about this via {$a->systemname} message.';
$string['password_reset_failed'] = 'Password reset failed due to missing permissions.';
$string['password_reset_request_successfull'] = 'The reset of the password of the exam organization was successfully applied. As soon as the password has been reset, you and all other lecturers of the {$a->systemname} course will be informed via {$a->systemname} message.';
$string['password_reset_request_failed'] = 'Password reset request failed. Please contact support via e-mail in the usual way.';
$string['password_reset_mailsubject'] = '[{$a->systemname}-Support] Zurücksetzen des Passwortes der Prüfungsorganisation "{$a->name}" im Kurs "{$a->coursename}" erfolgreich';
$string['password_reset_mailtext'] = 'Der {$a->systemname} Support hat wie angefordert das Passwort der Prüfungsorganisation "{$a->name}" im Kurs "{$a->coursename}" zurückgesetzt. Sie können nun ohne Eingabe eines Passwortes auf die Inhalte der Prüfungsorganisation zugreifen und falls gewünscht ein neues Passwort für die Prüfungsorganisation festlegen. <br>Viele Grüße, <br>Ihr {$a->systemname}-Team <br><br> <b>English version:</b> The {$a->systemname} support has resetted the password of the exam organization "{$a->name}" in course "{$a->coursename}". You can now access the contents of the exam organization without entering a password and, if required, define a new password for the exam organization. <br>Greetings, <br>Your {$a->systemname} team';
$string['password_reset_request_mailsubject'] = '{$a->systemname} Prüfungsorganisation: Anforderung eines Passwort-Resets für die Prüfungsorganisation "{$a->name}" im Kurs "{$a->coursename}"';
$string['password_reset_request_mailtext'] = 'Der bzw. die {$a->systemname} Benutzerin {$a->user} hat das Zurücksetzen des Passwortes für die Prüfungsorganisation im Kurs "{$a->coursename}" beantragt. <br> Durch einen Klick auf diesen <b><a href="{$a->url}">Link</a></b> können Sie als in {$a->systemname} angemeldeter Benutzer mit der Rolle Admin, Manager oder Kursverwalter das Passwort der Prüfungsorganisation zurücksetzen. Dadurch können sämtliche Lehrenden des Kurses wieder ohne Eingabe eines Passwortes auf die Inhalt der Prüfungsorganisation zugreifen und werden darüber automatisch per Mail informiert.';

//chooseRoomsForm.php
$string['chooseRooms'] = 'Select exam rooms';
$string['choose_rooms_str'] = 'The following rooms can be chosen as exam rooms.';
$string['export_default_rooms'] = 'Export default rooms';
$string['import_default_rooms'] = 'Import default rooms';
$string['add_custom_room'] = 'Add custom exam room';
$string['add_default_room'] = 'Add default exam room';
$string['roomid'] = 'Room ID';
$string['exam_room'] = 'Room';
$string['description'] = 'Description';
$string['room_type'] = 'Room type';
$string['options'] = 'Options';
$string['no_seatingplan_available'] = 'No seating plan available';
$string['default_room'] = 'Default room';
$string['custom_room'] = 'Custom room';
$string['change_room'] = 'Change room';
$string['delete_room'] = 'Delete room';
$string['delete_defaultroom_confirm'] = 'This action deletes the selected default room. If this room has already been selected by teachers as an exam room, its information is preserved in the corresponding exam organizations for the moment, but it can no longer be selected as a new exam room or used for the (re-) assignment of seats.';
$string['delete_room_confirm'] = 'This action deletes this self created room. Make sure that the room is not currently selected as an exam room.';
$string['places_already_assigned_rooms'] = '<strong>Warning:</strong> Seats in this room have already been assigned to some participants. If this room is now deselected as an exam room, the entire assignment of seats is deleted.';
$string['no_rooms_found'] = 'No rooms found';

//chooseRooms.php
$string['room_deselected_as_examroom'] = 'The room must first be deselected as an exam room.';

//exportDefaultRooms.php
$string['default_exam_rooms'] = 'Default exam rooms';
$string['no_default_rooms'] = 'No default exam rooms available';

//addDefaultRoomsForm.php
$string['addDefaultRooms'] = 'Import default rooms';
$string['import_default_rooms_str'] = 'Here, administrators can import the standard rooms available to all lecturers as possible exam rooms from a text file.';
$string['default_rooms_already_exists'] = '<strong>Warning:</strong> Default rooms have already been imported. These are overwritten by the new import.';
$string['default_rooms_file_structure'] = 'Import of default rooms from text file (.txt). <br><strong>Structure</strong>: One exam room per line. First column system-internal room id (roomname_variant; e.g. Audimax_2), second column user-visible room name (e.g. Audimax), third column user-visible description including number of free and total seats (e.g. 2 free seats, 56 total seats), fourth column for calculating the number of seats required array with the label of each individual seat in json syntax (e.g. ["R/R01/P07", "R/R01/P04", "R/R01/P01"] ), fifth column source code of an SVG file with the room plan to show it to users (if available, otherwise leave empty)';

//addCustomRoomForm.php
$string['addCustomRoom'] = 'Change or add custom room';
$string['change_custom_room_name'] = '<strong>Please note:</strong> If you change the name of an existing room, a new room will be created instead. In this case, the old room must still be deleted manually.';
$string['custom_room_places'] = '<strong>Please note:</strong> The exam room you have created here gets as many seats as you specify below, with the numbering starting at 1 and then incrementing (regardless of the actual number of seats or their numbering in the room). This means that you must ensure that you entered the correct number of seats and you must also adjust any deviations in the numbering of seats yourself.';
$string['customroom_name'] = 'Name of exam room';
$string['customroom_placescount'] = 'Count of places';
$string['customroom_description'] = 'Optional description shown when choosing rooms';
$string['add_room'] = 'Save room';
$string['no_description_new_room'] = 'No description available.';

//editDefaultRoomForm.php
$string['editDefaultRoom'] = 'Edit default room';
$string['edit_defaultroom_str'] = 'Here administrators can edit an existing default exam room or create a new one.';
$string['general'] = 'General information';
$string['roomid_internal'] = 'Room ID (system-internal; roomname_variant, e.g. L1.202_1, Audimax_2; permitted characters: Letters, numbers, periods and underscores)';
$string['defaultroom_name'] = 'Name of the room (user-visible, permitted characters: Letters, numbers, periods, and spaces)';
$string['defaultroom_description'] = 'Description (user-visible, e.g. modeling information such as number of free places between two seats, permitted characters: Letters, numbers, periods, minus and spaces)';
$string['defaultroom_placescount'] = 'Number of seats that can be occupied';
$string['placespreview'] = 'Designations of all seats that can be occupied';
$string['roomplan_available'] = 'Available seating plan';
$string['new_places'] = 'New seats';
$string['edit_places'] = 'Edit seats';
$string['places_mode'] = 'Mode of seats';
$string['placesmode_default'] = 'Default';
$string['placesmode_rows'] = 'Rows';
$string['placesmode_all_individual'] = 'Completely individual';
$string['placesroom'] = 'Number of total seats in the room';
$string['rowscount'] = 'Number of rows in the room';
$string['placesrow'] = 'Seats per row';
$string['placesfree'] = 'Free places between two seats that can be occupied';
$string['one_place_free'] = '1 place free';
$string['two_places_free'] = '2 places free';
$string['rowsfree'] = 'Free rows';
$string['no_row_free'] = 'No rows free';
$string['one_row_free'] = 'One row free';
$string['placesarray'] = 'All seats (designation of all seats separated by comma, permitted characters: Letters, numbers, periods, minus, slash and spaces)';
$string['new_seatingplan'] = 'New seating plan';
$string['defaultroom_svg'] = 'Seating plan (text file (.txt) with the source code of a SVG of the room plan)';

//editDefaultRoom.php
$string['no_editable_default_room'] = 'No editable default room because the room is a custom room created by a lecturer';

//setDateTimeForm.php
$string['setDateTime'] = 'Set exam date and time';
$string['set_date_time_str'] = 'The date and time of the exam can be selected here.';

//viewParticipants.php
$string['viewParticipants'] = 'View participants';
$string['import_participants_from_file_recommended'] = 'Import participants from file (recommended)';
$string['import_course_participants_optional'] = 'Import participants from course (optional)';
$string['view_added_partipicants'] = 'List of all participants added to the exam.';
$string['participants'] = 'Participants';
$string['matriculation_number'] = 'Matriculation number';
$string['course_groups'] = 'Groups in course';
$string['import_state'] = 'State';
$string['state_added_to_exam'] = 'Participant of exam';
$string['participants_with_special_state'] = 'Participants with special state';
$string['state_added_to_exam_no_moodle'] = 'Participant of exam (without {$a->systemname} account)';
$string['state_added_to_exam_no_moodle_help'] = 'This participant must log in to the system at least once to view the exam information in his participant view. He also cannot receive group messages sent via the exam organization and cannot be added to course groups.';
$string['state_added_to_exam_no_course'] = 'Participant of exam (no course participant)';
$string['state_added_to_exam_no_course_help'] = 'This participant must be added to the course before he or she can view the exam information in his or her participant view or be added to course groups.';
$string['delete_participant'] = 'Delete participant';
$string['participant_deletion_warning'] = 'This action deletes the selected exam participant and all results entered for him.';
$string['delete_all_participants'] = 'Delete all participants';
$string['all_participants_deletion_warning'] = 'This action deletes all exam participants and all entered results.';
$string['deleted_user'] = 'User deleted from {$a->systemname}';

//convertToGroup.php
$string['convert_to_group'] = 'Convert to group';
$string['convertToGroup'] = 'Convert to group';
$string['convert_to_group_str'] = 'Here selected or all imported exam participants can be converted into an {$a->systemname} group.';
$string['participants_convertable'] = 'exam participants will be added to the group.';
$string['participants_not_convertable'] = 'exam participants cannot be added to the group.';
$string['groupname'] = 'Groupname';
$string['groupdescription'] = 'Description of the group';
$string['group'] = 'Group';
$string['new_group'] = 'New group';

//addParticipantsForm.php
$string['import_participants_from_file'] = 'Import participants from files';
$string['import_from_text_file'] = 'Import participants from files (entries separated by tabs; first two lines are reserved for exam information and will not be used).';
$string['read_file'] = 'Read files';
$string['addParticipants'] = 'Import participants';
$string['import_new_participants'] = 'Import other participants';
$string['places_already_assigned_participants'] = '<strong>Warning:</strong> Seats have already been assigned.  If new exam participants are added, new seats must be assigned to them.';
$string['newmatrnr'] = 'Users will be added to exam.';
$string['badmatrnr'] = 'Users can not be added to exam (invalid matriculation numbers)!';
$string['oddmatrnr'] = 'Users with warnings will be added to exam.';
$string['existingmatrnr'] = 'Users are already exam participants (no changes)';
$string['deletedmatrnr'] = 'Participants will be deleted.';
$string['add_to_exam'] = 'Add to exam';
$string['select_deselect_all'] = 'Select/deselect all';
$string['no_participants_added_page'] = 'No participants added.';
$string['state_newmatrnr'] = 'New';
$string['state_nonmoodle'] = 'Without {$a->systemname} account';
$string['state_nonmoodle_help'] = 'After his import this participant must log in to the system at least once before he can view the exam information in his participant view. He also cannot receive group messages sent via the exam organization and cannot be added to course groups before.';
$string['state_badmatrnr'] = 'Invalid matriculation number';
$string['state_doubled'] = 'Duplicated matriculation number';
$string['state_no_courseparticipant'] = 'No course participant';
$string['state_no_courseparticipant_help'] = 'After his import this participant must be added to the course first before he or she can view the exam information in his or her participant view or be added to course groups.';
$string['state_existingmatrnr'] = 'Already exam participant';
$string['state_existingmatrnrnocourse'] = 'Already exam participant (no course participant)';
$string['state_existingmatrnrnomoodle'] = 'Already exam participant (without {$a->systemname} account)';
$string['state_to_be_deleted'] = 'Will be deleted';
$string['state_not_in_file_anymore'] = 'Not in file anymore';
$string['state_convertable_group'] = 'Assignable to group';
$string['state_not_convertable_group_moodle'] = 'Not assignable (without {$a->systemname} account)';
$string['state_not_convertable_group_course'] = 'Not assignable (no course participant)';

//addCourseParticipantsForm.php
$string['addCourseParticipants'] = 'Import participants from course';
$string['state_courseparticipant'] = 'Participant of course';
$string['view_added_and_course_partipicants'] = 'List of all course participants and all participants added to the exam.';
$string['deletedmatrnr_no_course'] = 'Participants will be deleted because they are no course participants.';
$string['existingmatrnr_course'] = 'Course participants are already exam participants (no changes)';
$string['course_participant_import_preventing_text_export'] = '<strong>Warning:</strong> It is possible to import the course participants as exam participants, but these participants will later be exported in a separate list for the exam office. Therefore their results maybe can not be reimported correctly. If you want to reimport the exam results you should import the participants using the corresponding exam participant lists.';

//configureTasksForm.php
$string['configureTasks'] = 'Configure tasks';
$string['configure_tasks_text'] = 'Define the quantity of tasks and their maximum score.';
$string['add_remove_tasks'] = 'Add or remove tasks';
$string['task'] = 'Task';
$string['points'] = 'Points';
$string['total'] = 'Total';
$string['results_already_entered'] = '<strong>Warning:</strong> Some exam results have already been entered. After changing the tasks, you should check whether they may need to be updated.';
$string['gradingscale_already_entered'] = '<strong>Warning:</strong> The gradingscale for the exam has already been entered. After changing the tasks, you should check if it needs to be updated.';

//setTextfieldForm.php
$string['setTextfield'] = 'Add textfield';
$string['content_of_textfield'] = 'Content of textfield';
$string['add_text_text'] = 'Any information for the exam can be entered here as <strong>free text</strong>, which is immediately displayed to the participants after saving.';

//sendGroupmessageForm.php
$string['sendGroupmessage'] = 'Send groupmessage';
$string['groupmessages_text'] = 'An email and {$a->systemname} notification with the text added below will be send to <strong>{$a->participantscount}</strong> participants of the exam.';
$string['groupmessages_warning'] = '<strong>Warning: {$a->participantscount}</strong> exam participants have no {$a->systemname} account and will not recieve this message. Please contact them manually via email using the following button:';
$string['send_manual_message'] = 'Write email';
$string['subject'] = 'Subject';
$string['content'] = 'Content';
$string['send_message'] = 'Send message';

//sendGroupmessage.php
$string['mailsubject'] = '[{$a->systemname} - Exam management] {$a->coursename}: {$a->subject}';
$string['mailfooter'] = 'This message was sent via the exam organization in {$a->systemname}. You can find all further information under the following link: <br> {$a->categoryname} -> {$a->coursename} -> Prüfungsorganisation -> {$a->name} <br> {$a->url}';

//assignPlaces.php
$string['assignPlaces'] = 'Assign seats';
$string['assign_places_text'] = 'Here, seats can be assigned automatically or manually to all exam participants.';
$string['revert_places_assignment'] = 'Reset seat assignment';
$string['choose_assignment_mode'] = 'Select assignment mode for rooms and seats';
$string['current_assignment_mode'] = 'Last assignment mode:';
$string['assignment_mode_places'] = 'Mode seat assignment';
$string['assignment_mode_rooms'] = 'Mode room assignment';
$string['mode_places_lastname'] = 'Lastname (alphabetical)';
$string['mode_places_matrnr'] = 'Matriculation number (ascending)';
$string['mode_places_random'] = 'Random';
$string['mode_places_manual'] = 'Manual assignment';
$string['mode_room_ascending'] = 'Ascending (from smallest to largest room)';
$string['mode_room_descending'] = 'Descending (from largest to smallest room)';
//$string['mode_room_equal'] = 'Equal (all rooms are filled equally starting with the largest one)';
$string['all_places_already_assigned'] = '<strong>Warning:</strong> All seats have already been assigned. These are completely overwritten during a reassignment.';
$string['keep_seat_assignment_left'] = 'Keep existing assignment';
$string['keep_seat_assignment_right'] = 'Keep already existing assignments during automatic reassignment. If not checked these will be deleted.';
$string['participants_missing_places'] = 'Some participants have not been assigned a seat yet. Add enough rooms to the exam and repeat the assignment or assign the missing seats manually.';
$string['edited_manually'] = 'Edited manually';

//importBonusForm.php
$string['importBonus'] = 'Import bonus points';
$string['import_bonus_text'] = 'Bonus points achieved by the participants can either be imported directly or converted into bonus grade steps for the exam.';
$string['revert_bonus'] = 'Revert all bonus';
$string['choose_bonus_import_mode'] = 'Select import mode';
$string['bonus_import_mode'] = 'Import mode';
$string['mode_bonussteps'] = 'Bonus grade steps';
$string['mode_bonuspoints'] = 'Bonus points';
$string['import_bonuspoints_text'] = 'In this mode, the imported bonus points are added directly to the points earned by the participants in the exam.';
$string['set_bonussteps'] = 'Set bonus steps';
$string['add_remove_bonusstep'] = 'Add or remove bonus step';
$string['bonusstep'] = 'Bonus step (max 3)';
$string['required_points'] = 'Required points for bonus step';
$string['configure_fileimport'] = 'Configure file import';
$string['import_mode'] = 'Import mode';
$string['moodle_export'] = 'Exported grades from {$a->systemname}';
$string['individual'] = 'Other';
$string['idfield'] = 'Column containing user id (e.g. A, B, C ... ; preselected for exported grades from {$a->systemname})';
$string['pointsfield'] = 'Column containing bonus points (e.g. A, B, C ...)';
$string['import_bonus_from_file'] = 'Import bonus points from excel file; Identificator ({$a->systemname} email adress or matriculation number) and bonus points must fit the chosen column';
$string['bonus_already_entered'] = '<strong>Warning:</strong> Bonus points or bonus grade steps for {$a->bonuscount} participants have already been entered. If new points are now imported for these participants the old values will be replaced through this import.';

//importBonus.php
$string['points_bonussteps_invalid'] = 'Points for bonus steps invalid';

//configureGradingscaleForm.php
$string['configureGradingscale'] = 'Configure gradingscale';
$string['configure_gradingscale_text'] = 'A gradingscale for the exam can be configured here.';
$string['configure_gradingscale_totalpoints'] = 'Number of total points:';

//inputResultsForm.php
$string['inputResults'] = 'Input exam results';
$string['input_results_text'] = 'After entering the matriculation number of a participant, the points achieved by this participant in the exam can be entered here.';
$string['confirm_matrnr'] = 'To confirm the matriculation number you can also press enter/return or tabulator.';
$string['exam_participant'] = 'Exam participant';
$string['matrnr_barcode'] = 'Matriculation number or barcode';
$string['matrnr'] = 'Matriculation number';
$string['participant'] = 'Participant';
$string['exam_state'] = 'Exam state';
$string['exam_points'] = 'Exam points';
$string['not_participated'] = 'Not participated';
$string['fraud_attempt'] = 'Fraud attempt';
$string['ill'] = 'Sick';
$string['max_points'] = 'Maximum points';
$string['save_and_next'] = 'Save and next';
$string['validate_matrnr'] = 'Validate matriculation number';
$string['input_other_matrnr'] = 'Change';
$string['noparticipant'] = 'No valid participant';
$string['invalid_matrnr'] = 'Invalid matriculation number';
$string['invalid_matrnr_format'] = 'Invalid format for matriculation number';
$string['invalid_barcode'] = 'Invalid barcode';

//participantsOverviewForm.php
$string['participants_overview_text'] = 'All participants already added to the exam can be viewed and edited in this list.';
$string['edit'] = 'Edit';
$string['participantsOverview'] = 'Participants and results list';
$string['matriculation_number_short'] = 'Matr. no.';
$string['bonuspoints'] = 'Bonus points';
$string['totalpoints_with_bonuspoints'] = 'Total and bonus points';
$string['totalpoints'] = 'Total points';
$string['result'] = 'Result';
$string['bonussteps'] = 'Bonus steps';
$string['resultwithbonus'] = 'Result with bonus steps';
$string['edit_user'] = 'Edit user';
$string['save_changes'] = 'Save changes';
$string['cancel'] = 'Back to exam organization';
$string['normal'] = 'Normal';
$string['nt'] = 'Not participated';
$string['fa'] = 'Fraud attempt';
$string['ill'] = 'Ill';
$string['available'] = 'Available';
$string['jump_to_end'] = 'Jump to end of table';

//participant list
$string['participantslist'] = 'list of participants';
$string['participantslist_names'] = 'list_of_participants_names)';
$string['participantslist_places'] = 'list_of_participants_places)';
$string['internal_use'] = 'FOR INTERNAL USE ONLY!';
$string['lastname'] = 'Name';
$string['firstname'] = 'Firstname';
$string['matrno'] = 'Matr.-no.';
$string['place'] = 'Place';

//seatingplan
$string['seatingplan'] = 'Seating plan';
$string['total_seats'] = 'Total seats';
$string['lecture_room'] = 'Lecture room';
$string['places_differ'] = 'This plan may differ from the actual seating numeration.';
$string['places_alternative'] = 'In this case use numeration on this plan.';
$string['numbered_seats_usable_seats'] = 'numbered seats = used seats';

// examlabels
$string['examlabels'] = 'exam labels';
$string['required_label_type'] = 'Required label type:';

// exportResultsExamReview.php
$string['pointslist_examreview'] = 'List of points exam review';

// exportResultsPercentages.php
$string['percentages'] = 'Percent';
$string['pointslist_percentages'] = 'List of points percentages';

// exportResultsStatistics.php
$string['examresults_statistics'] = 'Exam results statistics';
$string['examresults_statistics_description'] = 'Exam results statistics as MS Excel file';
$string['examresults_statistics_category'] = 'Exam results statistics';
$string['overview'] = 'Overview';
$string['examname'] = 'Name of exam';
$string['examterm'] = 'Term of exam';
$string['examdate'] = 'Date of exam';
$string['examtime'] = 'Time of exam';
$string['examrooms'] = 'Rooms of exam';
$string['grade'] = 'Grade';
$string['nobonus'] = 'Without bonus';
$string['withbonus'] = 'With bonus grade steps';
$string['inpercent'] = 'in %';
$string['registered'] = 'Registered';
$string['passed'] = 'Passed';
$string['notpassed'] = 'Not passed';
$string['notrated'] = 'Not rated';
$string['tasks_and_boni'] = 'Tasks and boni';
$string['mean'] = 'Mean';
$string['count'] = 'Count';
$string['details'] = 'Details';
$string['points_with_bonus'] = 'Points with bonus';

// exportResultsTextFile.php
$string['results'] = 'Results';
$string['cannot_create_zip_archive'] = 'Error creating zip archive';

// examReviewDateRoomForm.php
$string['examReviewDateRoom'] = 'Set date and room for exam review';
$string['examreview_dateroom_str'] = 'If you are planning an exam review, you can select the date and the room here.';
$string['examreview_date'] = 'Date';
$string['examreview_room'] = 'Room (enter as free text)';

// forms (common)
$string['operation_canceled'] = 'Operation canceled';
$string['operation_successfull'] = 'Operation successfull';
$string['alteration_failed'] = 'Alteration failed';
$string['no_rooms_added'] = 'No exam rooms added yet. Work step not possible';
$string['no_participants_added'] = 'No exam participants added yet. Work step not possible';
$string['no_places_assigned'] = 'No places assigned yet. Work step not possible';
$string['no_tasks_configured'] = 'No tasks configured yet. Work step not possible';
$string['no_results_entered'] = 'No exam results entered yet. Work step not possible';
$string['correction_not_completed'] = 'Marking not completed. Work step not possible';

//helptexts
$string['overview_help']= 'This is the <strong>overview page of the exam organization</strong>. Lecturers and / or their staff can see all necessary and helpful work steps for performing an exam. <br><br>
These are neatly arranged into different phases, which are ordered along a timeline. For each individual step, the processing state is indicated by corresponding symbols, texts and colors. There are mandatory work steps and optional ones, which are helpful but can be left out. As soon as all obligatory steps of one phase have been completed, it automatically closes and the next one opens. However, phases can also be opened and closed manually at any time. <br><br>
Each work step can be opened by clicking on the corresponding button that will appear as soon as all required steps for the worksteps have been completed.<br><br>
The "Configure password" button also allows you to (re)set a password, which must then be entered to access the exam organization. With this you can, for example, prevent your student assistants who supervise your courses from accessing the sensitive contents of the exam organization. <br><br>
<strong>Note:</strong> Students do not have access to this view. Instead, they will see all information of the exam which has been enabled for them in a separate view.';
$string['overview_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['checkPassword_help'] = 'A password for this exam management was set by the lecturer. You have to enter it below to gain access to the content of this module. <br><br> By clicking on the corresponding button, you can request a password reset via support if necessary. If the password has been reset, you and all other lecturers of the '. get_config('mod_exammanagement', 'moodlesystemname').' course will be informed via '. get_config('mod_exammanagement', 'moodlesystemname').' notification.';
$string['checkPassword_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['checkPasswordAdmin_help'] = 'A password for this exam management was set by the lecturer. You have to enter it below to gain access to the content of this module. <br> <br>
Admins can reset the password of the exam organization here if lecturers request this. All lecturers of the '. get_config('mod_exammanagement', 'moodlesystemname').' course will be informed about this via '. get_config('mod_exammanagement', 'moodlesystemname').' message.';
$string['checkPasswordAdmin_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['configurePassword_help'] = 'On this page, you can set or change the password for the exam organization. This password has to be entered by every lecturer of the '. get_config('mod_exammanagement', 'moodlesystemname').' course in order to access the contents of the exam organization.<br><br>
To set a password, it must initially be entered in the first and then confirmed in the second field.<br><br>
Remember to choose your password with sufficient security and especially do not use a password that you already use elsewhere (especially not in the context of university!).<br><br>
By clicking on the button "Reset password" you can revoke the password protection for the exam organization.';
$string['configurePassword_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['chooseRooms_help']= 'On this page you can view the list of all possible <strong>exam rooms</strong> available in the system and select one or more of them as the room for the current exam. <br /> <br />
After clicking on the button "Add custom exam room", you can also add your own exam rooms to the list (and later select them as exam rooms). <br /> <br />
To select a room as an exam room, first click on the box to the left of its name. A click on the button "Select rooms" saves the selected rooms as exam rooms. If a room is already marked after opening the page, it has already been saved as a room for the exam. <br /> <br />
The chosen exam rooms will be used later to assign seats to the participants added to the exam. Their seats will later be displayed to them in their view (as soon as you have made this information visible to the students on the overview page). The seat allocation is also used in documents such as the list of participants or the seating plan. <br /> <br />
A description of each room and the number of places available in it is given in the table. If a seating plan is stored in the system for a room, it can be viewed by pressing the left mouse button over the info icon in the "Seating plan" column. If a room is a self-created custom exam room, it can be edited by clicking on the pencil icon at the right end of the line, while a click on the trash can icon next to it and a subsequent confirmation deletes it (although it must not be selected as an exam room). <br /> <br />
<strong>Please note:</strong>
<ul><li>In order to be able to use the other functions of the '. get_config('mod_exammanagement', 'moodlesystemname').' exam organization, at least one room must be selected here as exam room. In addition, the selected rooms must offer at least as many seats as participants are to take part in the exam.</li>
<li>If an exam room is deselected after participants have been assigned seats in it, the entire seat assignment is deleted and must be repeated. Rooms affected by this are marked with a warning.</li>
<li>Some rooms are listed here several times. These are different models of the same room. "1 free space" means that every 2nd space will be used. "2 places free" means that every 3rd place will be used.</li></ul>
<strong>Attention:</strong> The system does not take the availability of the selected rooms into account. As a lecturer, you must book the rooms in which the exam should take place at the central room administration and clarify that the corresponding rooms are actually available at the time of the exam.';
$string['chooseRooms_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['addCustomRoom_help'] = 'On this page, you as a lecturer can create a <strong>custom exam room</strong> if the room in which you want to hold your exam is not listed within the system. Alternatively, you can also edit an existing exam room you have created yourself.<br><br>
To create a new room, first enter its name. Next, you must specify the number of seats of the room. Note that you must check for yourself how many seats are actually available in the room and that the numbering of the seats in the room created here in the system always starts with 1, regardless of the numbering actually available in the room. This means that you must manually adjust any discrepancies that may occur with the actual seat numbering. Finally, you can enter an optional description of the room. This should contain all important information about the room so you can use the room again later, for example in the next semester, if necessary. Finally, a click on the "Save room" button creates the new exam room.<br><br>
A room created in this way can be selected from the list of available exam rooms and can be used like any other exam room.<br><br>
If, on the other hand, an existing exam room has been selected for editing on the room selection page, it can now be modified here. In this case, the number of seats and the description of the selected room can now be altered and then saved by clicking on "Save room". If the number of seats is reduced, all exam participants still retain their previously assigned seats until you perform the automatic seat assignment again.';
$string['addCustomRoom_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['addDefaultRooms_help']= 'As a '. get_config('mod_exammanagement', 'moodlesystemname').' administrator, you can import a number of <strong>default rooms </strong> here which are after that available to all lecturers as possible rooms when they select their exam rooms. <br><br>
In order to import the default rooms, a correctly structured text file must first be selected in the lower area and then read in by clicking on the corresponding button.<br><br>
The text file to be imported must contain the following information for each exam room, where each line stands for one exam room:
<ul><li>First column: The system-internal room id according to the pattern <i>room_name_variant</i>, for example <i>Audimax_2</i></li>
<li>Second column: The user-visible room name, e.g. <i>Audimax</i></li>
<li>Third column: The user-visible room description including the number of free and total seats, for example <i>2 free seats, 56 total seats</i></li>
<li>Fourth column: An array needed to calculate the number of seats in a room, which contains the name of each individual seat in the room. The array must be written in json syntax, e.g. look like this: <i>["R/R01/P07", "R/R01/P04", "R/R01/P01", ...] </i></li>
<li>Fifth column: If a seating plan for the room is available as a .svg file and this should be displayed to the users, the source code of the SVG file must be in this column, otherwise this column can be left empty</li></ul>
All existing default rooms are overwritten by a new import. The information on any deleted or changed rooms is retained in all exam organizations in which they are currently used. However, deleted rooms can no longer be selected by instructors as new exam rooms or used for the (re)assignment of seats. Changes to the names or seats of default rooms also become only effective in the individual exam organizations after a new assignment of seats.';
$string['addDefaultRooms_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['editDefaultRoom_help']= 'Here, administrators can edit an existing <strong> default exam room </strong> or create a new one.<br><br>
First the basic information of the room is displayed, which also may be entered or edited. This is first the system-internal ID of the room, which is used internally by the plugin to identify the room and should be structured according to the following schema: The room name followed by an underscore followed by the variant of the room, which is particularly relevant for several models of the same room with a different number of free seats between the occupiable seats. For the part of the room name all letters, numbers and also dots are allowed, the room variant after the underscore may only consist of numbers. If an existing room is edited, the room ID cannot be changed. Next comes the name of the room, which is visible to all lecturers and may consist of letters, numbers, dots and spaces. The room description is also visible for the users, should contain information about the selected modelling (one or two free seats between two occupiable seats) and may contain the same characters as the room name. Under this information, if an existing room has been selected for editing, further information about the room is displayed, such as the previous number of available seats and an overview of their designations and (if available) the seating plan stored for the room. <br><br>
If seats should be edited in an existing room, this is possible as soon as the option "Yes" has been selected in the next section under "Edit seats". This is not necessary when creating a new room, in this case you can continue directly with entering the new seats in the "New seats" section. For the filling of the room with seats there are three different modes, which should enable the simple replication of all important modelling types of exam rooms: In the " Default " mode, a number of assignable seats is automatically created for a room until the specified total number of seats in the room is reached, taking into account the specified number of free seats between two assignable seats. The naming of the seats starts with 1 and then counts upwards. If a room is to be filled with 100 total seats and one seat should remain unused between them it would receive a total of 50 seats with the designations 1, 3, 5, ..., 100 that can be assigned in the exam organization. With two places free there would be 34 places with the designations 1, 4, 7, ..., 100. The seat mode "Rows" works similarly, only the number of rows existing in a room as well as the available places per row must be specified here. Each row is then filled with the corresponding number of seats, whereby the specified number of free seats and the number of rows to be left free are taken into account. The seats are named with a combination of row and seat number, for example R01/P01, R01/P03, R01/P05, R02/P01 ... . For all room modelling that cannot be replicated using these two modes, there is a third mode with the name "Completely individual". In this mode, the names of all places can be entered completely free, whereby a comma must always be placed between two seat designations. All letters, numbers, dots, minus signs, slashes and spaces are allowed inside the seat names. This mode is very well suited for more complex seat modelling or for adapting models created with the first two modes. This is useful, for example, if the first or last row of a room has fewer seats than the others due to structural conditions, or if continuous numbered seats are still arranged in rows and every second row is to be left empty. When editing an already existing room, this mode is therefore already preselected, but can of course be replaced by any other mode at any time.<br><br>
Finally, a new room plan can be added for a room. This must be drawn up outside the exam organization and should contain all the seats of the default room. The room plan must be saved as SVG in a text file (.txt), which then can be uploaded in the last section of this page. Please note that the contents of the file with the SVG of the room plan must be carefully checked before uploading, as the plugin cannot detect malicious or incorrect contents in the file at this point. If a file with a room plan was selected, it is saved after a click on "Save room" together with the rest of the specified information. <br><br>
The room thus created or modified can immediately be selected as the exam room by all teachers in their exam organizations. When a name is changed or a seat is changed in an existing exam room that is already used in exam organizations, the name and previous seat assignments remain stored there for the time being. Teachers must therefore reassign seats once before the changes to the room take effect.';
$string['editDefaultRoom_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['setDateTime_help']= 'The <strong>date and time of the exam</strong> can be selected here.<br><br>
The exam date selected here is displayed on the overview page of the exam organization and is used later in the documents generated, such as the list of participants or the exam labels. In addition, it will be displayed to the exam participants in their view as soon as you have made this information visible to the students on the overview page. <br /> <br />
The date and time of the exam should therefore be set here so that the exam organization can be used effectively in '. get_config('mod_exammanagement', 'moodlesystemname').'.';
$string['setDateTime_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['viewParticipants_help']= 'On this page you can view all <strong>exam participants</strong> added to the exam and information such as their profile, matriculation number and any groups assigned to them in '. get_config('mod_exammanagement', 'moodlesystemname').'. <br /> <br />
New participants can also be added to the exam here. There are two ways to do this: <br /> <br />
1. After clicking on the button "Add participants from file", participants can be imported from one or more exam lists. This is the recommended way of importing participants, as only in this way it is possible to export the exam results later according to the number and structure of these imported lists. You should therefore choose this variant if you want to reimport the exam results later.<br>
2. It is also possible to import participants of the '. get_config('mod_exammanagement', 'moodlesystemname').' course as exam participants by clicking on the button "Import participants from course". If this option is selected, the exam results can later only be exported in a single result list, a listwise export and a simple reimport of the exam results is then not possible. It is also not possible to "rewrite" participants who have been imported as course participants later by subsequently importing a list with exam participants. To do this, the participant must first be completely deleted.<br><br>
Adding participants is one of the most important steps in the exam organization. Only if you see at least one added participant here you will later be able to assign seats, enter exam points or export result documents. Students who have not been added as exam participants (even if they are already enrolled in the '. get_config('mod_exammanagement', 'moodlesystemname').' course) also do not have access to the participant view with the exam information and do not receive any notifications send with the messaging function on the overview page of the exam organization.<br /> <br />
If you see a lower part of the table separated by a subheading, you have imported exam participants who do not have a user account in '. get_config('mod_exammanagement', 'moodlesystemname').'. Although these can also be imported from a file, some steps, such as writing a notification, must be done manually for these participants and others (such as viewing the student view for the participants themselves) are completely impossible.<br><br>
It is also possible on this page to delete individual exam participants or all of them that have already been imported. To delete individual participants, simply click on the trash can in the participant´s row. To delete all participants, on the other hand, press the red button below the table. Please note, however, that deleting one or all participants automatically deletes all information stored for them, such as seats or entered exam points, and that this information cannot be restored afterwards.<br><br>
Finally, the button "Convert to group" can be used to convert individual or all participants listed here into a '. get_config('mod_exammanagement', 'moodlesystemname').' group.';
$string['viewParticipants_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['convertToGroup_help']= 'Here selected or all imported exam participants can be converted into an '. get_config('mod_exammanagement', 'moodlesystemname'). ' group. <br /> <br />
To do this, the name and optionally a description of the '.get_config('mod_exammanagement', 'moodlesystemname').' group to be created must be entered in the corresponding form fields. Then all desired participants can be selected in the lower section and then converted to a group by clicking the "Convert to group" button.<br /> <br />
Exam participants who are no course participants or do not have a '.get_config('mod_exammanagement', 'moodlesystemname').' user account are displayed in a separate section and cannot be added to the group. <br /> <br />
Groups created here can be used throughout the course and all it`s activities just like normal groups. After clicking on a group, for example in the participant overview, it can be edited or changed.';
$string['convertToGroup_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['addParticipants_help']= 'On this page you can add <strong>participants</strong> from exam lists to the exam. In this way their results can be exported later again and then can be simply reimported. <br /> <br />
To do this, you first need one or multiple lists with the matriculation numbers of your exam participants. These files can be dragged into the selection area and then be read in by clicking on the corresponding button. <br><br>
On the following page you will see all matriculation numbers read from the files. The state of a matriculation number and whether the corresponding student can be added to the exam are displayed in different sections. <br><br>
In the following the different states are briefly explained:<br>
<ul><li><strong>Bad matriculation number</strong>: The entered matriculation number is invalid because, for example, it contains illegal characters such as letters. It cannot therefore be read in as a participant. The number on the far left of the line indicates the number of the line and the file in which the invalid matriculation number can be found.</li>
<li><strong>Duplicated matriculation number</strong>: The matriculation number occurs several times in the file. However, it can only be read in once as a exam participant in the corresponding section.</li>
<li><strong>New (no course participant)</strong>: The student belonging to this matriculation number is not part of the '. get_config('mod_exammanagement', 'moodlesystemname').' course. He can easily be imported as an exam participant. However, since he cannot view the participant view of the plugin, he must be selected manually by ticking the checkbox in order to exclude the possibility of an error here.</li>
<li><strong>New (without '. get_config('mod_exammanagement', 'moodlesystemname').' account)</strong>: The student belonging to this matriculation number does not yet have an account in '. get_config('mod_exammanagement', 'moodlesystemname').'. This can happen, for example, if the student has never registered in '. get_config('mod_exammanagement', 'moodlesystemname').' before. Although the student can be imported as an exam participant, he or she cannot view the participant view of the exam organization and you cannot reach him or her via the notification function of the exam organization. Therefore you have to check this student here manually.</li>
<li><strong>Will be deleted</strong>: This participant was already imported as an exam participant but is no longer included in the current import files (for example, because he has deregistered from the exam in the meantime). You can now select this participant to remove him from the current exam.</li>
<li><strong>Already exam participant</strong>: This participant has already been imported as an exam participant and is not modified by the current import.</li>
<li><strong>New</strong>: This is a valid participant that can be added to the exam without any problems. All participants in this section are preselected to be added to the exam.</li>
</ul>
All participants to be added to (or removed from) the exam can now be selected either by checking the box next to the name or by checking the "Select/deselect all" box of the respective area. Then press the "Add to exam" button to add the selected participants to the exam.<br><br>
If you have read in a wrong file, you can repeat the import by clicking on the button "Import other participants". The currently loaded participants are not imported but discarded.';
$string['addParticipants_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['addCourseParticipants_help']= 'Here you can import all <strong>'. get_config('mod_exammanagement', 'moodlesystemname').'course participants</strong> as exam participants. <br><br>
All course participants who should be added to the exam must be selected by checking the box next to their name. To select (or deselect) all course participants it is sufficient to click in the corresponding box "Select/deselect all". In the corresponding section, you can also select existing participants who are not course participants. These are then removed from the exam by clicking on the "Add to exam" button at the bottom, while the selected course participants are then added to the exam. For all participants with the state "Already exam participants" nothing changes. <br><br>
If participants are added after seats have already been assigned, their seats need to be assigned.<br><br>
<strong>Please note:</strong> If this variant of participant import is selected, the results of all participants added in this way will be exported later in a single separate list for the exam office, which may make it difficult to reimport them. If you plan to have the exam results reimported, you should add participants to the exam using the appropriate participants lists in the corresponding import.';
$string['addCourseParticipants_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['configureTasks_help']= 'Here you can define the quantity of all <strong>exam tasks</strong> and their maximum points. <br><br>
By clicking the "+" button new tasks can be added to the exam. In the field below the respective task number, you must enter the maximum number of points that can be achieved in the respective task later. This number of points must be positive, but can be a decimal. By clicking on the "-" button, exam tasks can be removed again, but at least one task always remains. <br><br>
The tasks are a central element of the exam organization. They correspond to the tasks which are later available in the actual exam and are required in order to be able to enter the exam results for the participants after the exam. For each task, the points obtained by the participants can then be entered separately, up to a maximum of the maximum number of points specified here for the respective task. The tasks specified here and their maximum number of points are also required for setting the gradingscale and for exporting the exam results.
If the tasks are changed after the exam results have already been entered or after the gradingscale has been set, these must be adapted to the new number or the new maximum of points of the tasks.';
$string['configureTasks_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['setTextfield_help']= 'Any content can be entered here as <strong>free text</strong> for the exam, which is immediately displayed to the participants in their participant view after saving.  <br /> <br />
In this way, the exam participants can e. g. be easily informed about the equipment permitted in the exam. In addition to simple texts, more complex elements such as pictures or even formulas can be used. <br /> <br />
This function is purely optional. If, for example, you do not have any information for the exam participants, you can simply leave the field below empty and click on the "Cancel" button. <br /> <br />
<Strong>Note:</strong> This function is mainly intended for messages that are not time-critical. If, however, you would like to inform the exam participants e. g. about a short-term change of exam rooms on the day before the exam, it is advisable to use the function "Write message to participants" on the overview page instead. In this way, the exam participants will immediately receive an e-mail and will thus be able to see the information entered even if they do not actively look in '. get_config('mod_exammanagement', 'moodlesystemname').'.';
$string['setTextfield_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['sendGroupmessage_help']= 'On this page the subject and content of a <strong>message</strong> can be entered, which will be sent to all </strong> students added as <strong>exam participants</strong> after clicking the button "Send message". <br /> <br />
They receive the message immediately after sending it both as a '. get_config('mod_exammanagement', 'moodlesystemname').' notification and as an e-mail to their university e-mail account and can thus, for example, simply be made aware of short-term changes (such as changes of the exam times or rooms). <br /> <br />
If you have added participants to the exam who do not yet have a '. get_config('mod_exammanagement', 'moodlesystemname').' user account, this will be displayed below. Since these participants will not automatically receive the message written here, you will have to write to them manually by e-mail instead. You can do this, for example, by clicking on the "Write Email" button, which opens your email client and enters the email addresses of the corresponding participants. <br /> <br />
The whole notification function is purely optional, you do not have to use it to send a message to the participants.
<strong>Note:</strong> In order to give the participants more detailed information about the auxiliary means permitted in the exam, for example, the free textfield accessible via the overview page can also be used.';
$string['sendGroupmessage_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['assignPlaces_help']= 'On this page, all exam participants can be assigned <strong>seats</strong> for the exam. These can later be exported in corresponding documents such as the seating plans or exam labels.<br><br>
Seats can be assigned either manually or automatically.<br /> <br />
For <strong>manual assignment</strong>, the corresponding button must first be clicked. After that, a seat in a room can be manually assigned to each participant in the table. The available seats for each room will be displayed. After clicking on the button "Assign seats manually", all changes are saved for participants who have been assigned both a room and a seat.<br /><br />
For <strong>automated assignment</strong>, the assignment mode for the seats must first be selected. The following three alternatives are possible:<br />
1. allocation based on the (alphabetically sorted) last name. This corresponds to the previous automatic assignment.<br />
2. assignment based on the ascending sorted matriculation numbers.<br />
3. random assignment. Repeating this assignment multiple times will always result in a different seating order.<br /><br />
If several rooms have been selected as exam rooms, the assignment mode of the rooms can also be changed. Possible are:<br />
1. ascending allocation - first the smallest room is filled completely and then the next larger room.<br />
2. descending allocation - Here first the largest room is completely filled and then in each case the next smaller. This corresponds to the previous automatic allocation.<br /><br />
After clicking on the button "Assign seats automatically" then the seats are assigned according to the selected settings. By setting the corresponding check mark, it can be specified that existing assignments are kept and only unassigned seats are assigned to participants without a seat. Otherwise, existing assignments will be completely overwritten. Already existing assignments can also be completely reset via the corresponding button.<br><br>
<strong>Note:</strong> It is thus also possible to first assign selected participants certain seats (such as in the first rows) and then automatically assign all remaining seats.';
$string['assignPlaces_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['importBonus_help']= '<strong>Bonus points</strong> (for example earned from exercises) can either be directly imported or converted to <strong>bonus grade steps</strong> for the exam. The directly imported bonus points are later added to the points earned in the exam, while bonus grade steps are added to the final exam grade.
To do this, you must first select the import mode in the upper section. If the mode "Bonus grade steps" is selected here, the number of possible bonus grade steps for the exam must also be defined. A maximum of three bonus grading steps are possible (one grading step would be an improvement from 1.7 to 1.3 i.e.). Therefore, participants can improve their grade by a maximum of one grade in total. After that, for each bonus grade step must be stated, how many points the students must have achieved at least to receive it. <br><br>
In the lower section you can next specify the type of file import. There are two possibilities: <br><br>
1. Exported grades from '. get_config('mod_exammanagement', 'moodlesystemname').': If your students have submitted their exercise sheets via the '. get_config('mod_exammanagement', 'moodlesystemname').' assignment activity and these have been corrected and graded there, the exported grades from '. get_config('mod_exammanagement', 'moodlesystemname').' should be selected here, since in this way all bonus points for the complete '. get_config('mod_exammanagement', 'moodlesystemname').' course can be easily read in. <br>
To do this, the gradings from the '. get_config('mod_exammanagement', 'moodlesystemname').' course must first be exported (see <a href="https://docs.moodle.org/35/en/Grade_export" class="alert-link" target="_blank">here</a>). Then you have to open the exported file once and check in which column the points are entered. The name of the column must then be entered in the field provided in the lower section. <br><br>
2. Individual: If you have not managed your bonus points via the '. get_config('mod_exammanagement', 'moodlesystemname').' assignment activity, you can alternatively select the mode "Other". For this you need an Excel file, in which for each participant affected either the email address stored in '. get_config('mod_exammanagement', 'moodlesystemname').' or the matriculation number are entered in one and the achieved points in another column in a separate line. The name of both the column containing the user indexes of all students and the column containing all bonus points must then be entered in the corresponding fields in the lower section. <br><br>
Finally you have to select the file with the bonus points you want to import and then click on the "Import file" button to import the bonus points. The imported number of bonus grade steps or bonus points will be shown to the participants in their view as soon as this has been activated in the corresponding step of the overview page.';
$string['importBonus_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['configureGradingscale_help']= 'A <strong>gradingscale</strong> for the exam can be configured here.<br><br>
As soon as the exam results have been entered, the gradingscale is used to automatically calculate the exam grades for all participants. If no gradingscale has been configured, the automatic calculation of the exam grades is not possible.<br><br>
The minimum number of points required to reach a step must be specified individually for each single grade. A 70 in the field below 1.0 would therefore mean that a participant has to reach at least 70 points in order to get the grade 1.0.<br><br>
The number of points to be achieved for a grade step can be between 0 and the stated total number of points for all exam tasks, but it must be higher than the number of points required for the previous grade step. For example, more points must be required for achieving the grade 1.0 than for achieving a 1.3. In addition, it is also possible to use desimals as points. If a participant achieves fewer points than which are necessary for 4.0, he or she will receive the grade 5 instead.<br><br>
The gradingscale can be changed at any time (even after the exam results have been entered), in that case the participants grades are automatically adapted to the new gradingscale.';
$string['configureGradingscale_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['inputResults_help']= 'On this page you can enter the <strong>exam results</strong> of the participants.<br><br>
For this purpose, the matriculation number of the participant whose results should be entered must first be validated. There are two ways to do this:<br>
1. You can manually enter the matriculation number of the participant. To do this, click in the "Matriculation number or barcode" field, enter the matriculation number and confirm it by pressing Enter (or Return), Tab or the "Validate matriculation number" button. <br>
2. Alternatively, if you have used exam labels in your exam, you can also use a barcode scanner to enter the exam results more quickly. For this you need a barcode scanner or alternatively a smartphone with a corresponding app. With this you have to scan the barcode on the exam label of a participant, whereby his matriculation number is automatically entered into the field "Matriculation number or barcode" and confirmed immediately. If the automatic entry does not work immediately, you may have to click manually in the field "Matriculation number or barcode" once and then repeat the scan.<br><br>
As soon as a matriculation number has been entered and confirmed, it is checked by the system. If it is the valid matriculation number of a participant added to the exam, the page for entering the exam points is opened, otherwise there is a corresponding error message and the previous page is opened again, where a new matriculation number can be entered or the entry of the incorrect matriculation number can be repeated.<br><br>
In the case of a valid matriculation number, the exam results can now be entered on the page that opens. In the section "Exam participant" you will first see the matriculation number and the name of the selected participant. By clicking on the "Change matriculation number" button below, you can return to the previous page to enter a different matriculation number (e.g. in case of an error). In the "Exam points" section below, you can enter the points earned in each exam task by the selected participant. The corresponding points can be entered directly in the points field of the first task and then be continued in the field of the next task after pressing the tab key. A number between zero and the displayed maximum number of points of the respective task can be entered as the number of points, whereby decimals with up to two places are also permitted. If the participant is subject to a special exam state (e.g. if he has "not participated", committed a "fraud attempt" or was "sick"), this state can be selected in the last section "Exam state" by ticking the corresponding checkbox. This sets the task points to zero, disables the entry of points, and displays the selected state in all later documents (e.g. for export) instead of the result. Removing the check mark at the respective exam state reactivates the option to enter points. If results have already been entered for the participant in the past, both the section on the exam points and the exam state may already be filled in. In this case, the information can now be changed and the changes can then be saved.<br><br>
After a click on the button "Save and next" or after pressing the Enter or Return key, the entered results are then saved and the page is automatically reloaded so that the matriculation number of the next exam participant can be read in (either manually or by barcode scanner).';
$string['inputResults_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['participantsOverview_help']= 'In this <strong>participants and results overview</strong> the information of all imported exam participants and their results can be viewed and edited. <br><br>
The first name, surname and matriculation number of each exam participant will be displayed here in alphabetical order. If a place has already been assigned to a participant, this place and the corresponding room are also displayed in the correspondingly named columns. If exam tasks have already been created and exam results have already been entered for a participant, these are also displayed. In the "Points" column you can see how many points the participant has earned in each individual task, while in the "Total points" column the total number of points is displayed. If no exam tasks have been created yet, a click on the symbol displayed instead in the "Points" column allows you to do this directly. If no gradingscale has been entered yet, this can be done by clicking on the corresponding symbol in the "Result" column, otherwise the exam grade calculated using the gradingscale will be displayed in this column (if results have already been entered for the participant). If the participant has a special state (e.g. if he was ill during the exam or if he was trying to cheat) this will be displayed instead of the exam result. In addition, the columns "Bonus steps" and "Bonus points" show the bonus grade steps and bonus points already achieved by the participant for the exam, while the columns "Total and bonus points" and "Result with bonus steps" show the total number of points and the final score, taking into account the bonus points and bonus steps. <br><br>
In order to edit the information on a participant, simply click on the icon on the right in the line of the respective participant. Then you can edit all information for the participant. For example, the participant can be assigned one of the rooms already selected for the exam and any place in it. Below the field for entering the place, the available places in the selected room are displayed. In the "Points" column, on the other hand, the points achieved by the participant can be entered for each task. Alternatively, if necessary, a special exam state such as "Ill", "Not participated" or "Fraud attempt" can be selected from a dropdown menu, which automatically sets the points to zero and disables the possibility to enter points. Resetting the status to "Normal" allows you to enter points again. You can also manually enter either the bonus points achieved by a participant or his bonus steps. After saving the changes by clicking on the corresponding button, the total score and (if a gradingscale has already been entered) the result will be calculated considering the achieved bonus. <br><br>
For a participants, all these details can be entered or edited at the same time or individually. In this way, this page can not only be used to correct incorrectly entered information, but also to manually enter results for exam participants or to manually assign the desired places to them. In this way, exam results can also be entered for participants without a matriculation number.';
$string['participantsOverview_link'] = get_config('mod_exammanagement', 'additionalressources');
$string['examReviewDateRoom_help']=  'If you are planning an <strong>exam review</strong>, you can select the date and the room for it here. <br><br>
The name of the room can be freely entered in the lower textfield. In this way, you can select rooms that are not stored in the system as exam rooms, such as your office, as room for the exam review. <br><br>
If you change the exam results for the participants after the time of the exam review set on this page, you can simply export them separately for the exam office on the overview page. <br><br>
The information on the date and room of the exam review can later be made visible to the students on the overview page.';
$string['examReviewDateRoom_link'] = get_config('mod_exammanagement', 'additionalressources');

//errors and permissions
$string['missingidandcmid'] = 'Coursemodule-id missing';
$string['nopermissions'] = 'You have no permissions to do this. Action denied.';
$string['ldapnotenabled'] = 'LDAP usage disabled.';
$string['ldapnotconfigured'] = 'No valid LDAP configured.';
$string['ldapconfigmissing'] = 'LDAP is not configured completly. The following elements needed by the plugin have to be specified in the global plugin settings:';
$string['ldapconnectionfailed'] = 'Connection to ldap failed. Please retry or contact the system administrator.';
$string['nomatrnravailable'] = 'No matriculation numbers available because';
$string['not_possible_no_matrnr'] = 'Not possible because no matriculation numbers are available because';
$string['importmatrnrnotpossible'] = 'Import by matriculation number not possible because';
$string['enterresultsmatrnr'] = 'Entering results by matriculation number not possible because';
$string['err_underzero'] = 'Entered number ca not be lower than zero.';
$string['err_toohigh'] = 'Entered value is too high.';
$string['err_novalidinteger'] = 'Entered number has to be a valid.';
$string['err_overmaxpoints'] = 'Entered number exceeds maximal points.';
$string['err_bonusstepsnotcorrect'] = 'One or more bonus steps are invalid.';
$string['err_gradingstepsnotcorrect'] = 'One or more gradingscale steps are invalid.';
$string['err_taskmaxpoints'] = 'Entered number exceeds maximal points of task.';
$string['err_roomsdoubleselected'] = 'Double selection of one room with different configurations';
$string['err_invalidcheckboxid_rooms'] = 'Invalid room ID.';
$string['err_invalidcheckboxid_participants'] = 'Invalid participant ID.';
$string['err_nonvalidmatrnr'] = 'No valid matriculation number.';
$string['err_customroomname_taken'] = 'Roomname already taken';
$string['err_filloutfield'] = 'Please fill out this field';
$string['err_nofile'] = 'Please provide file';
$string['err_noalphanumeric'] = 'Contains invalid chars';
$string['err_js_internal_error'] = 'Internal error. Please retry.';
$string['err_password_incorrect'] = 'Password is not matching. Please enter again.';
$string['err_novalidpassword'] = 'Not a valid password.';
$string['err_examdata_deleted'] = 'The exam data has already been deleted. It is no longer possible to use the exam organization.';
$string['err_already_defaultroom'] = 'Already default room. Try instead room ID';
$string['err_novalidplacescount'] = 'Entered number is no valid places count';
$string['err_nocourseparticipants'] = 'No course participants available yet.';
$string['err_groupname_taken'] = 'Group name already taken.';
$string['err_too_long'] = 'Entered value is too long.';

//universal
$string['modulename'] = 'Exam management';
$string['modulenameplural'] = 'Exam managements';
$string['pluginname'] = 'Exam management';
$string['coursecategory_name_no_semester'] = 'DEFAULT_SEMESTER';

//add new module instance and mod_form.php
$string['modulename_help'] = 'The exam management allows to organize easy exams for a course and makes it possible to manage even large exams with many participants.

In a separate view a lecturer can

* set the basic exam data
* export documents that are useful for the exam, such as seating plans and lists of participants
* enter the exam results for the participants manually or using a barcode scanner
* export all results in various documents for further use (e.g. by the exam office).

The exam participants, on the other hand, see in their own view all the relevant information about the exam, such as the date, their seat or the bonus grade steps achieved for the exam. In addition, the notification function allows an easy and reliable communication with them.';
$string['modulename_link'] = 'https://docs.moodle.org/en/mod/exammanagement';
$string['exammanagement_name'] = 'Name of the exam management';
$string['exammanagement_name_help'] = 'The name of the activity displayed in the course (e.g. "Exam 1").';
$string['exammanagement:enable exam management'] = 'enable exam management';
$string['pluginadministration'] = 'Exam management administration';
$string['security_password'] = 'Security password';
$string['new_password'] = 'New password';
$string['security_password_help'] = 'Setting a security password allows you to restrict access to the exam organization. Other staff users like student tutors have to enter this passwort before they can access the contents of the exam organization.';
$string['confirm_new_password'] = 'Repeat new password';
$string['confirm_new_password_help'] = 'For setting a new password it has to be repeated here.';
$string['old_password'] = 'Current password';
$string['old_password_help'] = 'If an already existing password should be changed you need to enter it here.';
$string['incorrect_password_change'] = 'Incorrect password. Terminated password change';
$string['export_grades_as_exam_results'] = 'Export course grades as exam results';
$string['activate_mode'] = 'Activate grade export mode';
$string['export_grades_as_exam_results_help'] = 'If this option is activated, the course gradings of all participants can easily be exported in a text document with the exam organization. Each grading is assigned to the matriculation number of the respective participant.';

//capabilities
$string['exammanagement:addinstance'] = 'Add new exam organization';
$string['exammanagement:viewinstance'] = 'View exam organization';
$string['exammanagement:viewparticipantspage'] = 'View participants page';
$string['exammanagement:takeexams'] = 'Take exams';
$string['exammanagement:sendgroupmessage'] = 'Send group message to participants';
$string['exammanagement:importdefaultrooms'] = 'Import default rooms';
$string['exammanagement:resetpassword'] = 'Reset password';
$string['exammanagement:requestpasswordreset'] = 'Request password reset';
$string['exammanagement:receivedeletionwarningmessages'] = 'Receive deletion warnings';

//message providers
//$string['messageprovider:exam management messages'] = 'exam management messages';
$string['messageprovider:groupmessage'] = 'Group messages for exam organization';
$string['messageprovider:passwordresetrequest'] = 'Requests to reset the password of exam organizations';
$string['messageprovider:passwordresetmessage'] = 'Confirmation messages of resetting the password of exam organization';
$string['messageprovider:deletionwarningmessage'] = 'Warning messages of exam management for upcoming deletion of exam data';

//privacy
// $string['privacy:metadata:exammanagement'] = 'Contains no personal data. Contains all exams created with the exammanagement plugin and their general exam information instead.';
// $string['privacy:metadata:exammanagement_participants'] = 'Contains all exam participants from all exam managements and their respective personal data.';
// $string['privacy:metadata:exammanagement_temp_part'] = 'Contains all temporary saved potential exam participants from all exam managements and their respective personal data. This potential participants can not always be mapped to a Moodle user and will be deleted once a day via a sheduled task, so no export is possible and no further deletion is needed.';
// $string['privacy:metadata:exammanagement_rooms'] = 'Contains all default and custom exam rooms available in the exam managements.';
// $string['privacy:metadata:exammanagement_participants:exammanagement'] = 'Id of the exam management activity the participant belongs to';
// $string['privacy:metadata:exammanagement_participants:courseid'] = 'Course of the exam management activity the participant belongs to';
// $string['privacy:metadata:exammanagement_participants:categoryid'] = 'Course category id of the exammanagement activity the participant belongs to';
// $string['privacy:metadata:exammanagement_participants:moodleuserid'] = 'Moodle intern user id of the participant';
// $string['privacy:metadata:exammanagement_participants:login'] = 'Login of the participant';
// $string['privacy:metadata:exammanagement_participants:firstname'] = 'First name of the participant (in case of nonmoodle)';
// $string['privacy:metadata:exammanagement_participants:lastname'] = 'Last name of the participant (in case of nonmoodle)';
// $string['privacy:metadata:exammanagement_participants:email'] = 'Mail address of the participant (in case of nonmoodle)';
// $string['privacy:metadata:exammanagement_participants:headerid'] = 'Id of the header of the file the participant is imported from';
// $string['privacy:metadata:exammanagement_participants:roomid'] = 'Id of the room assigned to the participant';
// $string['privacy:metadata:exammanagement_participants:roomname'] = 'Name of the room assigned to the participant';
// $string['privacy:metadata:exammanagement_participants:place'] = 'Place assigned to the participant';
// $string['privacy:metadata:exammanagement_participants:exampoints'] = 'Exam results of the participant as object in json syntax';
// $string['privacy:metadata:exammanagement_participants:examstate'] = 'Exam state of the participant as object in json syntax';
// $string['privacy:metadata:exammanagement_participants:timeresultsentered'] = 'Timestamp of the date when the results where entered';
// $string['privacy:metadata:exammanagement_participants:bonussteps'] = 'Bonusstep of the participant';
// $string['privacy:metadata:exammanagement_participants:bonuspoints'] = 'Bonuspoints of the participant';
// $string['privacy:metadata:exammanagement_temp_part:identifier'] = 'Identifier of the potential participant';
// $string['privacy:metadata:exammanagement_temp_part:line'] = 'Line number of the potential participant in the imported participants list';
// $string['privacy:metadata:exammanagement_rooms:roomid'] = 'Room id (internal)';
// $string['privacy:metadata:exammanagement_rooms:name'] = 'Name of the room';
// $string['privacy:metadata:exammanagement_rooms:description'] = 'Description of the room';
// $string['privacy:metadata:exammanagement_rooms:seatingplan'] = 'Seating plan as scalable vector graphic';
// $string['privacy:metadata:exammanagement_rooms:places'] = 'Places of the room as array in json syntax';
// $string['privacy:metadata:exammanagement_rooms:type'] = 'Room type (default or custom)';
// $string['privacy:metadata:exammanagement_rooms:moodleuserid'] = 'Moodle intern id of user who created custom room';
// $string['privacy:metadata:exammanagement_rooms:misc'] = 'Other config values in json syntax (at the moment only timestamp when default room is created)';
// $string['privacy:metadata:core_message'] = 'The exam management plugin sends messages to users and saves their content in the database. This can either be group messages to the participants with exam information, messages to the support if a teacher wants the support to reset the password of an exam management or a system message that informs the teacher about the upcoming deletion of the sensible exam data of an exam management.';

//settings.php - admin settings
$string['enablepasswordresetrequest'] = 'Enable requesting password reset';
$string['enablepasswordresetrequest_help'] = 'As soon as this function has been activated, all teachers in their exam organizations can request the reset of the passwords set there by clicking on the corresponding button. If a lecturer has done this, all users with the role "Manager" receive an automatically generated message both as internal notification and forwarded to the e-mail address stored in their profile and can then reset the password by clicking on the link contained in this message. This means that all teachers of the exam organization concerned are automatically informed via internal notification and e-mail that the password has been reset and that the contents of the exam organization can be accessed again without entering a password. If this function is not activated, users cannot automatically request the password reset in their exam organization, but managers and administrators can still reset the password of any exam organization.';
$string['moodlesystemname'] = 'Moodle name';
$string['moodlesystemname_help'] = 'The name of the Moodle installation. Will be displayed in the plugin (e.g. in helptexts).';
$string['enableglobalmessage'] = 'Show message when creating an exam organization';
$string['enableglobalmessage_help'] = 'If this option is activated, the following global message will be displayed when creating a new exam organization.';
$string['globalmessage'] = 'Global message';
$string['globalmessage_help'] = 'Text of the global message that will be displayed when creating a new exam organization (if option above is activated).';
$string['enablehelptexts'] = 'Enable intern help texts';
$string['enablehelptexts_help'] = 'If this option is activated, plugin help texts are displayed in all exam organizations.';
$string['additionalressources'] = 'Additional ressources';
$string['additionalressources_help'] = 'The web link entered here is displayed in the plugin internal help texts as a source for further information (if these have been activated for the plugin).';
$string['enableldap'] = 'Use LDAP';
$string['enableldap_help'] = 'Checking this box allows the plugin to use an external LDAP server specified in the system to determine the basic data of the exam participants, such as their matriculation numbers. In order for this to work, the external LDAP server must be available and <a href="https://docs.moodle.org/35/en/LDAP_authentication"><u>configured</u></a> in Moodle. In addition, in the following settings the "distinguished name" (short dn) and the names of all LDAP fields containing the information required by the plugin must be entered. Once this is done, the plugin automatically uses the data stored in the LDAP. Otherwise the corresponding plugin functionalities (e.g. the import of participants, the entering of results by matriculation number or the export of exam labels) are not available.';
$string['ldapdn'] = '"Distinguished name" ("dn")';
$string['ldapdn_help'] = 'The "distinguished name" ("dn") must be entered in this field. It describes the positioning of the entries in your LDAP system. If this field is left empty, the plugin uses the value "contexts" from the global settings of the authentication plugin LDAP server ("auth_ldap"). If neither of these two fields is filled in, the LDAP dependent plugin functions cannot be used.';
$string['ldap_objectclass_student'] = 'LDAP class of the student object';
$string['ldap_objectclass_student_help'] = 'A class name can be entered here which is then used as an additional filter criterion for the participant object in LDAP.';
$string['ldap_field_map_username'] = 'Field username';
$string['ldap_field_map_username_help'] = 'The name of the field in the LDAP system in which the username of the participant is located must be entered here. This username must match the username of the participant in '. get_config('mod_exammanagement', 'moodlesystemname').'. If this field is left empty, the plugin uses the value "field_map_idnumber" from the global settings of the authentication plugin LDAP server ("auth_ldap"). If neither of these two fields is filled in, the LDAP dependent plugin functions cannot be used.';
$string['ldap_field_map_matriculationnumber'] = 'Field matriculation number';
$string['ldap_field_map_matriculationnumber_help'] = 'The name of the field in the LDAP system in which the participants matriculation number is located must be entered here. If this field is not filled in, all plugin functions that require matriculation numbers (e.g. entering results by matriculation number or displaying and exporting matriculation numbers) cannot be used.';
$string['ldap_field_map_firstname'] = 'Feld firstname';
$string['ldap_field_map_firstname_help'] = 'The name of the field in the LDAP system in which the participants given name is located must be entered here. If this field is left empty, the plugin uses the value "field_map_firstname" from the global settings of the authentication plugin LDAP server ("auth_ldap"). If neither of these two fields is filled in, participants that have no valid '. get_config('mod_exammanagement', 'moodlesystemname').' account can not be imported as exam participants.';
$string['ldap_field_map_lastname'] = 'Feld lastname';
$string['ldap_field_map_lastname_help'] = 'The name of the field in the LDAP system in which the participants surname is located must be entered here. If this field is left empty, the plugin uses the value "field_map_lastname" from the global settings of the authentication plugin LDAP server ("auth_ldap"). If neither of these two fields is filled in, participants that have no valid '. get_config('mod_exammanagement', 'moodlesystemname').' account can not be imported as exam participants.';
$string['ldap_field_map_mail'] = 'Feld emailadress';
$string['ldap_field_map_mail_help'] = 'The name of the field in the LDAP system in which the participants mail adress is located must be entered here. If this field is left empty, the plugin uses the value "field_map_email" from the global settings of the authentication plugin LDAP server ("auth_ldap"). If neither of these two fields is filled in, participants that have no valid '. get_config('mod_exammanagement', 'moodlesystemname').' account can not be imported as exam participants.';

//delete_temp_participants.php - task
$string['delete_temp_participants'] = 'Delete temporary saved participants';

//check_participants_without_moodle_account.php - task
$string['check_participants_without_moodle_account'] = 'Check participants without Moodle account';

//delete_old_exam_data.php - task
$string['delete_old_exam_data'] = 'Delete old exam data';
$string['warningmailsubjectone'] = '[Exam organization] Reminder: Future deletion of exam data';
$string['warningmailsubjecttwo'] = '[Exam organization] Warning: Soon deletion of old exam data';
$string['warningmailsubjectthree'] = '[Exam organization] Last warning: Exam data will be deleted tomorrow';
$string['warningmailcontent'] = 'Alle Prüfungsinformationen der Prüfung "{$a->examname}" im Kurs "{$a->coursename}" werden am {$a->datadeletiondate} gelöscht. Bitte stellen Sie sicher, dass Sie alle relevanten Prüfungsdaten zur weiteren Verwendung exportiert haben. Sie können dafür die Exportfunktionen der {$a->systemname} Prüfungsorganisation nutzen. Am angegebenen Datum werden sämtliche Prüfungsdaten endgültig gelöscht, eine nachrägliche Wiederherstellung der Daten ist ab diesem Zeitpunkt nicht mehr möglich!';
$string['warningmailcontentenglish'] = '<strong>English version</strong>: All information on the exam "{$a->examname}" in course "{$a->coursename}" will be deleted on {$a->datadeletiondate}. Please make sure that you have exported all relevant exam data for further use. To do this, you can use the export functions of the {$a->systemname} exam organization. On the specified date, all exam data will be finally deleted, a later recovery of the data is then no longer possible!';

//delete_unassigned_custom_rooms.php - task
$string['delete_unassigned_custom_rooms'] = 'Delete unassigned custom rooms';

//upb_migrate_plugininstanceid_to_exammanagement.php - task
$string['upb_migrate_plugininstanceid_to_exammanagement'] = 'Migrate db field plugininstance to exammanagement (UPB)'; // only for upb migration to pluginversion XXX