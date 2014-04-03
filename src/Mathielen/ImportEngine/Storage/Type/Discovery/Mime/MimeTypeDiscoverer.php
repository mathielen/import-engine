<?php
namespace Mathielen\ImportEngine\Storage\Type\Discovery\Mime;

class MimeTypeDiscoverer
{

    const MIME_PPTX = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    const MIME_DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    const MIME_XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    public function getMimeType($filePath)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filePath);

        //fix for ms office files
        if ($mimeType == 'application/zip') {
            $officeFileFormat = $this->getOfficeFileformat($filePath);
            if ($officeFileFormat) {
                $mimeType = $officeFileFormat;
            }
        }

        return $mimeType;
    }

    private function getOfficeFileformat($filePath)
    {
        $officeFileFormat = false;

        $zip = new \ZipArchive();
        if ($zip->open($filePath) === TRUE) {
            $officeFileFormat = $this->getOfficeFileFormatFromZip($zip);
            $zip->close();
        }

        return $officeFileFormat;
    }

    private function getOfficeFileFormatFromZip(\ZipArchive $zip)
    {
        //check for ms office xml filetypes
        for ($i=0; $i<$zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $fileName = $stat['name'];

            if ($fileName == 'xl/workbook.xml') {
                return self::MIME_XLSX;
            } elseif ($fileName == 'word/document.xml') {
                return self::MIME_DOCX;
            } elseif ($fileName == 'ppt/presentation.xml') {
                return self::MIME_PPTX;
            }
        }

        //check for one-file-in-file zips
        if ($zip->numFiles == 1) {
            $stat = $zip->statIndex(0);
            $fp = $zip->getStream($stat['name']);
            if (!$fp) {
                return false;
            }

            $contents = '';
            while (!feof($fp)) {
                $contents .= fread($fp, 2);
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($contents);

            return 'application/zip '.$mimeType;
        } else {
            return false;
        }
    }

}
