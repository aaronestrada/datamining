<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.lab3.CDbFixtureManager',
			),
			/* uncomment the following to provide lab3 database connection
			'db'=>array(
				'connectionString'=>'DSN for lab3 database',
			),
			*/
		),
	)
);
