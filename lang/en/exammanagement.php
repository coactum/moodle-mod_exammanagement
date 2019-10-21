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
$string['no_exam_hint'] = 'No exam notice is available.';
$string['bonus_for_exam'] = 'Bonus grade steps for the exam';
$string['bonus_for_exam_added_one'] = 'For the exam you have gained';
$string['bonus_for_exam_added_two'] = 'bonus grade steps.';
$string['bonus_for_exam_not_added'] = 'No bonus grade steps have been entered for you so far.';
$string['exam_review'] = 'Exam review';
$string['exam_review_one'] = 'The correction of the exam has now been completed. On ';
$string['exam_review_two'] = 'there will be the exam review in room ';
$string['examdata_deleted_one'] = 'The exam that has taken place on ';
$string['examdata_deleted_two'] = 'is now completed.';

//exammanagement_overview.mustache - can be seen on /view.php as lecturer
$string['maintitle'] = 'Exam management';
$string['view'] = 'Overview';
$string['js_confirm_correction_completion'] = 'This action completes the correction phase. You then have 3 months to export all exam results before they are irretrievably deleted for data protection reasons.';
$string['data_deleted'] = 'For data protection reasons, the exam data of all participants in this exam organization were deleted three months after completion of the correction phase. This exam organization can therefore no longer be used; only the basic data of the exam can still be viewed.';

//exammanagement_overview.mustache phases - can be seen on /view.php as lecturer
$string['phase_one'] = 'Before exam';
$string['phase_two'] = 'For exam';
$string['phase_three'] = 'After correction';
$string['phase_four'] = 'After exam';
$string['phase_five'] = 'Exam review (optional)';
$string['exam_appointment'] = 'Exam appointment';
$string['minimize_phase'] = 'Minimize';
$string['maximize_phase'] = 'Open';
$string['participants_and_results_overview'] = 'Participants & results overview';
$string['exam_rooms'] = 'Exam rooms';
$string['exam_date'] = 'Exam date';
$string['exam_participants'] = 'Exam participants';
$string['exam_tasks'] = 'Exam tasks';
$string['freetext_field'] = 'Textfield for free text';
$string['message_to_participants'] = 'Message to participants';
$string['assigning_places'] = 'Assignment of places';
$string['seatingplan'] = 'Seatingplan';
$string['set_visibility_of_examdate'] = 'Set visibility of exam date';
$string['exam_labels'] = 'Exam labels';
$string['set_visibility_of_examrooms_and_places'] = 'Set visibility of exam rooms and places';
$string['places'] = 'Places';
$string['participants_lists'] = 'Participants lists';
$string['bonus_gradesteps'] = 'Bonus grade steps';
$string['gradingscale'] = 'Grading scale';
$string['exam_results'] = 'Exam results';
$string['exam_results_overview'] = 'Exam results overview';
$string['complete_correction'] = 'Correction completion';
$string['points_for_exam_review'] = 'Points for exam review';
$string['results_with_percentages'] = 'Results with percentages';
$string['results_and_statistics'] = 'Results and statistics';
$string['results_for_exam_office'] = 'Results for exam office';
$string['delete_exam_data'] = 'Delete exam data';
$string['date_and_room_exam_review'] = 'Date and room for exam review';
$string['set_visibility_of_exam_review_information'] = 'Set visibility of exam review information';
$string['altering_exam_results'] = 'Altering exam results';
$string['export_altered_exam_results'] = 'Export of altered exam results';

//exammanagement_overview.mustache states - can be seen on /view.php as lecturer
$string['state_optional'] = 'Optional';
$string['state_required'] = 'Required';
$string['state_success'] = 'Success';
$string['state_notset'] = 'Not set';
$string['state_notpossible_participants_missing'] = 'Participants missing';
$string['state_notpossible_rooms_missing'] = 'Rooms missing';
$string['state_notpossible_examtime_missing'] = 'Date missing';
$string['state_notpossible_assignedplaces_missing'] = 'No places assigned';
$string['state_notpossible_tasks_missing'] = 'Tasks missing';
$string['state_notpossible_results_missing'] = 'Results missing';
$string['state_notpossible_correctioncompleted_missing'] = 'Correction not completed';
$string['state_notpossible_examreviewtime_missing'] = 'Time for exam review missing';
$string['state_notpossible_examreviewroom_missing'] = 'Room for exam review missing';
$string['state_notpossible_gradingscale_missing'] = 'Grading scale missing';
$string['state_loading'] = 'Loading ...';

//exammanagement_overview.mustache work step texts - can be seen on /view.php as lecturer
$string['important_note'] = 'Please note:';
$string['note'] = 'Note:';
$string['exam_rooms_set_one'] = '';
$string['exam_rooms_set_two'] = '<strong>rooms</strong> with ';
$string['exam_rooms_set_three'] = '<strong>places</strong> in total have been selected as exam rooms';
$string['exam_rooms_not_set'] = 'No rooms have yet been selected for the exam.';
$string['at'] = 'at';
$string['deleted_room'] = 'Deleted room';
$string['exam_date_set_one'] = 'The exam takes place on ';
$string['exam_date_set_two'] = '';
$string['exam_date_not_set'] = 'No date and time have yet been set for the exam.';
$string['exam_participants_set_one'] = 'participants have been added to the exam.';
$string['exam_participants_not_set'] = 'No participants have yet been added to the exam.';
$string['exam_tasks_set_one'] = '';
$string['exam_tasks_set_two'] = '<strong>tasks</strong> with ';
$string['exam_tasks_set_three'] = '<strong>points</strong> in total have been added to the exam';
$string['exam_tasks_not_set'] = 'No exam tasks have been set yet.';
$string['textfield_set'] = 'The text field contains the following content: ';
$string['textfield_not_set'] = 'No content has been entered for the text field yet.';
$string['message_to_participants_str'] = 'Messages (PANDA notifications) can be sent here to all participants added to the exam.';
$string['places_assigned_one'] = 'places have already been assigned to participants.';
$string['places_assigned_two'] = 'The assignment of places is now successfully completed.';
$string['places_assigned_three'] = 'Some participants still have to be assigned places before you can continue with the further work steps.';
$string['places_assigned_note'] = 'When the automatic assignment of places is performed (again), all existing assignments are overwritten.';
$string['export_seatingplan_str'] = 'Here the seating plan can be exported as a PDF document sorted by place or by matriculation number.';
$string['information_visible'] = 'The information was made visible to the participants.';
$string['information_not_visible'] = 'The information has not yet been made visible to the participants.';
$string['export_examlabels_str'] = 'Here exam labels can be exported as barcodes.';
$string['export_examlabels_note'] = 'Places only appear on the exam labels if places have been assigned to all participants of the exam.';
$string['export_participantslists_str'] = 'Here you can export participant lists sorted by last name or place as a PDF document.';
$string['export_participantslists_note'] = 'These lists are only intended for internal use by the teachers and must not be published for data protection reasons!';
$string['no_exam_date_set_str'] = 'No exam date and no exam rooms have been set yet.';
$string['bonussteps_set_one'] = 'Bonus grade steps for ';
$string['bonussteps_set_two'] = 'participants have been imported yet.';
$string['bonussteps_not_set'] = 'No bonus grade steps have been imported yet.';
$string['gradingscale_set_one'] = 'A grading scale has already been configured.';
$string['gradingscale_not_set'] = 'No grading scale has been configured yet.';
$string['results_set_one'] = '';
$string['results_set_two'] = 'exam results have been entered yet.';
$string['results_not_set'] = 'No exam results have yet been entered.';
$string['exam_results_overview_str'] = 'All exam results already entered can be viewed and manually modified here.';
$string['complete_correction_str'] = 'The data entered in this exam organization are very sensitive and must therefore be deleted for data protection reasons as soon as they are no longer needed. After you have confirmed the completion of the correction by moving the switch, you therefore have three months to export the exam results for further use before they are automatically deleted.';
$string['export_points_examreview_str'] = 'Here you can export the achieved points as a PDF document.';
$string['export_results_lists_note'] = 'This list of points is only intended for internal use by the teachers and must not be published for data protection reasons!';
$string['export_results_percentages_str'] = 'Here you can export the results with percentages as a PDF document.';
$string['export_results_statistics_str'] = 'Here the results and statistics can be exported as an Excel document.';
$string['export_results_paul_str'] = 'The results for the exam office can be exported here as a PAUL-compatible text document.';
$string['delete_data_one'] = 'On';
$string['delete_data_two'] = 'all data stored in this instance such as participants, exam information and exam results will be automatically deleted. Therefore, make sure you have exported all important data, such as exam results, for further use via the document export functions.';
$string['date_room_examreview_set_one'] = 'The exam review will take place on';
$string['date_room_examreview_set_two'] = 'in room ';
$string['date_room_examreview_set_three'] = '';
$string['date_room_examreview_set_four'] = 'The exam review takes place in room ';
$string['date_room_examreview_set_five'] = 'The exam review takes place on ';
$string['date_room_examreview_not_set'] = 'No date or room has yet been set for the exam review.';
$string['exam_results_altered_one'] = '';
$string['exam_results_altered_two'] = 'exam results have been altered after exam.';
$string['no_examresults_altered'] = 'No exam results have yet been altered after the exam review.';
$string['exam_results_altered_note'] = 'Only the number of exam results that have been changed since the date entered for the exam review appears here. Changing an exam result after clicking on the button below overwrites all previously saved results for the participant. Therefore, before changing the results, make sure that you have exported the old exam results once for backup purposes using the document export functions in the "After exam" phase.';
$string['export_altered_examresults_str'] = 'Here you can export the results changed after the exam as a PAUL-compatible text document.';
$string['export_altered_examresults_note'] = 'This button allows the easy export of all exam results changed since the date of the exam review to a file for the PAUL exam office. Instead, if you want to export the changed results separately according to the imported PAUL lists, you can again use the options for exporting the results from the "After exam" phase.';

