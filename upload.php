<?php
// **********************************************
//
// This software is licensed by the LGPL
// -> http://www.gnu.org/copyleft/lesser.txt
// (c) 2001 by Tomas Von Veschler Cox
//
// **********************************************
//
// $Id: Upload.php,v 1.23 2002/05/28 15:33:36 cox Exp $

/*
* Pear File Uploader class. Easy and secure managment of files
* submitted via HTML Forms.
*
* @see http://vulcanonet.com/soft/index.php?pack=uploader
* @author Tomas V.V.Cox <cox@vulcanonet.com>
*
* Leyend:
* - you can add error msgs in your language in the HTTP_Upload_Error class
*
* TODO:
* - addapt the class to new upload features of the 4.2 (?) release
*   (the new error entry in HTTP_POST_FILES)
* - try to think a way of having all the Error system in other
*   file and only include it when an error ocurrs
*
* -- Note for users of PHP > 4.1 --
*
* Due the fact that PHP now doesn't register the HTTP_POST_FILES if no
* uploads are done, the class is not able to give verbose information
* about what happens. To check that you have to use the new isMissing()
* method avaible from the $upload object too. For ex:
*
* $upload = new HTTP_Upload('en');
* $missing = $upload->isMissing();
* if (!$missing) {
*    <code for manage files>
* } else {
*    die ($missing->getMessage());
* }
*
* -- Notes --
*/

require_once __DIR__ . '/PEAR.php';

/**
 * Error Class for HTTP_Upload
 *
 * @author Tomas V.V.Cox <cox@vulcanonet.com>
 */
class HTTP_Upload_Error extends PEAR
{
    /**
     * Selected language for error messages
     * @var string
     */

    public $lang = 'en';

    /**
     * Constructor
     *
     * Creates a new PEAR_Error
     *
     * @param null $lang The language selected for error code messages
     */

