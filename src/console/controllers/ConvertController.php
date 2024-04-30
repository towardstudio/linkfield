<?php
namespace craft\ckeditor\console\controllers;

use Craft;
use craft\ckeditor\CkeConfig;
use craft\ckeditor\CkeConfigs;
use craft\ckeditor\Field;
use craft\ckeditor\Plugin;
use craft\console\Controller;
use craft\errors\OperationAbortedException;
use craft\fields\MissingField;
use craft\helpers\ArrayHelper;
use craft\helpers\Console;
use craft\helpers\Json;
use craft\helpers\ProjectConfig as ProjectConfigHelper;
use craft\helpers\StringHelper;
use craft\services\ProjectConfig;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\console\ExitCode;
use yii\helpers\Inflector;

class ConvertController extends Controller
{
    public $defaultAction = 'lenz';

    private ProjectConfig $projectConfig;
    private CkeConfigs $ckeConfigs;

    /**
     * Converts Lenz fields to Toward
     *
     * @return int
     */
    public function actionLinkField(): int
    {

    }

    private function outputFields(array $fields, string $typeName): void
    {
        $this->stdout('   ');
        $totalRedactorFields = count($fields);
        $this->stdout($this->markdownToAnsi(sprintf(
            '**%s**',
            $totalRedactorFields === 1
                ? "One $typeName field found:"
                : "$totalRedactorFields $typeName fields found:"
        )));
        $this->stdout(PHP_EOL);
        foreach ($fields as $path => $field) {
            $this->stdout(sprintf(" - %s\n", $this->markdownToAnsi($this->pathAndHandleMarkdown($path, $field))));
        }
    }

    private function pathAndHandleMarkdown(string $path, array $config): string
    {
        $handle = !empty($config['handle']) ? " (`{$config['handle']}`)" : '';
        return "`$path`$handle";
    }

}