//exammanagement_overview.mustache work steps buttons - can be seen on /view.php as lecturer
$string['configure_password'] = 'Configure password';
$string['choose_rooms'] = 'Choose rooms';
$string['set_date'] = 'Set date';
$string['add_participants'] = 'Add participants';
$string['configure_tasks'] = 'Configure tasks';
$string['edit_textfield'] = 'Edit textfield';
$string['send_groupmessage'] = 'Write message';
$string['assign_places'] = 'Assign places automatically';
$string['assign_places_manually'] = 'Assign places manually';
$string['export_seatingplan_place'] = 'Sorted by place';
$string['export_seatingplan_matrnr'] = 'Sorted by matriculation number';
$string['export_barcodes'] = 'Export barcodes';
$string['export_participantslist_names'] = 'Sorted by names';
$string['export_participantslist_places'] = 'Sorted by places';
$string['import_bonuspoints'] = 'Import bonuspoints';
$string['configure_gradingscale'] = 'Configure grading scale';
$string['add_examresults'] = 'Add results';
$string['check_results'] = 'Check results';
$string['export_as_pdf'] = 'Export PDF';
$string['export_as_excel'] = 'Export excel file';
$string['export_as_paultext'] = 'Export text file';
$string['examreview_dateroom'] = 'Set date and room for exam review';
$string['change_examresults'] = 'Change exam results';

//configurePasswordForm.php
$string['configurePassword'] = 'Configure password';
$string['configure_password'] = 'This page allows setting and modifying the password for the exam management';
$string['password'] = 'Password';
$string['reset_password'] = 'Reset password';

//checkPasswordForm.php
$string['checkPassword'] = 'Enter password';
$string['check_password'] = 'A password for this exam organization was set by the teacher. You have to enter it below to gain access to the content of this module.';
$string['confirm_password'] = 'Confirm password';
$string['reset_password_admin'] = 'Reset password and inform all teachers';
$string['request_password_reset'] = 'Request password reset via support';

//checkPassword.php
$string['wrong_password'] = 'Wrong password. Please retry.';
$string['password_reset_successfull'] = 'The password of the exam organization was successfully reset and all teachers of the PANDA course were informed about this via PANDA message.';
$string['password_reset_failed'] = 'Password reset failed due to missing permissions.';
$string['password_reset_request_successfull'] = 'The reset of the password of the exam organization was successfully applied for at the support. As soon as the password has been reset, you and all other teachers of the PANDA course will be informed via PANDA message.';
$string['password_reset_request_failed'] = 'Password reset request failed. Please contact support via e-mail in the usual way.';
$string['password_reset_mailsubject'] = '[PANDA-Support] Zurücksetzen des Passwortes der Prüfungsorganisation "{$a->name}" im Kurs "{$a->coursename}" erfolgreich';
$string['password_reset_mailtext'] = 'Der PANDA Support hat wie angefordert das Passwort der Prüfungsorganisation "{$a->name}" im Kurs "{$a->coursename}" zurückgesetzt. Sie können nun ohne Eingabe eines Passwortes auf die Inhalte der Prüfungsorganisation zugreifen und falls gewünscht ein neues Passwort für die Prüfungsorganisation festlegen. <br>Viele Grüße, <br>Ihr PANDA-Team <br><br> <b>English version:</b> The PANDA support has resetted the password of the exam organization "{$a->name}" in course "{$a->coursename}". You can now access the contents of the exam organization without entering a password and, if required, define a new password for the exam organization. <br>Greetings, <br>Your PANDA team';
$string['password_reset_request_mailsubject'] = 'PANDA Prüfungsorganisation: Anforderung eines Passwort-Resets für die Prüfungsorganisation "{$a->name}" im Kurs "{$a->coursename}"';
$string['password_reset_request_mailtext'] = 'Der bzw. die PANDA Benutzerin {$a->user} hat das Zurücksetzen des Passwortes für die Prüfungsorganisation im Kurs "{$a->coursename}" beantragt. <br> Durch einen Klick auf diesen <a href="{$a->url}">Link</a> können Sie als in PANDA angemeldeter Benutzer mit der Rolle Admin, Manager oder IMT-Kursersteller das Passwort der Prüfungsorganisation zurücksetzen. Dadurch können sämtliche Lehrenden des Kurses wieder ohne Eingabe eines Passwortes auf die Inhalt der Prüfungsorganisation zugreifen und werden darüber automatisch per Mail informiert.';

//chooseRoomsForm.php
$string['chooseRooms'] = 'Choose exam rooms';
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
$string['hint_room_modelling'] = '<strong>Please note:</strong> Some rooms are listed here several times. These are different models of the same room. "1 Platz frei" means that every 2nd space will be used. "2 Plätze frei" means that every 3rd place will be used.';
$string['places_already_assigned_rooms'] = '<strong>Warning:</strong> Seats in this room have already been assigned to some participants. If this room is now deselected as an exam room, the entire assignment of places is deleted and must then be carried out again.';
$string['no_rooms_found'] = 'No rooms found';

