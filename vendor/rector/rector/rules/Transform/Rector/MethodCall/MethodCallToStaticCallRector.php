<?php

declare (strict_types=1);
namespace Rector\Transform\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\Transform\ValueObject\MethodCallToStaticCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use RectorPrefix20220501\Webmozart\Assert\Assert;
/**
 * @see \Rector\Tests\Transform\Rector\MethodCall\MethodCallToStaticCallRector\MethodCallToStaticCallRectorTest
 */
final class MethodCallToStaticCallRector extends \Rector\Core\Rector\AbstractRector implements \Rector\Core\Contract\Rector\ConfigurableRectorInterface
{
    /**
     * @var MethodCallToStaticCall[]
     */
    private $methodCallsToStaticCalls = [];
    public function getRuleDefinition() : \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \Symplify\RuleDocGenerator\ValueObject\RuleDefinition('Change method call to desired static call', [new \Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample(<<<'CODE_SAMPLE'
final class SomeClass
{
    private $anotherDependency;

    public function __construct(AnotherDependency $anotherDependency)
    {
        $this->anotherDependency = $anotherDependency;
    }

    public function loadConfiguration()
    {
        return $this->anotherDependency->process('value');
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
final class SomeClass
{
    private $anotherDependency;

    public function __construct(AnotherDependency $anotherDependency)
    {
        $this->anotherDependency = $anotherDependency;
    }

    public function loadConfiguration()
    {
        return StaticCaller::anotherMethod('value');
    }
}
CODE_SAMPLE
, [new \Rector\Transform\ValueObject\MethodCallToStaticCall('AnotherDependency', 'process', 'StaticCaller', 'anotherMethod')])]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [\PhpParser\Node\Expr\MethodCall::class];
    }
    /**
     * @param MethodCall $node
     */
    public function refactor(\PhpParser\Node $node) : ?\PhpParser\Node
    {
        foreach ($this->methodCallsToStaticCalls as $methodCallToStaticCall) {
            if (!$this->isObjectType($node->var, $methodCallToStaticCall->getOldObjectType())) {
                continue;
            }
            if (!$this->isName($node->name, $methodCallToStaticCall->getOldMethod())) {
                continue;
            }
            return $this->nodeFactory->createStaticCall($methodCallToStaticCall->getNewClass(), $methodCallToStaticCall->getNewMethod(), $node->args);
        }
        return null;
    }
    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration) : void
    {
        \RectorPrefix20220501\Webmozart\Assert\Assert::allIsAOf($configuration, \Rector\Transform\ValueObject\MethodCallToStaticCall::class);
        $this->methodCallsToStaticCalls = $configuration;
    }
}
