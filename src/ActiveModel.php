<?php

namespace demokn\api;

use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ActiveModel extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'timestamps' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * @throws Exception
     * @return static
     */
    public static function create(array $attributes)
    {
        $model = new static();
        $model->loadDefaultValues();
        $model->setAttributes($attributes, false);

        if (!$model->validate()) {
            throw new Exception(sprintf('Model "%s" attributes validate failed: %s', get_class($model), current($model->firstErrors)));
        }

        $model->saveOrFail(false);

        return $model;
    }

    /**
     * @throws Exception
     * @return static
     */
    public static function firstOrCreate(array $attributes, array $values = [])
    {
        $model = static::find()->andWhere($attributes)->one();
        if (!$model) {
            $model = self::create($attributes + $values);
        }

        return $model;
    }

    /**
     * @throws ModelNotFoundException
     * @return static
     */
    public static function firstOrFail(array $attributes)
    {
        $model = static::find()->andWhere($attributes)->one();
        if (!$model) {
            throw new ModelNotFoundException(sprintf('%s(%s) not found.', static::class, var_export($attributes, true)));
        }

        return $model;
    }

    /**
     * @param $condition
     * @throws ModelNotFoundException
     * @return static
     */
    public static function findOneOrFail($condition)
    {
        $model = static::findOne($condition);
        if (!$model) {
            throw new ModelNotFoundException(sprintf('%s(%s) not found.', static::class, var_export($condition, true)));
        }

        return $model;
    }

    /**
     * @param  bool      $runValidation
     * @param  null      $attributeNames
     * @throws Exception
     */
    public function saveOrFail($runValidation = true, $attributeNames = null)
    {
        $isSucceeded = $this->save($runValidation, $attributeNames);

        if (!$isSucceeded) {
            $error = $this->hasErrors() ? current($this->firstErrors) : 'f**k, not validation error.';

            throw new Exception(sprintf('Failed to save "%s": %s.', get_class($this), $error));
        }
    }

    /**
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteOrFail()
    {
        $rows = $this->delete();

        if ($rows !== 1) {
            throw new Exception(sprintf('Failed to delete "%s".', get_class($this)));
        }
    }

    /**
     * @param  bool                         $runValidation
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function updateOrFail($runValidation = true, array $attributeNames = null)
    {
        $rows = $this->update($runValidation, $attributeNames);

        if ($rows !== 1) {
            $error = $this->hasErrors() ? current($this->firstErrors) : "the number of rows affected is {$rows}.";

            throw new Exception(sprintf('Failed to update "%s": %s', get_class($this), $error));
        }
    }

    /**
     * @throws Exception
     */
    public function updateAttributesOrFail(array $attributes)
    {
        // Note that this method will **not** perform data validation and will **not** trigger events.
        $rows = $this->updateAttributes($attributes);

        if ($rows !== 1) {
            $error = "the number of rows affected is {$rows}.";

            throw new Exception(sprintf('Failed to update "%s": %s', get_class($this), $error));
        }
    }

    /**
     * @param  array     $counters the counters to be updated (attribute name => increment value)
     * @throws Exception
     */
    public function updateCountersOrFail($counters)
    {
        $isSucceeded = parent::updateCounters($counters);

        if (!$isSucceeded) {
            throw new Exception(sprintf('Failed to update counters "%s".', get_class($this)));
        }
    }
}
