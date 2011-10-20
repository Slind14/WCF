<?php
namespace wcf\system\exception;
use wcf\util\StringUtil;

/**
 * A SystemException is thrown when an unexpected error occurs.
 *
 * @author	Marcel Werk
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	system.exception
 * @category 	Community Framework
 */
class SystemException extends \Exception implements IPrintableException {
	/**
	 * error description
	 * @var string
	 */
	protected $description = null;
	
	/**
	 * additional information
	 * @var string
	 */
	protected $information = '';
	
	/**
	 * additional information
	 * @var string
	 */
	protected $functions = '';
	
	/**
	 * Creates a new SystemException.
	 *
	 * @param	string		$message	error message
	 * @param	integer		$code		error code
	 * @param	string		$description	description of the error
	 */
	public function __construct($message = '', $code = 0, $description = '') {
		parent::__construct($message, $code);
		$this->description = $description;
	}
	
	/**
	 * Returns the description of this exception.
	 *
	 * @return 	string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * Removes database password from stack trace.
	 * @see	\Exception::getTraceAsString()
	 */
	public function __getTraceAsString() {
		$string = preg_replace('/Database->__construct\(.*\)/', 'Database->__construct(...)', $this->getTraceAsString());
		$string = preg_replace('/mysqli->mysqli\(.*\)/', 'mysqli->mysqli(...)', $string);
		return $string;
	}
	
	/**
	 * @see wcf\system\exception\IPrintableException::show()
	 */
	public function show() {
		// send status code
		@header('HTTP/1.1 503 Service Unavailable');
		
		// print report
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		
		?>

		<!DOCTYPE html>
		<html>
			<head>
				<title>Fatal error: <?php echo StringUtil::encodeHTML($this->getMessage()); ?></title>
				<style>
					.systemException {
						font-size: 80% !important;
						text-align: left !important;
						border: 1px solid #036;
						border-radius: 7px;
						background-color: #eee !important;
						overflow: auto !important;
					}
					.systemException h1 {
						font-size: 130% !important;
						font-weight: bold !important;
						line-height: 1.1 !important;
						text-decoration: none !important;
						text-shadow: 0 -1px 0 #003 !important;
						color: #fff !important;
						word-wrap: break-word !important;
						border-bottom: 1px solid #036;
						border-top-right-radius: 6px;
						border-top-left-radius: 6px;
						background-color: #369 !important;
						padding: 5px 10px !important;
					}
					.systemException div {
						border-top: 1px solid #fff;
						border-bottom-right-radius: 6px;
						border-bottom-left-radius: 6px;
						padding: 0 10px !important;
					}
					.systemException h2 {
						font-size: 130% !important;
						font-weight: bold !important;
						color: #369 !important;
						text-shadow: 0 1px 0 #fff !important;
						margin: 5px 0 !important;
					}
					.systemException pre, .systemException p {
						text-shadow: none !important;
						color: #666 !important;
						margin: 0 !important;
					}
					.systemException pre {
						font-size: .85em !important;
						font-family: "Courier New";
						text-overflow: ellipsis;
						padding-bottom: 10px;
						overflow: hidden !important;
					}
					.systemException pre:hover{
						text-overflow: clip;
						overflow: auto !important;
					}
				</style>
			</head>
			<body>
				<div class="systemException">
					<h1>Fatal error: <?php echo StringUtil::encodeHTML($this->getMessage()); ?></h1>

					<div>
						<p><?php echo $this->getDescription(); ?></p>
						
						<h2>Information:</h2>
						<p>
							<b>error message:</b> <?php echo StringUtil::encodeHTML($this->getMessage()); ?><br>
							<b>error code:</b> <?php echo intval($this->getCode()); ?><br>
							<?php echo $this->information; ?>
							<b>file:</b> <?php echo StringUtil::encodeHTML($this->getFile()); ?> (<?php echo $this->getLine(); ?>)<br>
							<b>php version:</b> <?php echo StringUtil::encodeHTML(phpversion()); ?><br>
							<b>wcf version:</b> <?php echo WCF_VERSION; ?><br>
							<b>date:</b> <?php echo gmdate('r'); ?><br>
							<b>request:</b> <?php if (isset($_SERVER['REQUEST_URI']))  echo StringUtil::encodeHTML($_SERVER['REQUEST_URI']); ?><br>
							<b>referer:</b> <?php if (isset($_SERVER['HTTP_REFERER'])) echo StringUtil::encodeHTML($_SERVER['HTTP_REFERER']); ?><br>
						</p>

						<h2>Stacktrace:</h2>
						<pre><?php echo StringUtil::encodeHTML($this->__getTraceAsString()); ?></pre>
					</div>

				<?php echo $this->functions; ?>
				</div>
			</body>
		</html>

		<?php
	}
}
