<?php 

namespace Lumenpress\Models\Acf\Fields;

class FlexibleContent extends Field
{
    protected $with = ['fields'];

    protected $currentLayoutKey;

    protected $defaults = [
        // 'key' => 'field_5979ac6c766e3',
        // 'label' => 'Flexible Content',
        // 'name' => 'flexible_content',
        'type' => 'flexible_content',
        'layouts' => [
            // '5979ac7105e3b' => [
            //     'key' => '5979ac7105e3b',
            //     'name' => '',
            //     'label' => '',
            //     'display' => 'block',
            //     'sub_fields' => [],
            //     'min' => '',
            //     'max' => ''
            // ]
        ],
        'button_label' => 'Add Row',
        'min' => '',
        'max' => ''
    ];

    public function layouts(callable $callable)
    {
        $callable($this);
        return $this;
    }

    public function layout($name)
    {
        $this->currentLayoutKey = $key = uniqid();
        $layouts = $this->getContentAttribute('layouts');
        $layouts[$key] = [
            'key' => $key,
            'name' => $name,
            'label' => $name,
            'display' => 'block',
            'min' => '',
            'max' => ''
        ];
        $this->setContentAttribute('layouts', $layouts);
        return $this;
    }

    public function setLayoutAttribute($key, $value)
    {
        $layouts = $this->getContentAttribute('layouts');
        if (!isset($layouts[$this->currentLayoutKey])) {
            throw new \Exception("Current layout key does not exist.", 1);
        }
        $layouts[$this->currentLayoutKey][$key] = $value;
        $this->setContentAttribute('layouts', $layouts);
        return $this;
    }

    public function label($value)
    {
        return $this->setLayoutAttribute('label', $value);
    }

    public function display($value)
    {
        return $this->setLayoutAttribute('display', $value);
    }

    public function min($value)
    {
        return $this->setLayoutAttribute('min', $value);
    }

    public function max($value)
    {
        return $this->setLayoutAttribute('max', $value);
    }

}