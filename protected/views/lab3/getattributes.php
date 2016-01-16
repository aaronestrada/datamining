%<br>
% Tweets Attributes<br>
%<br>
@relation 'tweets'<br>
<?php foreach ($attributelist as $attributeitem) :?>
@attribute '<?php echo $attributeitem; ?>' {'n','y'}<br>
<?php endforeach; ?><br>
@data<br>
<?php foreach($list as $catalogitem) : echo implode(",", $catalogitem);?><br><?php endforeach?>