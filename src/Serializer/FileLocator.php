<?php declare(strict_types=1);

namespace Neusta\JmsSerializerExtensionBundle\Serializer;

use Metadata\Driver\FileLocator as OriginalFileLocator;

class FileLocator extends OriginalFileLocator
{
    protected array $dirs = [];

    public function __construct(OriginalFileLocator $fileLocator, private readonly array $non_prefixed_namespaces)
    {
        $property = new \ReflectionProperty(OriginalFileLocator::class, 'dirs');
        $property->setAccessible(true);
        $this->dirs = $property->getValue($fileLocator);
        parent::__construct($this->dirs);
    }

    public function findFileForClass(\ReflectionClass $class, string $extension): ?string
    {
        $possibleMatches = [];

        foreach ($this->dirs as $prefix => $dir) {
            $nonPrefixed = \array_key_exists($prefix, $this->non_prefixed_namespaces);
            if (!$nonPrefixed && !str_starts_with($class->getNamespaceName(), $prefix)) {
                continue;
            }

            $path = $dir . '/' . str_replace(
                    '\\',
                    '.',
                    substr($class->name, $nonPrefixed ? 0 : \strlen($prefix) + 1)
                ) . '.' . $extension;

            if (file_exists($path)) {
                $possibleMatches[$prefix] = $path;
            }
        }

        if (!empty($possibleMatches)) {
            $possibleMatchesSorted = $this->sortPossibleMatchesByPrefixPriority($possibleMatches);
            // return last one -> the highest priority
            end($possibleMatchesSorted);
            return current($possibleMatchesSorted);
        }

        return null;
    }

    /**
     * @param array<string, string> $possibleMatches prefix => file
     * @return array<string, string> same as input but ordered by prefix priority
     */
    private function sortPossibleMatchesByPrefixPriority(array $possibleMatches): array
    {
        uksort($possibleMatches, function (string $prefix_a, string $prefix_b) {
            $getPrio = function (string $prefix): int {
                if (array_key_exists($prefix, $this->non_prefixed_namespaces) && array_key_exists('priority', $this->non_prefixed_namespaces[$prefix])) {
                    return (int) $this->non_prefixed_namespaces[$prefix]['priority'];
                }

                return 0;
            };

            return $getPrio($prefix_a) <=> $getPrio($prefix_b);
        });

        return $possibleMatches;
    }
}
