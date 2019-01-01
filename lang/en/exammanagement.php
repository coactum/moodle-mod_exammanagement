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

//configurePassword
$string['configure_password_str'] = 'Configure password';
$string['configure_password'] = 'This page allows setting and modifying password for the plugin instance';
$string['password'] = 'Password';


//configurePassword
$string['check_password_str'] = 'Enter password';
$string['check_password'] = 'A password for this exam management instance was set by the teacher. You have to enter it below to gain access to the content of this module.';

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

//showResultsForm.php
$string['show_results_str'] = 'Participants and results list';
$string['matriculation_number_short'] = 'Matr. no.';
$string['result'] = 'Result';
$string['resultwithbonus'] = 'Result with bonus';
$string['bonussteps'] = 'Bonus steps';
$string['change_bonus'] = 'Change bonus';
$string['cancel'] = 'Back to exam organisation';
$string['gradingscale_not_set'] = 'If you want to calculate a grade as the result you have to configure the grading scale.';
$string['nt'] = 'Not participated';
$string['fa'] = 'Fraud attempt';
$string['ill'] = 'Ill';

//participant list
$string['participantslist'] = 'list of participants';
$string['participantslist_names'] = 'list_of_participants_names)';
$string['participantslist_places'] = 'list_of_participants_places)';
$string['internal_use'] = 'FOR INTERNAL USE ONLY';
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
$string['examreview_room'] = 'Rooms (enter free text)';

//helptexts
$string['help'] = 'Help';

$string['helptext_str'] = 'Help text';
$string['helptext_link'] = 'A detailed explanation of the mentioned elements and functions can be found at the "IMT HilfeWiki" under the following link:';
$string['helptext_overview']= 'Here goes the description of this feature site.';
$string['helptext_configurePassword'] = 'Here goes the description of this feature site.';
$string['helptext_checkPassword'] = 'A password for this exam management instance was set by the teacher. You have to enter it below to gain access to the content of this module.';
$string['helptext_addRooms']= 'Here goes the description of this feature site.';
$string['helptext_addDefaultRooms']= 'Here goes the description of this feature site.';
$string['helptext_setDateTime']= 'Here goes the description of this feature site.';
$string['helptext_addParticipants']= 'Here goes the description of this feature site.';
$string['helptext_addCourseParticipants']= 'Here goes the description of this feature site.';
$string['helptext_configureTasks']= 'Here goes the description of this feature site.';
$string['helptext_setTextfield']= 'Here goes the description of this feature site.';
$string['helptext_sendGroupmessages']= 'Here goes the description of this feature site.';
$string['helptext_importBonus']= 'Here goes the description of this feature site.';
$string['helptext_configureGradingscale']= 'Here goes the description of this feature site.';
$string['helptext_inputResults']= 'Here goes the description of this feature site.';
$string['helptext_showResults']= 'Here goes the description of this feature site.';
$string['helptext_examReviewDateRoom']= 'Here goes the description of this feature site.';
$string['helptext_addCustomRoom'] = 'Here goes the description of this feature site.';

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

//universal
$string['modulename'] = 'Exam management';
$string['modulenameplural'] = 'Exam managements';
$string['pluginname'] = 'Exam management';

//add new module instance and mod_form.php
$string['modulename_help'] = 'The PANDA exammanagement allows you easy organizing the exams for your course and makes it possible to manage even large exams with many participants.';
$string['exammanagementname'] = 'Exam Management';
$string['exammanagement:enable exam management'] = 'enable exam management';
$string['messageprovider:exam management messages'] = 'exam management messages';
$string['pluginadministration'] = 'exam management administration';
$string['security_password'] = 'Security Password';
$string['security_password_help'] = 'Setting a security password allows you to restrict access to the exam organisation. Other staff users like student tutors have to enter this passwort before they can access the contents of the exam organisation.';

//capabilities
$string['exammanagement:addinstance'] = 'Add new exam organization';
$string['exammanagement:viewinstance'] = 'View exam organization';
$string['exammanagement:viewparticipantspage'] = 'View participants page';
$string['exammanagement:takeexams'] = 'Take exams';
$string['exammanagement:sendgroupmessage'] = 'Send group message to participants';
$string['exammanagement:addDefaultRooms'] = 'Import default rooms';
