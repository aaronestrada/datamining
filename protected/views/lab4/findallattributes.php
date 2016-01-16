<?php $counter = 1; ?>
<table>
    <thead>
    <tr>
        <th colspan="3">List</th>
    </tr>
    <tr>
        <th>ID</th>
        <th>Attribute</th>
        <th>Count</th>
    </tr>
    </thead>
    <?php foreach ($attributesList as $attribute => $value) : ?>
        <tr>
            <td><?php echo $counter; ?></td>
            <td><?php echo $attribute; ?></td>
            <td><?php echo $value; ?></td>
        </tr>
        <?php $counter++;
        if ($counter > $limitWords) break; endforeach; ?>
</table>