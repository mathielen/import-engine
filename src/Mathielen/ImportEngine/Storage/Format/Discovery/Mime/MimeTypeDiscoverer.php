<?php
namespace Mathielen\ImportEngine\Storage\Format\Discovery\Mime;

class MimeTypeDiscoverer
{

    const MIME_PPTX = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    const MIME_DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    const MIME_XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    public function discoverMimeType($filePath)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filePath);

        //TODO handle other compressed mimetypes
        if ($mimeType == 'application/zip') {
            $mimeType = $this->handleZipfile($filePath);
        }

        return $mimeType;
    }

    private function handleZipfile($filePath)
    {
        $zipMimeType = 'application/zip';

        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            $zipMimeType = $this->getMimetypeFromZipArchive($zip);
            $zip->close();
        }

        return $zipMimeType;
    }

    private function getMimetypeFromZipArchive(\ZipArchive $zip)
    {
        $hasMultipleFiles = $zip->numFiles > 1;

        if ($hasMultipleFiles) {
            return $this->getMimetypeFromMultifileZip($zip);
        } else {
            return $this->getMimetypeFromSinglefileZip($zip);
        }
    }

    private function getMimetypeFromMultifileZip(\ZipArchive $zip)
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

        return 'application/zip';
    }

    private function getMimetypeFromSinglefileZip(\ZipArchive $zip)
    {
        $stat = $zip->statIndex(0);
        $fp = $zip->getStream($stat['name']);
        if (!$fp) {
            return 'application/zip';
        }

        $contents = '';
        while (!feof($fp)) {
            $contents .= fread($fp, 2);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($contents);

        return 'application/zip '.$mimeType.'@'.$stat['name'];
    }

}
