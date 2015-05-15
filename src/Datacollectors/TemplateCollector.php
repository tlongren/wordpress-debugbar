<?php namespace Dennie170\DebugBar\Datacollectors;

use DebugBar\Bridge\Twig\TwigCollector;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\DataCollector\Util\ValueExporter;

class TemplateCollector extends TwigCollector {

	protected $templates = array();

	protected $collect_data;

	protected $usesBlade = false;

	/**
	 * Create a ViewCollector
	 *
	 * @param bool $collectData Collects view data when true
	 */
	public function __construct($collectData = true) {

	    $this->collect_data = $collectData;
	    $this->name = 'views';
	    $this->templates = array();
	    $this->exporter = new ValueExporter();
	    
	    # Blade Templates
	    add_action('template_include_blade', function($template) {
	    	$this->usesBlade = true;
	    	return $this->addTemplate($template);
	    });

	    # Wordpress Templates
	    add_action('template_include', function($template) {
	    	# Only do something when we do not use Blade!
	    	if($this->usesBlade === false) {
	    		return $this->addTemplate($template);
	    	}

	    	return $template;
	    });

	    # WooCommerce template parts
	    add_action('woocommerce_before_template_part', function($template_name) {
	    	
	    	# Determine if the file exists in the theme or in woocommerce
	    	$file = (file_exists($a = get_stylesheet_directory() . '/woocommerce/' . $template_name)) ? $a : WP_PLUGIN_DIR . '/woocommerce/templates/' . $template_name;
	    	
	    	$this->addTemplate("WC => " . $file);
	    });

	}

	/**
	 * Get name of the tab
	 * @return string
	 */
	public function getName() {
	    return 'views';
	}

	/**
	 * Create the widget
	 * @return array
	 */
	public function getWidgets() {
	    return array(
	        'views' => array(
	            'icon' => 'inbox',
	            'widget' => 'PhpDebugBar.Widgets.TemplatesWidget',
	            'map' => 'views',
	            'default' => '[]'
	        ),
	        'views:badge' => array(
	            'map' => 'views.nb_templates',
	            'default' => 0
	        )
	    );
	}

	/**
	 * Adds a template to the debugbar
	 * @param string
	 */
	public function addTemplate($template) {

		$path = basename($template);

		if (substr($path, -10) == '.blade.php') {
		    $type = 'blade';
		} else if (strpos($path, '.php') === false) {
			$type = 'cache';
		} else {
		    $type = pathinfo($path, PATHINFO_EXTENSION);
		}


		$this->templates[] = array(
			'name' => $template,
			'type' => $type,
			'data' => []
		);

		# Return the template back to Wordpress
		return $template;


	}

	/**
	 * Return the data to the DebugBar
	 * @return array
	 */
	public function collect() {

	    $templates = $this->templates;

	    return array(
            'nb_templates' => count($templates),
            'templates' => $templates,
        );
	}

}