//chooseRooms.php
$string['room_deselected_as_examroom'] = 'The room must first be deselected as an exam room.';

//exportDefaultRooms.php
$string['default_exam_rooms'] = 'Default exam rooms';
$string['no_default_rooms'] = 'No default exam rooms available';

//addDefaultRoomsForm.php
$string['addDefaultRooms'] = 'Import default rooms';
$string['import_default_rooms_str'] = 'Here, administrators can import the standard rooms available to all lecturers as possible exam rooms from a text file.';
$string['default_rooms_already_exists'] = '<strong>Warning:</strong> Default rooms have already been imported. These are overwritten by this new import.';
$string['default_rooms_file_structure'] = 'Import of default rooms from text file (.txt). <br><strong>Structure</strong>: One exam room per line. First column system-internal room id (roomname_variant; e.g. Audimax_2), second column user-visible room name (e.g. Audimax), third column user-visible description including number of free and total seats (e.g. 2 free seats, 56 total seats), fourth column for calculating the number of seats required array with the label of each individual seat in json syntax (e.g. ["R/R01/P07", "R/R01/P04", "R/R01/P01"] ), fifth column source code of an SVG file with the room plan to show it to users (if available, otherwise leave empty)';

//addCustomRoomForm.php
$string['addCustomRoom'] = 'Change or add custom room';
$string['change_custom_room_name'] = '<strong>Please note:</strong> If you change the name of an existing room, a new room is created instead. In this case, the old room must still be deleted manually.';
$string['custom_room_places'] = '<strong>Please note:</strong> The exam room you have created here gets as many seats as you specify below, with the numbering starting at 1 and then incrementing (regardless of the actual number of seats or their numbering in the room). This means that you must ensure that the number of seats you enter corresponds to the actual number of seats available, and you must also adjust any deviations in the numbering of seats yourself.';
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
$string['roomplan_available'] = ' Available seating plan';
$string['new_places'] = 'New seats';
$string['edit_places'] = 'Edit seats';
$string['places_mode'] = 'Mode of seats';
$string['placesmode_default'] = 'Default';
$string['placesmode_rows'] = 'Rows';
$string['placesmode_all_individual'] = 'Completely individual';
$string['placesroom'] = ' Number of total seats in the room';
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
$string['participants_without_panda_account'] = 'Participants of exam without PANDA account';
$string['state_added_to_exam_no_moodle'] = 'Participant of exam (without PANDA account)';
$string['delete_participant'] = 'Delete participant';
$string['participant_deletion_warning'] = 'This action deletes the selected exam participant and all results entered for him.';
$string['delete_all_participants'] = 'Delete all participants';
$string['all_participants_deletion_warning'] = 'This action deletes all exam participants and all results entered for them.';
$string['deleted_user'] = 'User deleted from PANDA';

//addParticipantsForm.php
$string['import_participants_from_file'] = 'Import participants from file';
$string['import_from_paul_file'] = 'Import participants from paul file (entries separated by tabs; first two lines with exam information) and add them to course.';
$string['read_file'] = 'Read file';
$string['addParticipants'] = 'Import participants';
$string['import_new_participants'] = 'Import other participants';
$string['places_already_assigned_participants'] = '<strong>Warning:</strong> Seats have already been assigned.  If new exam participants are added, new seats must be assigned to them.';
$string['newmatrnr'] = 'Users will be added to exam.';
$string['badmatrnr'] = 'Lines with invalid matriculation numbers (Users can not be added to exam).';
$string['oddmatrnr'] = 'Users with warnings (can still be added as participants).';
$string['existingmatrnr'] = 'Users are already exam participants (no changes)';
$string['deletedmatrnr'] = 'Participants will be deleted.';
$string['add_to_exam'] = 'Add to exam';
$string['select_deselect_all'] = 'Select/deselect all';
$string['no_participants_added_page'] = 'No participants added.';
$string['state_newmatrnr'] = 'New';
$string['state_nonmoodle'] = 'Without PANDA account';
$string['state_badmatrnr'] = 'Bad matriculation number';
$string['state_doubled'] = 'Duplicated matriculation number';
$string['state_no_courseparticipant'] = 'No course participant';
$string['state_existingmatrnr'] = 'Already exam participant';
$string['state_to_be_deleted'] = 'Will be deleted';
$string['state_not_in_file_anymore'] = 'Not in file anymore';

//addCourseParticipantsForm.php
$string['addCourseParticipants'] = 'Import participants from course';
$string['state_courseparticipant'] = 'Participant of course';
$string['view_added_and_course_partipicants'] = 'List of all course participants and all participants added to the exam.';
$string['deletedmatrnr_no_course'] = 'Participants will be deleted because they are no course participants.';
$string['existingmatrnr_course'] = 'Course participants are already exam participants (no changes)';
$string['course_participant_import_preventing_paul_export'] = '<strong>Warning:</strong> It is possible to import the course participants as exam participants, but these participants will later be exported in a separate list for the exam office. Their results can therefore maybe not be entered correctly in PAUL. If you intend to have the exam results entered in PAUL, you should import the participants using the corresponding PAUL participant lists of the exam.';

//configureTasksForm.php
$string['configureTasks'] = 'Configure Tasks';
$string['configure_tasks_text'] = 'Here you can define the number and the maximum points of all exam tasks.';
$string['add_remove_tasks'] = 'Add or remove tasks:';
$string['task'] = 'Task';
$string['points'] = 'Points';
$string['total'] = 'Total';
$string['results_already_entered'] = '<strong>Warning:</strong> Some exam results have already been entered. After changing the tasks, you should check whether they may need to be updated.';
$string['gradingscale_already_entered'] = '<strong>Warning:</strong> The grading scale for the exam has already been entered. After changing the tasks, you should check whether it may need to be updated.';

//setTextfieldForm.php
$string['setTextfield'] = 'Add textfield';
$string['content_of_textfield'] = 'Content of textfield';
$string['add_text_text'] = 'Any content can be entered here as <strong>free text</strong> for the exam, which is immediately displayed to the participants in their participant view after saving.';

//sendGroupmessageForm.php
$string['sendGroupmessage'] = 'Send groupmessage';
$string['groupmessages_text_1'] = 'An email with the text added below will be send to ';
$string['groupmessages_text_2'] = ' participants of the exam.';
$string['groupmessages_warning_1'] = 'Warning: ';
$string['groupmessages_warning_2'] = ' exam participants have no PANDA account and will not recieve this message. Please contact them manually via email using the following button:';
$string['send_manual_message'] = 'Write email';
$string['subject'] = 'Subject';
$string['content'] = 'Content';
$string['send_message'] = 'Send message';

//assignPlaces.php
$string['participants_missing_places'] = 'Some participants have not yet been assigned a place. Add enough rooms to the exam and repeat the assignment or assign the missing places manually.';

