<?php

namespace api\modules\v1\models;

use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;

/**
 * Image model
 *
 * @property integer $id
 * @property string $url
 * @property UploadedFile $imageFile
 * @property integer $created_at
 * @property integer $updated_at
 */
class Image extends ActiveRecord
{
    const IMAGE_FILE_ATTRIBUTE_NAME = 'imageFile';
    const UPLOAD_FOLDER = 'upload';
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['url', 'required'],
            [self::IMAGE_FILE_ATTRIBUTE_NAME, 'image', 'skipOnEmpty' => true],
            ['url', 'url']
        ];
    }

    /**
     * {@inheritdoc}
     */
    function attributes(): array
    {
        $attributes = parent::attributes();
        $attributes[] = self::IMAGE_FILE_ATTRIBUTE_NAME;
        return $attributes;
    }

    public function loadImageFile(): bool
    {
        $this->imageFile = UploadedFile::getInstanceByName(self::IMAGE_FILE_ATTRIBUTE_NAME);
        return $this->hasImageFile();
    }

    public function hasImageFile(): bool
    {
        return ($this->imageFile instanceof UploadedFile);
    }

    public function validateImageFile(): bool
    {
        return $this->validate(self::IMAGE_FILE_ATTRIBUTE_NAME);
    }

    public function saveImageFile()
    {
        if ($this->hasImageFile() && $this->validateImageFile()) {
            $pathDir = self::createPath();
            if (!file_exists($pathDir)) {
                mkdir($pathDir, 777, true);
            }
            do {
                $pathFile = $pathDir . uniqid() . '.' . $this->imageFile->extension;
            } while (file_exists($pathFile));
            // Do not use $this->imageFile->saveAs() as it works only with POST data, but we use also PUT data to
            // upload the file https://www.yiiframework.com/doc/api/2.0/yii-web-multipartformdataparser

            $status = copy($this->imageFile->tempName, $pathFile);
            if (is_uploaded_file($this->imageFile->tempName)) {
                unlink($this->imageFile->tempName);
            }
            if ($status) {
                $this->url = $pathFile;
                $this->save(false);
            }

        }
    }

    public function getSrc()
    {
        return Url::to($this->url, true);
    }

    public function fields(): array
    {
        $fields = parent::fields();

        $fields['src'] = 'src';

        return $fields;
    }

    protected static function createPath(): string
    {
        if (\Yii::$app->user->isGuest) {
            $userFolder = 'guest';
        } else {
            $userFolder = \Yii::$app->user->id;
        }
        return self::UPLOAD_FOLDER . '/' . $userFolder . '/';
    }

    public function afterDelete()
    {
        unlink($this->url);
    }
}