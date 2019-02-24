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
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//exammanagement_overview.mustache
$string['maintitle'] = 'Exam management';
$string['overview'] = 'Overview';

//exammanagement_overview.mustache phases
$string['phase_one'] = 'Before exam';
$string['phase_two'] = 'For exam';
$string['phase_three'] = 'After correction';
$string['phase_four'] = 'After exam';
$string['phase_five'] = 'Exam review (optional)';
$string['exam_appointment'] = 'Exam appointment';
$string['minimize_phase'] = 'Minimize phase';
$string['maximize_phase'] = 'Maximize phase';
$string['partricipants_and_results_overview'] = 'Participants & results overview';
$string['exam_rooms'] = 'Exam rooms';
$string['exam_date'] = 'Exam date';
$string['exam_participants'] = 'Exam partricipants';
$string['exam_tasks'] = 'Exam tasks';
$string['freetext_field'] = 'Textfield for free text';
$string['message_to_participants'] = 'Message to participants';
$string['assigning_places'] = 'Automaticaly assignment of places';
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
$string['complete_correction'] = 'Complete correction';
$string['points_for_exam_review'] = 'Points for exam review';
$string['results_with_percentages'] = 'Results with percentages';
$string['results_and_statistics'] = 'Results and statistics';
$string['results_for_exam_office'] = 'Results for exam office';
$string['delete_exam_data'] = 'Delete exam data';
$string['date_and_room_exam_review'] = 'Date and room for exam review';
$string['set_visibility_of_exam_review_information'] = 'Set visibility of exam review information';
$string['altering_exam_results'] = 'Altering exam results';
$string['export_altered_exam_results'] = 'Export of altered exam results';

//exammanagement_overview.mustache states
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
$string['state_loading'] = 'Loading ...';

//exammanagement_overview.mustache work stages buttons
$string['configure_password'] = 'Configure password';
$string['choose_rooms'] = 'Choose rooms';
$string['set_date'] = 'Set date';
$string['add_participants'] = 'Add participants';
$string['configure_tasks'] = 'Configure tasks';
$string['edit_textfield'] = 'Edit textfield';
$string['send_groupmessage'] = 'Write message';
$string['assign_places'] = 'Assign places';
$string['export_seatingplan'] = 'Export seatingplan';
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
$string['delete_examdata'] = 'Delete exam data';
$string['examreview_dateroom'] = 'Set date and room';
$string['change_examresults'] = 'Change exam results';

//configurePasswordForm.php
$string['configure_password_str'] = 'Configure password';
$string['configure_password'] = 'This page allows setting and modifying password for the exam management';
$string['password'] = 'Password';
$string['reset_password'] = 'Reset password';

//checkPasswordForm.php
$string['check_password_str'] = 'Enter password';
$string['check_password'] = 'A password for this exam organization was set by the teacher. You have to enter it below to gain access to the content of this module.';
$string['confirm_password'] = 'Confirm password';
$string['reset_password_admin'] = 'Reset password (only for administrators)';
$string['wrong_password'] = 'Wrong password. Please retry.';
$string['password_reset_successful'] = 'Password reset successful.';

//chooseRoomsForm.php
$string['import_default_rooms'] = 'Import default rooms';
$string['add_custom_room'] = 'Add custom exam room';

// addDefaultRoomsForm.php
$string['import_default_rooms_from_file'] = 'Import default rooms from text file';
$string['import_default_rooms_str'] = 'Administrators can import default rooms for teachers to choose from here as text file.';
$string['default_rooms_already_exists'] = 'Default rooms are already imported. New import will override old rooms.';

// addCustomRoomForm.php
$string['add_room'] = 'Add room';
$string['change_room'] = 'Change room';
$string['delete_room'] = 'Delete room';
$string['customroom_name'] = 'Name of exam room';
$string['customroom_placescount'] = 'Count of places';
$string['customroom_description'] = 'Optional description shown when choosing rooms';

//dateTimeForm.php
$string['set_date_time'] = 'Set exam date and time';