    public function __construct($lang = null)
    {
        $this->lang = $lang ?? $this->lang;

        global $_POST;

        $maxsize = $_POST['MAX_FILE_SIZE'] ?? null;

        $ini_size = ini_get('upload_max_filesize');

        if (empty($maxsize) || ($maxsize > $ini_size)) {
            $maxsize = $ini_size;
        }

        // XXXXX Add here error messages in your language

        $this->error_codes = [
            'TOO_LARGE' => [
                'es' => "Fichero demasiado largo. El maximo permitido es: $maxsize bytes",
                'en' => "File size too large. The maximum permitted size is: $maxsize bytes",
                'de' => "Datei zu gro&szlig;. Die zul&auml;ssige Maximalgr&ouml;&szlig;e ist: $maxsize bytes",
                'nl' => "Het bestand is te groot, de maximale grootte is: $maxsize bytes",
                'fr' => "Le fichier est trop gros. La taille maximum autoris&eacute;e est: $maxsize bytes",
                'it' => "Il file &eacute; troppo grande. Il massimo permesso &eacute: $maxsize bytes",
                ],
            'MISSING_DIR' => [
                'es' => 'Falta directorio destino',
                'en' => 'Missing destination directory',
                'de' => 'Kein Zielverzeichnis definiert',
                'nl' => 'Geen bestemmings directory.',
                'fr' => 'Le r&eacute;pertoire de destination n\'est pas d&eacute;fini',
                'it' => 'Manca la directory di destinazione',
                ],
            'IS_NOT_DIR' => [
                'es' => 'El directorio destino no existe o es un fichero regular',
                'en' => 'The destination directory doesn\'t exist or is a regular file',
                'de' => 'Das angebene Zielverzeichnis existiert nicht oder ist eine Datei',
                'nl' => 'De doeldirectory bestaat niet, of is een gewoon bestand',
                'fr' => 'Le r&eacute;pertoire de destination n\'existe pas ou il s\'agit d\'un fichier r&eacute;gulier',
                'it' => 'La directory di destinazione non esiste o &eacute; un file',
                ],
            'NO_WRITE_PERMS' => [
                'es' => 'El directorio destino no tiene permisos de escritura',
                'en' => 'The destination directory doesn\'t have write perms',
                'de' => 'Fehlende Schreibrechte f&uuml;r das Zielverzeichnis',
                'nl' => 'Geen toestemming om te schrijven in de doeldirectory.',
                'fr' => 'Le r&eacute;pertoire de destination n\'a pas les droits en &eacute;criture.',
                'it' => 'Non si hanno i permessi di scrittura sulla directory di destinazione',
                ],
            'NO_USER_FILE' => [
                'es' => 'No se ha escogido fichero para el upload',
                'en' => 'You haven\'t selected any file for uploading',
                'de' => 'Es wurde keine Datei für den Upload ausgew&auml;hlt',
                'nl' => 'Er is geen bestand opgegeven om te uploaden.',
                'fr' => 'Vous n\'avez pas s&eacute;lectionn&eacute; de fichier &agrave; envoyer.',
                'it' => 'Nessun file selezionato per l\'upload',
                ],
            'BAD_FORM' => [
                'es' => 'El formulario no contiene METHOD="post" ENCTYPE="multipart/form-data" requerido',
                'en' => 'The html form doesn\'t contain the required METHOD="post" ENCTYPE="multipart/form-data"',
                'de' => 'Das HTML-Formular enth&auml;lt nicht die Angabe METHOD="post" ENCTYPE="multipart/form-data" ' .
                        'im &gt;form&lt;-Tag',
                'nl' => 'Het HTML-formulier bevat niet de volgende benodigde ' .
                        'eigenschappen: method="post" enctype="multipart/form-data"',
                'fr' => 'Le formulaire HTML ne contient pas les attributs requis : ' .
                        ' method="post" enctype="multipart/form-data"',
                'it' => 'Il modulo HTML non contiene gli attributi richiesti: "' .
                        ' method="post" enctype="multipart/form-data"',
                ],
            'E_FAIL_COPY' => [
                'es' => 'Fallo al copiar el fichero temporal',
                'en' => 'Failed to copy the temporary file',
                'de' => 'Tempor&auml;re Datei konnte nicht kopiert werden',
                'nl' => 'Het tijdelijke bestand kon niet gekopieerd worden',
                'fr' => 'L\'enregistrement du fichier temporaire a échou&eacute;',
                'it' => 'Copia del file temporaneo fallita',
                ],
            'FILE_EXISTS' => [
                'es' => 'El fichero destino ya existe',
                'en' => 'The destination file already exists',
                'de' => 'Die zu erzeugende Datei existiert bereits',
                'nl' => 'Het doelbestand bestaat al',
                'fr' => 'Le fichier de destination existe d&eacute;j&agrave;',
                'it' => 'File destinazione gi&agrave; esistente',
                ],
            'CANNOT_OVERWRITE' => [
                'es' => 'El fichero destino ya existe y no se puede sobreescribir',
                'en' => 'The destination file already exists and could not be overwritten',
                'de' => 'Die zu erzeugende Datei existiert bereits und konnte nicht &uuml;berschrieben werden',
                'nl' => 'Het doelbestand bestaat al, en kon niet worden overschreven',
                'fr' => 'Le fichier de destination existe d&eacute;j&agrave; et ne peux pas &ecirc;tre remplac&eacute;',
                'it' => 'File destinazione gi&agrave; esistente e non si pu&ograve; sovrascrivere',
                ],
            'NOT_ALLOWED_EXTENSION' => [
                'es' => 'Extension de fichero no permitida',
                'en' => 'Not permitted file extension',
                'de' => 'Unerlaubte Dateiendung',
                'nl' => 'Niet toegestane bestands-extensie.',
                'fr' => 'Le fichier a une extension non autoris&eacute;e.',
                'it' => 'Estensione del File non permessa',
                ],
        ];
    }

    /**
     * returns the error code
     *
     * @param    string $e_code  type of error
     * @return   string          Error message
     */

    public function errorCode($e_code)
    {
        if (isset($this->lang) &&
            !empty($this->error_codes[$e_code][$this->lang])) {
            $msg = $this->error_codes[$e_code][$this->lang];
        } else {
            $msg = $e_code;
        }

        return 'Upload Error: ' . $msg;
    }

    /**
     * Overwrites the PEAR::raiseError method
     *
     * @param    string $e_code      type of error
     * @return   object PEAR_Error   a PEAR-Error object
     */

    public function raiseError($e_code)
    {
        return PEAR::raiseError($this->errorCode($e_code), $e_code);
    }
}

/**
 * This class provides an advanced file uploader system
 * for file uploads made from html forms
 *
 * @author   Tomas V.V.Cox <cox@vulcanonet.com>
 */
