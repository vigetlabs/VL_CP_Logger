<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * VL CP Logger Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Trevor Davis
 * @link		http://viget.com
 */

class Vl_cp_logger_ext {
	
	public $settings 		= array();
	public $description		= 'Log more actions to the control panel log.';
	public $docs_url		= '';
	public $name			= 'VL CP Logger';
	public $settings_exist	= 'n';
	public $version			= '1.0';
	
	private $EE;
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
		$this->EE->load->library('logger');
	}// ----------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		// Setup custom settings in this array.
		$this->settings = array();
		
		$hooks = array(
			'entry_submission_absolute_end'	=> 'entry_submission_absolute_end',
			'delete_entries_loop'	=> 'delete_entries_loop',
			'update_multi_entries_loop'	=> 'update_multi_entries_loop',
			'update_template_end'	=> 'update_template_end'
		);

		foreach ($hooks as $hook => $method)
		{
			$data = array(
				'class'		=> __CLASS__,
				'method'	=> $method,
				'hook'		=> $hook,
				'settings'	=> serialize($this->settings),
				'version'	=> $this->version,
				'enabled'	=> 'y'
			);

			$this->EE->db->insert('extensions', $data);			
		}
	}	

	// ----------------------------------------------------------------------
	
	/**
	 * entry_submission_absolute_end
	 *
	 * @param entry_id
	 * @param meta
	 * @return void
	 */
	public function entry_submission_absolute_end($entry_id, $meta)
	{
		$channel_id = $meta['channel_id'];
		$this->EE->logger->log_action("Publish/edit entry $entry_id from channel $channel_id");
	}

	// ----------------------------------------------------------------------
	
	/**
	 * delete_entries_loop
	 *
	 * @param val
	 * @param channel_id
	 * @return void
	 */
	public function delete_entries_loop($val, $channel_id)
	{
		$this->EE->logger->log_action("Delete entry $val from channel $channel_id");
	}

	// ----------------------------------------------------------------------
	
	/**
	 * update_multi_entries_loop
	 *
	 * @param id
	 * @param data
	 * @return void
	 */
	public function update_multi_entries_loop($id, $data)
	{
		$this->EE->logger->log_action("Edit entry $id from multi-entry edit");
	}

	// ----------------------------------------------------------------------

	/**
	 * update_template_end
	 *
	 * @param template_id
	 * @param message
	 * @return void
	 */
	public function update_template_end($template_id, $message)
	{
		$this->EE->logger->log_action("Updated template $template_id");
	}

	// ----------------------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}	
	
	// ----------------------------------------------------------------------
}

/* End of file ext.vl_cp_logger.php */
/* Location: /system/expressionengine/third_party/vl_cp_logger/ext.vl_cp_logger.php */