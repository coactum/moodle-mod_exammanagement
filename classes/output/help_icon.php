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
 * Help icon for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2024 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_exammanagement\output;

use renderer_base;
use stdClass;

/**
 * Help icon that substitutes the configured system name and the configured additional resources link.
 *
 * Language files must only contain plain string literals, so the formerly used executable code
 * (get_config()) inside the *_help and *_link strings has been removed. Instead the help texts use the
 * {$a} placeholder for the configured system name and the "More help" link is injected here at runtime.
 *
 * @package     mod_exammanagement
 * @copyright   2024 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class help_icon extends \help_icon {
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer used to render the help icon.
     * @return stdClass The data ready for the core/help_icon template.
     */
    public function export_for_template(renderer_base $output) {
        $data = parent::export_for_template($output);

        $systemname = get_config('mod_exammanagement', 'moodlesystemname');
        if (empty($systemname)) {
            $systemname = 'Moodle';
        }

        // Re-render the help body with the configured system name substituted for the {$a} placeholder.
        $formatted = get_formatted_help_string($this->identifier, $this->component, false, $systemname);
        $data->text = $formatted->text;

        // Provide the admin-configured additional resources URL as the "More help" link.
        $additionalressources = get_config('mod_exammanagement', 'additionalressources');
        if (!empty($additionalressources)) {
            $data->doclink = new stdClass();
            $data->doclink->link = $additionalressources;
            $data->doclink->linktext = get_string('morehelp');
            $data->doclink->class = ' helplinkpopup';
        } else {
            unset($data->doclink);
        }

        return $data;
    }
}
