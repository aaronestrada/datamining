<table>
    <tr>
        <th>ID</th>
        <th>Word</th>
        <th>Count</th>
        <th>%</th>
    </tr>
    <tr><?php $sequence = 1;
        foreach ($wordlist as $key => $value) : ?>
        <td><?php echo $sequence; ?></td>
        <td><?php echo $key; ?></td>
        <td><?php echo $value; ?></td>
        <td><?php echo ($value/$totalCounter) * 100; ?></td>
    </tr><?php $sequence++;
    endforeach; ?>
</table>