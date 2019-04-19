<?php

namespace demokn\api;

class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @param null $db
     * @return array|\yii\db\ActiveRecord|null
     * @throws ModelNotFoundException
     */
    public function oneOrFail($db = null)
    {
        $model = $this->one($db);
        if (!$model) {
            throw new ModelNotFoundException(sprintf('%s not found.', $this->modelClass));
        }

        return $model;
    }
}
