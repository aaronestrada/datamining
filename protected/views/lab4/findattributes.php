<?php foreach ($sentimentsList as $sentiment => $sentimentLabel): ?>
    <?php $counter = 1; ?>
    <table>
        <thead>
        <tr>
            <th colspan="3"><?php echo $sentimentLabel; ?></th>
        </tr>
        <tr>
            <th>ID</th>
            <th>Attribute</th>
            <th>Count</th>
        </tr>
        </thead>
        <?php foreach ($attributesList[$sentiment] as $attribute => $value) : ?>
            <tr>
                <td><?php echo $counter; ?></td>
                <td><?php echo $attribute; ?></td>
                <td><?php echo $value; ?></td>
            </tr>
            <?php $counter++;
            if ($counter > $limitWords) break; endforeach; ?>
    </table>
<?php endforeach; ?>
<table>
    <thead>
    <tr>
        <th colspan="2">Merged List</th>
    </tr>
    <tr>
        <th>ID</th>
        <th>Attribute</th>
    </tr>
    </thead>
    <?php $counter = 1; foreach ($mergedAttributesList as $attribute) : ?>
        <tr>
            <td><?php echo $counter; ?></td>
            <td><?php echo $attribute; ?></td>
        </tr>
        <?php $counter++; endforeach; ?>
</table>