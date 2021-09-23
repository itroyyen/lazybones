<?php
/**
 * 核心鍵值定義
 */
return array(
	'Router' => array(
		'class' => 'LZ_Router',
		'requires' => array(
			array('LZ_Router')
		)
	),
	'Input' => array(
		'class' => 'LZ_Input',
		'requires' => array(
			array('LZ_Input')
		)
	),
	'Output' => array(
		'class' => 'LZ_Output',
		'requires' => array(
			array('LZ_Output')
		)
	),
	'View' => array(
		'modes' => array(
			'Basic' => array(
				'class' => 'LZ_View',
				'requires' => array(
					array('LZ_View')
				)
			),
			'Magic' => array(
				'class' => 'LZ_ViewMagic',
				'requires' => array(
					array('LZ_View'),
					array('LZ_ViewMagic')
				)
			)
		),
		'dependences' => array('ViewInterface')
	),
	'ViewInterface' => array(
		'interfaces' => array(
			array('LZ_IView')
		)
	),
	'Controller' => array(
		'class' => 'LZ_Controller',
		'requires' => array(
			array('LZ_Controller')
		)
	),
	'Validator' => array(
		'class' => 'LZ_Validator',
		'requires' => array(
			array('LZ_Validator')
		)
	),
	'ACL' => array(
		'class' => 'LZ_Acl',
		'requires' => array(
			array('LZ_Acl')
		)
	),
	'Model' => array(
		'class' => 'LZ_Model',
		'requires' => array(
			array('LZ_ModelAssistant'),
			array('LZ_Model')
		),
		'dependences' => array('Database')
	),
	'Event' => array(
		'class' => 'LZ_Event',
		'requires' => array(
			array('LZ_Event')
		)
	),
	'Database' => array(
		'modes' => array(
			'MySQL' => array(
				'class' => 'LZ_MySql',
				'requires' => array(
					array('LZ_MySql','driver')
				)
			)
		),
		'dependences' => array('DbDriver')
	),
	'DbDriver' => array(
		'interfaces' => array(
			array('LZ_IDbDriver')
		)
	),
	'Layout' => array(
		'class' => 'LZ_Layout',
		'requires' => array(
			array('LZ_Layout')
		),
		'dependences' => array('View')
	)
);