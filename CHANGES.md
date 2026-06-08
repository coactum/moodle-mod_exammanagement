## Changelog ##

- [2.0.11]:
    - Removed executable code (get_config calls) from the language files; language strings are now plain data as required by the Moodle standards. The configured system name is now passed as a {$a} placeholder and the additional resources link is injected via a custom help icon.

- [2.0.10]:
    - Added moodle 4.5 compatibility.

- [2.0.9]:
    - Removed old indices (for legacy installations of the plugin).

- [2.0.8]:
    - Activity completion for views now possible.
    - Small last bugfixes.

- [2.0.7]:
    - Fixed typo in view event.

- [2.0.6]:
    - Small bugfixes and fixes for moodle coding style.

- [2.0.5]:
    - Fixed typo in github workflow.

- [2.0.4]:
    - Shortened long course names in exported documents.

- [2.0.3]:
    - Renamed more files from camelCase to lowercase.

- [2.0.2]:
    - Renamed files from camelCase to lowercase.

- [2.0.1]:
    - Some small bugfixes and further changes to reflect the moodle coding style.

- [2.0.0]:
    - Removing images und some legacy code.
    - Version for submission into the moodle plugins repository.

- [1.5.9]:
    - Total rework of all files and forms to make plugin fully compatible with the moodle coding style.
    - [Bugfix]: Exam labels now usable even if course name is very long.
    - SVGs now have the correct width.
    - Some other small fixes and improvements.

- [1.5.8]:
    - Removed some validation of matriculation numbers to allow a broader variety of numbers for the participants import.
    - Fixed some warnings related to json_decode functions that are displayed in current php versions.

- [1.5.7]:
    - Added monologo version of the plugin icon for Moodle 4.0 and above.
    - Added changes.md to keep track of plugin changes.
