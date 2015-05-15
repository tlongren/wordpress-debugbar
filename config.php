<?php

return array(

	# Inject the debugbar into the theme
	'render' => true,

	# Set all the collectors to load
	'collectors'  => array(

		'DebugBar\DataCollector\MessagesCollector',
		'DebugBar\DataCollector\MemoryCollector',
		// 'DebugBar\DataCollector\ExceptionsCollector',
		'Dennie170\DebugBar\Datacollectors\QueryCollector',
		'Dennie170\DebugBar\Datacollectors\TemplateCollector',
		'Dennie170\DebugBar\Datacollectors\TimeDataCollector',
		
	),

);