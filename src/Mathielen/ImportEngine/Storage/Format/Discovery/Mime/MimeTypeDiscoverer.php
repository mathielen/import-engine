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

        $mimeType = $this->handleSpecialMimetypes($mimeType, $filePath);

        return $mimeType;
    }

    private function handleSpecialMimetypes($mimeType, $filePath)
    {
        switch ($mimeType) {
            //TODO handle other compressed mimetypes

            case 'application/octet-stream': //handle octet stream as zip
            case 'application/zip':
                return $this->handleZipFile($filePath);

            //all old office files may return this mimetype
            case 'application/vnd.ms-office':
                return $this->handleOldOfficeFile($filePath);

            case 'text/plain':
                $contents = file_get_contents($filePath);
                $firstChar = trim($contents)[0];

                if (in_array($firstChar, ['{', '['])) {
                    return 'application/json';
                }
            break;
        }

        return $mimeType;
    }

    private function handleOldOfficeFile($filePath)
    {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'doc': return 'application/msword';
            case 'dot': return 'application/msword';
            case 'docx': return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            case 'dotx': return 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
            case 'docm': return 'application/vnd.ms-word.document.macroEnabled.12';
            case 'dotm': return 'application/vnd.ms-word.template.macroEnabled.12';
            case 'xls': return 'application/vnd.ms-excel';
            case 'xlt': return 'application/vnd.ms-excel';
            case 'xla': return 'application/vnd.ms-excel';
            case 'xlsx': return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            case 'xltx': return 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
            case 'xlsm': return 'application/vnd.ms-excel.sheet.macroEnabled.12';
            case 'xltm': return 'application/vnd.ms-excel.template.macroEnabled.12';
            case 'xlam': return 'application/vnd.ms-excel.addin.macroEnabled.12';
            case 'xlsb': return 'application/vnd.ms-excel.sheet.binary.macroEnabled.12';
            case 'ppt': return 'application/vnd.ms-powerpoint';
            case 'pot': return 'application/vnd.ms-powerpoint';
            case 'pps': return 'application/vnd.ms-powerpoint';
            case 'ppa': return 'application/vnd.ms-powerpoint';
            case 'pptx': return 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
            case 'potx': return 'application/vnd.openxmlformats-officedocument.presentationml.template';
            case 'ppsx': return 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
            case 'ppam': return 'application/vnd.ms-powerpoint.addin.macroEnabled.12';
            case 'pptm': return 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
            case 'potm': return 'application/vnd.ms-powerpoint.template.macroEnabled.12';
            case 'ppsm': return 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12';
        }

        return 'application/vnd.ms-office';
    }

    private function handleZipFile($filePath)
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
