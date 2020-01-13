<?php

namespace demokn\api;

class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @param  \yii\db\Connection|null    $db
     * @throws ModelNotFoundException
     * @return \yii\db\ActiveRecord|array
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