//importBonusForm.php
$string['importBonus'] = 'Import bonus points';
$string['import_bonus_text'] = 'Bonus points achieved by the participants can be imported here and converted to bonus steps for the exam.';
$string['set_bonussteps'] = 'Set bonus steps';
$string['add_remove_bonusstep'] = 'Add or remove bonus step:';
$string['bonusstep'] = 'Bonus step (max 3)';
$string['required_points'] = 'Required points for bonus step';
$string['configure_fileimport'] = 'Configure file import';
$string['import_mode'] = 'Import mode';
$string['moodle_export'] = 'Exported grades from PANDA';
$string['individual'] = 'Other';
$string['idfield'] = 'Column containing user id (e.g. A, B, C ... ; preselected for exported grades from PANDA)';
$string['pointsfield'] = 'Column containing bonus points (e.g. A, B, C ...)';
$string['import_bonus_from_file'] = 'Import bonus points from excel file; Identificator (PANDA email adress or matriculation number) and bonus points must fit the chosen column.';
$string['bonus_already_entered'] = '<strong>Warning:</strong> Bonus points for {$a->bonuscount} participants have already been entered. If new points are now imported for these participants the old values will be replaced through this import.';

//importBonus.php
$string['points_bonussteps_invalid'] = 'Points for bonus steps invalid';

//configureGradingscaleForm.php
$string['configureGradingscale'] = 'Configure grading scale';
$string['configure_gradingscale_totalpoints'] = 'Number of total points';

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
$string['ill'] = 'Ill';
$string['max_points'] = 'Maximum points';
$string['save_and_next'] = 'Save and next';
$string['validate_matrnr'] = 'Validate matriculation number';
$string['input_other_matrnr'] = 'Change matriculation number';
$string['noparticipant'] = 'No valid participant';
$string['invalid_matrnr'] = 'Invalid matriculation number';
$string['invalid_barcode'] = 'Invalid barcode';

//participantsOverviewForm.php
$string['participants_overview_text'] = 'All participants already added to the exam can be viewed and edited in this list.';
$string['edit'] = 'Edit';
$string['participantsOverview'] = 'Participants and results list';
$string['matriculation_number_short'] = 'Matr. no.';
$string['totalpoints'] = 'Total points';
$string['result'] = 'Result';
$string['bonussteps'] = 'Bonus steps';
$string['resultwithbonus'] = 'Result with bonus';
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
$string['examresults_statistics_description'] = 'Exam results statistics as ms excel file';
$string['examresults_statistics_category'] = 'Exam results statistics';
$string['overview'] = 'Overview';
$string['examname'] = 'Name of exam';
$string['examterm'] = 'Term of exam';
$string['examdate'] = 'Date of exam';
$string['examtime'] = 'Time of exam';
$string['examrooms'] = 'Rooms of exam';
$string['grade'] = 'Grade';
$string['nobonus'] = 'No bonus';
$string['withbonus'] = 'With bonus';
$string['inpercent'] = 'in %';
$string['registered'] = 'Registered';
$string['passed'] = 'Passed';
$string['notpassed'] = 'Not passed';
$string['notrated'] = 'Not rated';
$string['tasks_and_boni'] = 'Tasks and boni';
$string['mean'] = 'Mean';
$string['count'] = 'Count';
$string['details'] = 'Details';

// exportResultsPAULFile.php
$string['results'] = 'Results';
$string['cannot_create_zip_archive'] = 'Error creating zip archive';

// examReviewDateRoomForm.php
$string['examReviewDateRoom'] = 'Set date and room for exam review';
$string['examreview_dateroom_str'] = 'If you are planning an exam review, you can select the date and the room for it here.';
$string['examreview_date'] = 'Date';
$string['examreview_room'] = 'Room (enter as free text)';

// forms (common)
$string['operation_canceled'] = 'Operation canceled';
$string['operation_successfull'] = 'Operation successfull';
$string['alteration_failed'] = 'Alteration failed';
$string['no_rooms_added'] = 'No exam rooms added yet. Work step not possible';
$string['no_participants_added'] = 'No exam participants added yet. Work step not possible';
$string['not_all_places_assigned'] = 'Not all places assigned yet. Work step not possible';
$string['no_tasks_configured'] = 'No tasks configured yet. Work step not possible';
$string['no_results_entered'] = 'No exam results entered yet. Work step not possible';
$string['correction_not_completed'] = 'Marking not completed. Work step not possible';

