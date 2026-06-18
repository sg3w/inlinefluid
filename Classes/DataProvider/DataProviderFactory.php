<?php
namespace  Sg3w\Inlinefluid\DataProvider;
class DataProviderFactory
{
    public static function getStaticDataProvider($type) : DataProviderInterface
    {
       // debug($type);
        switch ($type) {
            case 'json':
            case 'dataproviderJson':
                return new JsonDataProvider();
            case 'serialized':
                return new SerializedDataProvider();
            case 'xml':
                return new XmlDataProvider();
            case 'yaml':
                return new YamlDataProvider();
            case 'csv':
                return new CsvDataProvider();
            case 'plain':
                return new PlainDataProvider();
            default:
                throw new \Exception('Unknown data provider type');
        }
    }
}