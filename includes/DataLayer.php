<?php


/**
 * Class DataLayer
 *
 * Use this class to set the default dataLayer values for a page.
 */
class DataLayer {

    protected $index = array();
    protected $data = array();
    protected $settings = array();

    /**
     * DataLayer constructor.
     *
     * @param $default_event
     *
     * @internal param array $settings
     */
    public function __construct($default_event)
    {
        $this->flush();
        $this->settings['event'] = $default_event;
    }

    /**
     * Flush all data and instance data from the object.
     *
     * Usually you want to call this after build() when you know you've
     * rendered the data.
     */
    public function flush()
    {
        $this->data = array(
            'instances' => array(),
            'defaults'  => array(),
        );
        $this->index = array();

        return $this;
    }

    /**
     * Set a default value to be present as defaults.
     *
     * @param $key
     * @param $value
     *
     * @link https://developers.google.com/tag-manager/devguide#adding-data-layer-variables-to-a-page
     */
    public function set($key, $value)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException("key must be a string.");
        }
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException("value must be a scalar.");
        }
        $this->data['defaults'][0][$key] = $value;
    }

    /**
     * Returns an array of javascript ready to be added to the page.
     *
     * @return array
     *   0: The defaul values to be placed before the container
     *   ...: Push instances to be placed after the container code.
     *
     * @link http://www.lunametrics.com/blog/2016/03/21/gtm-data-layer-best-practices/
     */
    public function build()
    {
        $build[] = 'var dataLayer = window.dataLayer = window.dataLayer || ' . json_encode($this->data['defaults']) . ';';
        foreach ($this->data['instances'] as $instance) {
            $build[] = 'dataLayer.push(' . json_encode($instance) . ');';
        }

        return $build;
    }

    /**
     * Pushes a eventTracker event
     *
     * @param        $category
     * @param        $action
     * @param string $label
     * @param string $value
     *
     * @return \DataLayer
     */
    public function event($category, $action, $label = '', $value = '', $event = '', $uuid = false)
    {
        $event = array_filter(array(
            'event'    => empty($event) ? $this->settings['event'] : $event,
            'eventCat' => $category,
            'eventAct' => $action,
            'eventLbl' => $label,
            'eventVal' => $value,
        ));

        return $this->push($event, $uuid);
    }

    /**
     * Create a push event
     *
     * @param array       $data
     * @param bool|string Defaults to false.  False means that duplicate pushes
     *                             of the same data will be pushed more than
     *                             once.  Set this to true and the json value
     *                             of data will be used to prevent repeated
     *                             pushes of the same data.  Set this to a
     *                             string and $uuid will be used to track
     *                             repeats, and block them.
     *
     * @return $this
     *
     * @link https://developers.google.com/tag-manager/devguide#events
     */
    public function push(array $data, $uuid = false)
    {
        $key = isset($uuid) ? $uuid : json_encode($data);
        if ($uuid === false || empty($this->index['instances'][$key])) {
            $this->data['instances'][] = $data;
            $this->index['instances'][$key] = true;
        }

        return $this;
    }
}
