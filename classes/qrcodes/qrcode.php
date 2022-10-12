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
 * This file contains a class that provides functions for displaying a QR code.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\qrcodes;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/exammanagement/thirdparty/vendor/autoload.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Class exammanagement_qrcode
 *
 * Display QR code.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exammanagement_qrcode {
    /**
     * QR code is saved in this file.
     * @var string
     */
    protected $file;

    /**
     * Size of qrcode.
     * @var int
     */
    protected $size;

    /**
     * Url to be encoded in the qr code.
     * @var int
     */
    protected $url;

    /**
     * Identifier (used for creating unique files for each qr code).
     * @var int
     */
    protected $identifier;

    /**
     * Module instance for which the qrcode is created.
     * @var \stdClass
     */
    protected $moduleinstance;

    /**
     * output_image constructor.
     * @param int $size image size
     * @param int $moduleinstance the exammanagement module instance
     */
    public function __construct($url, $identifier, $moduleinstance) {
        global $CFG, $DB;
        $this->size = 50;
        $this->url = $url;
        $this->identifier = $identifier;
        $this->moduleinstance = $moduleinstance;

        $file = $CFG->localcachedir . '/mod_exammanagement/exammanagement-' . $moduleinstance->id . '-qr-' . $this->identifier; // Set file path.

        // Add file ending.
        $file .= '.svg';
        $this->file = $file;
    }

    /**
     * Creates the QR code if it doesn't exist.
     */
    public function create_image() {
        global $CFG;
        // Checks if QR code already exists.
        if (file_exists($this->file)) {
            // File exists in cache.
            return;
        }

        // Checks if directory already exists.
        if (!file_exists(dirname($this->file))) {
            // Creates new directory.
            mkdir(dirname($this->file), $CFG->directorypermissions, true);
        }

        // Creates the QR code.
        $qrcode = new QrCode($this->url->out(false));
        $qrcode->setSize($this->size);

        // Set advanced options.
        $qrcode->setMargin(10);
        $qrcode->setEncoding('UTF-8');
        $qrcode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
        $qrcode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0]);
        $qrcode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255]);

        // SVG format.
        $qrcode->setWriterByName('svg');
        $qrcode->writeFile($this->file);
    }

    /**
     * Returns the file with the qr code.
     * @return string Path of the file with the qr code.
     */
    public function get_qrcode() {
        $this->create_image();

        return $this->file;
    }
}
