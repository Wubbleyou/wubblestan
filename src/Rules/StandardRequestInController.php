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
class StandardRequestInController implements Rule
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
                foreach($method->getParams() as $param) {
                    if($param->type && $this->isStandardRequest($param->type?->name)) {
                        return [
                            RuleErrorBuilder::message(sprintf(
                                'Method "%s::%s" uses the standard Laravel request class - each request should be handled in a dedicated FormRequest class',
                                $className,
                                (string) $method->name,
                            ))
                            ->identifier('controller.standardRequestClass')
                            ->build(),
                        ];
                    }
                }
            }
        }

        return [];
    }

    public function isStandardRequest(string $class): bool
    {
        return $class === 'Illuminate\Http\Request';
    }
}