<?php

namespace Wubbleyou\Wubblestan\Traits;

use PhpParser\Node\Stmt;

trait InteractsWithModels {
    /**
     * @param string $class
     * @return boolean
     */
    private function isModel(string $class): bool
    {
        return str_starts_with($class, 'App\Models');
    }

    /**
     * @param PhpParser\Node\Stmt $statement;
     * @return null|string;
     */
    private function getModelFromExpression(Stmt $statement): null|string
    {
        if($statement->expr->class) {
            return $statement->expr->class->name;
        }

        if($statement->expr->expr?->class) {
            return $statement->expr->expr->class->name;
        }

        return null;
    }
}