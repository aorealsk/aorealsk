<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\helpers\StringHelper;
use setasign\Fpdi\Fpdi;
use yii\filters\AccessControl;

use common\models\documents\search\Search;
use common\models\Template;
use common\models\TemplateCategory;
use common\models\TemplateVars;

// >>> add these for contractor page
use common\models\User;
use common\models\Partner;
use common\models\PdfTemplate;

class DocumentsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    // preview allowed for all (view-template, generate-pdf)
                    [
                        'allow'   => true,
                        'actions' => ['view-template', 'generate-pdf'],
                        'roles'   => ['@', '?'],
                    ],
                    // all other actions require login
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    private $result = ['status' => 'ok'];
    private $toSave = false;

    public function beforeAction($action)
    {
        if (StringHelper::startsWith($action->id, 'ajax-')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
        return parent::beforeAction($action);
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'index' => [
                'class' => 'backend\actions\documents\IndexAction',
            ],
            'add-document' => [
                'class' => 'backend\actions\documents\AddDocumentsAction',
            ],
            'edit' => [
                'class' => 'backend\actions\documents\EditAction',
            ],
            'search' => [
                'class' => 'backend\actions\documents\SearchAction',
            ],
            'auto-generate' => [
                'class' => 'backend\actions\documents\AutoGenerateAction',
            ],
            // NOTE: no 'generator' mapped here anymore
        ];
    }

    /**
     * NEW: Contractor page
     *
     * Route: /documents/contractor
     * View:  backend/views/documents/contractor.php
     *
     * This wires your existing contractor.php view into the DocumentsController,
     * while the POST actions (generate, upload-template) are still handled by ContractorController.
     */
    public function actionContractor()
    {
        $users     = User::find()->orderBy(['name_last' => SORT_ASC, 'name_first' => SORT_ASC])->all();
        $partners  = Partner::find()->orderBy(['partner_name' => SORT_ASC])->all();
        $templates = PdfTemplate::find()->orderBy(['id' => SORT_DESC])->all();

        return $this->render('contractor', [
            'users'     => $users,
            'partners'  => $partners,
            'templates' => $templates,
        ]);
    }

    private function getRefreshedRows(): array
    {
        return TemplateVars::find()
            ->select('code,desc,templ_type')
            ->where(['is', 'deleted_at', null])
            ->asArray()
            ->all();
    }

    // ----- TEMPLATE VARIABLE MANAGEMENT -----
    public function actionAjaxDeleteTemplateVariable(): array
    {
        $template = Yii::$app->request->post('templvar');
        $this->removeTemplate($template);
        return $this->result;
    }

    public function actionSaveTemplate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post('template_data');
        if (!$data) return ['status' => 'error', 'message' => 'No template data'];

        $template = new Template();
        $template->name = 'New Template ' . date('Y-m-d H:i');
        $template->content = $data;
        $template->save(false);

        return ['status' => 'ok'];
    }
 
 // ===== UPLOAD TEMPLATE =====
    public function actionUploadTemplate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new \yii\base\DynamicModel(['file']);
        $model->addRule(['file'], 'file', [
            'extensions' => ['pdf'],
            'mimeTypes' => ['application/pdf'],
            'checkExtensionByMimeType' => true,
            'maxSize' => 50 * 1024 * 1024,
        ]);
        $model->file = UploadedFile::getInstanceByName('file');

        if ($model->validate()) {
            $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $model->file->baseName);
            $fileName = time() . "_{$safeName}.pdf";
            $uploadDir = Yii::getAlias('@backend/web/uploads/templates/');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $path = $uploadDir . $fileName;
            if ($model->file->saveAs($path)) {
                $template = new Template([
                    'name' => $model->file->baseName,
                    'pdf_file_path' => $fileName,
                    'content' => '',
                ]);
                $template->save(false);

                return [
                    'status' => 'ok',
                    'template' => [
                        'name' => $template->name,
                        'pdf' => $template->pdf_file_path,
                        'content' => $template->content,
                    ],
                ];
            }
        }

        return ['status' => 'error', 'message' => 'Upload failed — only valid PDF files are allowed.'];
    }

    // ----- SAVE BLOCK POSITIONS -----
    public function actionSaveBlock()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data)) {
            return ['success' => false, 'message' => 'No data received'];
        }

        $block = new \common\models\TemplateBlock();
        $block->template_id = (int)$data['template_id'];
        $block->block_type = $data['block_type'];
        $block->pos_x = (float)$data['pos_x'];
        $block->pos_y = (float)$data['pos_y'];
        $block->width = $data['width'] ?? null;
        $block->height = $data['height'] ?? null;
        $block->font_size = $data['font_size'] ?? 12;
        $block->color = $data['color'] ?? '#000000';
        $block->save(false);

        return ['success' => true, 'id' => $block->id];
    }


 // ===== PDF GENERATION =====
    public function actionGeneratePdf()
    {
        $post = Yii::$app->request->post();
        $templateName = $post['template_name'] ?? null;
        $templateJson = json_decode($post['template_json'] ?? '[]', true);
        $userIds = $post['userIds'] ?? [];

        if (!$templateName) {
            return $this->asJson(['status' => 'error', 'message' => 'No template selected']);
        }

        $template = Template::find()->where(['name' => $templateName])->one();
        if (!$template) {
            return $this->asJson(['status' => 'error', 'message' => 'Template not found']);
        }

        $safeName = basename($template->pdf_file_path);
        $templatePath = Yii::getAlias('@backend/web/uploads/templates/' . $safeName);
        if (!file_exists($templatePath)) {
            $templatePath = Yii::getAlias('@webroot/uploads/templates/' . $safeName);
        }
        if (!file_exists($templatePath)) {
            return $this->asJson(['status' => 'error', 'message' => 'Template file not found']);
        }

        $outputFile = Yii::getAlias('@backend/web/uploads/templates/generated_' . time() . '.pdf');
        $pdf = new Fpdi();

        // handle any page size
        $pageCount = $pdf->setSourceFile($templatePath);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplIdx = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($tplIdx);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplIdx, 0, 0, $size['width']);

            // Add overlay (from JSON)
            $pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
            $pdf->SetFont('DejaVu', '', 11);

            foreach ($templateJson as $block) {
                $x = (float)$block['x'] * 0.75; // adjust from px to mm (≈96dpi)
                $y = (float)$block['y'] * 0.75;
                $tag = $block['tag'] ?? '';
                $value = $this->resolveTagValue($tag, $post);
                $pdf->SetXY($x, $y);
                $pdf->Write(5, $value);
            }
        }

        $pdf->Output($outputFile, 'F');
        return Yii::$app->response->sendFile($outputFile);
    }






    // ===== GENERATOR PAGE =====
    public function actionGenerator()
    {
        try {
            $users = \common\models\User::find()->all();
            $templates = \common\models\Template::find()->all();

            return $this->render('generator', [
                'users' => $users,
                'templates' => $templates,
            ]);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return "<div class='text-danger p-4'>Chyba pri načítaní editoru: {$e->getMessage()}</div>";
        }
    }

    // ===== PDF VIEWER FOR EMBED =====
    public function actionViewTemplate($file)
    {
        $safeName = basename($file);
        $path = Yii::getAlias('@backend/web/uploads/templates/' . $safeName);
        if (!file_exists($path)) {
            throw new \yii\web\NotFoundHttpException("PDF not found: {$safeName}");
        }

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/pdf');
        Yii::$app->response->headers->set('Content-Disposition', 'inline; filename="' . $safeName . '"');
        return file_get_contents($path);
    }


    public function actionAjaxUpdateTemplateVariable(): array
    {
        ['oldcode'=>$oldCode,'oldtxt'=>$oldText,'newcode'=>$newCode,'newtxt'=>$newText] = Yii::$app->request->post();
        $variable = TemplateVars::findOne(['code'=>$oldCode]);
        if ($oldCode <> $newCode) {
            $variable->code = $newCode;
            $this->toSave = true;
        }
        if ($oldText <> $newText) {
            $variable->desc = $newText;
            $this->toSave = true;
        }
        if ($this->toSave) {
            $res = $variable->save();
            if (!$res) {
                $this->result['status'] = 'error';
                $this->result['message'] = 'Error occured during the data save!';
                return $this->result;
            }
        }
        $this->result['details'] = $this->getRefreshedRows();
        return $this->result;
    }

    private function resolveTagValue(string $tag, array $post): string
    {
        if (empty($post['userIds'])) return $tag;

        $user = \common\models\User::findOne($post['userIds'][0]);
        if (!$user) return $tag;

        switch ($tag) {
            case '{first_name}': return $user->name_first ?? '';
            case '{last_name}': return $user->name_last ?? '';
            case '{full_name}': return trim(($user->name_first ?? '') . ' ' . ($user->name_last ?? ''));
            case '{email}': return $user->email ?? '';
            case '{birthdate}': return !empty($user->birth_date) ? date('d.m.Y', strtotime($user->birth_date)) : '';
            case '{address}': return trim(($user->street ?? '') . ' ' . ($user->house_number ?? ''));
            case '{city}': return $user->city ?? '';
            case '{date}': return date('d.m.Y');
            default: return $tag;
        }
    }

    public function actionAjaxAddTemplateVariable(): array
    {
        ['code'=>$code,'descr'=>$description] = Yii::$app->request->post();

        $variable = new TemplateVars();
        $variable->code = $code;
        $variable->desc = $description;
        $variable->templ_type = 'var';
        $res = $variable->save();
        if (!$res) {
            $this->result = [
                'status'    => 'error',
                'message'   => 'Error occured during the data save!'
            ];
            return $this->result;
        }
        $this->result['details'] = $this->getRefreshedRows();
        return $this->result;
    }

    private function removeTemplate($template): bool
    {
        $variable = TemplateVars::findOne(['code' => $template]);
        if (!$variable) {
            $this->result['status'] = 'error';
            $this->result['message'] = 'Variable or block was not found!';
            return false;
        } else {
            $variable->deleted_at = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
            $variable->deleted_by = Yii::$app->user->getId();
            $res = $variable->save();
            if (!$res) {
                $this->result['status'] = 'error';
                $this->result['message'] = 'Error occured during the data save!';
                return false;
            }
            $this->result['details'] = $this->getRefreshedRows();
        }
        return true;
    }

    // ----- BLOCK MANAGEMENT -----
    public function actionAjaxGetBlockDetails(): array
    {
        $blockCode = Yii::$app->request->post('block_code');
        $this->result['block_content'] = (TemplateVars::findOne(['code'=>$blockCode]))->content;
        return $this->result;
    }

    public function actionAjaxUpdateTemplateBlock(): array
    {
        [
            'oldcode'=>$oldCode,
            'oldtxt'=>$oldText,
            'newcode'=>$newCode,
            'newtxt'=>$newText,
            'blockcontent'=>$blockContent
        ] = Yii::$app->request->post();
        $block = TemplateVars::findOne(['code'=>$oldCode]);
        if ($oldCode <> $newCode) {
            $block->code = $newCode;
            $this->toSave = true;
        }
        if ($oldText <> $newText) {
            $block->desc = $newText;
            $this->toSave = true;
        }
        $block->content = $blockContent;
        if ($this->toSave) {
            $res = $block->save();
            if (!$res) {
                $this->result['status'] = 'error';
                $this->result['message'] = 'Error occured during the data save!';
                return $this->result;
            }
        }
        $this->result['details'] = $this->getRefreshedRows();
        return $this->result;
    }

    public function actionAjaxDeleteTemplateBlock(): array
    {
        $template = Yii::$app->request->post('templblk');
        $this->removeTemplate($template);
        return $this->result;
    }

    public function actionAjaxAddTemplateBlock(): array
    {
        [
            'code'=>$code,
            'descr'=>$description,
            'content'=>$content
        ] = Yii::$app->request->post();

        $block = new TemplateVars();
        $block->code = $code;
        $block->desc = $description;
        $block->templ_type = 'blk';
        $block->content = $content;
        $res = $block->save();
        if (!$res) {
            $this->result = [
                'status'=>'error',
                'message'=>'Error occured during the data save!'
            ];
            return $this->result;
        }
        $this->result['details'] = $this->getRefreshedRows();
        return $this->result;
    }

    // ----- CATEGORY MANAGEMENT -----
    public function actionAjaxAddTemplateCategory()
    {
        [
            'category_ids'=>$categoryIds,
            'category_name'=>$categoryName
        ] = Yii::$app->request->post();
        $tr = Yii::$app->db->beginTransaction();
        $categoryIds = json_decode($categoryIds);
        try {
            foreach ($categoryIds as $categoryId) {
                $category = new TemplateCategory();
                $category->category_name = $categoryName;
                $category->parent_id = $categoryId;
                $category->status = 1;
                $category->save();
            }
            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            $this->result = [
                'status'=>'error',
                'message'=>$e->getMessage()
            ];
        }
        return $this->result;
    }

    public function actionAjaxGetTemplateFullName()
    {
        ['template_id'=>$templateId] = Yii::$app->request->post();
        $this->result['name'] = 'https://korona.gov.sk/wp-content/uploads/2021/01/44_2021.pdf';
        return $this->result;
    }

    public function actionAjaxSaveCategoryNameUpdate()
    {
        ['category_id'=>$categoryId,'category_name'=>$categoryName] = Yii::$app->request->post();
        $category = TemplateCategory::findOne(['id'=>$categoryId]);
        if (!$category) {
            $this->result['error'] = 'Category with id: ' . $categoryId . ' was not found!';
        } else {
            $category->category_name = $categoryName;
            $res = $category->save();
            if (!$res) {
                $this->result['error'] = 'Category with id: ' . $categoryId . ' was not saved!';
            }
        }
        return $this->result;
    }

    public function actionAjaxDeleteCategory()
    {
        ['category_id'=>$categoryId] = Yii::$app->request->post();
        $category = TemplateCategory::findOne(['id'=>$categoryId]);
        $category->status = 0;
        $res = $category->save();
        if (!$res) {
            $this->result['error'] = 'Category with id: ' . $categoryId . ' was not deleted!';
        }
        return $this->result;
    }

    public function actionDocumentTest($id)
    {
        $document = Template::findOne(['id'=>$id]);
        echo $document->content;
        exit;
    }

    public function actionAjaxAutoSaveDocument()
    {
        $data = Yii::$app->request->post('template_data');
        $template = Template::findOne(['id'=>$data['template_id']]);
        if (!$template) {
            $template = new Template();
            $template->created_by = Yii::$app->user->identity->id;
        } else {
            $template->updated_by = Yii::$app->user->identity->id;
            $template->updated_at = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
        }
        $template->version = $data['version'];
        $template->template_type = $data['template_type'];
        $template->category_id = $data['category_id'];
        $template->content = base64_decode($data['content']);
        $template->name = $data['name'];

        $res = $template->save();

        if (!$res) {
            $this->result['error'] = 'Template was not saved...';
        }

        $this->result['template_id'] = $template->id;

        return $this->result;
    }

    public function actionAjaxSearchDocuments()
    {
        $data = Yii::$app->request->post('search_data');
        $search = new Search();
        $search->setSearchTerm($data['sterm']);
        $this->result['items'] = $search->execute();
        $this->result['sterm'] = $data['sterm'];
        return $this->result;
    }

    public function actionViewFile()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $template = Template::findOne(Yii::$app->request->post('template_id'));
        $templateContent = $template->content;
        preg_match_all('/\[(.*?)\]/', $template->content, $matches);
        foreach ($matches[0] as $field) {
            $templateContent = str_replace($field, '', $templateContent);
        }

        return $templateContent;
    }

    /** Map user data to XML contract row */
    private function mapUserToContractRow($u): array
    {
        $name = trim(($u->full_name ?? '')
            ?: trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')));
        $dob = '';
        if (!empty($u->birth_date)) {
            $ts = strtotime($u->birth_date);
            $dob = $ts ? date('d.m.Y', $ts) : '';
        }
        $addressParts = [
            $u->street ?? '',
            $u->house_number ?? '',
            $u->postal_code ?? '',
            $u->city ?? '',
        ];
        $address = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter($addressParts))));
        return [
            'person_name' => $name,
            'person_dob' => $dob,
            'person_address' => $address,
        ];
    }

    /** Build XML group payload */
    private function buildGroupPayload(array $users): array
    {
        $out = [];
        foreach ($users as $u) {
            $row = $this->mapUserToContractRow($u);
            if ($row['person_name'] !== '') {
                $out[] = $row;
            }
        }
        return $out;
    }

}
