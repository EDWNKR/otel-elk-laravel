<?php

namespace Edwinekr\OtelElkLaravel\Facades;

use Edwinekr\OtelElkLaravel\Helpers\RumHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isEnabled()
 * @method static array getConfig()
 * @method static string|null getInitScript()
 * @method static string getCdnUrl()
 * @method static string|null renderScript()
 *
 * @see \Edwinekr\OtelElkLaravel\Helpers\RumHelper
 */
class Rum extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return RumHelper::class;
    }
}
