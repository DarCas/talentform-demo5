<?php

namespace App\Traits;

trait FilesystemTrait
{
    protected function filesizeVerbose(int $bytes, int $decimals = 2, bool $iec = false): string
    {
        if ($iec) {
            /**
             * Dimensioni con sistema binario
             */
            $size = ['B', 'kiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
        } else {
            /**
             * Dimensioni con sistema decimale
             */
            $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        }

        /**
         * Setto il dividendo in funzione del paramentro $iec
         * che mi indica se voglio il valore con il sistema decimale
         * o il sistema binario
         */
        $divider = $iec ? 1024 : 1000;

        $factor = 0;

        while ($bytes >= $divider) {
            $bytes /= $divider;

            ++$factor;
        }

        return number_format($bytes, $decimals) .
            " {$size[$factor]}";
    }
}
