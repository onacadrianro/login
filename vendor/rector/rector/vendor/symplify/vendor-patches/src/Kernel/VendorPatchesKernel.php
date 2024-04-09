<?php

declare (strict_types=1);
namespace RectorPrefix20220501\Symplify\VendorPatches\Kernel;

use RectorPrefix20220501\Psr\Container\ContainerInterface;
use RectorPrefix20220501\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use RectorPrefix20220501\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class VendorPatchesKernel extends \RectorPrefix20220501\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \RectorPrefix20220501\Psr\Container\ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = \RectorPrefix20220501\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}