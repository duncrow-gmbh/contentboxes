<?php

// BE-Module
$GLOBALS['BE_MOD']['content']['contentboxes'] = array(
	'tables'  		=> array('tl_contentboxes_category','tl_contentboxes_article','tl_content')
);


// FE-Modules
$GLOBALS['FE_MOD']['miscellaneous']['contentboxes'] = 'ContentBoxesBundle\Module\ModuleContentBoxes';


// add news archive permissions
$GLOBALS['TL_PERMISSIONS'][] = 'contentboxes';
$GLOBALS['TL_PERMISSIONS'][] = 'contentboxes_newp';

// Models
$GLOBALS['TL_MODELS']['tl_contentboxes_category'] = 'ContentBoxesBundle\Model\CategoryModel';
$GLOBALS['TL_MODELS']['tl_contentboxes_article'] = 'ContentBoxesBundle\Model\ArticleModel';