//addParticipantsForm.php
$string['view_participants'] = 'View participants';
$string['import_participants'] = 'Import participants';
$string['import_new_participants'] = 'Import other participants';
$string['import_participants_from_file'] = 'Import participants from file';
$string['import_course_participants'] = 'Import participants from course';
$string['view_added_partipicants'] = 'List of all participants added to the exam.';
$string['view_added_and_course_partipicants'] = 'List of all course participants and all participants added to the exam.';
$string['participants'] = 'Participants';
$string['matriculation_number'] = 'Matriculation number';
$string['course_groups'] = 'Groups in course';
$string['import_state'] = 'State';
$string['newmatrnr'] = 'Users will be added to exam.';
$string['badmatrnr'] = 'Lines with invalid matriculation numbers (Users can not be added to exam).';
$string['oddmatrnr'] = 'Users with warnings (can still be added as participants).';
$string['existingmatrnr'] = 'Users are already exam participants (no changes)';
$string['deletedmatrnr'] = 'Users will be deleted.';
$string['add_to_exam'] = 'Add to exam';
$string['import_from_excel_file'] = 'Import participants from excel file (matriculation number in any column) and add them to course.';
$string['paul_file'] = 'PAUL file';
$string['import_from_paul_file'] = 'Import participants from paul file (entries separated by tabs; first two lines with exam information) and add them to course.';
$string['no_participants_added'] = 'No participants added.';
$string['read_file'] = 'Read file';
$string['state_added_to_exam'] = 'Participants of exam';
$string['state_added_to_exam_no_moodle'] = 'Participants of exam (without PANDA account)';
$string['state_courseparticipant'] = 'Participants of course';
$string['state_newmatrnr'] = 'New';
$string['state_newmatrnr_no_moodle'] = 'New (without PANDA account)';
$string['state_badmatrnr'] = 'Bad matriculation number';
$string['state_doubled'] = 'Duplicated matriculation number';
$string['state_oddmatrnr_nocourseparticipant'] = 'New (no course participant)';
$string['state_existingmatrnr'] = 'Already exam participant';
$string['state_deletedmatrnr'] = 'Deleted';

//showParticipants.php
$string['delete_all_participants'] = 'Delete all participants';

//configureTasksForm.php
$string['configure_tasks_str'] = 'Configure Tasks';
$string['configure_tasks_text'] = 'Configure tasks for the exam';
$string['add_remove_tasks'] = 'Add or remove tasks:';
$string['task'] = 'Task';
$string['points'] = 'Points';
$string['total'] = 'Total';

//textfield.php
$string['add_text_str'] = 'Add textfield';
$string['add_text_text'] = 'All text added below will be immediately displayed to the participants in their view of this activity.';

//groupmessagesForm.php
$string['groupmessages_str'] = 'Add Messagetext';
$string['groupmessages_text_1'] = 'An email with the text added below will be send to ';
$string['groupmessages_text_2'] = ' participants of the exam.';
$string['groupmessages_text_3'] = ' exam participants have no PANDA account and will not recieve this message. Please contact them under their email adresses shown below:';

//importBonusForm.php
$string['import_bonus_str'] = 'Import bonus points';
$string['import_bonus_text'] = 'Bonus points achieved by the participants can be imported and converted to bonus steps for the exam.';
$string['set_bonussteps'] = 'Set bonus steps';
$string['add_remove_bonusstep'] = 'Add or remove bonus step:';
$string['bonusstep'] = 'Bonus step (max 3)';
$string['required_points'] = 'Requried points for bonus step';
$string['configure_fileimport'] = 'Configure file import';
$string['import_mode'] = 'Import mode';
$string['moodle_export'] = 'Exported grades from PANDA';
$string['individual'] = 'Other';
$string['idfield'] = 'Column containing user id (e.g. A, B, C ... ; preselected for exported grades from PANDA)';
$string['pointsfield'] = 'Column containing bonus points (e.g. A, B, C ...)';
$string['import_bonus_from_file'] = 'Import bonus points from excel file; Identificator (PANDA email adress or matriculation number and bonus points must fit the choosen column).';

//configureGradingscaleForm.php
$string['configure_gradingscale_str'] = 'Configure grading scale';
$string['configure_gradingscale_totalpoints'] = 'Number of total points';

//inputResultsForm.php
$string['input_results_str'] = 'Input exam results';
$string['matrnr_barcode'] = 'Barcode / matriculation number';
$string['participant'] = 'Participant';
$string['exam_state'] = 'Exam state';
$string['exam_points'] = 'Exam points';
$string['not_participated'] = 'Not participated';
$string['fraud_attempt'] = 'Fraud attempt';
$string['ill'] = 'Ill';
$string['max_points'] = 'Maximum points';
$string['save_and_next'] = 'Save and next';
$string['validate_matrnr'] = 'Validate matriculation number';
$string['confirm_matrnr'] = 'To confirm the matriculation number you can also press enter/return.';
$string['input_other_matrnr'] = 'Change matriculation number';

