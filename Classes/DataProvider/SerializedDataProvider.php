<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\DataProvider;

class SerializedDataProvider implements DataProviderInterface
{
    public function renderData(string $dataString): mixed
    {
        $data = unserialize(trim($dataString), ['allowed_classes' => false]);
        if ($data === false && trim($dataString) !== serialize(false)) {
            throw new \RuntimeException('The given data could not be unserialized.', 1760000012);
        }

        return $data;
    }
}
