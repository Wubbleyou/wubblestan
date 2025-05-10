<?php
declare(strict_types=1);

namespace Wubbleyou\Wubblestan\Rules;

use Wubbleyou\Wubblestan\Traits\InteractsWithControllers;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Class_>
 */
class AuthorisationInController implements Rule
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
        $response = [];

        if ($this->isController($className)) {
            foreach ($node->getMethods() as $method) {
                $authorisation = false;

                $statements = $method->getStmts();
                if ($statements === null) {
                    continue;
                }
                
                foreach ($statements as $statement) {
                    if(get_class($statement) === Stmt\Expression::class) {
                        if(get_class($statement->expr) === MethodCall::class) {
                            if($statement->expr->name && $statement->expr->name->name === 'authorize') {
                                $authorisation = true;
                            }
                        }
                    }
                }

                if(!$authorisation) {
                    $response[] = RuleErrorBuilder::message(sprintf(
                        'Method "%s::%s" has no authorisation - implement $this->authorize(...)',
                        $className,
                        (string) $method->name,
                    ))
                    ->identifier('controller.noAuthorisation')
                    ->build();
                }
            }
        }

        return $response;
    }
}