//participantsOverviewForm.php
$string['edit'] = 'Edit';
$string['show_results_str'] = 'Participants and results list';
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

//participant list
$string['participantslist'] = 'list of participants';
$string['participantslist_names'] = 'list_of_participants_names)';
$string['participantslist_places'] = 'list_of_participants_places)';
$string['internal_use'] = 'FOR INTERNAL USE ONLY!';
$string['lastname'] = 'Name';
$string['firstname'] = 'Firstname';
$string['matrno'] = 'Matr.-no.';
$string['room'] = 'Room';
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

// examReviewDateRoomForm.php
$string['examreview_dateroom'] = 'Date and rooms for exam review';
$string['examreview_date'] = 'Date';
$string['examreview_room'] = 'Rooms (enter as free text)';

// forms (common)
$string['operation_canceled'] = 'Operation canceled';
$string['alteration_failed'] = 'Alteration failed';
$string['correction_not_completed'] = 'Marking not completed';

//helptexts
$string['help'] = 'Help';
$string['helptext_str'] = 'Help text';
$string['helptext_link'] = 'A detailed explanation of all elements and functions of the exam management can be found at the "IMT HilfeWiki" under the following link:';
$string['helptext_open'] = 'Open/close helptext';
$string['helptext_overview']= 'This is the <strong>overview page of the exam organization</strong>. Lecturers and / or their staff can see all necessary and helpful work steps for performing an exam. <br><br>
These are neatly arranged into different phases, which are ordered along a timeline. For each individual step, the processing status is indicated by corresponding symbols, texts and colors. There are mandatory work steps and optional ones, which are helpful but can be left out. As soon as all obligatory steps of one phase have been completed, it automatically closes and the next one opens. However, phases can also be opened and closed manually at any time. <br><br>
Each work step can be opened by clicking on the corresponding button. This will appear as soon as all other required steps have been completed.<br><br>
The "Configure password" button also allows you to (re)set a password, which must then be entered to access the exam organization. With this you can, for example, prevent your student assistants who supervise your PANDA courses from accessing the sensitive contents of the exam organization. <br><br>
Note: Students do not have access to this view. Instead, they will see all information of the exam which has been enabled for them in a separate view.';
$string['helptext_configurePassword'] = 'On this page, you can set or change a password for the exam organization. This password has to be entered by every teacher of the PANDA course in order to access the contents of the exam organisation.<br><br>
To set a password, it must initially be entered in the first and then confirmed in the second field.<br><br>
Remember to choose your password with sufficient security and especially do not use a password that you already use elsewhere (especially not in the context of university!).<br><br>
By clicking on the button "Reset password" you can revoke the password protection for the exam organisation.';
$string['helptext_checkPassword'] = 'A password for this exam management was set by the teacher. You have to enter it below to gain access to the content of this module.';
$string['helptext_checkPasswordAdmin'] = 'A password for this exam management was set by the teacher. You have to enter it below to gain access to the content of this module. <br> <br> Admins can reset the password of the exam organization here if teachers request this. All teachers of the PANDA course will be informed about this via PANDA message.';
$string['helptext_addRooms']= 'Here goes the description of this feature site.';
$string['helptext_addDefaultRooms']= 'Here goes the description of this feature site.';
$string['helptext_addCustomRoom'] = 'Here goes the description of this feature site.';
$string['helptext_setDateTime']= 'Here goes the description of this feature site.';
$string['helptext_addParticipants']= 'Here goes the description of this feature site.';
$string['helptext_addCourseParticipants']= 'Here goes the description of this feature site.';
$string['helptext_configureTasks']= 'Here goes the description of this feature site.';
$string['helptext_setTextfield']= 'Here goes the description of this feature site.';
$string['helptext_sendGroupmessages']= 'Here goes the description of this feature site.';
$string['helptext_importBonus']= 'Here goes the description of this feature site.';
$string['helptext_configureGradingscale']= 'Here a <strong>gradingscale</strong> can be configured for the exam.<br><br>
As soon as the exam results have been entered, the gradingscale is used to automatically calculate the exam grades for all participants. If no gradingscale has been configured, the automatic calculation of the exam grades is not possible.<br><br>
The minimum number of points required to reach a step must be specified individually for each single grade. A 70 in the field below 1.0 would therefore mean that a participant has to reach at least 70 points in order to get the grade 1.0.<br><br>
The number of points to be achieved for a grade step can be between 0 and the stated total number of points for all exam tasks, but it must be higher than the number of points required for the previous grade step. For example, more points must be required for achieving the grade 1.0 than for achieving a 1.3. In addition, it is also possible to use comma numbers as points. If a participant achieves fewer points than which are necessary for 4.0, he or she will receive the grade 5 instead.<br><br>
The gradingscale can be changed at any time (even after the exam results have been entered), in that case the participants grades are automatically adapted to the new gradingscale.';
$string['helptext_inputResults']= 'Here goes the description of this feature site.';
$string['helptext_participantsOverview']= 'Here goes the description of this feature site.';
$string['helptext_examReviewDateRoom']=  'If you are planning an exam review, you can select the date and the room for it here. <br><br>
The name of the room can be freely entered as normal text in the lower form field. In this way, you can select rooms that are not stored in the system as exam rooms, such as your office, as room for the exam review. <br><br>
If you change the exam results for the participants after the time of the exam review, you can simply export them separately for the examination office on the overview page. <br><br>
The information on the date and room of the exam review can later be made visible to the students on the overview page.';

