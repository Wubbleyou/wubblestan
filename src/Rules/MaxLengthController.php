<?php
declare(strict_types=1);

namespace Wubbleyou\Wubblestan\Rules;

use Wubbleyou\Wubblestan\Traits\InteractsWithControllers;
use Wubbleyou\Wubblestan\Traits\InteractsWithModels;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Class_>
 */
class MaxLengthController implements Rule
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
                $totalLines = ($method->getEndLine() - $method->getStartLine()) - 2;
                
                if($totalLines > 20) {
                    return [
                        RuleErrorBuilder::message(sprintf(
                            'Method "%s::%s" is too long - there should be a maximum of 20 lines.',
                            $className,
                            (string) $method->name,
                        ))
                        ->identifier('controller.maxLength')
                        ->build(),
                    ];
                }
            }
        }

        return [];
    }
}