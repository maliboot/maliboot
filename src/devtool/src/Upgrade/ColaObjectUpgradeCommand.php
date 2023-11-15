<?php

declare(strict_types=1);

namespace MaliBoot\Devtool\Upgrade;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

#[Command(name: 'cola-up:o')]
class ColaObjectUpgradeCommand extends HyperfCommand
{
    /**
     * php bin/hyperf.php cola-up:o --dir module/bill
     */
    public function configure()
    {
        parent::configure();
        $this->setDescription('将DTO、VO、Entity、DO升级为最新版本');
        $this->addOption('dir', 'D', InputOption::VALUE_OPTIONAL, 'Which dir will be rewrite.', 'module');
    }

    public function handle()
    {
        $dir = $this->input->getOption('dir');

        $dir = BASE_PATH . '/' . $dir;
        if (! is_dir($dir)) {
            $this->output->error('The dir does not exists.');
            return;
        }

        $finder = Finder::create()->files()
            ->ignoreVCS(true)
            ->path(['Client/Dto/Command', 'Client/Dto/Query', 'Client/ViewObject', 'Domain/Model', 'Infra/DataObject'])
            ->in($dir);

        foreach ($finder as $file) {
            /** @var SplFileInfo $file .. */
            $path = $file->getRealPath(); // \Symfony\Component\Finder\SplFileInfo
            $this->upgrade($path);
        }
    }