class HTTP_Upload extends HTTP_Upload_Error
{
    /**
     * Contains an array of "uploaded files" objects
     * @var array
     */

    public $files = [];

    /**
     * Constructor
     *
     * @param null $lang Language to use for reporting errors
     * @see Upload_Error::error_codes
     */

    public function __construct($lang = null)
    {
        parent::__construct($lang);

        global $HTTP_POST_FILES, $HTTP_SERVER_VARS;

        $this->post_files = $HTTP_POST_FILES;

        $this->content_type = $HTTP_SERVER_VARS['CONTENT_TYPE'];
    }

    /**
     * Get files
     *
     * @param mixed $file If:
     *    - not given, function will return array of upload_file objects
     *    - is int, will return the $file position in upload_file objects array
     *    - is string, will return the upload_file object corresponding
     *        to $file name of the form. For ex:
     *        if form is <input type="file" name="userfile">
     *        to get this file use: $upload->getFiles('userfile')
     *
     * @return mixed array or object (see @param above $file) or Pear_Error
     */

    public function getFiles($file = null)
    {
        //build only once for multiple calls

        if (!isset($this->is_built)) {
            $this->files = $this->_buildFiles();

            if (PEAR::isError($this->files)) {
                return $this->files;
            }

            $this->is_built = true;
        }

        if (null !== $file) {
            if (is_int($file)) {
                $pos = 0;

                foreach ($this->files as $key => $obj) {
                    if ($pos == $file) {
                        return $obj;
                    }

                    $pos++;
                }
            } elseif (is_string($file) && isset($this->files[$file])) {
                return $this->files[$file];
            }

            return $this->raiseError('requested file not found'); // XXXX
        }

        return $this->files;
    }

    /**
     * Creates the list of the uploaded file
     *
     * @return array of HTTP_Upload_File objects for every file
     */

    public function _buildFiles()
    {
        // Form method check

        if (!preg_match('^multipart/form-data', $this->content_type)) {
            return $this->raiseError('BAD_FORM');
        }

        // In 4.1 HTTP_POST_FILES isn't initialized when no uploads

        if (function_exists('version_compare') && version_compare(phpversion(), '4.1', 'ge')) {
            $error = $this->isMissing();

            if (PEAR::isError($error)) {
                return $error;
            }
        }

        // Parse $HTTP_POST_FILES

        $files = [];

        foreach ($this->post_files as $userfile => $value) {
            if (is_array($value['name'])) {
                foreach ($value['name'] as $key => $val) {
                    $name = basename($value['name'][$key]);

                    $tmp_name = $value['tmp_name'][$key];

                    $size = $value['size'][$key];

                    $type = $value['type'][$key];

                    $formname = $userfile . "[$key]";

                    $files[$formname] = new HTTP_Upload_File(
                        $name,
                        $tmp_name,
                        $formname,
                        $type,
                        $size,
                        $this->lang
                    );
                }

                // One file
            } else {
                $name = basename($value['name']);

                $tmp_name = $value['tmp_name'];

                $size = $value['size'];

                $type = $value['type'];

                $formname = $userfile;

                $files[$formname] = new HTTP_Upload_File(
                    $name,
                    $tmp_name,
                    $formname,
                    $type,
                    $size,
                    $this->lang
                );
            }
        }

        return $files;
    }

    /**
     * Checks if the user submited or not some file
     *
     * @return mixed False when are files or PEAR_Error when no files
     * @see Read the note in the source code about this function
     */

    public function isMissing()
    {
        if (empty($this->post_files) || count($this->post_files) < 1) {
            return $this->raiseError('NO_USER_FILE');
        }

        return false;
    }
}

/**
 * This class provides functions to work with the uploaded file
 *
 * @author   Tomas V.V.Cox <cox@vulcanonet.com>
 */
class HTTP_Upload_File extends HTTP_Upload_Error
{
    /**
     * If the random seed was initialized before or not
     * @var  bool;
     */

    public $_seeded = 0;

    /**
     * Assoc array with file properties
     * @var array
     */

    public $upload = [];

    /**
     * If user haven't selected a mode, by default 'safe' will be used
     * @var bool
     */

    public $mode_name_selected = false;

