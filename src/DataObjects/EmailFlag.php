<?php


namespace Effectra\LaravelEmail\DataObjects;


class EmailFlag
{
    public function __construct(
        public bool $recent,
        public bool $flagged,
        public bool $answered,
        public bool $deleted,
        public bool $seen,
        public bool $draft,
    ) {
    }

    public function toArray(): array
    {
        return [
            'recent' => $this->recent,
            'flagged' => $this->flagged,
            'answered' => $this->answered,
            'deleted' => $this->deleted,
            'seen' => $this->seen,
            'draft' => $this->draft,
        ];
    }
}