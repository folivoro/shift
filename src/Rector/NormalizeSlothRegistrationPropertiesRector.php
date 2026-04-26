<?php

declare(strict_types=1);

namespace Folivoro\Shift\Rector;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use Rector\Privatization\NodeManipulator\VisibilityManipulator;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class NormalizeSlothRegistrationPropertiesRector extends AbstractRector
{
    private const MODEL_PROPERTIES = ['layotter', 'options', 'names', 'labels', 'icon', 'register', 'postType'];
    private const TAXONOMY_PROPERTIES = ['postTypes', 'unique', 'options', 'names', 'labels', 'register'];

    private const ARRAY_PROPERTIES = ['layotter', 'options', 'names', 'labels', 'postTypes', 'register'];

    public function __construct(
        private readonly VisibilityManipulator $visibilityManipulator
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Normalize Sloth registration properties to public static without type',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class Page extends \Sloth\Model\Model
{
    protected array $options = ['public' => true];
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
class Page extends \Sloth\Model\Model
{
    public static $options = ['public' => true];
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $extends = $node->extends;
        if ($extends === null) {
            return null;
        }

        $extendsName = $extends->toLowerString();
        $isModel = str_ends_with($extendsName, 'model');
        $isTaxonomy = str_ends_with($extendsName, 'taxonomy');

        if (!$isModel && !$isTaxonomy) {
            return null;
        }

        $validProperties = $isModel ? self::MODEL_PROPERTIES : self::TAXONOMY_PROPERTIES;
        $hasChanged = false;

        foreach ($node->stmts as $stmt) {
            if (!$stmt instanceof Property) {
                continue;
            }

            $propertyName = $this->getName($stmt);
            if ($propertyName === null) {
                continue;
            }

            if (!in_array($propertyName, $validProperties, true)) {
                continue;
            }

            if (in_array($propertyName, self::ARRAY_PROPERTIES, true)) {
                if ($stmt->type === null) {
                    $stmt->type = new Name('array');
                }
            } else {
                $stmt->type = null;
            }

            if (!$stmt->isPublic()) {
                $this->visibilityManipulator->makePublic($stmt);
            }

            if (!$stmt->isStatic()) {
                $this->visibilityManipulator->makeStatic($stmt);
            }

            $hasChanged = true;
        }

        if (!$hasChanged) {
            return null;
        }

        return $node;
    }
}