    /**
     * It's a common security risk in pages who has the upload dir
     * under the document root (remember the hack of the Apache web?)
     *
     * @var array
     * @see HTTP_Upload_File::setValidExtensions()
     */

    public $_extensions_check = ['php', 'phtm', 'phtml', 'php3', 'inc'];

    /**
     * @see HTTP_Upload_File::setValidExtensions()
     * @var string
     */

    public $_extensions_mode = 'deny';

    /**
     * Constructor
     *
     * @param null $name     destination file name
     * @param null $tmp      temp file name
     * @param null $formname name of the form
     * @param null $type     Mime type of the file
     * @param null $size     size of the file
     * @param null $lang     used language for errormessages
     */

    public function __construct(
        $name = null,
        $tmp = null,
        $formname = null,
        $type = null,
        $size = null,
        $lang = null
    ) {
        parent::__construct($lang);

        $error = null;

        $ext = null;

        if (empty($name)) {
            $error = 'NO_USER_FILE';
        } elseif ('none' == $tmp) {
            $error = 'TOO_LARGE';
        } else {
            // strpos needed to detect files without extension

            if (false !== ($pos = mb_strrpos($name, '.'))) {
                $ext = mb_substr($name, $pos + 1);
            }
        }

        global $_POST;

        // Seems that PHP 4.1.1 doesn't handle the MAX_FILE_SIZE correctly

        // or I didn't find the new way

        if (isset($_POST['MAX_FILE_SIZE']) &&
            $size > $_POST['MAX_FILE_SIZE']) {
            $error = 'TOO_LARGE';
        }

        $this->upload = [
            'real' => $name,
            'name' => $name,
            'form_name' => $formname,
            'ext' => $ext,
            'tmp_name' => $tmp,
            'size' => $size,
            'type' => $type,
            'error' => $error,
        ];
    }

    /**
     * Sets the name of the destination file
     *
     * @param string $mode    A valid mode: 'uniq', 'safe' or 'real' or a file name
     * @param null   $prepend A string to prepend to the name
     * @param null   $append  A string to append to the name
     *
     * @return string The modified name of the destination file
     */

    public function setName($mode, $prepend = null, $append = null)
    {
        switch ($mode) {
            case 'uniq':
                $name = $this->nameToUniq();
                $this->upload['ext'] = $this->nameToSafe($this->upload['ext'], 10);
                $name .= '.' . $this->upload['ext'];
                break;
            case 'safe':
                $name = $this->nameToSafe($this->upload['real']);
                if (false !== ($pos = mb_strrpos($name, '.'))) {
                    $this->upload['ext'] = mb_substr($name, $pos + 1);
                } else {
                    $this->upload['ext'] = '';
                }
                break;
            case 'real':
                $name = $this->upload['real'];
                break;
            default:
                $name = $mode;
        }

        $this->upload['name'] = $prepend . $name . $append;

        $this->mode_name_selected = true;

        return $this->upload['name'];
    }

    /**
     * Unique file names in the form: 9022210413b75410c28bef.html
     * @see HTTP_Upload_File::setName()
     */

    public function nameToUniq()
    {
        if (!$this->_seeded) {
            mt_srand((float) microtime() * 1000000);

            $this->_seeded = 1;
        }

        $uniq = uniqid(mt_rand());

        return $uniq;
    }

    /**
     * Format a file name to be safe
     *
     * @param    int    $maxlen Maximun permited string lenght
     * @param mixed $name
     * @return   string Formatted file name
     * @see HTTP_Upload_File::setName()
     */

    public function nameToSafe($name, $maxlen = 250)
    {
        $noalpha = 'áéíóúàèìòùäëïöüÁÉÍÓÚÀÈÌÒÙÄËÏÖÜâêîôûÂÊÎÔÛñçÇ@';

        $alpha = 'aeiouaeiouaeiouAEIOUAEIOUAEIOUaeiouAEIOUncCa';

        $name = mb_substr($name, 0, $maxlen);

        $name = strtr($name, $noalpha, $alpha);

        // not permitted chars are replaced with "_"

        return preg_replace('[^a-zA-Z0-9,._\+\()\-]', '_', $name);
    }

    /**
     * The upload was valid
     *
     * @return bool If the file was submitted correctly
     */

    public function isValid()
    {
        if (null === $this->upload['error']) {
            return true;
        }

        return false;
    }

