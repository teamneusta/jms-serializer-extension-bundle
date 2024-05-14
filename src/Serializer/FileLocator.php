<?php declare(strict_types=1);

namespace Neusta\JmsSerializerExtensionBundle\Serializer;

use Metadata\Driver\FileLocator as OriginalFileLocator;

class FileLocator extends OriginalFileLocator
{
    protected const APP_IDENTIFIER = 'app';
    protected const HEADLESS_IDENTIFIER = 'Neusta\Pimcore\HeadlessBundle';

    protected array $dirs = [];

    public function __construct(OriginalFileLocator $fileLocator, string $projectDir, array $non_prefixed_namespaces)
    {
        $property = new \ReflectionProperty(OriginalFileLocator::class, 'dirs');
        $property->setAccessible(true);
        $this->dirs = $property->getValue($fileLocator);
        $this->dirs = array_merge([
            self::APP_IDENTIFIER => $projectDir . '/config/serializer',
            self::HEADLESS_IDENTIFIER => $this->dirs[self::HEADLESS_IDENTIFIER],
        ], $this->dirs);
        parent::__construct($this->dirs);
    }

    public function findFileForClass(\ReflectionClass $class, string $extension): ?string
    {
        foreach ($this->dirs as $prefix => $dir) {
            $nonPrefixed = \in_array($prefix, ['', self::APP_IDENTIFIER, self::HEADLESS_IDENTIFIER]);
            if (!$nonPrefixed && !str_starts_with($class->getNamespaceName(), $prefix)) {
                continue;
            }
            $path = $dir . '/' . str_replace(
                    '\\',
                    '.',
                    substr($class->name, $nonPrefixed ? 0 : \strlen($prefix) + 1)
                ) . '.' . $extension;
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
