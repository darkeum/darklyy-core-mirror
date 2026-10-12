<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Testing\Foundation\Concerns;

trait InteractsWithSession
{
    /**
     * Set the session to the given array.
     *
     * @param  array  $data
     * @return $this
     */
    public function withSession(array $data)
    {
        $this->session($data);

        return $this;
    }

    /**
     * Set the session to the given array.
     *
     * @param  array  $data
     * @return $this
     */
    public function session(array $data)
    {
        $this->startSession();

        foreach ($data as $key => $value) {
            $this->app['session']->put($key, $value);
        }

        return $this;
    }

    /**
     * Start the session for the application.
     *
     * @return $this
     */
    protected function startSession()
    {
        if (! $this->app['session']->isStarted()) {
            $this->app['session']->start();
        }

        return $this;
    }

    /**
     * Flush all of the current session data.
     *
     * @return $this
     */
    public function flushSession()
    {
        $this->startSession();

        $this->app['session']->flush();

        return $this;
    }
}
