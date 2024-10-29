<?php

/*
 * Zip file creation class
 * makes zip files on the fly...
 *
 * use the functions add_dir() and add_file() to build the zip file;
 * see example code below
 *
 * by Eric Mueller
 * http://www.themepark.com
 *
 * v1.1 9-20-01
 *   - added comments to example
 *
 * v1.0 2-5-01
 *
 * initial version with:
 *   - class appearance
 *   - add_file() and file() methods
 *   - gzcompress() output hacking
 * by Denis O.Philippov, webmaster@atlant.ru, http://www.atlant.ru
 */

// official ZIP file format: http://www.pkware.com/appnote.txt

class zipfile
{
    var $datasec = array();  // array to store compressed data
    var $ctrl_dir = array();  // central directory
    var $eof_ctrl_dir = "PK\x05\x06\0\0\0\0";  // end of Central directory record
    var $old_offset = 0;

    function add_dir($name)
    // adds "directory" to archive - do this before putting any files in directory!
    // $name - name of directory... like this: "path/"
    // ...then you can add files using add_file with names like "path/file.txt"
    {
        $name = str_replace('\\', '/', $name);

        $fr = "PK\x03\x04";
        $fr .= "\n\0";  // ver needed to extract
        $fr .= "\0\0";  // gen purpose bit flag
        $fr .= "\0\0";  // compression method
        $fr .= "\0\0\0\0";  // last mod time and date

        $fr .= pack('V', 0);  // crc32
        $fr .= pack('V', 0);  // compressed filesize
        $fr .= pack('V', 0);  // uncompressed filesize
        $fr .= pack('v', strlen($name));  // length of pathname
        $fr .= pack('v', 0);  // extra field length
        $fr .= $name;
        // end of "local file header" segment

        // no "file data" segment for path

        // "data descriptor" segment (optional but necessary if archive is not served as file)
        $fr .= pack('V', $crc);  // crc32
        $fr .= pack('V', $c_len);  // compressed filesize
        $fr .= pack('V', $unc_len);  // uncompressed filesize

        // add this entry to array
        $this->datasec[] = $fr;

        $new_offset = strlen(implode('', $this->datasec));

        // ext. file attributes mirrors MS-DOS directory attr byte, detailed
        // at http://support.microsoft.com/support/kb/articles/Q125/0/19.asp

        // now add to central record
        $cdrec = "PK\x01\x02";
        $cdrec .= "\0\0";  // version made by
        $cdrec .= "\n\0";  // version needed to extract
        $cdrec .= "\0\0";  // gen purpose bit flag
        $cdrec .= "\0\0";  // compression method
        $cdrec .= "\0\0\0\0";  // last mod time &amp; date
        $cdrec .= pack('V', 0);  // crc32
        $cdrec .= pack('V', 0);  // compressed filesize
        $cdrec .= pack('V', 0);  // uncompressed filesize
        $cdrec .= pack('v', strlen($name));  // length of filename
        $cdrec .= pack('v', 0);  // extra field length
        $cdrec .= pack('v', 0);  // file comment length
        $cdrec .= pack('v', 0);  // disk number start
        $cdrec .= pack('v', 0);  // internal file attributes
        $ext = "\0\0\x10\0";
        $ext = "\xff\xff\xff\xff";
        $cdrec .= pack('V', 16);  // external file attributes  - 'directory' bit set

        $cdrec .= pack('V', $this->old_offset);  // relative offset of local header
        $this->old_offset = $new_offset;

        $cdrec .= $name;
        // optional extra field, file comment goes here
        // save to array
        $this->ctrl_dir[] = $cdrec;
    }

