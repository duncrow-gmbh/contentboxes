<?php

/**
 * Table tl_contentboxes_category
 */
$GLOBALS['TL_DCA']['tl_contentboxes_category'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'					=> 'Table',
		'enableVersioning'				=> true,
		'ctable'						=> array('tl_contentboxes_article'),
		'switchToEdit'					=> true,
		'onload_callback' 				=> array(array('tl_contentboxes_category', 'checkPermission')),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'						=> 1,
			'fields'					=> array('name'),
			'flag'						=> 1,
			'panelLayout'				=> 'filter;search,limit',
		),
		'label' => array
		(
			'fields'					=> array('name'),
			'format'					=> '%s',
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'					=> 'act=select',
				'class'					=> 'header_edit_all',
				'attributes'			=> 'onclick="Backend.getScrollOffset();" accesskey="e"'
			),
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_contentboxes_category']['edit'],
				'href'					=> 'table=tl_contentboxes_article',
				'icon'					=> 'edit.gif'
			),
			'editheader' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_contentboxes_category']['editheader'],
				'href'					=> 'act=edit',
				'icon'					=> 'header.gif'
			),
			'copy' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_contentboxes_category']['copy'],
				'href'					=> 'act=copy',
				'icon'					=> 'copy.gif',
				'button_callback'     	=> array('tl_contentboxes_category', 'copyArchive')
			),
			'delete' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_contentboxes_category']['delete'],
				'href'					=> 'act=delete',
				'icon'					=> 'delete.gif',
				'attributes'			=> 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
				'button_callback'     	=> array('tl_contentboxes_category', 'deleteArchive')
			),
			'show' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_contentboxes_category']['show'],
				'href'					=> 'act=show',
				'icon'					=> 'show.gif'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'						=> '{name_legend},name;{publish_legend},published,start,stop',
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'						=> "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'						=> "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_contentboxes_category']['name'],
			'exclude'					=> true,
			'inputType'					=> 'text',
			'eval'						=> array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'						=> "varchar(255) NOT NULL default ''"
		),
	)
);


class tl_contentboxes_category extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	
/**
	 * Check permissions to edit table tl_contentboxes
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		// Set root IDs
		if (!is_array($this->User->contentboxes) || count($this->User->contentboxes) < 1)
		{
			$root = array(0);
		}
		else
		{
			$root = $this->User->contentboxes;
		}

		$GLOBALS['TL_DCA']['tl_contentboxes_category']['list']['sorting']['root'] = $root;

		// Check permissions to add archives
		// if a no add-permissions, implict no edit-permission
		if (!$this->User->hasAccess('create', 'contentboxes_newp'))
		{
			$GLOBALS['TL_DCA']['tl_contentboxes_category']['config']['closed'] = true;
			unset($GLOBALS['TL_DCA']['tl_contentboxes_category']['list']['operations']['editheader']);
		}

		// Check current action
		switch ($this->Input->get('act'))
		{
			case 'create':
			case 'select':
				// Allow
				break;

			case 'edit':
				// Dynamically add the record to the user profile
				if (!in_array($this->Input->get('id'), $root))
				{
					$arrNew = $this->Session->get('new_records');

					if (is_array($arrNew['tl_contentboxes_category']) && in_array($this->Input->get('id'), $arrNew['tl_contentboxes_category']))
					{
						// Add permissions on user level
						// @todo if rights are extended, add to group instead!
						// but BackendUser inherits no rights for contentboxes-row
						if ($this->User->inherit == 'custom' || !$this->User->groups[0] || $this->User->inherit == 'extend')
						{
							$objUser = $this->Database->prepare("SELECT contentboxes, contentboxes_newp FROM tl_user WHERE id=?")
													   ->limit(1)
													   ->execute($this->User->id);

							$arrNewp = deserialize($objUser->contentboxes_newp);

							if (is_array($arrNewp) && in_array('create', $arrNewp))
							{
								$arrNews = deserialize($objUser->contentboxes);
								$arrNews[] = $this->Input->get('id');

								$this->Database->prepare("UPDATE tl_user SET contentboxes=? WHERE id=?")
											   ->execute(serialize($arrNews), $this->User->id);
							}
						}

						// Add permissions on group level
						elseif ($this->User->groups[0] > 0)
						{
							$objGroup = $this->Database->prepare("SELECT contentboxes, contentboxes_newp FROM tl_user_group WHERE id=?")
													   ->limit(1)
													   ->execute($this->User->groups[0]);

							$arrNewp = deserialize($objGroup->contentboxes_newp);

							if (is_array($arrNewp) && in_array('create', $arrNewp))
							{
								$arrNews = deserialize($objGroup->contentboxes);
								$arrNews[] = $this->Input->get('id');

								$this->Database->prepare("UPDATE tl_user_group SET contentboxes=? WHERE id=?")
											   ->execute(serialize($arrNews), $this->User->groups[0]);
							}
						}

						// Add new element to the user object
						$root[] = $this->Input->get('id');
						$this->User->contentboxes = $root;
					}
				}
				// No break;

			case 'copy':
			case 'delete':
			case 'show':
				if (!in_array($this->Input->get('id'), $root) || ($this->Input->get('act') == 'delete' && !$this->User->hasAccess('delete', 'contentboxes_newp')))
				{
					$this->log('Not enough permissions to '.$this->Input->get('act').' contentboxes category ID "'.$this->Input->get('id').'"', 'tl_contentboxes_category checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'editAll':
			case 'overrideAll':
				$session = $this->Session->getData();
				if (!$this->User->hasAccess('create', 'contentboxes_newp'))
				{
					$session['CURRENT']['IDS'] = array();
				}
				else
				{
					$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
				}
				$this->Session->setData($session);
				break;

			case 'deleteAll':
				$session = $this->Session->getData();
				if ($this->Input->get('act') == 'deleteAll' && !$this->User->hasAccess('delete', 'contentboxes_newp'))
				{
					$session['CURRENT']['IDS'] = array();
				}
				else
				{
					$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
				}
				$this->Session->setData($session);
				break;

			default:
				if (strlen($this->Input->get('act')))
				{
					$this->log('Not enough permissions to '.$this->Input->get('act').' contentboxes categories', 'tl_contentboxes_category checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}
	
	
	/**
	 * Return the copy archive button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function copyArchive($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('create', 'contentboxes_newp')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : ' ';
	}


	/**
	 * Return the delete archive button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('delete', 'contentboxes_newp')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : ' ';
	}	
}
