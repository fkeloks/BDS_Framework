<?php

namespace BDSCore\Observer;

/**
 * Class Observer : Event manager
 * Classe Observer : Gestionnaire d'événement
 *
 * @package BDSCore\Observer
 */
class Observer
{

    /**
     * @var self Observer
     */
    private static $class;

    /**
     * @var array Lists of events
     */
    private $events = [];

    /**
     * Return an instance of the observer
     * Renvoi une instance de l'observer
     *
     * @param bool $newInstance If TRUE, new instance.
     *
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
     * Send an event
     * Emet un évènement
     *
     * @param string $eventName Name of event
     * @param array ...$args Arguments
     *
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
     * Defines a function to execute when capturing a defined event
     * Définit une fonction à éxecuter lors de la capture d'un evenement définit
     *
     * @param string $eventName Name of events
     * @param callable $callback Arguments
     *
     * @return void
     */
    public function on(string $eventName, callable $callback) {
        $this->events[$eventName][] = $callback;
    }

}