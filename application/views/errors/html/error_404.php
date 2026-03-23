<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$exception_type = isset($exception) && is_object($exception) ? get_class($exception) : '404 Not Found';
$exception_file = isset($exception) && is_object($exception) ? $exception->getFile() : 'N/A';
$exception_line = isset($exception) && is_object($exception) ? $exception->getLine() : 'N/A';
$exception_trace = isset($exception) && is_object($exception) ? $exception->getTrace() : array();
?>

An uncaught Exception was encountered

Type:        <?php echo $exception_type, "\n"; ?>
Message:     <?php echo isset($message) ? $message : 'The page you requested was not found.', "\n"; ?>
Filename:    <?php echo $exception_file, "\n"; ?>
Line Number: <?php echo $exception_line; ?>

<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE && !empty($exception_trace)): ?>

Backtrace:
<?php foreach ($exception_trace as $error): ?>
<?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
	File: <?php echo $error['file'], "\n"; ?>
	Line: <?php echo isset($error['line']) ? $error['line'] : 'N/A', "\n"; ?>
	Function: <?php echo $error['function'], "\n\n"; ?>
<?php endif; ?>
<?php endforeach; ?>

<?php endif; ?>
