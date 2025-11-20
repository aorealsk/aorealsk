<?php
namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "template".
 *
 * @property int $id
 * @property string $name
 * @property string|null $content
 * @property string|null $pdf_file_path
 * @property int|null $category_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TemplateBlock[] $blocks
 */
class Template extends ActiveRecord
{
    /**
     * 🔹 Database table name
     */
    public static function tableName()
    {
        return 'template';
    }

    /**
     * 🔹 Relationship to template categories (if any)
     */
    public function getTemplateDetails(): ActiveQuery
    {
        return $this->hasMany(TemplateCategory::class, ['id' => 'category_id']);
    }

    /**
     * 🔹 Relationship to stored block positions
     */
    public function getBlocks(): ActiveQuery
    {
        return $this->hasMany(TemplateBlock::class, ['template_id' => 'id']);
    }

    /**
     * 🔹 Handles upload and creation of a new template record
     */
    public static function createFromUpload($uploadedFile): ?self
    {
        if (!$uploadedFile) {
            return null;
        }

        // Generate unique filename with timestamp
        $fileName = time() . '_' . preg_replace('/\s+/', '_', $uploadedFile->baseName) . '.' . $uploadedFile->extension;
        $uploadDir = Yii::getAlias('@webroot/uploads/templates/');
        $filePath = $uploadDir . $fileName;

        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        if ($uploadedFile->saveAs($filePath)) {
            $template = new self();
            $template->name = $uploadedFile->baseName;
            $template->pdf_file_path = $fileName;
            $template->content = ''; // optional HTML overlay area
            $template->save(false);
            return $template;
        }

        return null;
    }

    /**
     * 🔹 Returns full category path list (if categories used)
     */
    public function getFullCategoryPaths(): array
    {
        $categories = $this->getTemplateDetails()->all();
        $paths = [];
        foreach ($categories as $category) {
            $paths[] = $category->getFullCategoryPath();
        }
        return $paths;
    }

    /**
     * 🔹 Validation rules
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['content'], 'string'],
            [['category_id'], 'integer'],
            [['name', 'pdf_file_path'], 'string', 'max' => 255],
        ];
    }

    /**
     * 🔹 Attribute labels (for admin/UI)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Názov šablóny',
            'content' => 'HTML obsah šablóny',
            'pdf_file_path' => 'Cesta k PDF súboru',
            'category_id' => 'Kategória',
        ];
    }

    /**
     * 🔹 Returns the absolute filesystem path of the background PDF
     */
    public function getPdfAbsolutePath(): ?string
    {
        if (!$this->pdf_file_path) {
            return null;
        }
        $path = Yii::getAlias('@webroot/uploads/templates/' . $this->pdf_file_path);
        return file_exists($path) ? $path : null;
    }

    /**
     * 🔹 Returns public URL (for PDF preview in browser)
     */
    public function getPdfWebUrl(): ?string
    {
        if (!$this->pdf_file_path) {
            return null;
        }
        return Yii::getAlias('@web/uploads/templates/' . $this->pdf_file_path);
    }

    /**
     * 🔹 Checks whether this template has a valid PDF background file
     */
    public function hasBackgroundPdf(): bool
    {
        return $this->getPdfAbsolutePath() !== null;
    }

    /**
     * 🔹 Deletes the associated PDF file from filesystem on record delete
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $filePath = $this->getPdfAbsolutePath();
        if ($filePath && file_exists($filePath)) {
            @unlink($filePath);
        }

        // Optionally remove blocks linked to this template
        TemplateBlock::deleteAll(['template_id' => $this->id]);
    }
}
