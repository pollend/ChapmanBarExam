<?php


namespace App;


use Serializable;

class JobStatus
{
    private $message;
    private $progress;
    private $maxProgress;
    private $finished = false;
    public function __construct($message, $progress, $maxProgress,$finished=false)
    {
        $this->message = $message;
        $this->progress = $progress;
        $this->maxProgress = $maxProgress;
        $this->finished = $finished;
    }

    /**
     * @return mixed
     */
    public function getMaxProgress()
    {
        return $this->maxProgress;
    }

    /**
     * @return mixed
     */
    public function getProgress()
    {
        return $this->progress;
    }

    public function getPercent()
    {
        if($this->progress == 0 || $this->maxProgress == 0){
            return 0;
        }
        return ($this->progress / $this->maxProgress) * 100.0;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function getFinished() {
        return $this->finished;
    }

}