//helptexts
$string['help'] = 'Help';
$string['helptext_str'] = 'Help text';
$string['helptext_link'] = 'A detailed explanation of all elements and functions of the exam management can be found at the "IMT HilfeWiki" under the following link:';
$string['helptext_open'] = 'Open/close helptext';
$string['helptext_overview']= 'This is the <strong>overview page of the exam organization</strong>. Lecturers and / or their staff can see all necessary and helpful work steps for performing an exam. <br><br>
These are neatly arranged into different phases, which are ordered along a timeline. For each individual step, the processing state is indicated by corresponding symbols, texts and colors. There are mandatory work steps and optional ones, which are helpful but can be left out. As soon as all obligatory steps of one phase have been completed, it automatically closes and the next one opens. However, phases can also be opened and closed manually at any time. <br><br>
Each work step can be opened by clicking on the corresponding button that will appear as soon as all required steps for the worksteps have been completed.<br><br>
The "Configure password" button also allows you to (re)set a password, which must then be entered to access the exam organization. With this you can, for example, prevent your student assistants who supervise your PANDA courses from accessing the sensitive contents of the exam organization. <br><br>
<strong>Note:</strong> Students do not have access to this view. Instead, they will see all information of the exam which has been enabled for them in a separate view.';
$string['helptext_configurePassword'] = 'On this page, you can set or change the password for the exam organization. This password has to be entered by every teacher of the PANDA course in order to access the contents of the exam organization.<br><br>
To set a password, it must initially be entered in the first and then confirmed in the second field.<br><br>
Remember to choose your password with sufficient security and especially do not use a password that you already use elsewhere (especially not in the context of university!).<br><br>
By clicking on the button "Reset password" you can revoke the password protection for the exam organization.';
$string['helptext_checkPassword'] = 'A password for this exam management was set by the teacher. You have to enter it below to gain access to the content of this module. <br><br> By clicking on the corresponding button, you can request a password reset via support if necessary. If the password has been reset, you and all other teachers of the PANDA course will be informed via PANDA notification.';
$string['helptext_checkPasswordAdmin'] = 'A password for this exam management was set by the teacher. You have to enter it below to gain access to the content of this module. <br> <br>
Admins can reset the password of the exam organization here if teachers request this. All teachers of the PANDA course will be informed about this via PANDA message.';
$string['helptext_chooseRooms']= 'On this page you can view the list of all possible <strong>exam rooms</strong> available in the system and select one or more of them as the room for the current exam. <br /> <br />
After clicking on the button "Add custom exam room", you can also add your own exam rooms to the list (and later select them as exam rooms). <br /> <br />
To select a room as an exam room, first click on the box to the left of its name. A click on the button "Choose rooms" saves the selected rooms as exam rooms. If a room is already marked after opening the page, it has already been saved as a room for the exam. <br /> <br />
The chosen exam rooms will be used later to assign seats to the participants added to the exam. Their seats will later be displayed to them in their view (as soon as you have made this information visible to the students on the overview page). The seat allocation is also used in documents such as the list of participants or the seating plan. <br /> <br />
A description of each room and the number of places available in it is given in the table. If a seating plan is stored in the system for a room, it can be viewed by pressing the left mouse button over the info icon in the "Seating plan" column. If a room is a self-created custom exam room, it can be edited by clicking on the pencil icon at the right end of the line, while a click on the trash can icon next to it and a subsequent confirmation deletes it (although it must not be selected as an exam room). <br /> <br />
<strong>Please note:</strong>
<ul><li>In order to be able to use the other functions of the PANDA exam organization, at least one room must be selected here as exam room. In addition, the selected rooms must offer at least as many seats as participants are to take part in the exam.</li>
<li>If an exam room is deselected after participants have been assigned seats in it, the entire seat assignment is deleted and must be repeated. Rooms affected by this are marked with a warning.</li>
<li>Some rooms are listed here several times. These are different models of the same room. "1 free space" means that every 2nd space will be used. "2 places free" means that every 3rd place will be used.</li></ul>
<strong>Attention:</strong> The system does not take the availability of the selected rooms into account. As a lecturer, you must book the rooms in which the exam should take place at the central room administration of the University of Paderborn and clarify that the corresponding rooms are actually available at the time of the exam.';
$string['helptext_addDefaultRooms']= 'As a PANDA administrator, you can import a number of <strong>default rooms </strong> here which are after that available to all lecturers as possible rooms when they select their exam rooms. <br><br>
In order to import the default rooms, a correctly structured text file must first be selected in the lower area and then read in by clicking on the corresponding button.<br><br>
The text file to be imported must contain the following information for each exam room, where each line stands for one exam room: 
<ul><li>First column: The system-internal room id according to the pattern <i>room_name_variant</i>, for example <i>Audimax_2</i></li>
<li>Second column: The user-visible room name, e.g. <i>Audimax</i></li>
<li>Third column: The user-visible room description including the number of free and total seats, for example <i>2 free seats, 56 total seats</i></li>
<li>Fourth column: An array needed to calculate the number of seats in a room, which contains the name of each individual seat in the room. The array must be written in json syntax, e.g. look like this: <i>["R/R01/P07", "R/R01/P04", "R/R01/P01", ...] </i></li>
<li>Fifth column: If a seating plan for the room is available as a .svg file and this should be displayed to the users, the source code of the SVG file must be in this column, otherwise this column can be left empty</li></ul>
All existing default rooms are overwritten by a new import. The information on any deleted or changed rooms is retained in all exam organizations in which they are currently used. However, deleted rooms can no longer be selected by instructors as new exam rooms or used for the (re)assignment of seats. Changes to the names or seats of default rooms also become only effective in the individual exam organizations after a new assignment of seats.';
$string['helptext_editDefaultRoom']= 'Here, administrators can edit an existing <strong> default exam room </strong> or create a new one.<br><br>
First the basic information of the room is displayed, which also may be entered or edited. This is first the system-internal ID of the room, which is used internally by the plugin to identify the room and should be structured according to the following schema: The room name followed by an underscore followed by the variant of the room, which is particularly relevant for several models of the same room with a different number of free seats between the occupiable seats. For the part of the room name all letters, numbers and also dots are allowed, the room variant after the underscore may only consist of numbers. If an existing room is edited, the room ID cannot be changed. Next comes the name of the room, which is visible to all lecturers and may consist of letters, numbers, dots and spaces. The room description is also visible for the users, should contain information about the selected modelling (one or two free seats between two occupiable seats) and may contain the same characters as the room name. Under this information, if an existing room has been selected for editing, further information about the room is displayed, such as the previous number of available seats and an overview of their designations and (if available) the seating plan stored for the room. <br><br>
If seats should be edited in an existing room, this is possible as soon as the option "Yes" has been selected in the next section under "Edit seats". This is not necessary when creating a new room, in this case you can continue directly with entering the new seats in the "New seats" section. For the filling of the room with seats there are three different modes, which should enable the simple replication of all important modelling types of exam rooms: In the " Default " mode, a number of assignable seats is automatically created for a room until the specified total number of seats in the room is reached, taking into account the specified number of free seats between two assignable seats. The naming of the seats starts with 1 and then counts upwards. If a room is to be filled with 100 total seats and one seat should remain unused between them it would receive a total of 50 seats with the designations 1, 3, 5, ..., 100 that can be assigned in the exam organization. With two places free there would be 34 places with the designations 1, 4, 7, ..., 100. The seat mode "Rows" works similarly, only the number of rows existing in a room as well as the available places per row must be specified here. Each row is then filled with the corresponding number of seats, whereby the specified number of free seats and the number of rows to be left free are taken into account. The seats are named with a combination of row and seat number, for example R01/P01, R01/P03, R01/P05, R02/P01 ... . For all room modelling that cannot be replicated using these two modes, there is a third mode with the name "Completely individual". In this mode, the names of all places can be entered completely free, whereby a comma must always be placed between two seat designations. All letters, numbers, dots, minus signs, slashes and spaces are allowed inside the seat names. This mode is very well suited for more complex seat modelling or for adapting models created with the first two modes. This is useful, for example, if the first or last row of a room has fewer seats than the others due to structural conditions, or if continuous numbered seats are still arranged in rows and every second row is to be left empty. When editing an already existing room, this mode is therefore already preselected, but can of course be replaced by any other mode at any time.<br><br>
Finally, a new room plan can be added for a room. This must be drawn up outside the exam organization and should contain all the seats of the default room. The room plan must be saved as SVG in a text file (.txt), which then can be uploaded in the last section of this page. Please note that the contents of the file with the SVG of the room plan must be carefully checked before uploading, as the plugin cannot detect malicious or incorrect contents in the file at this point. If a file with a room plan was selected, it is saved after a click on "Save room" together with the rest of the specified information. <br><br>
The room thus created or modified can immediately be selected as the exam room by all teachers in their exam organizations. When a name is changed or a seat is changed in an existing exam room that is already used in exam organizations, the name and previous seat assignments remain stored there for the time being. Teachers must therefore reassign seats once before the changes to the room take effect.';
$string['helptext_addCustomRoom'] = 'On this page, you as a lecturer can create a <strong>custom exam room</strong> if the room in which you want to hold your exam is not listed as an exam room within the system. Alternatively, you can also edit an existing exam room you have created yourself here.<br><br>
To create a new room, first enter its name. Next, you must specify the number of seats you want the room to have. Note that you must check for yourself how many seats are actually available in the room and that the numbering of the seats in the room created here in the system always starts with 1, regardless of the numbering actually available in the room. This means that you must manually adjust any discrepancies that may occur with the actual seat numbering. Finally, you can enter an optional description of the room. This should contain all important information about the room so that you can use the room again later, for example in the next semester, if necessary. Finally, a click on the "Save room" button creates the new exam room.<br><br>
A room created in this way can then be selected from the list of available exam rooms as a room and can then be used regularly like any other exam room.<br><br>
If, on the other hand, an existing exam room has been selected for editing on the room selection page, it can now be modified here. In this case, the number of seats and the description of the selected room can now be altered and then saved by clicking on "Save room". If the number of seats is reduced, all exam participants still retain their previously assigned seats until you perform the automatic seat assignment again.';
$string['helptext_setDateTime']= 'The <strong>date and time of the exam</strong> can be selected here.<br><br>
The exam date selected here is displayed on the overview page of the exam organization and is used later in the documents generated, such as the list of participants or the exam labels. In addition, it will be displayed to the exam participants in their view as soon as you have made this information visible to the students on the overview page. <br /> <br />
The date and time of the exam should therefore be set here so that the exam organization can be used effectively in PANDA.';
$string['helptext_viewParticipants']= 'On this page you can view all <strong>exam participants</strong> added to the exam and information such as their profile, matriculation number and any groups assigned to them in PANDA. <br /> <br />
New participants can also be added to the exam here. There are two ways to do this: <br /> <br />
1. After clicking on the button "Add participants from file", participants can be imported from one or more exam lists exported from PAUL. This is the recommended way of importing participants, as only in this way it is possible to export the exam results later according to the number and structure of these imported PAUL lists. You should therefore choose this variant if you want to enter the exam results directly in PAUL later.<br>
2. It is also possible to import participants of the PANDA course as exam participants by clicking on the button "Import participants from course". If this option is selected, the exam results can later only be exported in a single result list, a listwise export and a simple subsequent entry of the exam results in PAUL is then not possible. It is also not possible to "rewrite" participants who have been imported as course participants later by subsequently importing a PAUL list. To do this, the participant must first be completely deleted.<br><br>
Adding participants is one of the most important steps in the exam organization. Only if you see at least one added participant here you will later be able to assign seats, enter exam points or export result documents. Students who have not been added as exam participants (even if they are already enrolled in the PANDA course) also do not have access to the participant view with the exam information and do not receive any notifications send with the messaging function on the overview page of the exam organization.<br /> <br />
If you see a lower part of the table separated by a subheading, you have imported exam participants who do not have a user account in PANDA. Although these can also be imported from a PAUL file, some steps, such as writing a notification, must be done manually for these participants and others (such as viewing the student view for the participants themselves) are completely impossible.<br><br>
It is also possible on this page to delete individual exam participants or all of them that have already been imported. To delete individual participants, simply click on the trash can in the participant´s row. To delete all participants, on the other hand, press the red button below the table. Please note, however, that deleting one or all participants automatically deletes all information stored for them, such as seats or entered exam points, and that this information cannot be restored afterwards.';
$string['helptext_addParticipants']= 'On this page you can add <strong>participants</strong> from PAUL exam lists to the exam. In this way their results can be exported later again list by list and then simply entered in PAUL. <br /> <br />
To do this, you first need the list of your exam participants exported from PAUL. You can then select this file in the selection area and import it by clicking on the corresponding button. <br><br>
On the following page you will see all matriculation numbers read from the file. The state of a matriculation number and whether the corresponding student can be added to the exam are displayed in different sections. <br><br>
In the following the different states are briefly explained:<br>
<ul><li><strong>Bad matriculation number</strong>: The entered matriculation number is invalid because, for example, it contains illegal characters such as letters. It cannot therefore be read in as a participant. The number on the far left of the line indicates the number of the line in which the invalid matriculation number is located in the read in PAUL file and where it can be checked if necessary.</li>
<li><strong>Duplicated matriculation number</strong>: The matriculation number occurs several times in the file. However, it can only be read in once as a exam participant in the corresponding section.</li>
<li><strong>New (no course participant)</strong>: The student belonging to this matriculation number is not part of the PANDA course. He can easily be imported as an exam participant. However, since he cannot view the participant view of the plugin, he must be selected manually by ticking the checkbox in order to exclude the possibility of an error here.</li>
<li><strong>New (without PANDA account)</strong>: The student belonging to this matriculation number does not yet have an account in PANDA. This can happen, for example, if the student has never registered in PANDA before. Although the student can be imported as an exam participant, he or she cannot view the participant view of the exam organization and you cannot reach him or her via the notification function of the exam organization. Therefore you have to check this student here manually.</li>
<li><strong>Will be deleted</strong>: This participant was already imported as an exam participant with an earlier version of the used PAUL list, but is no longer included in the current one (for example, because he has deregistered from the PAUL exam in the meantime). You can now select this participant to remove him from the current exam.</li>
<li><strong>Already exam participant</strong>: This participant has already been imported as an exam participant and is not modified by the current import.</li>
<li><strong>New</strong>: This is a valid participant that can be added to the exam without any problems. All participants in this section are preselected to be added to the exam.</li>
</ul>
All participants to be added to (or removed from) the exam can now be selected either by checking the box next to the name or by checking the "Select/deselect all" box of the respective area. Then press the "Add to exam" button to add the selected participants to the exam.<br><br>
If you have read in the wrong file, you can immediately read in a new file by clicking on the button "Import other participants". The currently readed participants then will not be imported but discarded.<br><br>
You can perform this procedure several times for the import of participants from several lists.';
$string['helptext_addCourseParticipants']= 'Here you can import all <strong>course participants</strong> enrolled in PANDA  as exam participants. <br><br>
In the lowest section, all course participants who should be added to the exam must be selected. Individual participants can be selected by checking the box next to their name. To select (or deselect) all course participants it is sufficient to click in the corresponding box "Select/deselect all". In the corresponding section, you can also select existing participants who are not course participants. These are then removed from the exam by clicking on the "Add to exam" button at the bottom, while the selected course participants are then added to the exam. For all participants with the state "Already exam participants" nothing changes. <br><br>
If participants are added after places have already been assigned, they must still be assigned places.<br><br>
<strong>Please note:</strong> If this variant of participant import is selected, the results of all participants added in this way will be exported later in a single separate list for the exam office, which may make it difficult to enter them in PAUL. If you plan to have the exam results entered into PAUL, you may want to add participants to the exam using the appropriate PAUL participant lists in the corresponding import.';
$string['helptext_configureTasks']= 'Here you can define the number and maximum points of all <strong>exam tasks</strong>. <br><br>
By clicking the "+" button new tasks can be added to the exam. In the field below the respective task number, you must enter the maximum number of points that can be achieved in the respective task later. This number of points must be positive, but can be a decimal. By clicking on the "-" button, exam tasks can be removed again, but at least one task always remains. <br><br>
The tasks are a central element of the exam organization. They correspond to the tasks which are later available in the actual exam and are required in order to be able to enter the exam results for the participants after the exam. For each task, the points obtained by the participants can then be entered separately, up to a maximum of the maximum number of points specified here for the respective task. The tasks specified here and their maximum number of points are also required for setting the grading scale and for exporting the exam results.
If the tasks are changed after the exam results have already been entered or after the grading scale has been set, these must be adapted to the new number or the new maximum of points of the tasks.';
$string['helptext_setTextfield']= 'Any content can be entered here as <strong>free text</strong> for the exam, which is immediately displayed to the participants in their participant view after saving.  <br /> <br />
In this way, the exam participants can e. g. be easily informed about the equipment permitted in the exam. In addition to simple texts, more complex elements such as pictures or even formulas can be used. <br /> <br />
This function is purely optional. If, for example, you do not have any information for the exam participants, you can simply leave the field below empty and click on the "Cancel" button. <br /> <br />
<Strong>Note:</strong> This function is mainly intended for messages that are not time-critical. If, however, you would like to inform the exam participants e. g. about a short-term change of exam rooms on the day before the exam, it is advisable to use the function "Write message to participants" on the overview page instead. In this way, the exam participants will immediately receive an e-mail and will thus be able to see the information entered even if they do not actively look in PANDA.';
$string['helptext_sendGroupmessage']= '
On this page the subject and content of a <strong>message</strong> can be entered, which will be sent to all </strong> students added as <strong>exam participants</strong> after clicking the button "Send message". <br /> <br />
They receive the message immediately after sending it both as a PANDA notification and as an e-mail to their university e-mail account and can thus, for example, simply be made aware of short-term changes (such as changes of the exam times or rooms). <br /> <br />
If you have added participants to the exam who do not yet have a PANDA user account, this will be displayed below. Since these participants will not automatically receive the message written here, you will have to write to them manually by e-mail instead. You can do this, for example, by clicking on the "Write Email" button, which opens your email client and enters the email addresses of the corresponding participants. <br /> <br />
The whole notification function is purely optional, you do not have to use it to send a message to the participants. ';
$string['helptext_importBonus']= 'Here you can import the participants bonus points and convert them into <strong>bonus grade steps</strong> for the exam. In this way, for example, bonus points earned by students while completing exercises can be directly converted into bonus grade steps for the exam. <br><br>
To do this, the number of possible bonus grade steps for the exam must first be specified in the upper section. A maximum of three bonus grading steps are possible (one grading step would be an improvement from 1.7 to 1.3 i.e.). Therefore, participants can improve their grade by a maximum of one grade in total. After that, for each bonus grade step must be stated, how many points the students must have achieved at least to receive it. <br><br>
In the lower section you can next specify the type of file import. There are two possibilities: <br><br>
1. Exported grades from PANDA: If your students have submitted their exercise sheets via the PANDA assignment activity and these have been corrected and graded there, the exported grades from PANDA should be selected here, since in this way all bonus points for the complete PANDA course can be easily read in. <br>
To do this, the gradings from the PANDA course must first be exported (see <a href="https://hilfe.uni-paderborn.de/Dozent:_Bewertungen_Export_von_Gruppen#Setup_f.C3.BCr_Bewertungen" class="alert-link" target="_blank">here</a>). Then you have to open the exported file once and check in which column the points are entered. The name of the column must then be entered in the field provided in the lower section. <br><br>
2. Individual: If you have not managed your bonus points via the PANDA assignment activity, you can alternatively select the mode "Other". For this you need an Excel file, in which for each participant affected either the email address stored in PANDA or the matriculation number are entered in one and the achieved points in another column in a separate line. The name of both the column containing the user indexes of all students and the column containing all bonus points must then be entered in the corresponding fields in the lower section. <br><br>
Finally you have to select the file with the bonus points you want to import and then click on the "Import file" button to import the bonus points. The imported bonus grade steps are immediately displayed to the participants in their view.';
$string['helptext_configureGradingscale']= 'Here a <strong>grading scale</strong> can be configured for the exam.<br><br>
As soon as the exam results have been entered, the grading scale is used to automatically calculate the exam grades for all participants. If no grading scale has been configured, the automatic calculation of the exam grades is not possible.<br><br>
The minimum number of points required to reach a step must be specified individually for each single grade. A 70 in the field below 1.0 would therefore mean that a participant has to reach at least 70 points in order to get the grade 1.0.<br><br>
The number of points to be achieved for a grade step can be between 0 and the stated total number of points for all exam tasks, but it must be higher than the number of points required for the previous grade step. For example, more points must be required for achieving the grade 1.0 than for achieving a 1.3. In addition, it is also possible to use desimals as points. If a participant achieves fewer points than which are necessary for 4.0, he or she will receive the grade 5 instead.<br><br>
The grading scale can be changed at any time (even after the exam results have been entered), in that case the participants grades are automatically adapted to the new grading scale.';
$string['helptext_inputResults']= 'On this page you can enter the <strong>exam results</strong> of the participants.<br><br>
For this purpose, the matriculation number of the participant whose results should be entered must first be validated. There are two ways to do this:<br>
1. You can manually enter the matriculation number of the participant. To do this, click in the "Matriculation number or barcode" field, enter the matriculation number and confirm it by pressing the Enter (or Return) key, the Tab key or the "Validate matriculation number" button. <br> 
2. Alternatively, if you have used exam labels in your exam, you can also use a barcode scanner to enter the exam results more quickly. For this you need a barcode scanner or alternatively a smartphone with a corresponding app. With this you then have to scan the barcode on the exam label of a participant, whereby his matriculation number is automatically entered into the field "Matriculation number or barcode" and confirmed immediately. If the automatic entry does not work immediately, you may have to click manually in the field "Matriculation number or barcode" once and then repeat the scan.<br><br>
As soon as a matriculation number has been entered and confirmed, it is checked by the system. If it is the valid matriculation number of a participant added to the exam, the page for entering the exam points is opened, otherwise there is a corresponding error message and the previous page is opened again, where a new matriculation number can be entered or the entry of the incorrect matriculation number can be repeated.<br><br>
In the case of a valid matriculation number, the exam results can now be entered on the page that opens. In the section "Exam participant" you will first see the matriculation number and the name of the selected participant. By clicking on the "Change matriculation number" button below, you can return to the previous page to enter a different matriculation number (e.g. in case of an error). In the "Exam points" section below, you can enter the points earned in each exam task by the selected participant. The corresponding points can be entered directly in the points field of the first task and then be continued in the field of the next task after pressing the tab key. A number between zero and the displayed maximum number of points of the respective task can be entered as the number of points, whereby decimals with up to two places are also permitted. If the participant is subject to a special exam state (e.g. if he has "not participated", committed a "fraud attempt" or was "sick"), this state can be selected in the last section "Exam state" by ticking the corresponding checkbox. This sets the task points to zero, disables the entry of points, and displays the selected state in all later documents (e.g. for PAUL export) instead of the result. Removing the check mark at the respective exam state reactivates the option to enter points. If results have already been entered for the participant in the past, both the section on the exam points and the exam state may already be filled in. In this case, the information can now be changed and the changes can then be saved.<br><br>
After a click on the button "Save and next" or after pressing the Enter or Return key, the entered results are then saved and the page is automatically reloaded so that the matriculation number of the next exam participant can be read in (either manually or by barcode scanner).';
$string['helptext_participantsOverview']= 'In this <strong>participants and results overview</strong> the information of all imported exam participants and their results can be viewed and edited. <br><br>
The first name, surname and matriculation number of each exam participant will be displayed here in alphabetical order. If a place has already been assigned to a participant, this place and the corresponding room are also displayed in the correspondingly named columns. If exam tasks have already been created and exam results have already been entered for a participant, these are also displayed. In the "Points" column you can see how many points the participant has earned in each individual task, while in the "Total points" column the total number of points is displayed. If no exam tasks have been created yet, a click on the symbol displayed instead in the "Points" column allows you to do this directly. If no grading scale has been entered yet, this can be done by clicking on the corresponding symbol in the "Result" column, otherwise the exam grade calculated using the grading scale will be displayed in this column (if results have already been entered for the participant). If the participant has a special state (e.g. if he was ill during the exam or if he was trying to cheat) this will be displayed instead of the exam result. In addition, the "Bonus steps" column shows the bonus grade steps already achieved by the participant for the exam, while the "Result with bonus" column shows the final grade, taking the bonus grade steps into account. <br><br>
In order to edit the information on a participant, simply click on the icon on the right in the line of the respective participant. Then you can edit all information for the participant. For example, the participant can be assigned one of the rooms already selected for the exam and any place in it. Below the field for entering the place, the available places in the selected room are displayed. In the "Points" column, on the other hand, the points achieved by the participant can be entered for each task. Alternatively, if necessary, a special exam state such as "Ill", "Not participated" or "Fraud attempt" can be selected from a dropdown menu, which automatically sets the points to zero and disables the possibility to enter points. Resetting the status to "Normal" allows you to enter points again. It is also possible to manually select the bonus steps achieved by a participant. After saving the changes by clicking on the corresponding button, the result and the result taking into account all the bonus steps achieved are calculated (if a grading scale has already been entered). <br><br>
For a participants, all these details can be entered or edited at the same time or individually. In this way, this page can not only be used to correct incorrectly entered information, but also to manually enter results for exam participants or to manually assign the desired places to them. In this way, exam results can also be entered for participants without a matriculation number.';
$string['helptext_examReviewDateRoom']=  'If you are planning an <strong>exam review</strong>, you can select the date and the room for it here. <br><br>
The name of the room can be freely entered as normal text in the lower form field. In this way, you can select rooms that are not stored in the system as exam rooms, such as your office, as room for the exam review. <br><br>
If you change the exam results for the participants after the time of the exam review set on this page, you can simply export them separately for the exam office on the overview page. <br><br>
The information on the date and room of the exam review can later be made visible to the students on the overview page.';