    protected function upgrade(string $filePath): void
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new class() extends NodeVisitorAbstract {
            public ?Node\Stmt\Namespace_ $namespace_ = null;

            public ?Class_ $class_ = null;

            public function beforeTraverse(array $nodes)
            {
                foreach ($nodes as $namespace) {
                    if ($namespace instanceof Node\Stmt\Declare_) {
                        continue;
                    }

                    if ($namespace instanceof Node\Stmt\Namespace_) {
                        $this->namespace_ = $namespace;
                        foreach ($namespace->stmts as $class) {
                            if ($class instanceof Class_) {
                                $this->class_ = $class;
                                break;
                            }
                        }
                    }
                }
                return null;
            }

            public function leaveNode(Node $node)
            {
                switch ($node) {
                    case $node instanceof Node\Stmt\Namespace_:
                        $this->visitNamespace($node);
                        break;
                    case $node instanceof Interface_:
                        // ...
                        break;
                    case $node instanceof Class_:
                        $this->visitClass($node);
                        break;
                    case $node instanceof ClassMethod:
                        $this->visitClassMethod($node);
                        break;
                    case $node instanceof Property:
                        $this->visitProperty($node);
                        break;
                }
                return null;
            }

            protected function visitNamespace(Node\Stmt\Namespace_ $namespace_): void
            {
                $newStmts = [];
                foreach ($namespace_->stmts as $stmt) {
                    if ($stmt instanceof Use_) {
                        $newUse = $this->visitUse($stmt);
                        $newUse !== null && $newStmts[] = $newUse;
                        continue;
                    }

                    $newStmts[] = $stmt;
                }
                $namespace_->stmts = $newStmts;

                // 添加use
                $insertUses = $this->insertUses();
                empty($insertUses) || $namespace_->stmts = [
                    ...$insertUses,
                    ...$namespace_->stmts,
                ];
            }

            /**
             * @return Use_[] ...
             */
            private function insertUses(): array
            {
                return [
                ];
            }

            protected function visitUse(Use_ $use_): ?Use_
            {
                $useName = $use_->uses[0]->name->toString();
                $dbWillData = [
                    'delete' => [
                        'MaliBoot\Cola\Infra\AbstractDatabaseDO' => 1,
                    ],
                    'update' => [
                        'MaliBoot\Cola\Annotation\DataObject' => 'MaliBoot\Cola\Annotation\Database',
                    ],
                ];

                // 删除
                if (isset($dbWillData['delete'][$useName])) {
                    return null;
                }

                // 修改
                if (isset($dbWillData['update'][$useName])) {
                    $use_->uses[0]->name = new Name($dbWillData['update'][$useName]);
                }

                return $use_;
            }

            protected function visitTraitUse(Node\Stmt\TraitUse $traitUse): ?Node\Stmt\TraitUse
            {
                $newNames = [];
                foreach ($traitUse->traits as $traitName) {
                    if ($traitName->toString() !== 'SoftDeletes') {
                        $newNames[] = $traitName;
                    }
                }

                if (empty($newNames)) {
                    return null;
                }

                $traitUse->traits = $newNames;
                return $traitUse;
            }

            protected function visitClassMethod(ClassMethod $classMethod): void {}

            protected function visitClassAttribute(Class_ $class_, AttributeGroup $attributeGroup): ?AttributeGroup
            {
                $attrGroupName = $attributeGroup->attrs[0]->name->toString();

                // DataObject升级为Database
                if ($attrGroupName === 'DataObject') {
                    $attributeGroup->attrs[0]->name = new Name('Database');
                    if (isset($this->getTraitUseNames()['SoftDeletes'])) {
                        $attributeGroup->attrs[0]->args = [new Node\Arg(
                            value: new Node\Expr\ConstFetch(new Name('true')),
                            name: new Node\Identifier('softDeletes')
                        )];
                    } else {
                        $attributeGroup->attrs[0]->args = [];
                    }
                }

                // DataTransferObject-page升级为query-page
                $extendName = $class_->extends?->toString();
                if ($attrGroupName === 'DataTransferObject' && $extendName === 'AbstractPageQuery') {

                    foreach ($attributeGroup->attrs[0]->args as &$arg) {
                        if ($arg->name->toString() !== 'type') {
                            continue;
                        }

                        $arg->value = new Node\Scalar\String_("query-page");
                    }
                }

                return $attributeGroup;
            }

            /**
             * @return array{string, 1}
             */
            private function getTraitUseNames(): array
            {
                $result = [];
                foreach ($this->class_->stmts as $item) {
                    if (! $item instanceof Node\Stmt\TraitUse) {
                        continue;
                    }
                    foreach ($item->traits as $traitName) {
                        $result[$traitName->toString()] = 1;
                    }
                }

                return $result;
            }

            protected function visitClass(Class_ $class_): void
            {
                $objectAttributes = [
                    'DataObject' => 1, 'DataTransferObject' => 1, 'ViewObject' => 1, 'AggregateRoot' => 1, 'ValueObject' => 1, 'Entity' => 1,
                ];

                $filterAttributes = [];
                $newAttrGroup = [];
                foreach ($class_->attrGroups as $attrGroup) {
                    $attrGroupName = $attrGroup->attrs[0]->name->toString();
                    isset($objectAttributes[$attrGroupName]) && $filterAttributes[] = $objectAttributes[$attrGroupName];

                    $newAttr = $this->visitClassAttribute($class_, $attrGroup);
                    $newAttr !== null && $newAttrGroup[] = $newAttr;
                }
                if (empty($filterAttributes)) {
                    return;
                }

                $newClassStmts = [];
                foreach ($class_->stmts as $classStmt) {
                    if ($classStmt instanceof Node\Stmt\TraitUse) {
                        $classTrait = $this->visitTraitUse($classStmt);
                        $classTrait !== null && $newClassStmts[] = $classTrait;
                        continue;
                    }
                    $newClassStmts[] = $classStmt;
                }
                $class_->stmts = $newClassStmts;

                $class_->attrGroups = $newAttrGroup;
                $class_->extends = null;
                $class_->implements = [];

                $oldDocStr = $class_->getDocComment()?->getText();
                if (!empty($oldDocStr)) {
                    $newDocStr = preg_replace('/\s\*\s*@method.*?\*\//s', ' */', $oldDocStr);
                    $class_->setDocComment(new Doc($newDocStr));
                }
            }

            private function getAttributeArgValByName(AttributeGroup $attributeGroup, string $name): string
            {
                foreach ($attributeGroup->attrs[0]->args as $arg) {
                    if ($arg->name->toString() === $name) {
                        return $arg->value->value;
                    }
                }

                return '';
            }

            protected function visitProperty(Property $property): void
            {
                foreach ($property->attrGroups as $attrGroup) {
                    $attributeName = $attrGroup->attrs[0]->name->toString();
                    $attributeArgName = match ($attributeName) {
                        'Column' => 'desc',
                        'Field' => 'name',
                        default => null
                    };

                    if ($attributeArgName != null) {
                        $propertyDocStr = $this->getAttributeArgValByName($attrGroup, $attributeArgName);
                        empty($propertyDocStr) || $property->setDocComment(new Doc(sprintf(
                            "\n/**\n * %s.\n */",
                            trim(str_replace(["\n", '\\"', "\\'"], ['；', '"'], $propertyDocStr))
                        )));
                        break;
                    }
                }
                $property->attrGroups = [];
            }
        });

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        try {
            $stmts = $parser->parse(file_get_contents($filePath));
            // $stmts is an array of statement nodes
        } catch (\Exception $e) {
            throw $e;
        }
        $modifiedStmts = $traverser->traverse($stmts);
        $printer = new Standard();
        $newCode = $printer->prettyPrintFile($modifiedStmts);
        file_put_contents($filePath, $newCode);
    }
}