    /**
     * User haven't submit a file
     *
     * @return bool If the user submitted a file or not
     */

    public function isMissing()
    {
        if ('NO_USER_FILE' == $this->upload['error']) {
            return true;
        }

        return false;
    }

    /**
     * Some error occured during upload (most common due a file size problem,
     * like max size exceeded or 0 bytes long).
     * @return bool If there were errors submitting the file (probably
     *              because the file excess the max permitted file size)
     */

    public function isError()
    {
        if ('TOO_LARGE' == $this->upload['error']) {
            return true;
        }

        return false;
    }

    /**
     * Moves the uploaded file to its destination directory.
     *
     * @param    string  $dir_dest  Destination directory
     * @param    bool    $overwrite Overwrite if destination file exists?
     * @return   mixed   True on success or Pear_Error object on error
     */

    public function moveTo($dir_dest, $overwrite = true)
    {
        if (!$this->isValid()) {
            return $this->raiseError($this->upload['error']);
        }

        //Valid extensions check

        if (!$this->_evalValidExtensions()) {
            return $this->raiseError('NOT_ALLOWED_EXTENSION');
        }

        $err_code = $this->_chk_dir_dest($dir_dest);

        if (false !== $err_code) {
            return $this->raiseError($err_code);
        }

        // Use 'safe' mode by default if no other was selected

        if (!$this->mode_name_selected) {
            $this->setName('safe');
        }

        $name_dest = $dir_dest . DIRECTORY_SEPARATOR . $this->upload['name'];

        if (@is_file($name_dest)) {
            if (true !== $overwrite) {
                return $this->raiseError('FILE_EXISTS');
            } elseif (!is_writable($name_dest)) {
                return $this->raiseError('CANNOT_OVERWRITE');
            }
        }

        // Copy the file and let php clean the tmp

        if (!@copy($this->upload['tmp_name'], $name_dest)) {
            return $this->raiseError('E_FAIL_MOVE');
        }

        @chmod($name_dest, 0660);

        return $this->getProp('name');
    }

    /**
     * Check for a valid destination dir
     *
     * @param    string  $dir_dest Destination dir
     * @return   mixed   False on no errors or error code on error
     */

    public function _chk_dir_dest($dir_dest)
    {
        if (!$dir_dest) {
            return 'MISSING_DIR';
        }

        if (!@is_dir($dir_dest)) {
            return 'IS_NOT_DIR';
        }

        if (!is_writable($dir_dest)) {
            return 'NO_WRITE_PERMS';
        }

        return false;
    }

    /**
     * Retrive properties of the uploaded file
     * @param null $name     The property name. When null an assoc array with
     *                       all the properties will be returned
     * @return mixed         A string or array
     * @see HTTP_Upload_File::HTTP_Upload_File()
     */

    public function getProp($name = null)
    {
        if (null === $name) {
            return $this->upload;
        }

        return $this->upload[$name];
    }

    /**
     * Returns a error message, if a error occured
     * (deprecated) Use getMessage() instead
     * @return string    a Error message
     */

    public function errorMsg()
    {
        return $this->errorCode($this->upload['error']);
    }

    /**
     * Returns a error message, if a error occured
     * @return string    a Error message
     */

    public function getMessage()
    {
        return $this->errorCode($this->upload['error']);
    }

    /**
     * Function to restrict the valid extensions on file uploads
     *
     * @param array $exts File extensions to validate
     * @param string $mode The type of validation:
     *                       1) 'deny'   Will deny only the supplied extensions
     *                       2) 'accept' Will accept only the supplied extensions
     *                                   as valid
     */

    public function setValidExtensions($exts, $mode = 'deny')
    {
        $this->_extensions_check = $exts;

        $this->_extensions_mode = $mode;
    }

    /**
     * Evaluates the validity of the extensions set by setValidExtensions
     *
     * @return bool False on non valid extension, true if they are valid
     */

    public function _evalValidExtensions()
    {
        $exts = $this->_extensions_check;

        $exts = (array) $exts;

        if ('deny' == $this->_extensions_mode) {
            if (in_array($this->getProp('ext'), $exts, true)) {
                return false;
            }

            // mode == 'accept'
        } else {
            if (!in_array($this->getProp('ext'), $exts, true)) {
                return false;
            }
        }

        return true;
    }
}
