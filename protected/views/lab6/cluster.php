<h3>Iteration and Error Criterion</h3>
<table>
    <tr>
        <th>Iteration</th>
        <th>Squared Error Criterion</th>
    </tr><?php foreach($iterationErrorList as $iteration => $errorValue) : ?>
        <tr>
        <td><?php echo $iteration; ?></td>
        <td><?php echo $errorValue; ?></td>
        </tr><?php endforeach; ?>
</table>
<?php foreach ($clusterTweets as $clusterIteration => $tweetList) : ?>
<h1>Cluster <?php echo $clusterIteration; ?></h1>
<table>
    <tr>
        <th>Hashtag</th>
        <th>Nickname</th>
        <th>Tweet</th>
    </tr><?php foreach($tweetList as $tweetItem) : ?>
    <tr>
        <td><?php echo $tweetItem->hashtag; ?></td>
        <td><?php echo $tweetItem->nickname; ?></td>
        <td><?php echo $tweetItem->tweet; ?></td>
    </tr><?php endforeach; ?>
</table>
<hr>
<?php endforeach; ?>