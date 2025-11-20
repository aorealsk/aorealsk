<?php
namespace common\models\documents\templatedocuments;

use DateTimeImmutable;

abstract class TemplateDocument
{
    protected $templateData = [];
    protected $templateContent = null;
    protected $templateContentStore = null;
    protected $fileNameTemplate = null;
    protected $fileName = null;

    public function setTemplateContent(string $content): void
    {
        $this->templateContent = $content;
        $this->templateContentStore = $content;
    }

    public function getTemplateContent(): string
    {
        return $this->templateContent;
    }

    public function setTemplateData(array $templateData): void
    {
        $this->templateData = $templateData;
    }

    public function setFileNameTemplate(string $template): void
    {
        $this->fileNameTemplate = $template;
    }

    public function setFileName(string $name): void 
    {
        $this->fileName = $name;
    }

    public function getFileName(): string 
    {
        return $this->fileName;
    }

    public function getExportName(): string 
    {
       return 'export_'. (new DateTimeImmutable("now"))->format('YmdHis');
    }

    abstract public function create(): void;
    abstract public function writeToFile(): void;
    abstract public function downloadFile(string $content): void;
}