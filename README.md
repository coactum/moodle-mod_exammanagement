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
@copyright   coactum GmbH 2019
@license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

# Exam management #

## Description ##

The exam management allows the easy organization of exams for a course and makes it possible to manage even large exams with many participants.

In the lecturers view a lecturer can

- set the basic exam data
- export documents that are useful for the exam, such as seating plans and lists of participants
- enter the exam results for the participants manually or using a barcode scanner
- export all results in various documents for further use (e.g. by the exam office)

The exam participants, on the other hand, can see all the relevant information about the exam, such as the date, their seat or the bonus grade steps achieved for the exam in their own view. In addition, the notification function allows an easy and reliable communication with the participants.

## Quick installation instructions ##

1) Be sure you have at least Moodle 3.5 installed.
2) Be sure you have the latest version of the module.
3) Be sure you have the latest version of all your used language packs.
4) Move the plugin folder to your moodles /mod directory.
5) Open the site admninistration to start plugin installation.
6) Wait untill installation is finished.
7) Create an exam management activity and add or import default exam rooms that can be used by all teachers.
8) Optional: Enable the possibility for all teachers to request a passwort reset.
9) Have fun organizing your exams!

## Dependencies ##

To ensure that the exam management plugin works as intended make sure that

- you have configured the mail server for your moodle
- sheduled tasks are performed automatically
- the ldap authentication plugin is installed within your moodle and that you have configured it to connect to your own ldap server to fetch matriculation numbers for your participants

Comments and suggestions are always welcome.

D.