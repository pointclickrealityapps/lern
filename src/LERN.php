<?php

namespace Tylercd100\LERN;

use Exception;
use Tylercd100\LERN\Model\ExceptionModel;
use Tylercd100\LERN\Notifications\Notifier;

/**
* The master class
*/
class LERN 
{

    private $exception;
    private $notifier;
    
    /**
     * @param Notifier|null $notifier Notifier instance
     */
    public function __construct(Notifier $notifier = null)
    {
        if(empty($notifier))
            $notifier = new Notifier();
        $this->notifier = $notifier;
    }

    /**
     * Will execute record and notify methods
     * @param  Exception $e   [description]
     * @return ExceptionModel [description]
     */
    public function handle(Exception $e)
    {
        $this->exception = $e;
        $this->notify($e);
        return $this->record($e);
    }

    /**
     * Stores the exception in the database
     * @param  Exception $e   [description]
     * @return ExceptionModel [description]
     */
    public function record(Exception $e)
    {
        $this->exception = $e;
        $opts = [
            'class'       => get_class($e),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            'code'        => $e->getCode(),
            'message'     => $e->getMessage(),
            'trace'       => $e->getTraceAsString(),
        ];

        if($e instanceof HttpExceptionInterface)
            $opts['status_code'] = $e->getStatusCode();

        return ExceptionModel::create($opts);
    }

    /**
     * Will send the exception to all monolog handlers
     * @param  Exception $e [description]
     * @return [type]       [description]
     */
    public function notify(Exception $e)
    {
        $this->exception = $e;
        $this->notifier->send($e);
    }

    /**
     * Get Notifier
     * @return Notifier [description]
     */
    public function getNotifier()
    {
        return $this->notifier;
    }

    /**
     * Set Notifier
     * @param Notifier $notifier [description]
     */
    public function setNotifier(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

}