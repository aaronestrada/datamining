<h1>Transition probabilities for states</h1>
<table>
    <tr>
        <th>From / To</th><?php foreach ($states as $stateItem) : ?>
        <th><?php echo $stateItem; ?></th><?php endforeach; ?>
    </tr><?php foreach($transitionProbabilities as $transitionProbabilityItem => $probabilityValues) : ?>
    <tr>
        <td><?php echo $transitionProbabilityItem; ?></td><?php foreach($probabilityValues as $value) :?>
        <td style="color:<?php if($value == 0) : ?>#FF3333<?php else : ?>#006600<?php endif;?>"><?php echo number_format($value, 4, ',', '.'); ?></td><?php endforeach; ?>
    </tr><?php endforeach; ?>
</table>