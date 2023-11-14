<?php 

if (!function_exists('inspector')) {
    /**
     * @return \Boot\System\Inspector\Inspector
     */
    function inspector(): \Boot\System\Inspector\Inspector
    {
        return app('inspector');
    }
}
