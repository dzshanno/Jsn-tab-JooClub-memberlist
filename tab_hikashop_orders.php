<?php
/**
* @copyright	Copyright (C) 2016 Kyra.lt All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html

* plus a copuple of changes by dzshanno@yahoo.com to see if we can get thios to work for admin use on the front end
*/

defined('_JEXEC') or die;

class PlgJsnTab_jooclub_members extends JPlugin
{
	
	public function renderTabs($data, $config)
	{
		
		$plugin=array(JText::_($this->params->get('tabtitle','Players')));
		
		$id	= JRequest::getInt( 'id', 0 );

		$user = JFactory::getUser();

		if( $id === 0 && $user->id > 0 )
		{
			$id = $user->id;
		}
		include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php');
		include_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');
	
		//load member info
		$database	= JFactory::getDBO();
		$searchMap = array('p.surname','p.firstname');
		$filters = array('p.cmsuserId='.$id);

		$order = ' ORDER BY p.firstname DESC';
		$query = 'FROM #__cmperson.' AS p WHERE '.implode(' AND ',$filters).$order;
		$database->setQuery('SELECT p.* '.$query);
		$rows = $database->loadObjectList();

		if(empty($rows)){
			return;
		}

		ob_start();
		?>
			<table class="Jooclub members adminlist" style="width:100%" cellpadding="1">
				<tbody>
					<?php
						$k = 0;
						for($i = 0,$a = count($rows);$i<$a;$i++){
							$row =& $rows[$i];
					?>
						<tr class="<?php echo "row$k"; ?>">
							<td class="hikashop_order_number_value" align="center">
								<a href="<?php echo hikashop_completeLink('order&task=show&cid='.$row->order_id.'&Itemid='.$item_id.'&cancel_url='.urlencode(base64_encode(JRoute::_('index.php?option=com_jsn&view=profile')))); ?>">
									<?php echo hikashop_encode($row); ?>
								</a>
							</td>
							<td class="hikashop_order_date_value" align="center">
								<?php echo hikashop_getDate($row->order_created,'%Y-%m-%d %H:%M');?>
							</td>
						</tr>
					<?php
							$k = 1-$k;
						}
	
					?>
				</tbody>
			</table>
		<?php
	    $content = ob_get_clean();
		$plugin[]= $content;
		
		return $plugin;
		
		}
}

