<?php

namespace AryToNeX\MPRISLyrics;

class Versioning{

    public const VERSION_MAJOR = 1;
    public const VERSION_MINOR = 0;
    public const VERSION_PATCH = 0;
    public const VERSION_NOTES = "beta1";

    public static function getVersion() : string{
        return implode(".", [self::VERSION_MAJOR, self::VERSION_MINOR, self::VERSION_PATCH]) . "-" . self::VERSION_NOTES;
    }

}