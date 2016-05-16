<?php

/**
 * Class Data
 *
 * @property integer $uptime
 * @property float $load1
 * @property float $load2
 * @property float $load3
 * @property integer $mem_total
 * @property integer $mem_used
 * @property integer $mem_free
 * @property integer $swap_total
 * @property integer $swap_used
 * @property integer $swap_free
 * @property float $cpu_total_usage
 *
 *
 * @property array $raw
 *
 */
class Data extends CModel
{


    private $_raw = null;


    public $load1;
    public $load2;
    public $load3;
    public $cpu_total_usage;
    public $mem_total;
    public $mem_used;
    public $mem_free;
    public $swap_total;
    public $swap_free;
    public $swap_used;


    public function setRaw($data)
    {
        $this->_raw = $data;
        $this->load1 = $data['load']['load1'];
        $this->load2 = $data['load']['load2'];
        $this->load3 = $data['load']['load3'];
        $this->cpu_total_usage = $data['cpu']['total'];
        $this->mem_total = $data['mem']['mem_total_mb'];
        $this->mem_free = $data['mem']['mem_free_mb'];
        $this->mem_used = $data['mem']['mem_used_mb'];
        $this->swap_total = $data['mem']['swap_total_mb'];
        $this->swap_free = $data['mem']['swap_free_mb'];
        $this->swap_used = $data['mem']['swap_used_mb'];
    }

    public function getRaw()
    {
        return $this->_raw;
    }

    public function attributeNames()
    {
        return array(

        );
    }

    /**
     * Fill new model with raw data
     * @param array $data
     * @return Data
     */
    public static function createFromData($data)
    {
        $model = new Data();
        $model->setRaw($data);
        return $model;
    }


    /**
     * @return Data
     */
    public static function getActual()
    {
        $data = DataCollector::collectAll();
        return self::createFromData($data);
    }

}