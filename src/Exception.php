<?php

namespace BlackFox2;

class Exception extends \Exception {

	/** @var array list of messages */
	protected $messages = [];

	/**
	 * Exception constructor.
	 * @param string|array $message either a string or a list of exception messages to throw
	 * @param int $code [optional] The Exception code.
	 * @param \Throwable $previous [optional] The previous throwable used for the exception chaining.
	 */
	public function __construct($message = [], $code = 0, \Throwable $previous = null) {
		if (empty($message)) {
			$message = get_called_class();
		}
		if (is_array($message)) {
			$this->messages = $message;
			$this->message = implode($this->getImplodeSymbols(), $message);
		}
		if (is_string($message)) {
			$this->message = $message;
			$this->messages = [$message];
		}
		parent::__construct($this->message, $code, $previous);
	}

	public function getMessages() {
		return $this->messages;
	}

	public function getImplodeSymbols() {
		return (php_sapi_name() === 'cli') ? "\r\n" : '<br/>';
	}
}