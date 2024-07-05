<?php

namespace App\Traits\DataGrid;

trait ProvideExceptionHandler
{
    /**
     * Required row properties keys.
     *
     * @var array
     */
    protected $requiredRowPropertiesKeys = ['condition'];

    /**
     * Required column keys.
     *
     * @var array
     */
    protected $requiredColumnKeys = ['index', 'label'];

    /**
     * Required action keys.
     *
     * @var array
     */
    protected $requiredActionKeys = ['title', 'method', 'route', 'icon'];

    /**
     * This will check the keys which are needed for row properties.
     *
     * @param  array  $column
     * @return void|\App\Exceptions\DataGrid\ConditionKeyException
     */
    public function checkRequiredRowPropertiesKeys($rowProperty)
    {
        $this->checkRequiredKeys($this->requiredRowPropertiesKeys, $rowProperty, function ($missingKeys) {
            $message = 'Missing Keys: ' . implode(', ', $missingKeys);

            throw new \App\Exceptions\DataGrid\ConditionKeyException($message);
        });
    }

    /**
     * This will check the keys which are needed for column.
     *
     * @param  array  $column
     * @return void|\App\Exceptions\DataGrid\ColumnKeyException
     */
    public function checkRequiredColumnKeys($column)
    {
        $this->checkRequiredKeys($this->requiredColumnKeys, $column, function ($missingKeys) {
            $message = 'Missing Keys: ' . implode(', ', $missingKeys);

            throw new \App\Exceptions\DataGrid\ColumnKeyException($message);
        });
    }

    /**
     * This will check the keys which are needed for action.
     *
     * @param  array  $action
     * @return void|\App\Exceptions\DataGrid\ActionKeyException
     */
    public function checkRequiredActionKeys($action)
    {
        $this->checkRequiredKeys($this->requiredActionKeys, $action, function ($missingKeys) {
            $message = 'Missing Keys: ' . implode(', ', $missingKeys);

            throw new \App\Exceptions\DataGrid\ActionKeyException($message);
        });
    }

    /**
     * Check required keys.
     *
     * @param  array  $requiredKeys
     * @param  array  $actualKeys
     * @param  \Closure  $operation
     * @return void|\Closure
     */
    public function checkRequiredKeys($requiredKeys, $actualKeys, $operation)
    {
        $requiredKeys = array_flip($requiredKeys);

        $missingKeys = array_flip(array_diff_key($requiredKeys, $actualKeys));

        return ! empty($missingKeys) ? $operation($missingKeys) : null;
    }
}
