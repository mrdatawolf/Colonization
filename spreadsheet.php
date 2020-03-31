<?php
require 'start.php';
use Controllers\MagicNumbers;
use \Controllers\Clusters;
use \Controllers\Servers;
use \Controllers\Ores;
use \Controllers\Ingots;
use \Controllers\Components;

$magic          = new MagicNumbers();
$cluster        = new Clusters(2);
$servers        = new Servers(2);
$ores           = new Ores(2);
$ingots         = new Ingots(2);
$components     = new Components(2);

$magicData      = $magic->basicData();
$clusterData    = $cluster->basicData(2);
$serversData    = $servers->read();
$oresData       = $ores->read();
$ingotsData     = $ingots->read();
$componentsData = $components->read();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spreadsheet</title>
    <script src="https://kit.fontawesome.com/b61a9642d4.js" crossorigin="anonymous"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <link href="public/css/default.css" type="text/css" rel="stylesheet">
    <script src="public/js/default.js"></script>
</head>
<body>
<?php require_once('menubar.php'); ?>
<article class="tabs">
    <section id="magicNumbers" class="simpleDisplay">
        <h2><a class="headerTitle" href="#magicNumbers">Magic Numbers</a></h2>
        <div class="tab-content">

            <h3>Single Magic Variables</h3>
            <h4>Universal Constants</h4>
            <table>
                <thead>
                <tr>
                    <th>Receipt base efficiency</th>
                    <th>Base Multiplier for Buy vs Sell</th>
                    <th>base refinery kWh</th>
                    <th>Cost per kWh</th>
                    <th>Base refinery speed</th>
                    <th>Base Labor/h</th>
                    <th>Drill kWh</th>
                    <th>Ore gather and process markup</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$magicData->receipt_base_efficiency*100;?>%</td>
                    <td><?=$magicData->base_multiplier_for_buy_vs_sell*100;?>%</td>
                    <td><?=$magicData->base_refinery_kwh;?></td>
                    <td><?=$magicData->cost_kw_hour;?></td>
                    <td><?=$magicData->base_refinery_speed*100;?>%</td>
                    <td><?=$magicData->base_labor_per_hour;?></td>
                    <td><?=$magicData->base_drill_per_kw_hour;?></td>
                    <td><?=$magic->getOreGatherCost();?></td>
                </tr>
                </tbody>
            </table>
            <h4>Server/Cluster Variables</h4>
            <table>
                <thead>
                <tr>
                    <th>How much weight does the system stock have?</th>
                    <th>Number of Systems in cluster * 10</th>
                    <th>Scaling Modifier</th>
                    <th>Foundational (<?='platinum';?>) Ore base value</th>
                    <th>Modifier to Stone Value</th>
                    <th>Total Systems in Cluster</th>
                    <th>Base asteroid scarcity modifier</th>
                    <th>Base planet scarcity modifier</th>
                    <th>Server base multiplier</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th><?=$magicData->base_weight_for_system_stock;?></th>
                    <th><?=$cluster->getTotalServers()*10;?></th>
                    <th><?=$clusterData->scaling_modifier;?></th>
                    <th><?=$clusterData->economy_ore;?></th>
                    <th><?=$clusterData->economy_stone_modifier;?></th>
                    <th><?=$cluster->getTotalServers();?></th>
                    <th><?=$clusterData->asteroid_scarcity_modifier;?></th>
                    <th><?=$clusterData->planet_scarcity_modifier;?></th>
                    <th><?=$clusterData->base_modifier;?></th>
                </tr>
                </tbody>
            </table>
            <h4>Grouped Magic Variables</h4>
            <table>
                <thead>
                <tr>
                    <th>Thing</th>
                    <th>Base processing time per ore</th>
                    <th>Base conversion efficiency</th>
                    <th>Max eff mods</th>
                    <?php foreach($serversData as $server) : ?>
                        <th><?=$server->title;?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($oresData as $ore) : ?>
                    <tr>
                        <td><?=$ore->title;?></td>
                        <td><?=$ore->base_processing_time_per_ore;?></td>
                        <td><?=$ore->getOreEfficiency(0)*100;?>%</td>
                        <td><?=round($ore->getOreEfficiency(4)*100, 2);?>%</td>
                        <?php foreach($servers as $server) : ?>
                            <?php $hasOre = (in_array($ore->id, $server->getOreIds()));?>
                            <td class="<?=($hasOre) ? 'hasOre' : '';?>"><?= ($hasOre) ? "1" : "0"; ?></td>
                        <?php endforeach ?>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>
    <section id="ores" class="simpleDisplay">
      <h2><a class="headerTitle" href="#ores">Ores</a></h2>
      <div class="tab-content">
        <table>
          <thead>
          <tr>
                <th>Name</th>
                <th>Refinery Speed/Base time per ore</th>
                <th>kWh/Ore<br>Refinery kWh</th>
                <th>Ore per Ingot</th>
                <th>Ore per Ingot Max effec</th>
                <th>Base Value</th>
                <th>Store Adjusted</th>
                <th>Scarcity Adjusted Value</th>
                <th>Keen crap fix</th>
                <th>Scarcity Adjustment</th>
                <th>Base cost to gather 1 ore</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach($oresData as $ore) : ?>
          <?php $baseOrePerIngot = $ingots->getOreRequiredPerIngot($ore->id, $ore->module_efficiency_modifier,0); ?>
            <tr>
              <td><?=$ore->title;?></td>
              <td><?=$magicData->base_refinery_speed/$ore->base_processing_time_per_ore;?></td>
              <td><?=round($ore->getRefineryKiloWattHour($magicData->base_refinery_speed),7);?></td>
              <td><?=round($baseOrePerIngot, 2);?></td>
              <td><?=$ingots->getOreRequiredPerIngot($ore->id, $ore->module_efficiency_modifier,4);?></td>
              <td><?=$ore->getBaseValue($baseOrePerIngot);?></td>
              <td><?=round($ore->getStoreAdjustedValue());?></td>
              <td><?=round($ore->getScarcityAdjustedValue());?></td>
              <td><?=$ore->getKeenCrapFix();?></td>
              <td><?=$ore->getScarcityAdjustment();?></td>
              <td><?=$ore->getBaseCostToGatherOre(1);?></td>
            </tr>
          <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </section>
    <section id="ingots" class="simpleDisplay">
        <h2><a class="headerTitle" href="#ingots">Ingots</a></h2>
        <div class="tab-content">
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>base effeciency * conversion efficiency * Ore processed per second</th>
                    <th>Base Value</th>
                    <th>Value with maximum eff modules</th>
                    <th>Store Adjusted Min</th>
                    <th>KEEEN!!!</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($ingotsData as $ingot) : ?>
                    <tr>
                        <td><?= $ingot->title;?></td>
                        <td><?=$ingot->getEfficiencyPerSecond();?></td>
                        <td><?=$ingot->getBaseValue();?></td>
                        <td><?=$ingot->getBaseValueWithEfficiency(4);?></td>
                        <td><?=$ingot->getStoreAdjustedMinimum();?></td>
                        <td><?=$ingot->getKeenCrapFix();?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php foreach($componentsData as $component) : ?>
        <section id="<?=$component->title;?>" class="simpleDisplay">
            <h2><a class="headerTitle" href="#<?=$component->title;?>"><?=$component->title;?></a></h2>
            <div class="tab-content">
                <table>
                    <thead>
                    <tr>
                        <?php foreach($component as $header) : ?>
                            <th><?=$header; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($component->title['rows'] as $data) : ?>
                        <tr>
                            <?php foreach($data as $row) : ?>
                                <td><?= $row; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endforeach; ?>
    <section id="generalValues" class="simpleDisplay">
        <h2><a class="headerTitle" href="#generalValues">General Values</a></h2>
        <div class="tab-content">
            <table>
                <thead>
                <tr><th colspan="3">Ore</th> </tr>
                <tr>
                    <th>Name</th>
                    <th>Store Adjusted</th>
                    <th>Store Adjusted  Scarcity</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($oresData as $ore) : ?>
                    <tr>
                        <td><?=$ore->title;?></td>
                        <td><?=round($ore->getStoreAdjustedValue());?></td>
                        <td><?=round($ore->getScarcityAdjustedValue());?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <thead>
                <tr><th colspan="3">Ingots</th> </tr>
                <tr>
                    <th>Name</th>
                    <th>Store Adjusted</th>
                    <th>Store Adjusted with Scarcity</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($ingotsData as $ingot) : ?>
                    <tr>
                        <td><?=$ingot->title;?></td>
                        <td><?=round($ingot->getStoreAdjustedMinimum());?></td>
                        <td><?=round($ingot->getScarcityAdjustedValue());?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

                <thead>
                    <tr><th colspan="3">Components</th> </tr>
                    <tr>
                        <th>Name</th>
                        <th>Store Adjusted</th>
                        <th>Store Adjusted with Scarcity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($componentsData as $component) : ?>
                    <tr>
                        <td><?=$component->title;?></td>
                        <td><?=round($component->getStoreAdjustedMinimum());?></td>
                        <td><?=round($component->getScarcityAdjustedValue());?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <section id="systemValues" class="simpleDisplay">
        <h2><a class="headerTitle" href="#systemValues">System Values</a></h2>
        <h3>ORE</h3>
        <div class="tab-content">
            <table>
                <thead>
                <tr><th colspan="3">Ore</th> </tr>
                <tr>
                    <th>Name</th>
                    <th>Stock</th>
                    <th>Goal</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($oresData as $thing) : ?>
                    <tr>
                        <td><?= $thing->title; ?></td>
                        <td><?= $thing->getSystemStock(); ?></td>
                        <td><?= $thing->getSystemStockGoal(); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php //variables
    $specialHeaders = ["TZ Data" => 5,"System Data" => 3,"Goals" =>3];
    $baseHeaders = ["Name","Buy","Sell","Stock","Goal","Base Value","Stock","Goal","TZ","System","Adjusted"];
    ?>
    <section id="AlphaTrade" class="simpleDisplay">
        <h2><a class="headerTitle" href="#AlphaTrade">Alpha Trade</a></h2>
        <div class="tab-content">
            <table>
                <thead>
                <tr><th colspan="11">Ore</th> </tr>
                <tr>
                    <?php foreach ($specialHeaders as $header => $span) : ?>
                        <th colspan="<?=$span;?>"><?=$header;?></th>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <?php foreach ($baseHeaders as $header) : ?>
                    <th><?=$header;?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($oresData as $thing) : ?>
                    <tr>
                        <td><?=$thing->title;?></td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                <?php endforeach; ?>
                <tr><th colspan="11">Ingot</th> </tr>
                <tr>
                    <th colspan="5">TZ Data</th>
                    <th colspan="3">System Data</th>
                    <th colspan="3">Goals</th>
                </tr>
                <tr>
                    <th>Name</th>
                    <th>Buy</th>
                    <th>Sell</th>
                    <th>Stock</th>
                    <th>Goal</th>
                    <th>Base Value</th>
                    <th>Stock</th>
                    <th>Goal</th>
                    <th>TZ</th>
                    <th>System</th>
                    <th>Adjusted</th>
                </tr>
                <?php foreach($oresData as $thing) : ?>
                    <tr>
                        <td><?=$thing->title;?></td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                <?php endforeach; ?>
                <tr><th colspan="11">Component</th> </tr>
                <tr>
                    <th colspan="5">TZ Data</th>
                    <th colspan="3">System Data</th>
                    <th colspan="3">Goals</th>
                </tr>
                <tr>
                    <th>Name</th>
                    <th>Buy</th>
                    <th>Sell</th>
                    <th>Stock</th>
                    <th>Goal</th>
                    <th>Base Value</th>
                    <th>Stock</th>
                    <th>Goal</th>
                    <th>TZ</th>
                    <th>System</th>
                    <th>Adjusted</th>
                </tr>
                <?php foreach($componentsData as $thing) : ?>
                    <tr>
                        <td><?=$thing->title;?></td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</article>
</body>
</html>