//errors and permissions
$string['missingidandcmid'] = 'Coursemodule-id missing';
$string['nopermissions'] = 'You have no permissions to do this. Action denied.';
$string['err_underzero'] = 'Entered number ca not be lower than zero.';
$string['err_novalidinteger'] = 'Entered number has to be a valid number.';
$string['err_overmaxpoints'] = 'Entered number exceeds maximal points.';
$string['err_bonusstepsnotcorrect'] = 'One or more bonus steps are invalid.';
$string['err_gradingstepsnotcorrect'] = 'One or more grading scale steps are invalid.';
$string['err_taskmaxpoints'] = 'Entered number exceeds maximal points of task.';
$string['err_roomsdoubleselected'] = 'Double selection of one room with different configurations';
$string['err_invalidcheckboxid_rooms'] = 'Invalid room id.';
$string['err_invalidcheckboxid_participants'] = 'Invalid participant id.';
$string['err_nonvalidmatrnr'] = 'No valid matriculation number.';
$string['err_customroomname_taken'] = 'Roomname already taken';
$string['err_filloutfield'] = 'Please fill out field';
$string['err_nofile'] = 'Please provide file';
$string['err_noalphanumeric'] = 'Contains invalid chars';
$string['err_js_internal_error'] = 'Internal error. Please retry.';
$string['err_password_incorrect'] = 'Password is not matching. Please enter again.';
$string['err_novalidpassword'] = 'Not a valid password.';
$string['err_examdata_deleted'] = 'The exam data has already been deleted. It is no longer possible to use the exam organization.';
$string['err_already_defaultroom'] = 'Already default room. Try instead room ID ';
$string['no_param_given'] = 'Couldn`t match matriculation number';
$string['not_possible_no_matrnr'] = 'Not possible because no matriculation numbers are available';

