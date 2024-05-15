<?php

declare(strict_types=1);

namespace Application;

interface ServiceInterface
{
    public function handle(InputModelInterface $input): ViewModelInterface;
}
