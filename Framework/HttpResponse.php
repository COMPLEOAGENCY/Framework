<?php

namespace Framework;

use Illuminate\Http\Response;

class HttpResponse extends Response
{
    protected $beforeSendCallback;
    protected $afterSendCallback;

    /**
     * Envoie la réponse HTTP au client et retourne $this pour chaînage.
     * @return static
     */
    public function send(): static
    {
        // Exécute le callback avant l'envoi, s'il est défini
        if (is_callable($this->beforeSendCallback)) {
            call_user_func($this->beforeSendCallback);
        }

        // Appel de la méthode parente pour envoyer la réponse
        parent::send();

        // Exécute le callback après l'envoi, s'il est défini
        if (is_callable($this->afterSendCallback)) {
            call_user_func($this->afterSendCallback);
        }

        return $this;
    }

    /**
     * Définit le callback à exécuter avant l'envoi de la réponse.
     *
     * @param callable $callback
     */
    public function setBeforeSendCallback(callable $callback)
    {
        $this->beforeSendCallback = $callback;
    }

    /**
     * Définit le callback à exécuter après l'envoi de la réponse.
     *
     * @param callable $callback
     */
    public function setAfterSendCallback(callable $callback)
    {
        $this->afterSendCallback = $callback;
    }
}
