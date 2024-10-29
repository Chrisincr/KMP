<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AppSetting Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $value
 */
class AppSetting extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        "name" => true,
        "value" => true,
        "type" => true,
        "raw_value" => true,
    ];

    protected function _getRawValue()
    {
        switch ($this->type) {
            case 'json':
                return json_encode($this->value);
            case 'yaml':
                return yaml_emit($this->value);
            default:
                return $this->value;
        }
    }
    protected function _setRawValue($value)
    {
        switch ($this->type) {
            case 'json':
                $this->value = json_decode($value);
            case 'yaml':
                $this->value = yaml_parse($value);
            default:
                $this->value = $value;
        }
    }

    protected function _setValue($value)
    {
        switch ($this->type) {
            case 'json':
                return json_encode($value);
            case 'yaml':
                return yaml_emit($value);
            default:
                return $value;
        }
    }

    protected function _getValue($value)
    {
        switch ($this->type) {
            case 'json':
                return json_decode($value);
            case 'yaml':
                return yaml_parse($value);
            default:
                return $value;
        }
    }
}