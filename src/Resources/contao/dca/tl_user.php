<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user']['palettes']['extend'] = str_replace('formp;', 'formp;{contentboxes_legend},contentboxes,contentboxes_newp;', $GLOBALS['TL_DCA']['tl_user']['palettes']['extend']);
$GLOBALS['TL_DCA']['tl_user']['palettes']['custom'] = str_replace('formp;', 'formp;{contentboxes_legend},contentboxes,contentboxes_newp;', $GLOBALS['TL_DCA']['tl_user']['palettes']['custom']);


/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['contentboxes'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['contentboxes'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_contentboxes_category.name',
	'eval'                    => array('multiple'=>true),
	'sql'					  => 'blob NULL'
);
$GLOBALS['TL_DCA']['tl_user']['fields']['contentboxes_newp'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['contentboxes_newp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('create', 'delete'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'					  => 'blob NULL'
);
