<?php

class GlobalErrorHandler {
    private static $logFile;

    public static function init($logFilePath) {
        self::$logFile = $logFilePath;
        
        // Register handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatalError']);
    }

    public static function handleError($level, $message, $file, $line) {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function handleException(Throwable $exception) {
        self::logError($exception);
        self::sendResponse($exception);
    }

    public static function handleFatalError() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $exception = new ErrorException(
                $error['message'], 0, $error['type'], $error['file'], $error['line']
            );
            self::logError($exception);
            self::sendResponse($exception);
        }
    }

    private static function logError(Throwable $exception) {
        $logMessage = sprintf(
            "[%s] Exception: %s in %s on line %d\nStack trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        error_log($logMessage, 3, self::$logFile);
    }

    private static function sendResponse(Throwable $exception) {
        // Clear any previous output to ensure a clean response
        if (ob_get_length()) ob_clean();

        http_response_code(500);
        $isApi = strpos($_SERVER['REQUEST_URI'], '/api/') !== false;
        
        $errorDetails = [
            'success' => false,
            'error' => [
                'code' => 500,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]
        ];

        if ($isApi) {
            header('Content-Type: application/json');
            echo json_encode($errorDetails);
        } else {
            echo "<h1>500 Internal Server Error</h1>";
            echo "<h2>" . htmlspecialchars($exception->getMessage()) . "</h2>";
            echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . "</p>";
            echo "<p><strong>Line:</strong> " . htmlspecialchars($exception->getLine()) . "</p>";
            echo "<h3>Stack Trace:</h3>";
            echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        }
        exit;
    }
}

// Initialize handler automatically
GlobalErrorHandler::init(realpath(__DIR__ . '/../logs') . '/app.log');
