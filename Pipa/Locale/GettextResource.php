<?php

namespace Pipa\Locale;
use Exception;

class GettextResource extends Resource {

    private $bigEndian;

    private $file;

    function load($filename) {
        $this->bigEndian = false;
        $this->file = @fopen($filename, 'rb');
        $data = [];

        if (!$this->file) throw new Exception("Error opening translation file '$filename'");
        if (@filesize($filename) < 10) throw new Exception("'$filename' is not a gettext file");

        // get Endian
        $input = $this->readMOData(1);
        if (strtolower(substr(dechex($input[1]), -8)) == "950412de") {
            $this->bigEndian = false;
        } elseif (strtolower(substr(dechex($input[1]), -8)) == "de120495") {
            $this->bigEndian = true;
        } else {
            throw new Exception("'$filename' is not a gettext file");
        }
        // read revision - not supported for now
        $input = $this->readMOData(1);

        // number of bytes
        $input = $this->readMOData(1);
        $total = $input[1];

        // number of original strings
        $input = $this->readMOData(1);
        $OOffset = $input[1];

        // number of translation strings
        $input = $this->readMOData(1);
        $TOffset = $input[1];

        // fill the original table
        fseek($this->file, $OOffset);
        $origtemp = $this->readMOData(2 * $total);
        fseek($this->file, $TOffset);
        $transtemp = $this->readMOData(2 * $total);

        for ($count = 0; $count < $total; ++$count) {
            if ($origtemp[$count * 2 + 1] != 0) {
                fseek($this->file, $origtemp[$count * 2 + 2]);
                $original = @fread($this->file, $origtemp[$count * 2 + 1]);
                $original = explode("\0", $original);
            } else {
                $original[0] = '';
            }

            if ($transtemp[$count * 2 + 1] != 0) {
                fseek($this->file, $transtemp[$count * 2 + 2]);
                $translate = fread($this->file, $transtemp[$count * 2 + 1]);
                $translate = explode("\0", $translate);
                if ((count($original) > 1) && (count($translate) > 1)) {
                    $data[$original[0]] = $translate;
                    array_shift($original);
                    foreach ($original as $orig) {
                        $data[$orig] = '';
                    }
                } else {
                    $data[$original[0]] = $translate[0];
                }
            }
        }

        return $data;
    }

    private function readMOData($bytes) {
        if ($this->bigEndian === false) {
            return unpack('V' . $bytes, fread($this->file, 4 * $bytes));
        } else {
            return unpack('N' . $bytes, fread($this->file, 4 * $bytes));
        }
    }

}
