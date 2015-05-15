<?php namespace Dennie170\DebugBar\Datacollectors;

use stdClass;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\TimeDataCollector;

class QueryCollector extends PDOCollector {
	
	protected $query;

	protected $queries = array();

	protected $showHints = true;
	
	function __construct() {

		$this->query = new stdClass();

		# Add the results to the debugbar lateron
		add_action('posts_results', function($x) {
			return $x;
		});

		# Get the where clause of a query
		add_action('posts_clauses', function($clauses) {
			$this->query->clauses = $clauses;
			return $clauses;
		});

		add_action('pre_get_posts', function($wp_query) {			
			$this->query->params = $wp_query->query_vars;

			return $wp_query;

		});

		add_action('posts_request', function($query) {
			$this->query->sql = $query;
			
			$this->getPost($this->query);
			
			return $query;
		});

		# Commented out for later use
		/*add_action('woocommerce_product_query', function() {
			dd(func_get_args());
		});*/

		
	}

	/**
	 * Here we paste the query into the the queries screen, and return the query back to WP!!!!
	 * @return void
	 */
	public function getPost($query) {
		$this->addQuery($query, 'info', true);
	}

	/**
	 * {@inheritDoc}
	 */
	public function collect() {
        $totalTime = 0;
        $queries = $this->queries;

        $statements = array();

        foreach ($queries as $query) {
            $totalTime += $query['time'];

            $statements[] = array(
                'sql' => $this->formatSql($query['query']),
                'duration' => $query['time'],
                'duration_str' => $this->formatDuration($query['time']),
                'params' => $query['params'],
                'stmt_id' => $query['source'],
                'connection' => $query['connection'],
            );

            //Add the results from the explain as new rows
            foreach($query['explain'] as $explain){
                $statements[] = array(
                    'sql' => ' - EXPLAIN #' . $explain->id . ': `' . $explain->table . '` (' . $explain->select_type . ')',
                    'params' => $explain,
                    'row_count' => $explain->rows,
                    'stmt_id' => $explain->id,
                );
            }
        }

        $data = array(
            'nb_statements' => count($queries),
            'nb_failed_statements' => 0,
            'accumulated_duration' => $totalTime,
            'accumulated_duration_str' => $this->formatDuration($totalTime),
            'statements' => $statements
        );

        return $data;
    }

    /**
     * {@inheritDoc}
     */
	public function getName() {
		return 'queries';
	}
	

	/**
	 * Removes extra spaces at the beginning and end of the SQL query and its lines.
	 *
	 * @param string $sql
	 * @return string
	 */
	protected function formatSql($sql)
	{
	    return trim(preg_replace("/\s*\n\s*/", "\n", $sql));
	}

	 /**
     * {@inheritDoc}
     */
	public function getWidgets() {
		$widgets = array(
		    "queries" => array(
                "icon" => "inbox",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "queries",
                "default" => "[]"
            ),
            "queries:badge" => array(
                "map" => "queries.nb_statements",
                "default" => 0
            )
		);

		return $widgets;
	}

	/**
	 * Make the bindings safe for outputting.
	 *
	 * @param array $bindings
	 * @return array
	 */
	protected function escapeBindings($bindings)
	{
	    foreach ($bindings as &$binding) {
	        $binding = htmlentities($binding, ENT_QUOTES, 'UTF-8', false);
	    }
	    return $bindings;
	}

	    /**
	     * Explainer::performQueryAnalysis()
	     *
	     * Perform simple regex analysis on the code
	     *
	     * @package xplain (https://github.com/rap2hpoutre/mysql-xplain-xplain)
	     * @author e-doceo
	     * @copyright 2014
	     * @version $Id$
	     * @access public
	     * @param string $query
	     * @return string
	     */
	    protected function performQueryAnalysis($query)
	    {
	        $hints = array();
	        if (preg_match('/^\\s*SELECT\\s*`?[a-zA-Z0-9]*`?\\.?\\*/i', $query)) {
	            $hints[] = 'Use <code>SELECT *</code> only if you need all columns from table';
	        }
	        if (preg_match('/ORDER BY RAND()/i', $query)) {
	            $hints[] = '<code>ORDER BY RAND()</code> is slow, try to avoid if you can.
					You can <a href="http://stackoverflow.com/questions/2663710/how-does-mysqls-order-by-rand-work" target="_blank">read this</a>
					or <a href="http://stackoverflow.com/questions/1244555/how-can-i-optimize-mysqls-order-by-rand-function" target="_blank">this</a>';
	        }
	        if (strpos($query, '!=') !== false) {
	            $hints[] = 'The <code>!=</code> operator is not standard. Use the <code>&lt;&gt;</code> operator to test for inequality instead.';
	        }
	        if (stripos($query, 'WHERE') === false && preg_match('/^(SELECT) /i', $query)) {
	            $hints[] = 'The <code>SELECT</code> statement has no <code>WHERE</code> clause and could examine many more rows than intended';
	        }
	        if (preg_match('/LIMIT\\s/i', $query) && stripos($query, 'ORDER BY') === false) {
	            $hints[] = '<code>LIMIT</code> without <code>ORDER BY</code> causes non-deterministic results, depending on the query execution plan';
	        }
	        if (preg_match('/LIKE\\s[\'"](%.*?)[\'"]/i', $query, $matches)) {
	            $hints[] = 	'An argument has a leading wildcard character: <code>' . $matches[1]. '</code>.
									The predicate with this argument is not sargable and cannot use an index if one exists.';
	        }
	        return implode("<br />", $hints);
	    }



	public function addQuery($query, $label = 'info', $hints = false) {
	

		$explainResults = array();

		$startTime = microtime(true);;
		$endTime = microtime(true);
		$time = $endTime - $startTime;

		$this->queries[] = array(
		    'query' => $query->sql,
		    'time' => $time,
		    'params' => (object) $query->clauses,
		    'explain' => $explainResults,
		    'connection' => DB_NAME,
		    'source' => $label,
		    'hints' => $hints,
		);

	}


}
