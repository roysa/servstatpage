<?php

    /** @var SiteController $this */


    $data = Data::getActual();

?>
<div class="row marketing">
    <div class="col-lg-6">
        <h4>Uptime</h4>
        <p><?= $data->raw['load']['uptime_s'] ?></p>

        <h4>CPU</h4>
        <p>
            <?= $data->cpu_total_usage ?>%
            <div class="progress progress-striped">
                <div class="progress-bar progress-bar-warning" style="width: <?= round($data->cpu_total_usage) ?>%;"></div>
            </div>
        </p>

        <h4>Temperature</h4>
        <p>
            <table>
            <?php foreach ($data->raw['temperature'] as $device) : ?>
            <?php foreach ($device['sensors'] as $sensor) : ?>
                    <tr>
                        <td><?= $sensor['name'] ?>:&nbsp;</td>
                        <td class="<?= $this->temperatureClass($sensor['temperature']) ?>"><?= $this->temperatureSign($sensor['temperature']) ?> <?= number_format($sensor['temperature'],1) ?> C&nbsp;</td>
                        <td><small class="text-muted"><small><?= $sensor['additional'] ?></small></small></td>
                    </tr>
            <?php endforeach; ?>

            <?php endforeach; ?>
        </table>
        </p>
    </div>

    <div class="col-lg-6">
        <h4>Load</h4>
        <p><?= $data->load1 ?>&nbsp;&nbsp;&nbsp;<?= $data->load2 ?>&nbsp;&nbsp;&nbsp;<?= $data->load3 ?></p>

        <h4>Mem</h4>
        <p>
            <?php $mempc = round(($data->mem_used / $data->mem_total)*100); ?>
            RAM <?= $data->mem_used ?> / <?= $data->mem_total ?> (<?= $mempc ?>%), <?= $data->mem_free ?>M free
            <div class="progress">
                <div class="progress-bar progress-bar-success" style="width: <?= $mempc ?>%;"></div>
            </div>
            <?php $mempc = round(($data->swap_used / $data->swap_total)*100); ?>
            Swap <?= $data->swap_used ?> / <?= $data->swap_total ?> (<?= $mempc ?>%), <?= $data->swap_free ?>M free
            <div class="progress">
                <div class="progress-bar" style="width: <?= $mempc ?>%;"></div>
            </div>
        </p>

        <h4>Network</h4>
        <p>
            <?php foreach ($data->raw['net'] as $section) : ?>
                <?= $section['name'] ?>: Rx <?= $section['rx_speed'] ?>k Tx <?= $section['tx_speed'] ?>k
                <?php if ($section['inet']) : ?>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;inet: <?= $section['inet'] ?>
                <?php endif; ?>
                <?php if ($section['inet6']) : ?>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;inet6: <?= $section['inet'] ?>
                <?php endif; ?>
                <?php if ($section['hw']) : ?>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;hw: <?= $section['hw'] ?>
                <?php endif; ?>
                <br>
            <?php endforeach; ?>
        </p>
    </div>
</div>
