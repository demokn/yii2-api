<?php

namespace demokn\api;

use Yii;
use yii\console\Controller;

class CmdController extends Controller
{
    public function beforeAction($action)
    {
        Yii::beginProfile($this->getRoute(), 'console');

        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        Yii::endProfile($this->getRoute(), 'console');
        $profilingResults = Yii::getLogger()->getProfiling(['console']);
        foreach ($profilingResults as $result) {
            Log::info('Command %s elapsed %.4fs.', $result['info'], $result['duration']);
        }

        return $result;
    }

    public function stdoutln($string)
    {
        return parent::stdout($string . PHP_EOL);
    }

    public function stderrln($string)
    {
        return parent::stderr($string . PHP_EOL);
    }
}
