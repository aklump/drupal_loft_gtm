<?php


class DataLayer
{
    protected $data = array('push' => array());

    public function push($key, $value)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException("key must be a string.");
        }
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException("value must be a scalar.");
        }
        $this->data['push'][$key] = $value;
    }

    public function __toString()
    {
        return 'dataLayer.push(' . json_encode($this->data['push']) . ');';
    }
}
