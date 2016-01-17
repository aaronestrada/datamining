<?php
$clusterCount = 0;
$hashtagSum = 0;
?>
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
<?php foreach ($clusterTweets as $clusterIteration => $tweetData) : ?>
<h1>Cluster <?php echo $clusterIteration; ?> (<?php echo count($tweetData['tweetList']); ?> tweet<?php if(count($tweetData['tweetList']) != 1) :?>s<?php endif; ?>, <?php echo $tweetData['hashtagCount']; ?> hashtag<?php if($tweetData['hashtagCount'] != 1) :?>s<?php endif; ?>)</h1>
    <p style="word-break: break-all"><small><?php echo $tweetData['centroid']; ?></small></p>
<table>
    <tr>
        <th>Hashtag</th>
        <th>Nickname</th>
        <th>Tweet</th>
    </tr><?php foreach($tweetData['tweetList'] as $tweetItem) : ?>
    <tr>
        <td><?php echo $tweetItem->hashtag; ?></td>
        <td><?php echo $tweetItem->nickname; ?></td>
        <td><?php echo $tweetItem->tweet; ?></td>
    </tr><?php endforeach; ?>
</table>
<hr>
<?php endforeach; ?>
<h1>Statistics</h1>
<table>
    <tr>
        <th>Cluster</th>
        <th>Hashtags</th>
        <th>Tweets</th>
    </tr><?php foreach ($clusterTweets as $clusterIteration => $tweetData) : ?>
        <tr>
        <td>Cluster <?php echo $clusterIteration ?></td>
        <td><?php echo $tweetData['hashtagCount']; $hashtagSum = $hashtagSum + $tweetData['hashtagCount']; ?></td>
        <td><?php echo count($tweetData['tweetList']); ?></td>
        </tr><?php $clusterCount++; endforeach; ?>
    <tr>
        <td><b>AVG</b></td>
        <td><?php echo number_format($hashtagSum / $clusterCount, 2); ?></td>
        <td>&nbsp;</td>
    </tr>
</table>