<?php

namespace Youwe\FixturesBundle\Alice\Providers;

class DateTime {
    /**
     * @param string $date
     * @return int
     */
    public static function exactDateTime($date = 'now') {
        return new \DateTime( $date );
    }
}
