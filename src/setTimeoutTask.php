<?php
namespace MeTooIDK\SuperHarvest;
use pocketmine\scheduler\Task;

class setTimeoutTask extends Task
{
    private $callback;
    private $args;

    public function __construct(callable $callback, array $args = [])
    {
        $this->callback = $callback;
        $this->args = $args;
    }

    public function onRun(): void
    {
        ($this->callback)(...$this->args);
    }
}
