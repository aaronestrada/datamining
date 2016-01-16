<table border="1">
    <tr>
        <th>ID</th><?php foreach ($attributelist as $attributeitem) :?>
            <th><?php echo $attributeitem; ?></th><?php endforeach; ?>
        <th>TOTAL</th>
    </tr>
    <tr><?php foreach($cataloglist as $catalogitem) : $count = 0;?>
        <td><?php echo $catalogitem['id']; ?></td><?php foreach ($attributelist as $attributeitem) :?>
            <td<?php if ($catalogitem[$attributeitem] == true): $count++; ?> style="background-color: #AAA;"<?php endif; ?>><?php echo $catalogitem[$attributeitem] == true ? 1 : '0'; ?></td><?php endforeach; ?>
        <td><?php echo $count; ?></td>
    </tr><?php endforeach; ?>
</table>