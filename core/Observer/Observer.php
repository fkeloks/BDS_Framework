<?php

namespace BDSCore\Observer;

/**
 * Class Observer
 * @package BDSCore\Observer
 */
class Observer
{

    private static $class;

    /**
     * @var array
     */
    private $events = [];

    /**
     * @param bool $newInstance
     * @return Observer
     */
    public static function getObserver($newInstance = false): Observer {
        if ($newInstance) {
            self::$class = new self();
        } else {
            if (!self::$class) {
                self::$class = new self();
            }
        }

        return self::$class;
    }

    /**
     * @param string $eventName
     * @param array ...$args
     * @return void
     */
    public function emit(string $eventName, ...$args) {
        if (array_key_exists($eventName, $this->events)) {
            foreach ($this->events[$eventName] as $event) {
                call_user_func_array($event, $args);
            }
        }
    }

    /**
     * @param string $eventName
     * @param callable $callback
     * @return void
     */
    public function on(string $eventName, callable $callback) {
        $this->events[$eventName][] = $callback;
    }

}