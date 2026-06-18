<?php

/*  | This extension is made for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 CCDS  GmbH <kontakt@ccds.de>
 */

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Inline Fluid Template for tt_content',
	'description' => 'Fluid Editor for tt_content with inline editing and typoscript support',
	'category' => 'plugin',
	'author' => 'sg3w.com',
	'author_email' => 'info@sg3w.com',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 1,
	'version' => '0.0.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '10.4.0-13.9.99',
		),
		'conflicts' => array(
		),
        'suggests' => array(
        )
	),
);