//universal
$string['modulename'] = 'Exam management';
$string['modulenameplural'] = 'Exam managements';
$string['pluginname'] = 'Exam management';
$string['coursecategory_name_no_semester'] = 'DEFAULT_SEMESTER';

//add new module instance and mod_form.php
$string['modulename_help'] = 'The exam management allows the easy organization of exams for a course and makes it possible to manage even large exams with many participants.

In a separate view a lecturer can

* set the basic exam data
* export documents that are useful for the exam, such as seating plans and lists of participants
* enter the exam results for the participants manually or using a barcode scanner
* export all results in various documents for further use (e.g. by the exam office).

The exam participants, on the other hand, see in their own view all the relevant information about the exam, such as the date, their seat or the bonus grade steps achieved for the exam. In addition, the notification function allows an easy and reliable communication with them.';
$string['modulename_link'] = 'https://hilfe.uni-paderborn.de/PANDA';
$string['exammanagement_name'] = 'Name of the exam management';
$string['exammanagement_name_help'] = 'The name of the activity displayed in the course (e.g. "Exam 1").';
$string['exammanagement:enable exam management'] = 'enable exam management';
$string['messageprovider:exam management messages'] = 'exam management messages';
$string['pluginadministration'] = 'Exam management administration';
$string['security_password'] = 'Security password';
$string['new_password'] = 'New password';
$string['security_password_help'] = 'Setting a security password allows you to restrict access to the exam organization. Other staff users like student tutors have to enter this passwort before they can access the contents of the exam organization.';
$string['confirm_new_password'] = 'Repeat new password';
$string['confirm_new_password_help'] = 'For setting the new password it has to be repeated here.';
$string['old_password'] = 'Current password (only necessary if an already existing password should be changed)';
$string['old_password_help'] = 'If an already existing password should be changed you need to enter it here.';
$string['incorrect_password_change'] = 'Incorrect password. Terminated password change';

