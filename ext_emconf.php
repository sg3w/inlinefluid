<?php

/*  | This extension is made for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2026 CCDS GmbH <kontakt@ccds.de>
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Inline Fluid Editor for Content Elements',
    'description' => 'Lets integrators and editors write Fluid templates directly in a content element '
        . 'and render them in the frontend, without deploying template files. Adds two content element '
        . 'types for cached (USER) and uncached (USER_INT) rendering, a backend code editor with syntax '
        . 'highlighting, custom variables (input, text, link, RTE), structured data from inline JSON, '
        . 'YAML, XML, CSV or serialized PHP as well as from files, FAL image references, runtime data '
        . 'from your own PHP provider classes, and predefined variables for the current page, content '
        . 'object and TypoScript setup. Supports TYPO3 11.5, 12.4, 13.4 and 14.',
    'category' => 'plugin',
    'author' => 'sg3w.com',
    'author_email' => 'info@sg3w.com',
    'state' => 'beta',
    'clearCacheOnLoad' => 1,
    'version' => '0.2.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.99.99',
            'typo3' => '11.5.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
