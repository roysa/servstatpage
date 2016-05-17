<?php

class DataCollector
{


    protected static $_topRawData = null;

    public static function getTop($refresh = false)
    {
        if (!self::$_topRawData || $refresh) {
            $out = null;
            exec('top -b -n 1', $out);
            self::$_topRawData = $out;
        }
        return self::$_topRawData;
    }


    public static function getCPUTotal()
    {
        $out = null;
        $total = 0;
        exec("top -b -n 2 -d0.7 | grep Cpu", $out);
        foreach ($out as $l) {
            if (preg_match('/([\d\.\%]+)\s+us,\s+([\d\.\%]+)\s+sy/', $l, $m)) {
                $total += (float)$m[1] + (float)$m[2];
            }
        }
        $total = $total / count($out);
        return $total;
    }

    public static function getCPU()
    {
        return array(
            'total' => self::getCPUTotal(),
        );
    }

    public static function getLoad()
    {
        $out = null;
        exec('uptime', $out);

        if (preg_match('/(\d+\:\d+\:\d+) up (.*?) (\d+) users,\s+load average:\s+([\d\.]+),\s+([\d\.]+),\s+([\d\.]+)/', $out[0], $m)) {
            return array(
                'localtime_s' => $m[1],
                'uptime_s' => trim(trim($m[2]),','),
                'load1' => (float)$m[4],
                'load2' => (float)$m[5],
                'load3' => (float)$m[6],
                'users' => (int)$m[3],
            );
        }

    }

    public static function collectAll()
    {

        return array(
            'cpu' => self::getCPU(),
            'load' => self::getLoad(),
            'mem' => self::getMem(),
            'net' => self::getNet(),
            'temperature' => self::getTemperature(),
        );


    }

    public static function getMem()
    {
        $out = null;
        exec('free -m', $out);

        $stats = array();

        if (preg_match('/(\d+)\s+(\d+)/', $out[2], $m)) {
            $stats['mem_free_mb'] = (int)$m[2];
            $stats['mem_used_mb'] = (int)$m[1];
            $stats['mem_total_mb'] = $stats['mem_free_mb'] + $stats['mem_used_mb'];
        }
        if (preg_match('/(\d+)\s+(\d+)\s+(\d+)/', $out[3], $m)) {
            $stats['swap_free_mb'] = (int)$m[3];
            $stats['swap_used_mb'] = (int)$m[2];
            $stats['swap_total_mb'] = (int)$m[1];
        }
        return $stats;
    }

    /**
     * Get network interfaces info
     * @return array
     */
    public static function getNet()
    {
        $stats = array();

        $out = null;
        exec('ip addr', $out);

        foreach ($out as $li => $l) {
            if (preg_match('/^(\d+)\:\s(\w+)\:.*?state UP/', $l, $m)) {
                $if = $m[2];
                // get speed
                list($tx,$rx) = self::getNetSpeed($if);
                $stats[$if] = array(
                    'rx_speed' => $rx,
                    'tx_speed' => $tx,
                    'name' => $if,
                );
                $lii = $li+1;
                do {
                    // get additional info
                    if (!isset($out[$lii])) break;
                    $ll = $out[$lii];
                    if (preg_match('/^\s+(inet|inet6)\s+(.*?)\//', $ll, $mm)) {
                        // IP address
                        $stats[$if][$mm[1]] = $mm[2];
                    } elseif (preg_match('/link\/ether\s+([\:\da-f]+)/', $ll, $mm)) {
                        // hardware address
                        $stats[$if]['hw'] = $mm[1];
                    }
                    $lii++;
                } while (!preg_match('/^\d+\:/', $ll));
            }
        }

        return $stats;
    }

    /**
     * Get interface transfer speed
     * @param string $if interface name
     * @return array ($tx,$rx)
     */
    public static function getNetSpeed($if)
    {
        $command = 'sh ' . Yii::getPathOfAlias('application.scripts') . '/netspeed1.sh ' . $if;

        $out = null;
        exec($command, $out);

        if (preg_match('/^(\d+)\s+(\d+)$/', $out[0], $m)) {
            return array((int)$m[1],(int)$m[2]);
        }
    }

    public static function getTemperature()
    {
        $out = null;
        exec('sensors', $out);

        $st = 0;
        $name = '';
        $adapter = '';

        $total = array();
        $info = null;

        foreach ($out as $l) {
            if ($l === '') {
                $st = 0;
                $total[] = $info;
                $info = null;
                continue;
            } elseif ($st === 0) {
                $name = $l;
                $st = 1;
                $info = array('name' => $name);
            } elseif ($st === 1) {
                $adapter = str_replace('Adapter: ', '', $l);
                $st = 2;
                $info['adapter'] = $adapter;
                $info['sensors'] = array();
            } else {
                if (preg_match('/^(.*?)\:\s+([\+\-\d\.]+)\s+C\s*(.*?)$/', $l, $m)) {
                    $info['sensors'][] = array('temperature' => (float)$m[2], 'additional' => isset($m[3]) ? $m[3] : '', 'name' => $m[1]);
                }
            }
        }

        if ($info && $info['sensors']) {
            $total[] = $info;
        }


        // Ttry to get HDDs. This requires 'hddtemp' to be installed with SUID
        $info['adapted'] = 'drives';
        $info['name'] = 'drives';
        $info['sensors'] = array();
        $disks = self::getDisks();
        foreach ($disks as $disk) {
            $dev = '/dev/' . $disk['name'];
            $out = null;
            $command = 'hddtemp ' . $dev;
            exec($command, $out);
            if ($out) {
                foreach ($out as $line) {
                    if (preg_match('/:\s+([\d\-\+\.]+)\s+/', $line, $m)) {
                        $info['sensors'][] = array(
                            'temperature' => (float)$m[1],
                            'additional' => '',
                            'name' => $disk['name'],
                        );
                    }
                }
            }
        }
        if ($info['sensors']) {
            $total[] = $info;
        }

        return $total;

    }

    public static function getDisks()
    {
        $out = null;
        exec('lsblk', $out);

        $st = 0;
        $disk = null;
        $data = array();

        foreach ($out as $line) {

            if (preg_match('/^(.*?)disk$/', $line, $m)) {
                $st = 1;
                if ($disk) {
                    $data[] = $disk;
                }
                $info = preg_replace('/\s+/', ' ', $m[1]);
                $info = explode(' ', $info);
                $disk = array(
                    'name' => $info[0],
                    'size_h' => $info[3],
                    'partitions' => array(),
                );
            } elseif ($st === 1 && preg_match('/^[^\w]+(.*?)part(.*?)$/', $line, $m)) {

                $info = preg_replace('/\s+/', ' ', $m[1]);
                $info = explode(' ', $info);

                $disk['partitions'][] = array(
                    'name' => $info[0],
                    'size_h' => $info['3'],
                    'mountpoint' => isset($m[2]) ? $m[2] : null,
                );

            }

        }

        if ($disk) {
            $data[] = $disk;
        }

        return $data;

    }


}