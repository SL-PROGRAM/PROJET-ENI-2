<?php


namespace App\Service;

/**
 * Class ConvertisseurHeureSeconde
 * @package App\Service
 */
class ConvertisseurHeureSeconde
{
    /**
     * @param int $time
     * @return int
     */
    public function heureVersSeconde(int $time):int
    {
        return $time*3600;
    }

    /**
     * @param int $time
     * @return int
     */
    public function secondeVersHeure(int $time):int
    {
        return $time/3600;
    }
}