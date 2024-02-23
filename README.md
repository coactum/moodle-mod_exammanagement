# License #

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.

@package     mod_exammanagement
@copyright   2022 coactum GmbH
@license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

# Exam management #

## Description ##

The exam management allows the easy organization of exams in courses and makes it possible to manage even large attendance exams with many participants.

In their view teachers can

- set the basic exam data
- export documents that are useful for the exam, such as seating plans and lists of participants
- enter the exam results for the participants manually or using a barcode scanner or a smartphone with qr codes
- export all results in various documents for further use (e.g. by the exam office)

The exam participants, on the other hand, can see all the relevant information about the exam, such as the date, their seat, the bonus grade steps achieved for the exam or the exam results in their own view. In addition, the notification function allows an easy and reliable communication with the participants.

## Quick installation instructions ##

1) Be sure you have at least Moodle 3.9 installed.
2) Be sure you have the latest version of the module.
3) Be sure you have the latest version of all your used language packs.
4) Move the plugin folder to your moodles /mod directory.
5) Open the site admninistration to start plugin installation.
6) Wait until installation is finished.
7) Set plugin settings.
8) Make sure you have all necessarry components active and configured (see next chapter of this file)
9) Create an exam management activity and add or import default exam rooms that can be used by all teachers.
10) Optional: Enable the possibility for all teachers to request a passwort reset (see below).
11) Have fun organizing your exams!

## Dependencies ##

To ensure that the exam management plugin works as intended make sure that ...

- you have configured the mail server for your moodle (if not no external mails for groupmessages or resetting password can be send)
- sheduled tasks are performed automatically (if not old exam data won't be deleted by moodle)
- the ldap authentication plugin "LDAP server" (auth_ldap) is installed within your moodle and that you have configured it to connect to your own ldap server (see https://docs.moodle.org/403/en/LDAP_authentication)
- you have entered the correct ldap settings in the admin plugin settings (see the next chapter of this file) to fetch matriculation numbers for the participants (else you can't use matriculation numbers and depending features like exam labels)

## Configure ldap in the plugin settings ##

If you want to use some very usefull plugin functionalities like the import of participants (even without a moodle account) from an external exam list, the entering of results by matriculation number or the export of exam labels you have to make sure that the ldap authentication plugin "LDAP server" (auth_ldap) is enabled and configured. Then you have to enable and configure the ldap part in the settings of this plugin.

To achieve this you have to do the following steps:
- (Required) Enable the use of an external ldap server specified in ldap authentication plugin "LDAP server" ("auth_ldap") by ticking the enableldap admin setting of the plugin.
- (Optional) If you want to use a different ldap then the one speicifed in the auth_ldap plugin you can set it's "distinguished name" dn here. If this field is left empty, the plugin uses the value "contexts" from the global settings of auth_ldap. If neither of these two fields is filled in, the ldap dependent plugin functions cannot be used.
- (Optional) You can specify the name of the ldap field where the username of the participant is located. This username must match the username of the participant in moodle. If this field is left empty, the plugin uses the value "field_map_idnumber" from the global settings of auth_ldap. If neither of these two fields is filled in, the ldap dependent plugin functions cannot be used.
- (Required) You have to enter the name of the ldap field where the participants matriculation number is located here. If this field is not filled in, the ldap dependent plugin functions cannot be used.
- (Optional) You can also specify the ldap fields containing the names and email adresses of the users. These information is saved for all exam participants that don't have an account in moodle. If these fields are left empty, the plugin uses the corresponding values from the global settings of auth_ldap.
- (Optional) You can also set a class name that is then used as an additional filter criterion for the participant object in ldap.

If all necessary steps are done the plugin automatically uses the data stored in the ldap.

## Additional plugin settings

As an admin you can set the following additional plugin settings:

- moodlesystemname: The name of the moodle installation that will be displayed in the plugin (e.g. in helptexts).
- enableglobalmessage & globalmessage: You can enable and set a short message that will be shown to all teachers when they create a new exam management.
- enablehelptexts & additionalressources: You can enable the module internal helptexts for each work step in the plugin and set a link to an additional external ressource (e.g. your own documentation or the plugin page in the moodle docs).
- enablepasswordresetrequest: You can enable teachers to request the reset of a password in an exam management. If a teacher does so, all users with the role "Manager" receive an automatically generated message both as internal notification and forwarded to the e-mail address stored in their profile and can then reset the password by clicking on the link contained in this message. This means that all teachers of the exam management concerned are automatically informed via internal notification and e-mail that the password has been resetted and that the contents of the exam management can be accessed again without entering a password. If this function is not activated, users cannot automatically request the password reset in their exam management, but managers and administrators can still reset the password of any exam management.

## For themes ##

To change the color of the panels or the table headers through a theme just override exammanagement_brand_backgroundcolor.

## Use of logos in documents ##

If you want to customize the exported result documents with a logo you can add two files to a /data folder in the plugins base folder.

A file called logo.ai will be used in the documents from exportresultsexamreview.php and exportresultspercentages.php while logo_full.ai will be used in documents from participantsList.php and seatingplan.php.

If no files are uploaded to /data the exported result files will not contain any logos.