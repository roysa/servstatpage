<?php

class SiteController extends CController
{


    public function actionIndex()
    {
        $this->render('index');
    }

    public function temperatureClass($t)
    {
        $good = 15;
        $high = 55;
        $crit = 75;
        if ($t < $good) {
            return 'text-info';
        } elseif ($t < $high) {
            return 'text-success';
        } elseif ($t < $crit) {
            return 'text-warning';
        } else {
            return 'text-danger';
        }
    }

    public function temperatureSign($t)
    {
        $good = 15;
        $high = 55;
        $crit = 75;
        if ($t < $good) {
            return '<i class="text-info fa fa-asterisk"></i>';
        } elseif ($t < $high) {
            return '';
        } elseif ($t < $crit) {
            return '<i class="text-warning fa fa-exclamation"></i>';
        } else {
            return '<i class="text-danger fa fa-fire"></i>';
        }
    }

}