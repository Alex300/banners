<?php defined('COT_CODE') or die('Wrong URL.');
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

// Banners API is available everywhere

if(!defined('COT_ADMIN')) require_once cot_incfile('banners', 'plug');
