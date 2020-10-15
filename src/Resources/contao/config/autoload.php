<?php

// Register the namespace
ClassLoader::addNamespace('Duncrow');

// Register the classes
ClassLoader::addClasses(array
(
	// Boxes4ward
	'Duncrow\ContentBoxesBundle\Module\ModuleContentBoxes'  => 'system/modules/content-boxes/Module/ModuleContentBoxes.php',

	// Models
	'Duncrow\ContentBoxesBundle\Model\ArticleModel'         => 'system/modules/content-boxes/Model/ArticleModel.php',
	'Duncrow\ContentBoxesBundle\Model\CategoryModel'        => 'system/modules/content-boxes/Model/CategoryModel.php',
));

// Register the templates
TemplateLoader::addFiles(array
(
	'mod_contentboxes' 					=> 'system/modules/content-boxes/templates',
));
