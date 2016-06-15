<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2016.
 * @link      https://www.kuestenschmiede.de
 */

namespace c4g\Forum;

/**
 * Single file upload handler to return only 1 input field for a single file upload.
 *
 * Class C4gForumSingleFileUpload
 */
class C4gForumSingleFileUpload extends \Contao\FileUpload
{

    /**
     * Overwrite parents method to only output 1 input field with no multiple attribute.
     *
     * @return string
     */
    public function generateMarkup()
    {
        $sField = '<input type="file" name="' . $this->strName . '[]" class="tl_upload_field" onfocus="Backend.getScrollOffset()"><br>';

        return '<div id="upload-fields">'.$sField.'</div>';
    }

}