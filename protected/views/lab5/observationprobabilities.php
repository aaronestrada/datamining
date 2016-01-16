<h1>Observation probabilities</h1>
<table>
    <tr>
        <th>Observation / State</th><?php foreach($observationList as $observation) :?>
        <th><?php echo $observation; ?></th><?php endforeach; ?>
    </tr><?php foreach($stateObservationProbabilities as $state => $observationProbList) :?>
    <tr>
        <td><?php echo $state; ?></td><?php foreach($observationProbList as $observation => $value) : ?>
        <td style="color:<?php if($value == 0) : ?>#FF3333<?php else : ?>#006600<?php endif;?>"><?php echo number_format($value, 4, ',', '.'); ?></td><?php endforeach; ?>
    </tr><?php endforeach; ?>
</table>
<!--h1>Observation probabilities</h1>
<table>
    <tr>
        <th>Observation / State</th><?php foreach($stateObservationProbabilities as $state => $observationProbList) :?>
            <th><?php echo $state; ?></th><?php endforeach; ?>
    </tr><?php foreach($observationList as $observation) :?>
        <tr>
        <td><?php echo $observation; ?></td><?php foreach($stateObservationProbabilities as $state => $observationProbList) :?>
            <td><?php echo number_format($observationProbList[$observation], 4, ',', '.'); ?></td><?php endforeach; ?>
        </tr><?php endforeach; ?>
</table-->