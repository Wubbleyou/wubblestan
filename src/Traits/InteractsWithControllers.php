<?php

namespace Wubbleyou\Wubblestan\Traits;

trait InteractsWithControllers {
    /**
     * @param string $class
     * @return boolean
     */
    private function isController(string $class): bool
    {
        return str_starts_with($class, 'App\Http') && str_ends_with($class, 'Controller');
    }
}