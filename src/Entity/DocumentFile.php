<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Entity;

use finfo;
use DataSearchEngine\Entity\Adresse;

/**
 * Class DocumentFile to describe a document file.
 *
 * @package DataSearchEngine\Entity
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class DocumentFile {

    private $name;

    private $path;

    private $format;

    private $typology;

    private $openDataUrl;

    private $hash;

    private $deleteFile;

    public function __construct(string $filename, string $filepath, $deleteFile = false) {
        $this->name         = $filename;
        $this->path         = $filepath;
        $this->format       = $this->getMimeType($filepath, $filename);
        $this->hash         = md5_file($this->path);
        $this->deleteFile   = $deleteFile;
    }

    /**
     * Get the value of name
     */ 
    public function getName() {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName(string $fileName) {
        $this->name = $fileName;
        return $this;
    }

    /**
     * Get the value of path
     */ 
    public function getPath() {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @return  self
     */ 
    public function setPath(string $filePath) {
        $this->path = $filePath;
        return $this;
    }

    /**
     * Get the value of format
     */ 
    public function getFormat() {
        return $this->format;
    }

    /**
     * Set the value of format
     *
     * @return  self
     */ 
    public function setFormat(string $format) {
        $this->format = $format;
        return $this;
    }

    /**
     * Get the value of typology
     */ 
    public function getTypology() {
        return $this->typology;
    }

    /**
     * Set the value of typology
     *
     * @return  self
     */ 
    public function setTypology(string $typology) {
        $this->typology = $typology;
        return $this;
    }

    /**
     * Get the value of openDataUrl
     */ 
    public function getOpenDataUrl() {
        return $this->openDataUrl;
    }

    /**
     * Set the value of openDataUrl
     *
     * @return  self
     */ 
    public function setOpenDataUrl(string $openDataUrl) {
        $this->openDataUrl = $openDataUrl;
        return $this;
    }

    /**
     * Get the value of hash
     */ 
    public function getHash() {
        return $this->hash;
    }

    /**
     * Set the value of hash
     *
     * @return  self
     */ 
    public function setHash(string $hash) {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Get the value of deleteFile
     */ 
    public function isDeleteFile() {
        return $this->deleteFile;
    }

    /**
     * Set the value of deleteFile
     *
     * @return  self
     */ 
    public function setDeleteFile($deleteFile) {
        $this->deleteFile = $deleteFile;
        return $this;
    }

    /**
     * Get mime type for a file by its path. 
     * First verification with its content then with its extension.
     * Results are finally comparated, because mime type determination by content does not distinguish each text/plain format
	 * 
     *
     * @param string $path File path with specific Pastell yml extension 
     * @param string $filename File name
     * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
     * @return string
     */
    private function getMimeType($path, $filename) {
        $fileInfo = new finfo();
        $mimeType1 = $fileInfo->file($path, FILEINFO_MIME_TYPE);

        $mimeType2 = $this->getMimeTypeFromExtension(pathinfo($filename, PATHINFO_EXTENSION));

        if ($mimeType1 == $mimeType2) {
            return $mimeType1;
        } else {
            return $mimeType2;
        }
    }

    /**
     * Get mime type from extension
	 * 
     *
     * @param string $extension File extension 
     * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
     * @return string
     */
    private function getMimeTypeFromExtension($extension) {
		$extension = strtolower($extension);
		if (!(strpos($extension, '.') !== false)) {
			$extension = '.'.$extension ;
		}
		switch ($extension) {
            case '.aac':    // AAC audio
                $mime ='audio/aac'; break; 
            case '.abw':    // AbiWord document
                $mime ='application/x-abiword'; break; 
            case '.arc':    // Archive document
                $mime ='application/octet-stream'; break; 
            case '.avi':    // AVI: Audio Video Interleave
                $mime ='video/x-msvideo'; break; 
            case '.azw':    // Amazon Kindle eBook format
                $mime ='application/vnd.amazon.ebook'; break; 
            case '.bin':    // Any kind of binary data
                $mime ='application/octet-stream'; break; 
            case '.bmp':    // Windows OS/2 Bitmap Graphics
                $mime ='image/bmp'; break; 
            case '.bz':     // BZip archive
                $mime ='application/x-bzip'; break; 
            case '.bz2':    // BZip2 archive
                $mime ='application/x-bzip2'; break; 
            case '.csh':    // C-Shell script
                $mime ='application/x-csh'; break; 
            case '.css':    // Cascading Style Sheets (CSS)
                $mime ='text/css'; break; 
            case '.csv':    // Comma-separated values (CSV)
                $mime ='text/csv'; break; 
            case '.doc':    // Microsoft Word
                $mime ='application/msword'; break; 
            case '.docx':   // Microsoft Word (OpenXML)
                $mime ='application/vnd.openxmlformats-officedocument.wordprocessingml.document'; break; 
            case '.eot':    // MS Embedded OpenType fonts
                $mime ='application/vnd.ms-fontobject'; break; 
            case '.epub':   // Electronic publication (EPUB)
                $mime ='application/epub+zip'; break; 
            case '.gif':    // Graphics Interchange Format (GIF)
                $mime ='image/gif'; break; 
            case '.htm':    // HyperText Markup Language (HTML)
                $mime ='text/html'; break; 
            case '.html':   // HyperText Markup Language (HTML)
                $mime ='text/html'; break; 
            case '.ico':    // Icon format
                $mime ='image/x-icon'; break; 
            case '.ics':    // iCalendar format
                $mime ='text/calendar'; break; 
            case '.jar':    // Java Archive (JAR)
                $mime ='application/java-archive'; break; 
            case '.jpeg':   // JPEG images
                $mime ='image/jpeg'; break;
            case '.jpg':    // JPEG images
                $mime ='image/jpeg'; break; 
            case '.js':     // JavaScript (IANA Specification) (RFC 4329 Section 8.2)
                $mime ='application/javascript'; break; 
            case '.json':   // JSON format
                $mime ='application/json'; break; 
            case '.mid':    // Musical Instrument Digital Interface (MIDI)
                $mime ='audio/midi audio/x-midi'; break; 
            case '.midi':   // Musical Instrument Digital Interface (MIDI)
                $mime ='audio/midi audio/x-midi'; break; 
            case '.mpeg':   // MPEG Video
                $mime ='video/mpeg'; break; 
            case '.mpkg':   // Apple Installer Package
                $mime ='application/vnd.apple.installer+xml'; break; 
            case '.odp':    // OpenDocument presentation document
                $mime ='application/vnd.oasis.opendocument.presentation'; break; 
            case '.ods':    // OpenDocument spreadsheet document
                $mime ='application/vnd.oasis.opendocument.spreadsheet'; break; 
            case '.odt':    // OpenDocument text document
                $mime ='application/vnd.oasis.opendocument.text'; break; 
            case '.oga':    // OGG audio
                $mime ='audio/ogg'; break; 
            case '.ogv':    // OGG video
                $mime ='video/ogg'; break; 
            case '.ogx':    // OGG
                $mime ='application/ogg'; break; 
            case '.otf':    // OpenType font
                $mime ='font/otf'; break; 
            case '.png':    // Portable Network Graphics
                $mime ='image/png'; break; 
            case '.pdf':    // Adobe Portable Document Format (PDF)
                $mime ='application/pdf'; break; 
            case '.ppt':    // Microsoft PowerPoint
                $mime ='application/vnd.ms-powerpoint'; break; 
            case '.pptx':   // Microsoft PowerPoint (OpenXML)
                $mime ='application/vnd.openxmlformats-officedocument.presentationml.presentation'; break; 
            case '.rar':    // RAR archive
                $mime ='application/x-rar-compressed'; break; 
            case '.rtf':    // Rich Text Format (RTF)
                $mime ='application/rtf'; break; 
            case '.sh':     // Bourne shell script
                $mime ='application/x-sh'; break; 
            case '.svg':    // Scalable Vector Graphics (SVG)
                $mime ='image/svg+xml'; break; 
            case '.swf':    // Small web format (SWF) or Adobe Flash document
                $mime ='application/x-shockwave-flash'; break; 
            case '.tar':    // Tape Archive (TAR)
                $mime ='application/x-tar'; break; 
            case '.tif':    // Tagged Image File Format (TIFF)
                $mime ='image/tiff'; break; 
            case '.tiff':   // Tagged Image File Format (TIFF)
                $mime ='image/tiff'; break; 
            case '.ts':     // Typescript file
                $mime ='application/typescript'; break; 
            case '.ttf':    // TrueType Font
                $mime ='font/ttf'; break; 
            case '.txt':    // Text, (generally ASCII or ISO 8859-n)
                $mime ='text/plain'; break; 
            case '.vsd':    // Microsoft Visio
                $mime ='application/vnd.visio'; break; 
            case '.wav':    // Waveform Audio Format
                $mime ='audio/wav'; break; 
            case '.weba':   // WEBM audio
                $mime ='audio/webm'; break; 
            case '.webm':   // WEBM video
                $mime ='video/webm'; break; 
            case '.webp':   // WEBP image
                $mime ='image/webp'; break; 
            case '.woff':   // Web Open Font Format (WOFF)
                $mime ='font/woff'; break; 
            case '.woff2':  // Web Open Font Format (WOFF)
                $mime ='font/woff2'; break; 
            case '.xhtml':  // XHTML
                $mime ='application/xhtml+xml'; break; 
            case '.xls':    // Microsoft Excel
                $mime ='application/vnd.ms-excel'; break; 
            case '.xlsx':   // Microsoft Excel (OpenXML)
                $mime ='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'; break; 
            case '.xml':    // XML
                $mime ='application/xml'; break; 
            case '.xul':    // XUL
                $mime ='application/vnd.mozilla.xul+xml'; break; 
            case '.zip':    // ZIP archive
                $mime ='application/zip'; break; 
            case '.3gp':    // 3GPP audio/video container
                $mime ='video/3gpp'; break; 
            case '.3g2':    // 3GPP2 audio/video container 
                $mime ='video/3gpp2'; break; 
            case '.7z':     // 7-zip archive
                $mime ='application/x-7z-compressed'; break; 
            default:        // general purpose MIME-type
                $mime = 'application/octet-stream' ; 
        }
        
		return $mime ;
    }
}