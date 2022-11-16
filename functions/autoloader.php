<?php
// Exit if accessed directly.
if ( !defined('ABSPATH') ) {
	die();
}

/**
 *
 * @class WD_Autoloader
 */
class WD_Autoloader
{
	protected $base_path = '';

	/**
	 * Whether to use the legacy API classes.
	 *
	 * @var bool
	 */
	protected $legacy_api = false;

	/**
	 * BCDN_Autoloader constructor.
	 *
	 * @param string $base_path
	 */
	public function __construct($base_path)
	{
		$this->base_path = untrailingslashit($base_path);
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		$this->unregister();
	}

	/**
	 * Register the autoloader.
	 *
	 * @author Jeremy Pry
	 */
	public function register()
	{
		spl_autoload_register(array($this, 'autoload'));
	}

	/**
	 * Unregister the autoloader.
	 */
	public function unregister()
	{
		spl_autoload_unregister(array($this, 'autoload'));
	}


	/**
	 * Autoload a class.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $class The class name to autoload.
	 */
	public function autoload($class)
	{
		$class = strtolower($class);


		if (!$this->should_autoload($class)) {
			return;
		}


		$parent_folder = trim($class, WD_TOKEN . '_');
		$parent_folder = str_replace('_', '-', $parent_folder);


		$full_path = $this->base_path . $this->get_relative_class_path($parent_folder) . $this->get_file_name($class);


		if (is_readable($full_path)) {
			require_once($full_path);
		}
	}


	/**
	 * Determine whether we should autoload a given class.
	 *
	 * @param string $class The class name.
	 *
	 * @return bool
	 */
	protected function should_autoload($class)
	{
		return false !== strpos($class, 'wd_');
	}

	/**
	 * Convert the class name into an appropriate file name.
	 *
	 * @param string $class The class name.
	 *
	 * @return string The file name.
	 */
	protected function get_file_name($class)
	{
		$file_prefix = 'class-';
		if ($this->is_class_abstract($class)) {
			$file_prefix = 'abstract-';
		}
		return $file_prefix . str_replace('_', '-', $class) . '.php';
	}

	/**
	 * Determine if the class is one of our abstract classes.
	 *
	 * @param string $class The class name.
	 *
	 * @return bool
	 */
	protected function is_class_abstract($class)
	{
		static $abstracts = array(
		'' => true,
		);

		return isset($abstracts[$class]);
	}




	/**
	 * Get the relative path for the class location.
	 *
	 * This handles all of the special class locations and exceptions.
	 *
	 * @param string $class The class name.
	 *
	 * @return string The relative path (from the plugin root) to the class file.
	 */
	protected function get_relative_class_path($class)
	{
		$path = '/classes/';
		return trailingslashit($path);
	}



	/**
	 * Set whether the legacy API should be used.
	 *
	 * @param bool $use_legacy_api Whether to use the legacy API classes.
	 *
	 * @return $this
	 */
	public function use_legacy_api($use_legacy_api)
	{
		$this->legacy_api = (bool) $use_legacy_api;

		return $this;
	}
}