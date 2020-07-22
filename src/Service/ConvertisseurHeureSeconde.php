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
     * @return array
     */
    public function timeInDay(int $time):array
    {
        $duree =[];
        $jour = 0;
        $dureeHeure = $this->secondeVersHeure($time);
        while ($dureeHeure >= 24) {
            $jour++;
            $dureeHeure = $dureeHeure-24;
        }
        $duree['jour'] = $jour;
        $duree['heure'] = $dureeHeure;

        return $duree;
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