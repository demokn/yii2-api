<?php

namespace demokn\api;

class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @param  null                            $db
     * @throws ModelNotFoundException
     * @return array|\yii\db\ActiveRecord|null
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
