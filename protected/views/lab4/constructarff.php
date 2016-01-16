%<br>
% Tweets Attributes<br>
%<br>
@relation 'tweets'<br>
<?php foreach ($attributesList as $attribute) :?>
    @attribute '<?php echo $attribute; ?>' {'n','y'}<br>
<?php endforeach; ?>
@attribute 'sentiment' {'Positive', 'Neutral', 'Negative'}<br>

<br>
@data<br>
<?php foreach($tweetClassification as $tweetItem) :
    echo implode(",", $tweetItem);?><br>
<?php endforeach;?>