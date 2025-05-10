<?php
declare(strict_types=1);

namespace Wubbleyou\Wubblestan\Rules;

use Wubbleyou\Wubblestan\Traits\InteractsWithControllers;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Class_>
 */
class BusinessLogicInController implements Rule
{
    use InteractsWithControllers;

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return array<\PHPStan\Rules\IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var Class_ $node */
        $className = (string) $node->namespacedName;

        if ($this->isController($className)) {
            foreach ($node->getMethods() as $method) {
                $statements = $method->getStmts();
                if ($statements === null) {
                    continue;
                }
                
                foreach ($statements as $statement) {
                    if ($this->isStatementWithBusinessLogic($statement)) {
                        return [
                            RuleErrorBuilder::message(sprintf(
                                'Method "%s::%s" contains business logic - this should be in services.',
                                $className,
                                (string) $method->name,
                            ))
                            ->identifier('controller.businessLogic')
                            ->build(),
                        ];
                    }
                }
            }
        }

        return [];
    }

    /**
     * @param Node\Stmt $statement
     */
    private function isStatementWithBusinessLogic(Node\Stmt $statement): bool
    {
        return in_array(get_class($statement), [
            Stmt\If_::class,
            Stmt\For_::class,
            Stmt\Foreach_::class,
            Stmt\While_::class,
            Stmt\Switch_::class,
        ]);
    }
}