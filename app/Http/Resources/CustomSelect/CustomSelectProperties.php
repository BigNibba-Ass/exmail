<?php

namespace App\Http\Resources\CustomSelect;

class CustomSelectProperties {
    protected string $textFieldName;

    protected string $valueFieldName;

    /**
     * @param string $textFieldName
     * @param string $valueFieldName
     */
    public function __construct(string $textFieldName, string $valueFieldName)
    {
        $this->textFieldName = $textFieldName;
        $this->valueFieldName = $valueFieldName;
    }

    /**
     * @return string
     */
    public function getTextFieldName(): string
    {
        return $this->textFieldName;
    }

    /**
     * @param string $textFieldName
     */
    public function setTextFieldName(string $textFieldName): void
    {
        $this->textFieldName = $textFieldName;
    }

    /**
     * @return string
     */
    public function getValueFieldName(): string
    {
        return $this->valueFieldName;
    }

    /**
     * @param string $valueFieldName
     */
    public function setValueFieldName(string $valueFieldName): void
    {
        $this->valueFieldName = $valueFieldName;
    }
}
