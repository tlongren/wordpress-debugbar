<?php namespace Dennie170\DebugBar;
 
use DebugBar\DebugBar;
use \Exception;

class WordpressDebugbar extends DebugBar {


	/*
		# COmmented out for later use
	public function __construct() {		
		set_exception_handler(array($this, 'addException'));
	}*/


	public function addMessage($message, $label = 'info') {
	    if ($this->hasCollector('messages')) {
	        /** @var \DebugBar\DataCollector\MessagesCollector $collector */
	        $collector = $this->getCollector('messages');
	        $collector->addMessage($message, $label);
	    }
	}

	/**
	 * Adds a query to the debugbar
	 * @param mixed   The query to display
	 * @param string $label   [description]
	 */
	public function addQuery($message, $label = 'info') {
	    if ($this->hasCollector('queries')) {
	        /** @var \DebugBar\DataCollector\MessagesCollector $collector */
	        $collector = $this->getCollector('queries');
	        $collector->addQuery($message, $label);
	    }
	}


	/**
	 * Adds an exception to be profiled in the debug bar
	 *
	 * @param Exception $e
	 */
	// public function addException(Exception $e) {
	//     if($this->hasCollector('exceptions')){
	//         * @var \DebugBar\DataCollector\ExceptionsCollector $collector 
	//         $collector = $this->getCollector('exceptions');
	//         $collector->addException($e);
	//     }
	// }

}