//errors and permissions
$string['missingidandcmid'] = 'Coursemodule-id missing';
$string['nopermissions'] = 'You have no permissions to do this. Action denied.';
$string['err_underzero'] = 'Entered number ca not be lower than zero.';
$string['err_novalidinteger'] = 'Entered number has to be a valid number.';
$string['err_overmaxpoints'] = 'Entered number exceeds maximal points.';
$string['err_bonusstepsnotcorrect'] = 'One or more bonus steps are invalid.';
$string['err_gradingstepsnotcorrect'] = 'One or more gradingscale steps are invalid.';
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

//universal
$string['modulename'] = 'Exam management';
$string['modulenameplural'] = 'Exam managements';
$string['pluginname'] = 'Exam management';
$string['coursecategory_name_no_semester'] = 'DEFAULT_SEMESTER';

//add new module instance and mod_form.php
$string['modulename_help'] = 'The PANDA exammanagement allows you the easy organization of exams for your course and makes it possible to manage even large exams with many participants.';
$string['exammanagementname'] = 'Exam Management';
$string['exammanagement:enable exam management'] = 'enable exam management';
$string['messageprovider:exam management messages'] = 'exam management messages';
$string['pluginadministration'] = 'exam management administration';
$string['security_password'] = 'Security Password';
$string['new_password'] = 'New password';
$string['security_password_help'] = 'Setting a security password allows you to restrict access to the exam organization. Other staff users like student tutors have to enter this passwort before they can access the contents of the exam organization.';
$string['old_password'] = 'Current password (only necessary if an already existing password should be changed)';
$string['old_password_help'] = 'If some already existing password should be changed you need to enter it here.';
$string['incorrect_password_change'] = 'Incorrect password. Terminated password change';

//capabilities
$string['exammanagement:addinstance'] = 'Add new exam organization';
$string['exammanagement:viewinstance'] = 'View exam organization';
$string['exammanagement:viewparticipantspage'] = 'View participants page';
$string['exammanagement:takeexams'] = 'Take exams';
$string['exammanagement:sendgroupmessage'] = 'Send group message to participants';
$string['exammanagement:addDefaultRooms'] = 'Import default rooms';

//delete_old_exam_data.php
$string['delete_old_exam_data'] = 'Delete old exam data';
$string['warningmailsubjectone'] = 'Reminder: Future deletion of exam data';
$string['warningmailsubjecttwo'] = 'Warning: Soon deletion of old exam data';
$string['warningmailsubjectthree'] = 'Last warning: Exam data will be deleted tomorrow';
$string['warningmailcontentpartone'] = 'All exam data from the exam ';
$string['warningmailcontentparttwo'] = 'in the course ';
$string['warningmailcontentpartthree'] = 'will be deleted at ';
$string['warningmailcontentpartfour'] = '. Please make sure you have exported all relevant data for further use via the export functions. After the data all exam data will be finally deleted and there is no possibility to access them again.';
