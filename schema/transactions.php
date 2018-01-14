<?php

return [
	'table' => 'transactions',
	'fields' => [
		'eventDatetime' => [
			'name'		=> 'eventDatetime',
			'type' 		=> 'DATETIME',
			'length'	=> false,
			'nullable' 	=> false,
			'default' 	=> false,
			'rule'		=> 'datetime',
			'comment'	=> 'Event Date Time',
		],
		'eventAction' => [
			'name'		=> 'eventAction',
			'type'		=> 'CHAR',
			'nullable'	=> false,
			'length'	=> 20,
			'default' 	=> false,
			'rule'		=> 'required',
			'comment'	=> 'Event Action',
		],
		'callRef' => [
			'name'		=> 'callRef',
			'type' 		=> 'INT',
			'nullable' 	=> false,
			'length'	=> 11,
			'default' 	=> false,
			'rule'		=> 'required',
			'comment'	=> 'Reference',
		],
		'eventValue' => [
			'name'		=> 'eventValue',
			'type' 		=> 'DECIMAL',
			'nullable' 	=> true,
			'length'	=> '10,2',
			'default' 	=> 0,
			'rule'		=> null,
			'comment'	=> 'Event value',
		],
		'eventCurrencyCode' => [
			'name'		=> 'eventCurrencyCode',
			'type' 		=> 'CHAR',
			'nullable' 	=> true,
			'length'	=> 3,
			'default' 	=> null,
			'rule'		=> 'required_if:eventValue:isNot:0|length:3',
			'comment'	=> 'Currency Code e.g GBP',
		],
		'created_at' => [
			'name'		=> 'created_at',
			'type' 		=> 'TIMESTAMP',
			'nullable' 	=> false,
			'length'	=> false,
			'default' 	=> "CURRENT_TIMESTAMP",
			'rule'		=> null,
			'comment'	=> 'Created Date Time',
		],
	],
		
];