//capabilities
$string['exammanagement:addinstance'] = 'Add new exam organization';
$string['exammanagement:viewinstance'] = 'View exam organization';
$string['exammanagement:viewparticipantspage'] = 'View participants page';
$string['exammanagement:takeexams'] = 'Take exams';
$string['exammanagement:sendgroupmessage'] = 'Send group message to participants';
$string['exammanagement:importdefaultrooms'] = 'Import default rooms';
$string['exammanagement:resetpassword'] = 'Reset password';
$string['exammanagement:requestpasswordreset'] = 'Request password reset';

//settings.php - admin settings
$string['enablepasswordresetrequest'] = 'Enable requesting password reset';
$string['enablepasswordresetrequest_help'] = 'As soon as this function has been activated, all teachers in their exam organizations can request the reset of the passwords set there by clicking on the corresponding button. If a lecturer has done this, all users with the role "Manager" receive an automatically generated message both as PANDA notification and forwarded to the e-mail address stored in their profile and can then reset the password by clicking on the link contained in this message. This means that all teachers of the exam organization concerned are automatically informed via PANDA notification and e-mail that the password has been reset and can then access the contents of the exam organization again without having to enter a password. If the function is deactivated here, users cannot automatically request the password reset in their exam organization, but managers and administrators can still reset the password of any exam organization.';

//delete_temp_participants.php - task
$string['delete_temp_participants'] = 'Delete temporary saved participants';

//check_participants_without_moodle_account.php - task
$string['check_participants_without_moodle_account'] = 'Check participants without moodle account';

//delete_old_exam_data.php - task
$string['delete_old_exam_data'] = 'Delete old exam data';
$string['warningmailsubjectone'] = '[Exam organization] Reminder: Future deletion of exam data';
$string['warningmailsubjecttwo'] = '[Exam organization] Warning: Soon deletion of old exam data';
$string['warningmailsubjectthree'] = '[Exam organization] Last warning: Exam data will be deleted tomorrow';
$string['warningmailcontentpartone'] = 'Alle Prüfungsinformationen der Prüfung "';
$string['warningmailcontentparttwo'] = '" im Kurs "';
$string['warningmailcontentpartthree'] = '" werden am ';
$string['warningmailcontentpartfour'] = ' gelöscht. Bitte stellen Sie sicher, dass Sie alle relevanten Prüfungsdaten zur weiteren Verwendung exportiert haben. Sie können dafür die Exportfunktionen der PANDA Prüfungsorganisation nutzen. Am angegebenen Datum werden sämtliche Prüfungsdaten endgültig gelöscht, eine nachrägliche Wiederherstellung der Daten ist ab diesem Zeitpunkt nicht mehr möglich!';
$string['warningmailcontentpartoneenglish'] = '<strong>English version</strong>: All information on the exam "';
$string['warningmailcontentparttwoenglish'] = '" in course "';
$string['warningmailcontentpartthreeenglish'] = '" will be deleted on ';
$string['warningmailcontentpartfourenglish'] = ' . Please make sure that you have exported all relevant exam data for further use. To do this, you can use the export functions of the PANDA exam organization. On the specified date, all exam data will be finally deleted, a later recovery of the data is then no longer possible!';