    function add_file($data, $name)
    // adds "file" to archive
    // $data - file contents
    // $name - name of file in archive. Add path if your want
    {
        $name = $this->fix_zipname(str_replace('\\', '/', $name));
        // $name = str_replace("\\", "\\\\", $name);

        $fr = "PK\x03\x04";
        $fr .= "\x14\0";  // ver needed to extract
        $fr .= "\0\0";  // gen purpose bit flag
        $fr .= "\x08\0";  // compression method
        $fr .= "\0\0\0\0";  // last mod time and date

        $unc_len = strlen($data);
        $crc = crc32($data);
        $zdata = gzcompress($data);
        $zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2);  // fix crc bug
        $c_len = strlen($zdata);
        $fr .= pack('V', $crc);  // crc32
        $fr .= pack('V', $c_len);  // compressed filesize
        $fr .= pack('V', $unc_len);  // uncompressed filesize
        $fr .= pack('v', strlen($name));  // length of filename
        $fr .= pack('v', 0);  // extra field length
        $fr .= $name;
        // end of "local file header" segment

        // "file data" segment
        $fr .= $zdata;

        // "data descriptor" segment (optional but necessary if archive is not served as file)
        $fr .= pack('V', $crc);  // crc32
        $fr .= pack('V', $c_len);  // compressed filesize
        $fr .= pack('V', $unc_len);  // uncompressed filesize

        // add this entry to array
        $this->datasec[] = $fr;

        $new_offset = strlen(implode('', $this->datasec));

        // now add to central directory record
        $cdrec = "PK\x01\x02";
        $cdrec .= "\0\0";  // version made by
        $cdrec .= "\x14\0";  // version needed to extract
        $cdrec .= "\0\0";  // gen purpose bit flag
        $cdrec .= "\x08\0";  // compression method
        $cdrec .= "\0\0\0\0";  // last mod time &amp; date
        $cdrec .= pack('V', $crc);  // crc32
        $cdrec .= pack('V', $c_len);  // compressed filesize
        $cdrec .= pack('V', $unc_len);  // uncompressed filesize
        $cdrec .= pack('v', strlen($name));  // length of filename
        $cdrec .= pack('v', 0);  // extra field length
        $cdrec .= pack('v', 0);  // file comment length
        $cdrec .= pack('v', 0);  // disk number start
        $cdrec .= pack('v', 0);  // internal file attributes
        $cdrec .= pack('V', 32);  // external file attributes - 'archive' bit set

        $cdrec .= pack('V', $this->old_offset);  // relative offset of local header
        //        echo "old offset is ".$this->old_offset.", new offset is $new_offset<br>";
        $this->old_offset = $new_offset;

        $cdrec .= $name;
        // optional extra field, file comment goes here
        // save to central directory
        $this->ctrl_dir[] = $cdrec;
    }

    function file()
    {  // dump out file
        $data = implode('', $this->datasec);
        $ctrldir = implode('', $this->ctrl_dir);

        return
            $data
            . $ctrldir
            . $this->eof_ctrl_dir
            . pack('v', sizeof($this->ctrl_dir))  // total # of entries "on this disk"
            . pack('v', sizeof($this->ctrl_dir))  // total # of entries overall
            . pack('V', strlen($ctrldir))  // size of central dir
            . pack('V', strlen($data))  // offset to start of central dir
            . "\0\0";  // .zip file comment length
    }

    function fix_zipname($name)
    {
        $fixed_name = strtr(
            $name,
            "\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff",
            "\xad\xbd\x9c\xcf\xbe\xdd\xf5\xf9\xb8\xa6\xae\xaa\xf0\xa9\xee\xf8\xf1\xfd\xfc\xef\xe6\xf4\xfa\xf7\xfb\xa7\xaf\xac\xab\xf3\xa8\xb7\xb5\xb6\xc7\x8e\x8f\x92\x80\xd4\x90\xd2\xd3\xde\xd6\xd7\xd8\xd1\xa5\xe3\xe0\xe2\xe5\x99\x9e\x9d\xeb\xe9\xea\x9a\xed\xe8\xe1\x85 \x83\xc6\x84\x86\x91\x87\x8a\x82\x88\x89\x8d\xa1\x8c\x8b\xd0\xa4\x95\xa2\x93\xe4\x94\xf6\x9b\x97\xa3\x96\x81\xec\xe7\x98"
        );
        return $fixed_name;
    }
}
?>