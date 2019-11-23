<?php

namespace Ortnit\Entity\Validator;

class Validator
{
    /**
     * @param string $type
     * @return bool
     */
    public function typeExists(string $type): bool
    {
        if (true || class_exists($type)) {
            return true;
        }
        return false;
    }
}
