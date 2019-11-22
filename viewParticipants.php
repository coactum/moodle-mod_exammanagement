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
 * Shows participants of mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$dap  = optional_param('dap', 0, PARAM_INT);
$dpmatrnr  = optional_param('dpmatrnr', 0, PARAM_TEXT);
$dpmid  = optional_param('dpmid', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    if($ExammanagementInstanceObj->isExamDataDeleted()){
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {

        if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

            global $OUTPUT;

            $MoodleObj->setPage('viewParticipants');
            $MoodleObj->outputPageHeader();

            #### delete participants if neccassary ####

            if($dap){
                $UserObj->deleteAllParticipants();
                redirect ('viewParticipants.php?id='.$id, null, null, null);
            }

            if($dpmid){
                $UserObj->deleteParticipant($dpmid, false);
            } else{
                $UserObj->deleteParticipant(false, $dpmatrnr);
            }

            ###### list of participants ... ######

            echo('<div class="row"><div class="col-xs-4">');
            echo('<h3>'.get_string("viewParticipants", "mod_exammanagement"). $OUTPUT->help_icon('viewParticipants', 'mod_exammanagement', '') . '</h3>');
            //echo('</div><div class="col-xs-1"><a class="helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;" title="'.get_string("helptext_open", "mod_exammanagement").'"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');

            echo ('</div><div class="col-xs-8">');

            echo('<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addCourseParticipants", $id).'" class="btn btn-primary pull-right m-b-1" role="button" title="'.get_string("import_course_participants_optional", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_course_participants_optional", "mod_exammanagement").'</span><i class="fa fa-user d-lg-none" aria-hidden="true"></i></a><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addParticipants", $id).'" role="button" class="btn btn-primary pull-right m-r-1" title="'.get_string("import_participants_from_file_recommended", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_participants_from_file_recommended", "mod_exammanagement").'</span><i class="fa fa-file-text d-lg-none" aria-hidden="true"></i></a>');


            echo ('<div class="popover fade bs-popover-right" role="tooltip" id="popover295950" style="will-change: transform; position: absolute; transform: translate3d(569px, 5px, 0px); top: 0px; left: 0px;" x-placement="right"><div class="arrow" style="top: 325px;"></div><h3 class="popover-header"></h3><div class="popover-body"><div class="no-overflow"><p>Auf dieser Seite können alle zur Prüfung hinzugefügten <strong>Prüfungsteilnehmer</strong> und Informationen wie deren Profil, Matrikelnummer sowie die ihnen gegebenenfalls in {$a-&gt;systemname} zugewiesenen Gruppen angesehen werden. <br><br>
            Es können hier zudem neue Teilnehmerinnen zur Prüfung hinzugefügt werden. Dazu gibt es zwei Möglichkeiten: <br><br>
            1. Es können nach einem Klick auf den Button "Teilnehmer aus Datei hinzufügen" Teilnehmer aus einer oder mehreren, aus PAUL exportierten Prüfungslisten importiert werden. Dies ist der empfohlene Weg des Teilnehmerimportes, da nur auf diese Weise später ein Export der Prüfungsergebnisse der Anzahl und dem Aufbau dieser eingelesenen PAUL-Listen entsprechend möglich ist. Diese Variante sollte also gewählt werden, möchte man später die Prüfungsergebnisse direkt in PAUL eintragen (lassen).<br>
            2. Es besteht außerdem die Möglichkeit, nach einem Klick auf den Button "Kursteilnehmer importieren" Teilnehmer des {$a-&gt;systemname}-Kurses als Prüfungsteilnehmer zu importieren. Wird diese Variante gewählt können die Prüfungsergebnisse später allerdings nur in einer einzigen Ergebnisliste exportiert werden, ein listenweiser Export und ein einfaches anschließendes Eintragen der Prüfungsergebnisse in PAUL ist dann somit nicht möglich. Es besteht zudem auch nicht die Möglichkeit, einmal als Kursteilnehmer importierte Teilnehmer später durch nachträgliches Einlesen einer PAUL-Liste "umzuschreiben". Dafür muss der oder die Teilnehmerin zunächst komplett gelöscht werden.<br><br>
            Das Hinzufügen von TeilnehmerInnen ist einer der wichtigsten Arbeitsschritte in der Prüfungsorganisation. Nur wenn Sie hier mindestens einen hinzugefügten Teilnehmemenden sehen können Sie später Sitzplätze zuweisen, Prüfungspunkte eintragen oder Ergebnisdokumente exportieren. Nicht als PrüfungsteilnehmerInnen hinzugefügte Studierende haben (selbst wenn sie bereits im {$a-&gt;systemname} Kurs eingeschrieben sind) außerdem keinen Zugriff auf die Teilnehmeransicht mit den Prüfungsinformationen und erhalten auch keine Benachrichtigungen über die Nachrichtenfunktion auf der Übersichtsseite der Prüfungsorganisation. <br><br>
            Falls Sie einen durch eine Zwischenüberschrift abgetrennten unteren Teil der Tabelle sehen, dann haben Sie Prüfungsteilnehmer importiert, die keinen Benutzeraccount in Testmoodle haben. Diese können zwar auch aus einer PAUL-Datei importiert werden, einige Arbeitsschritte wie etwa das Schreiben einer Benachrichtigung müssen für diese Teilnehmer jedoch manuell durchgeführt werden und andere (etwa das Ansehen der Studentenansicht für die Teilnehmer selbst) sind gänzlich unmöglich.<br><br>
            Es besteht auf dieser Seite außerdem die Möglichkeit, einzelne oder alle bereits importierte Prüfungsteilnehmer wieder zu löschen. Um einzelne Teilnehmer zu löschen genügt ein Klick auf den Mülleimer in der Zeile des jeweiligen Teilnehmenden, um alle Teilnehmer zu löschen muss hingegen der rote Button unter der Tabelle gedrückt werden. Beachten Sie jedoch, dass durch das Löschen eines oder aller Teilnehmer automatisch alle für diese hinterlegten Informationen wie etwa Sitzplätze oder eingetragene Prüfungspunkte gelöscht werden und dass diese Informationen danach nicht wieder hergestellt werden können.</p>
            </div> <div class="helpdoclink"><a href="https://docs.moodle.org/35/de/"><i class="icon fa fa-info-circle fa-fw iconhelp icon-pre" aria-hidden="true"></i>Weitere Hilfe</a></div></div></div>');
            echo('</div></div>');


            echo($ExammanagementInstanceObj->ConcatHelptextStr('viewParticipants'));

            echo('<p>'.get_string("view_added_partipicants", "mod_exammanagement").'</p>');

            $moodleParticipants = $UserObj->getExamParticipants(array('mode'=>'moodle'), array('matrnr', 'profile', 'groups'));        

            $noneMoodleParticipants = $UserObj->getExamParticipants(array('mode'=>'nonmoodle'), array('matrnr'));

            $i = 1;

            if($moodleParticipants || $noneMoodleParticipants){

                $courseGroups = groups_get_all_groups($ExammanagementInstanceObj->getCourse()->id);

                if(count($courseGroups) > 0){
                    $courseGroups = true;
                } else {
                    $courseGroups = false;                    
                }

                echo('<div class="table-responsive">');
                echo('<table class="table table-striped exammanagement_table">');
                echo('<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">'.get_string("participants", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th>');
                
                if($courseGroups){
                    echo('<th scope="col">'.get_string("course_groups", "mod_exammanagement").'</th>');
                }

                echo('<th scope="col">'.get_string("import_state", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_whiteborder_left">'.get_string("options", "mod_exammanagement").'</th></thead>');
                echo('<tbody>');

                // show participants with moodle account
                if($moodleParticipants){
                    foreach ($moodleParticipants as $key => $participant) {

                        echo('<tr>');
                        echo('<th scope="row" id="'.$i.'">'.$i.'</th>');
                        echo('<td>'.$participant->profile.'</td>');
                        echo('<td>'.$participant->matrnr.'</td>');

                        if($courseGroups){
                            echo('<td>'.$participant->groups.'</td>');
                        }

                        echo('<td>'.get_string("state_added_to_exam", "mod_exammanagement").'</td>');
                        echo('<td class="exammanagement_brand_bordercolor_left"><a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/viewParticipants.php', $id, 'dpmid', $participant->moodleuserid).'" onClick="javascript:return confirm(\''.get_string("participant_deletion_warning", "mod_exammanagement").'\');" title="'.get_string("delete_participant", "mod_exammanagement").'"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a>');
                        echo('<a class="pull-right" href="#end" title="'.get_string("jump_to_end", "mod_exammanagement").'"><i class="fa fa-2x fa-lg fa-arrow-down" aria-hidden="true"></i></a></td>');
                        echo('</tr>');

                        $i++;
                    }
                }

                // show participants withouth moodle account

                if($noneMoodleParticipants){

                    if(!$moodleParticipants){
                        if($courseGroups){
                            echo('<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>');                        
                        } else {
                            echo('<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>');
                        }
                    }

                    echo('<tr class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><td colspan="6" class="text-center"><strong>'.get_string("participants_without_moodle_account", "mod_exammanagement",['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]).'</strong></td></tr>');
                    
                    foreach ($noneMoodleParticipants as $key => $participant) {

                        echo('<tr>');
                        echo('<th scope="row" id="'.$i.'">'.$i.'</th>');
                        echo('<td>'.$participant->firstname.' '.$participant->lastname.'</td>');
                        echo('<td>'.$participant->matrnr.'</td>');

                        if($courseGroups){
                            echo('<td> - </td>');
                        }

                        echo('<td>'.get_string("state_added_to_exam_no_moodle", "mod_exammanagement",['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]).'</td>');
                        echo('<td class="exammanagement_brand_bordercolor_left"><a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/viewParticipants.php', $id, 'dpmatrnr', $participant->imtlogin).'" onClick="javascript:return confirm(\''.get_string("participant_deletion_warning", "mod_exammanagement").'\');" title="'.get_string("delete_participant", "mod_exammanagement").'"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a>');
                        echo('<a class="pull-right" href="#end" title="'.get_string("jump_to_end", "mod_exammanagement").'"><i class="fa fa-2x fa-lg fa-arrow-down" aria-hidden="true"></i></a></td>');
                        echo('</tr>');
                    
                        $i++;

                    }

                }
                echo('</tbody></table></div>');
        
            } else {
                    echo('<div class="row"><p class="col-xs-12 text-xs-center">'.get_string("no_participants_added_page", "mod_exammanagement").'</p></div>');
            }

            echo('<div class="row" id="end"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $id).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a>');

            if($moodleParticipants || $noneMoodleParticipants){
            echo ('<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/viewParticipants.php', $id, 'dap', true).'" class="btn btn-default m-l-1" onClick="javascript:return confirm(\''.get_string("all_participants_deletion_warning", "mod_exammanagement").'\');">'.get_string("delete_all_participants", "mod_exammanagement").'</a></div>');
            }

            $MoodleObj->outputFooter();
        } else { // if user hasnt entered correct password for this session: show enterPasswordPage
            redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
        }
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}