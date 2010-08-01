{strip}
{*
 * $Header: $
 *
 * Copyright (c) 2010 Tekimaki LLC http://tekimaki.com
 * Copyright (c) 2010 Will James will@tekimaki.com
 *
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details *
 * $Id: $
 * @package libertygraph
 * @subpackage templates
 *}
	<ul>
		{if $gBitUser->hasPermission( 'p_libertygraph_view')}
			<li><a class="item" href="{$smarty.const.LIBERTYGRAPH_PKG_URL}index.php">{tr}Libertygraph Home{/tr}</a></li>


		{/if}


	</ul>
{/strip}