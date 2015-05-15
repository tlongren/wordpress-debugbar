<?php namespace Dennie170\DebugBar; 


use DebugBar\StandardDebugBar as StandardDebugBar;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DataCollector\LocalizationCollector;


class DebugbarInit {

	/**
	 * Store the debugbar
	 * @var DebugBar\DebugBar
	 */
	protected $debugbar;
	
	/**
	 * Store the renderer
	 */
	protected $debugbarRenderer;

	/**
	 * Store the config
	 * @var array
	 */
	protected $config;

	public function __construct($config) {
		$this->config = $config;

		$this->debugbar = new WordpressDebugBar();

		$this->loadAssets();

		$this->injectDebugBar();

		$this->addCollectors();

		$this->debugbarRenderer = $this->debugbar->getJavascriptRenderer()
								->setBaseUrl(plugin_dir_url(__FILE__) . '../vendor/maximebf/debugbar/src/DebugBar/Resources')
								->setEnableJqueryNoConflict(true);

		return $this->debugbar;
	}

	/**
	 * Return the debugbar instance
	 * @return Dennie170\DebugBar\WordpressDebugBar
	 */
	public function getDebugBar() {
		return $this->debugbar;
	}


	/**
	 * If turned on in the config,
	 * inject the head and body of the debugbar in to the pages
	 * 
	 * @return void
	 */
	public function injectDebugBar() {

		if($this->config->render) {
			add_action('wp_head', function() {
				echo $this->debugbarRenderer->renderHead();
			});

			add_action('wp_footer', function() {
				echo $this->debugbarRenderer->render();
			});
		}
	}


	# Adds an wordpress action
	public function addAction($action, $function, $priority = 10, $args = 1) {
		add_action($action, array($this, $function), $priority, $args);
	}
	
	/**
	 * Load the nesscesary assets
	 * @return void
	 */
	public function loadAssets() {
		$this->addAction('wp_enqueue_scripts', 'loadScripts');
		$this->addAction('wp_enqueue_scripts', 'loadStyles');
	}


	/**
	 * Load the nesscesary scripts
	 * @return void
	 */
	public function loadScripts() {
		wp_enqueue_script( 'debugbar-js', plugin_dir_url(__FILE__) . '/assets/js/debugbar.js', array('jquery'), false, true);
	}

	/**
	 * Load the nesscesary styles
	 * @return void
	 */
	public function loadStyles() {
		wp_enqueue_style('debubar-css', plugin_dir_url(__FILE__) . '/assets/debugbar.css');
	}

	/**
	 * Add all the collectors to the debugbar
	 *
	 * @return  void
	 */
	public function addCollectors() {

		foreach($this->config->collectors as $collector) {
			$this->debugbar->addCollector(new $collector);
		}

	}



}
