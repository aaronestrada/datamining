<h1>Initial probabilities for states</h1>
<table>
    <tr>
        <th>State</th>
        <th>Initial probability</th>
    </tr><?php foreach ($initialStateProbabilities as $state => $probability) : ?>
    <tr>
        <td><?php echo $state; ?></td>
        <td style="color:<?php if($probability == 0) : ?>#FF3333<?php else : ?>#006600<?php endif;?>"><?php echo number_format($probability, 4, ',', '.'); ?></td>
    </tr><?php endforeach; ?>
</table>