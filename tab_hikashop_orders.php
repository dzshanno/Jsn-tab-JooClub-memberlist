<?php
/**
* @copyright	Copyright (C) 2016 Kyra.lt All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnTab_Hikashop_orders extends JPlugin
{
	
	public function renderTabs($data, $config)
	{
		$plugin=array(JText::_($this->params->get('tabtitle','Orders')));
		
		$item_id = $this->params->get('item_id','');
		
		$id	= JRequest::getInt( 'id', 0 );

		$user = JFactory::getUser();

		if( $id === 0 && $user->id > 0 )
		{
			$id = $user->id;
		}
		include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php');
		include_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');
		
		// try and find the ID of the user whos profile you want to look at not the logged in user
		$profileuser2 = $this -> JRequest::getInt('uid');
		$profileuser = JsnHelper::getUser();

		//load order info
		$database	= JFactory::getDBO();
		$searchMap = array('a.order_id','a.order_status');
		$filters = array('a.order_user_id='.hikashop_loadUser());

		$order = ' ORDER BY a.order_created DESC';
		$query = 'FROM '.hikashop_table('order').' AS a WHERE '.implode(' AND ',$filters).$order;
		$database->setQuery('SELECT a.* '.$query);
		$rows = $database->loadObjectList();

		if(empty($rows)){
			return;
		}
		$currencyHelper = hikashop_get('class.currency');
		$trans = hikashop_get('helper.translation');
		$statuses = $trans->getStatusTrans();

		ob_start();
		?>
			<table class="hikashop_orders adminlist" style="width:100%" cellpadding="1">
				<thead>
					<tr>
						<th class="hikashop_order_number_title title" style="text-align:center;" align="center">
							<?php echo JText::_('ORDER_NUMBER'); ?> :
							<?php echo $profileuser->id; ?> :
							<?php echo $profileuser2->id; ?> ;
							
						</th>
						<th class="hikashop_order_date_title title" style="text-align:center;" align="center">
							<?php echo JText::_('DATE'); ?>
						</th>
						<th class="hikashop_order_status_title title" style="text-align:center;" align="center">
	 			 		    <?php echo JText::_('ORDER_STATUS'); ?>
						</th>
						<th class="hikashop_order_total_title title" style="text-align:center;" align="center">
							<?php echo JText::_('HIKASHOP_TOTAL'); ?>
						</th>
					</tr>
				</thead>
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
							<td class="hikashop_order_status_value" align="center">
								<?php
									//get translation
									echo $statuses[$row->order_status];
								?>
							</td>
							<td class="hikashop_order_total_value" align="center">
								<?php echo $currencyHelper->format($row->order_full_price,$row->order_currency_id);?>
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

