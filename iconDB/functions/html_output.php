<?php
//
// +----------------------------------------------------------------------+
// |Liga Manager Online						          |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004-2007                     			  |
// |                                                                      |
// | http://www.liga-manager-online.de                                    |
// |                                                                      |
// | Copyright (c) 2006 LMO Group					  |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU General Public License as       |
// | published by the Free Software Foundation; either version 2 of       |
// | the License, or (at your option) any later version.		  |
// |									  |
// | This program is distributed in the hope that it will be useful,	  |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of	  |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU	  |
// | General Public License for more details.				  |
// |									  |
// | REMOVING OR CHANGING THE COPYRIGHT NOTICES IS NOT ALLOWED!           |
// +----------------------------------------------------------------------+
//

/**
 * @author  Tim Schumacher <webobjects@gmx.net>
 * @package classLib
 * @access 	public
 * @version $Id$
 */
function findImage($key, $path, $imgType, $htmlParameter = '', $alternative_text = '')
{
    $key = str_replace('/', '', $key);
	if ($imgType == ".svg")
           $htmlParameter .= " width='24'";
	   
    if (!file_exists(ICON_PATH . $path . $key . $imgType)) {
        $key = preg_replace('/[^a-zA-Z0-9]/', '', $key);
    } else {
        $imgdata = getimagesize(ICON_PATH . $path . $key . $imgType);
        $size = isset($imgdata[3]) ? $imgdata[3] : '';
        return ("<img src='" . $path . rawurlencode($key) . "$imgType' " . $size . '  ' . $htmlParameter . ' ' . $alternative_text . ' /> ');
    }

    if (!file_exists(ICON_PATH . $path . $key . $imgType)) {
        $key = preg_replace('/[I(A)0-9]+$/', '', $key);
    } else {
        $imgdata = getimagesize(ICON_PATH . $path . $key . $imgType);
        $size = isset($imgdata[3]) ? $imgdata[3] : '';
        return ("<img src='" . $path . rawurlencode($key) . "$imgType' " . $size . '  ' . $htmlParameter . ' ' . $alternative_text . ' /> ');
    }

    if (!file_exists(ICON_PATH . $path . $key . $imgType)) {
        return $alternative_text;
    } else {
        $imgdata = getimagesize(ICON_PATH . $path . $key . $imgType);
        $size = isset($imgdata[3]) ? $imgdata[3] : '';
        return ("<img src='" . $path . rawurlencode($key) . "$imgType' " . $size . '  ' . $htmlParameter . ' ' . $alternative_text . ' /> ');
    }
}

/**
 * Returns HTML image code for a small team icon
 *
 * @param        string     $team       Long name of the team
 * @param        string     $alternative_text      If image not found return this instead
 * @return       string     HTML image-Code for the small team icon
 */
function HTML_TeamIcon($team, $html = '', $alternative_text = '')
{
    foreach (const_array(IMG_TYPES) as $extension) {
        if ($imgHTML = findImage($team, ICON_URL, $extension, $html, $alternative_text)) {
            break;
        }
    }
    return $imgHTML;
}

/**
 * Returns fullpath image for a team icon
 *
 * @param        string     $team       Long name of the team
 * @return       string     fullpath of image location
 */
function GET_TeamIcon($team)
{
    $file = '';
    foreach (const_array(IMG_TYPES) as $extension) {
        if (file_exists(ICON_PATH . ICON_URL . $team . $extension)) {
            $file = ICON_URL . $team . $extension;
            break;
        }
    }
    return $file;
}

/**
 * Returns constants as array
 *
 * @param       string     $constant	Constants
 * @return	array	   $array	Array of $constant
 */
function const_array($constant)
{
    $array = explode(',', $constant);
    return $array;
}

?>