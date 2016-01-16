<div class="form">
    <form method="post" action="/lab5/findforward">
        <div class="row">Sentence</div>
        <div class="row"><input type="text" value="<?php echo $sentence; ?>" name="sentence"></div>
        <div class="row"><input type="submit" value="Calculate"></div>
    </form>
</div><?php if ($sentence != '') : ?>
<div class="row">
    <div class="row"><b>PROBABILITY</b></div>
    <div class="row"><?php echo sprintf('%.20f', $totalProbabilityValue * 100); ?> %</div>
</div>
<br>
<div class="row">
    <div class="row"><b>STATE SEQUENCE</b></div>
    <div class="row">
        <table>
            <tr>
                <th>Observation</th>
                <th>State</th>
            </tr>
            <tr><?php for ($i = 0; $i < count($sentenceWords); $i++) : ?>
                    <td><?php echo $sentenceWords[$i]; ?></td>
                    <td><?php if($transitionPath[$i] != null) : echo $transitionPath[$i]; else : ?><b>undefined</b><?php endif; ?></td>
            </tr><?php endfor; ?>
        </table>
    </div>
</div><?php endif; ?>