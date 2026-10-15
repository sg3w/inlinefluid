<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\DataProvider;

class DataProviderFactory
{
    public static function getStaticDataProvider(string $type): DataProviderInterface
    {
        return match ($type) {
            'json', 'dataproviderJson' => new JsonDataProvider(),
            'serialized' => new SerializedDataProvider(),
            'xml' => new XmlDataProvider(),
            'yaml' => new YamlDataProvider(),
            'csv' => new CsvDataProvider(),
            'plain' => new PlainDataProvider(),
            default => throw new \InvalidArgumentException(
                'Unknown data provider type "' . $type . '"',
                1760000010
            ),
        };
    }
}
