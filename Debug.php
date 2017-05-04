<?php

/**
 * Debug
 *
 **/
Class Debug {

	public static $debugs = array();
	public static $debugLabels = array();
	public static $allowAdd = true;

	/**
	 * Adds some data to show in the debug info "window". Also has a Shortcut da
	 *
	 * Example:
	 *   Debug::add($myVariable);
	 *   da($myVariable);
	 *
	 * @param        $data
	 * @param string $label
	 * @param int    $backTraceLevel
	 */
	public static function add($data, $label = '', $backTraceLevel = 0) {
		if (static::$allowAdd === true) {
			static::$debugs[] = $data;
			if ($label === '') {
				$backtrace = debug_backtrace();
				$filePath = $backtrace[$backTraceLevel]['file'];
				if (defined('ROOT_DIR')) {
					$filePath = str_replace(ROOT_DIR, '', $filePath);
				}
				$label = $filePath . ':' . $backtrace[$backTraceLevel]['line'];
			}
			static::$debugLabels[] = $label;
		}
	}


	/**
	 * Enables you to only show debug within a certain part of your code.
	 * Typical use case is for function who are called very often but you are only interested in certain calls to them.
	 * Has a shorthand dStart();
	 *
	 * Example:
	 *   Debug::start();
	 *   someComplexFunctionCalls();
	 *   // only debug messages within this function and it's subfunctions are recorded
	 *   Debug::stop();
	 *
	 *   dStart();
	 *   someComplexFunctionCalls();
	 *   dStop();
	 */
	public static function start() {
		static::$debugs = array();
		static::$allowAdd = true;
	}

	/**
	 * Disabled all further adds to the debug stack
	 * Has a shorthand dStop();
	 *
	 * For Example see static::start()
	 */
	public static function stop() {
		static::$allowAdd = false;
	}

	/**
	 * Show the debug info "window".
	 * Call this function only ONCE and usually AFTER the html closing tag
	 *
	 * Example:
	 *   </html><?php Debug::render(); ?>
	 */
	public static function render() {
		if (count(static::$debugs) > 0) {
			echo '<div style="position: fixed !important; left: 0 !important; top: 0 !important; background: #ddd !important; color: #333 !important; z-index: 9999 !important; padding: 20px 10px 10px 10px !important; font-size: 14px !important; line-height: 22px !important; overflow: auto !important; max-height: 100% !important; text-align: left !important; max-width: 70% !important;">';
			echo '  <span style="position: absolute !important; right: 13px !important; top: 5px !important; cursor: pointer !important;" onClick="this.parentNode.style.display = \'none\'">[[x]]</span>';

			foreach(static::$debugs as $key => $debug) {
				echo '<div style="position: relative !important; padding: 10px !important;">';
					echo '<span style="position: absolute !important; right: 10px !important; top: 5px !important; cursor: pointer !important;" onClick="this.parentNode.style.display = \'none\'">[x]</span>';
					echo isset(static::$debugLabels[$key]) &&  is_string(static::$debugLabels[$key]) ? '<p style="font-weight: bold !important; padding-right: 30px !important; margin: 0 !important; ">' . static::$debugLabels[$key] . '</p>' : '';
					var_dump($debug);
				echo '</div>';
			}

			echo '</div>';
		}
	}

	/**
	 * Return all the info from static::render() as string
	 *
	 * @return string
	 */
	public static function getRendered() {
		ob_start();

		static::render();

		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * Debugs to Browser JavaScript Console
	 * Must be within the body tag of an html webpage otherwise the Browser will only output an error.
	 * There is also a shortcode available pc($variable);
	 *
	 * Example:
	 *   Debug::console($variable);
	 * Output (in console - depending on the variable):
	 *   $ Object {((min-width: 361px): Array[2], (min-width: 1367px): Array[1], (min-width: 1025px): Array[2], (min-width: 667px): Array[3]…}
	 *
	 * @param mixed  $data
	 * @param string $title
	 */
	public static function console($data, $title = '$') {
		if (is_array($data) || is_object($data)) {
			echo('<script type="text/javascript">if (console && console.log) { console.log("' . $title . '", ' . json_encode($data) . '); }</script>');
		} else {
			echo('<script type="text/javascript">if (console && console.log) { console.log("' . $title . '", \'' . str_replace("'", "\\'", $data) . '\'); }</script>');
		}
	}

}

// ----------------------------------------------------------------
// SHORTHANDS
// ----------------------------------------------------------------

	function da($var, $label = '') {
		Debug::add($var, $label, 1);
	}

	function de($var, $label = '') {
		Debug::add($var, $label, 1);
		Debug::render();
	}

	function dStart() {
		Debug::start();
	}

	function dStop() {
		Debug::stop();
	}

	function pc($data, $title = '$') {
		Debug::console($data, $title);
	}